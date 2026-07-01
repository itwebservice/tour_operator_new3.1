<?php
include '../../../../model/model.php';

header('Content-Type: application/json');

$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$term = isset($_POST['term']) ? trim($_POST['term']) : '';
$city_id = isset($_POST['city_id']) ? intval($_POST['city_id']) : 0;

if ($term === '' || $action === '') {
    echo json_encode(null);
    exit;
}

$termClean = preg_replace('/\s*\/\s*similar/i', '', $term);
$termClean = trim($termClean);
$termEsc = mysqlREString($termClean);

function ai_match_score($needle, $haystack)
{
    $needle = strtolower(trim($needle));
    $haystack = strtolower(trim($haystack));
    if ($needle === '' || $haystack === '') {
        return 0;
    }
    if ($haystack === $needle) {
        return 100;
    }
    if (strpos($haystack, $needle) !== false || strpos($needle, $haystack) !== false) {
        return 80;
    }
    $parts = preg_split('/[\s,–-]+/', $needle);
    $hits = 0;
    foreach ($parts as $part) {
        if (strlen($part) > 2 && strpos($haystack, $part) !== false) {
            $hits++;
        }
    }
    return $hits > 0 ? (50 + ($hits * 5)) : 0;
}

function ai_pick_best($rows, $term)
{
    $best = null;
    $bestScore = 0;
    foreach ($rows as $row) {
        $score = isset($row['score']) ? $row['score'] : ai_match_score($term, $row['text']);
        if ($score > $bestScore) {
            $bestScore = $score;
            $best = $row;
        }
    }
    if ($best && $bestScore >= 30) {
        unset($best['score']);
        return $best;
    }
    return null;
}

if ($action === 'city') {
    $rows = array();
    $sq = mysqlQuery("select city_id, city_name from city_master where active_flag='Active' and (city_name like '%$termEsc%' or city_name like '$termEsc%') order by city_name limit 50");
    while ($row = mysqli_fetch_assoc($sq)) {
        $rows[] = array(
            'id' => $row['city_id'],
            'text' => $row['city_name'],
            'score' => ai_match_score($termClean, $row['city_name'])
        );
    }
    echo json_encode(ai_pick_best($rows, $termClean));
    exit;
}

if ($action === 'destination') {
    $rows = array();

    $sq = mysqlQuery("select airport_id, airport_name, airport_code from airport_master where flag='Active' and (airport_name like '%$termEsc%' or airport_code like '%$termEsc%' or airport_name like '$termEsc%') limit 40");
    while ($row = mysqli_fetch_assoc($sq)) {
        $text = $row['airport_name'] . ' (' . $row['airport_code'] . ')';
        $rows[] = array(
            'id' => 'airport-' . $row['airport_id'],
            'text' => $text,
            'group' => 'airport',
            'score' => max(ai_match_score($termClean, $text), ai_match_score($termClean, $row['airport_name']))
        );
    }

    $sq = mysqlQuery("select city_id, city_name from city_master where active_flag='Active' and (city_name like '%$termEsc%' or city_name like '$termEsc%') limit 40");
    while ($row = mysqli_fetch_assoc($sq)) {
        $rows[] = array(
            'id' => 'city-' . $row['city_id'],
            'text' => $row['city_name'],
            'group' => 'city',
            'score' => ai_match_score($termClean, $row['city_name'])
        );
    }

    $sq = mysqlQuery("select hotel_id, hotel_name from hotel_master where active_flag='Active' and (hotel_name like '%$termEsc%' or hotel_name like '$termEsc%') limit 40");
    while ($row = mysqli_fetch_assoc($sq)) {
        $rows[] = array(
            'id' => 'hotel-' . $row['hotel_id'],
            'text' => $row['hotel_name'],
            'group' => 'hotel',
            'score' => ai_match_score($termClean, $row['hotel_name'])
        );
    }

    echo json_encode(ai_pick_best($rows, $termClean));
    exit;
}

if ($action === 'hotel') {
    $rows = array();
    $cityFilter = $city_id > 0 ? "and city_id='$city_id'" : '';
    $sq = mysqlQuery("select hotel_id, hotel_name, rating_star from hotel_master where active_flag='Active' $cityFilter and (hotel_name like '%$termEsc%' or hotel_name like '$termEsc%') order by hotel_name limit 50");
    while ($row = mysqli_fetch_assoc($sq)) {
        $rows[] = array(
            'id' => $row['hotel_id'],
            'text' => $row['hotel_name'],
            'category' => $row['rating_star'],
            'score' => ai_match_score($termClean, $row['hotel_name'])
        );
    }

    if (!$rows && $city_id > 0) {
        $sq = mysqlQuery("select hotel_id, hotel_name, rating_star from hotel_master where active_flag='Active' and (hotel_name like '%$termEsc%' or hotel_name like '$termEsc%') order by hotel_name limit 50");
        while ($row = mysqli_fetch_assoc($sq)) {
            $rows[] = array(
                'id' => $row['hotel_id'],
                'text' => $row['hotel_name'],
                'category' => $row['rating_star'],
                'score' => ai_match_score($termClean, $row['hotel_name'])
            );
        }
    }

    echo json_encode(ai_pick_best($rows, $termClean));
    exit;
}

echo json_encode(null);
