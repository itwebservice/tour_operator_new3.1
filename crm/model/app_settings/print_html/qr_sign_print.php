<?php
function get_qr($type, $branch_admin_id = 0)
{
    // Get branch-wise QR URL
    $qr_url = get_branch_qr_url($branch_admin_id);
    
    // If no QR URL found, return empty
    if(empty($qr_url)) {
        return '';
    }

    if($type == 'Landscape Advanced')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100   class="img-thumbnail">';
        return $htmlQR;
    }
    // Protrait Advance
    if($type == 'Protrait Advance')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100  class="img-thumbnail">';
        return $htmlQR;
    }
    if($type == 'Protrait Creative')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100  class="img-thumbnail">';
        return $htmlQR;
    }
    //Landscape
    if($type == 'Landscape Creative')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100  class="img-thumbnail">';
        return $htmlQR;
    }
    //Standard
    if($type == 'Landscape Standard')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100  class="img-thumbnail">';
        return $htmlQR;
    }
    //protrait standard
    if($type == 'Protrait Standard')
    {
        $htmlQR = '<img src="'.$qr_url.'" alt="" width=100  class="img-thumbnail">';
        return $htmlQR;
    }
    
    return '';
}

?>

