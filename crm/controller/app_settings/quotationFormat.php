<?php 
include_once('../../model/model.php');
include_once(__DIR__ . '/../../view/app_settings/app_format/quot_format_config.php');

$format = quot_format_resolve_type(isset($_POST['format']) ? $_POST['format'] : '');
$destination = $_POST['destination'];
$imgUrl = imgUrl($_POST['imgUrl']);

mysqlQuery("INSERT INTO `format_image_master`(`dest_id`, `type`, `img_url`, `is_client_upload`) VALUES ('$destination','$format','$imgUrl','1')");

echo "Success";


function imgUrl($urlMain)
  {
    $url = $urlMain;
    $pos = strstr($url,'uploads');
    if ($pos != false)   {
        $newUrl1 = preg_replace('/(\/+)/','/',$urlMain); 
        $newUrl = BASE_URL.str_replace('../', '', $newUrl1);
    }
    else{
        $newUrl =  $urlMain; 
    }
    return $newUrl;
  }
?>