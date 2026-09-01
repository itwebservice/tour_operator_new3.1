<?php
 $year = date("Y");
 $month = date("M");
 $day = date("d");
 $timestamp = date('U');
 $year_status = false;
 $month_status = false;
 $day_status = false;
 
 function check_dir($current_dir, $type)
{	 	
	if(!is_dir($current_dir))
	{
		mkdir($current_dir, 0777, true);
	}
	if(!is_dir($current_dir."/".$type))
	{
		mkdir($current_dir."/".$type, 0777, true);		
	}	
	$current_dir = $current_dir."/".$type."/";
		return $current_dir;	
}

$current_dir = '../../../../uploads/';
$current_dir = check_dir($current_dir , 'vendor-csv');
$current_dir = check_dir($current_dir , $year);
$current_dir = check_dir($current_dir , $month);
$current_dir = check_dir($current_dir , $day);
$current_dir = check_dir($current_dir , $timestamp);

$file = $current_dir . basename($_FILES['uploadfile']['name']); 

$filename = str_replace(' ', '_', $file);

if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename)) { 
  echo $filename; 
} else {
	echo "error";
}
?>