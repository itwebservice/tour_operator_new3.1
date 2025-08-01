<?php
include "../model/model.php"; 
include "../model/b2c_settings/b2c_system_settings.php";

$setting_master = new b2c_system_settings(); 
$setting_master->save();
?>