<?php 
class city_master{

///////////////////////***City Master save start*********//////////////
function city_master_save($city_name, $active_flag_arr){
  for($i=0; $i<sizeof($city_name); $i++){
    $city_name1 = ltrim($city_name[$i]);
    $city_count = mysqli_num_rows(mysqlQuery("select city_name from city_master where city_name='$city_name1'"));
    if($city_count>0){
      echo "error--".$city_name1." already exist!";
      exit;
    }
  }  

  for($i=0; $i<sizeof($city_name); $i++)
  {
      $max_id1 = mysqli_fetch_assoc(mysqlQuery("select max(city_id) as max from city_master"));
      $max_id = $max_id1['max']+1;

      $sq = mysqlQuery("insert into city_master (city_id, city_name, active_flag) values ('$max_id', '$city_name[$i]', '$active_flag_arr[$i]') ");
      if(!$sq)
      {
        echo "error--".$city_name[$i]." not saved!";
        exit;
      }
  } 
  echo "City has been successfully saved.";
}
///////////////////////***City Master save end*********//////////////

///////////////////////***City quick save (single, JSON) start*********//////////////
function city_quick_save($city_name, $active_flag = 'Active'){
  $city_name1 = ltrim($city_name);
  if($city_name1 == ''){
    echo json_encode(array('status' => 'error', 'message' => 'City name is required.'));
    exit;
  }

  $sq_existing = mysqlQuery("select city_id, city_name from city_master where city_name='$city_name1' limit 1");
  if(mysqli_num_rows($sq_existing) > 0){
    $row = mysqli_fetch_assoc($sq_existing);
    echo json_encode(array(
      'status' => 'success',
      'city_id' => $row['city_id'],
      'city_name' => $row['city_name'],
      'existing' => true
    ));
    exit;
  }

  $max_id1 = mysqli_fetch_assoc(mysqlQuery("select max(city_id) as max from city_master"));
  $max_id = $max_id1['max'] + 1;

  $sq = mysqlQuery("insert into city_master (city_id, city_name, active_flag) values ('$max_id', '$city_name1', '$active_flag') ");
  if(!$sq){
    echo json_encode(array('status' => 'error', 'message' => $city_name1 . ' not saved!'));
    exit;
  }

  echo json_encode(array(
    'status' => 'success',
    'city_id' => $max_id,
    'city_name' => $city_name1,
    'existing' => false
  ));
}
///////////////////////***City quick save (single, JSON) end*********//////////////


///////////////////////***City Master Update start*********//////////////
function city_master_update($city_id, $city_name, $active_flag){
  $city_name1 = ltrim($city_name);
  $city_count = mysqli_num_rows(mysqlQuery("select city_name from city_master where city_name='$city_name1' and city_id!='$city_id'"));
  if($city_count>0)
  {
    echo "error--".$city_name1." already exit!";
    exit;
  } 

  $sq = mysqlQuery("update city_master set city_name='$city_name', active_flag='$active_flag' where city_id='$city_id' ");
  if(!$sq)
  {
    echo "error--City name not updated!";
    exit;
  }  
  else
  {
    echo "City has been successfully updated.";
    return true;
  }  
}
///////////////////////***City Master Update end*********//////////////

}
?>