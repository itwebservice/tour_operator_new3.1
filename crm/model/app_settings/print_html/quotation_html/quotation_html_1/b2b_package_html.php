<?php
//Generic Files
include "../../../../model.php"; 
include "printFunction.php";
global $app_quot_img,$app_quot_format;

// Get branch-wise logo (no branch_admin_id in B2B, use default)
$branch_admin_id = isset($_SESSION['branch_admin_id']) ? $_SESSION['branch_admin_id'] : 0;
$admin_logo_url = get_branch_logo_url($branch_admin_id);
$branch_qr_url = get_branch_qr_url($branch_admin_id);

$package_id=$_GET['package_id'];
$sq_pckg = mysqli_fetch_assoc(mysqlQuery("select * from custom_package_master where package_id = '$package_id'"));
$sq_dest = mysqli_fetch_assoc(mysqlQuery("select * from destination_master where dest_id='$sq_pckg[dest_id]'"));
?>

<section class="headerPanel main_block">
  <div class="headerImage">
    <img src="<?= getFormatImg($app_quot_format,$sq_pckg['dest_id'])?>" class="img-responsive"  >
    <div class="headerImageOverLay"></div>
    <!-- style="height:180px !important;" -->
  </div>

  <!-- header -->
  <section class="print_header main_block side_pad mg_tp_30">
    <div class="col-md-4 no-pad">
      <div class="print_header_logo">
        <img src="<?= $admin_logo_url ?>" class="img-responsive mg_tp_10">
      </div>
    </div>
    <div class="col-md-4 no-pad text-center mg_tp_30">
      <span class="title"><i class="fa fa-pencil-square-o"></i> PACKAGE TOUR</span>
    </div>

    <?php 
    include "standard_header_html.php";
    ?>

  </section>

      <!-- Package -->
      <section class="print_sec main_block side_pad mg_tp_30">
        <div class="section_heding">
          <h2>PACKAGE DETAILS</h2>
          <div class="section_heding_img">
            <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="print_info_block">
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M439.1 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L371.2 256 233.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L179.2 256 41.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg><span>DESTINATION :</span> <?= $sq_dest['dest_name'] ?></li>
              </ul>
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M439.1 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L371.2 256 233.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L179.2 256 41.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg><span>PACKAGE NAME :</span> <?= $sq_pckg['package_name'] ?></li>
                <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M439.1 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L371.2 256 233.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L179.2 256 41.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg><span>PACKAGE CODE :</span> <?=  $sq_pckg['package_code'] ?></li>
              </ul>
              <ul class="main_block">
                <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M439.1 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L371.2 256 233.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L179.2 256 41.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg><span>TOTAL DAYS :</span> <?= $sq_pckg['total_days'] ?></li>
                <li class="col-md-6 mg_tp_10 mg_bt_10"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="10.5" viewBox="0 0 448 512" style="margin-right: 5px;"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="<?= $theme_color ?>" d="M439.1 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L371.2 256 233.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160zm-352 160l160-160c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L179.2 256 41.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0z"/></svg><span>TOTAL NIGHTS :</span> <?= $sq_pckg['total_nights'] ?></li>
              </ul>
            </div>
          </div>
        </div>
        <!-- Hotel -->
        <?php
        $sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id='$package_id'"));
        if($sq_hotelc!=0){?>
          <div class="section_heding mg_tp_20">
            <h2>HOTEL DETAILS</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                  <tr class="table-heading-row">
                    <th>City</th>
                    <th>Hotel Name</th>
                    <th>Total Nights</th>
                  </tr>
                </thead>
                <tbody> 
                <?php $sq_hotel = mysqlQuery("select * from custom_package_hotels where package_id='$package_id'");
                while($row_hotel = mysqli_fetch_assoc($sq_hotel)){
                  $hotel_name = mysqli_fetch_assoc(mysqlQuery("select * from hotel_master where hotel_id='$row_hotel[hotel_name]'"));
                  $city_name = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$row_hotel[city_name]'"));
                ?>
                <tr>
                    <?php
                    $sql = mysqli_fetch_assoc(mysqlQuery("select * from hotel_vendor_images_entries where hotel_id='$row_hotel[hotel_name]'"));
                    $sq_count_h = mysqli_num_rows(mysqlQuery("select * from custom_package_hotels where package_id='$package_id' "));
                    if($sq_count_h ==0){
                      $download_url =  BASE_URL.'images/dummy-image.jpg';
                    }
                    else{  
                          $image = $sql['hotel_pic_url']; 
                          $download_url = preg_replace('/(\/+)/','/',$image);
                    }
                    ?>
                    <td><?php echo $city_name['city_name']; ?></td>
                    <td><?php echo $hotel_name['hotel_name'].$similar_text; ?></td>
                    <td><?php echo $row_hotel['total_days']; ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          </div>
        <?php } ?>
        <!-- Transport -->
        <?php
        $sq_hotelc = mysqli_num_rows(mysqlQuery("select * from custom_package_transport where package_id='$package_id'"));
        if($sq_hotelc!=0){?>
          <div class="section_heding mg_tp_10">
            <h2>Transport DETAILS</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered no-marg" id="tbl_emp_list">
                <thead>
                      <tr class="table-heading-row">
                        <th>VEHICLE</th>
                        <th>Pickup</th>
                        <th>Drop</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $count = 0;
                      $sq_hotel = mysqlQuery("select * from custom_package_transport where package_id='$package_id'");
                      while($row_hotel = mysqli_fetch_assoc($sq_hotel)){
                        $transport_name = mysqli_fetch_assoc(mysqlQuery("select * from b2b_transfer_master where entry_id ='$row_hotel[vehicle_name]'"));
                        // Pickup
                        if($row_hotel['pickup_type'] == 'city'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[pickup]'"));
                          $pickup = $row['city_name'];
                        }
                        else if($row_hotel['pickup_type'] == 'hotel'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[pickup]'"));
                          $pickup = $row['hotel_name'];
                        }
                        else{
                          $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[pickup]'"));
                          $airport_nam = clean($row['airport_name']);
                          $airport_code = clean($row['airport_code']);
                          $pickup = $airport_nam." (".$airport_code.")";
                          $html = '<optgroup value="airport" label="Airport Name"><option value="'.$row['airport_id'].'">'.$pickup.'</option></optgroup>';
                        }
                        // Drop
                        if($row_hotel['drop_type'] == 'city'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select city_id,city_name from city_master where city_id='$row_hotel[drop]'"));
                          $drop = $row['city_name'];
                        }
                        else if($row_hotel['drop_type'] == 'hotel'){
                          $row = mysqli_fetch_assoc(mysqlQuery("select hotel_id,hotel_name from hotel_master where hotel_id='$row_hotel[drop]'"));
                          $drop = $row['hotel_name'];
                        }
                        else{
                          $row = mysqli_fetch_assoc(mysqlQuery("select airport_name, airport_code, airport_id from airport_master where airport_id='$row_hotel[drop]'"));
                          $airport_nam = clean($row['airport_name']);
                          $airport_code = clean($row['airport_code']);
                          $drop = $airport_nam." (".$airport_code.")";
                          $html = '<optgroup value="airport" label="Airport Name"><option value="'.$row['airport_id'].'">'.$pickup.'</option></optgroup>';
                        }
                        ?>
                        <tr>
                          <td><?= $transport_name['vehicle_name'].$similar_text ?></td>
                          <td><?= $pickup ?></td>
                          <td><?= $drop ?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
              </table>
            </div>
          </div>
          </div>
        <?php } ?>
        </section>
        
        <!-- Tour Itinenary -->
        <section class="print_sec main_block side_pad mg_tp_30">
          <div class="section_heding">
            <h2>TOUR ITINERARY</h2>
            <div class="section_heding_img">
              <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="print_itinenary main_block no-pad no-marg">          
              <?php 
              $count = 1;
              $sq_package_program = mysqlQuery("select * from custom_package_program where package_id = '$package_id'");
              while($row_itinarary = mysqli_fetch_assoc($sq_package_program)){
              ?>
                <section class="print_single_itinenary main_block">
                  <div class="print_itinenary_count print_info_block">DAY - <?php echo $count++; ?></div>
                  <div class="print_itinenary_desciption print_info_block">
                    <div class="print_itinenary_attraction">
                      <span class="print_itinenary_attraction_icon"><svg xmlns="http://www.w3.org/2000/svg" height="12" width="9" viewBox="0 0 384 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M0 188.6C0 84.4 86 0 192 0S384 84.4 384 188.6c0 119.3-120.2 262.3-170.4 316.8-11.8 12.8-31.5 12.8-43.3 0-50.2-54.5-170.4-197.5-170.4-316.8zM192 256a64 64 0 1 0 0-128 64 64 0 1 0 0 128z"/></svg></span>
                      <samp class="print_itinenary_attraction_location"><?= $row_itinarary['attraction'] ?></samp>
                    </div>
                    <p><?= $row_itinarary['day_wise_program'] ?></p>
                  </div>
                  <div class="print_itinenary_details">
                    <div class="print_info_block">
                      <ul class="main_block no-pad">
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span><svg xmlns="http://www.w3.org/2000/svg" height="12" width="13.5" viewBox="0 0 576 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M32 32c17.7 0 32 14.3 32 32l0 224 224 0 0-128c0-17.7 14.3-32 32-32l160 0c53 0 96 43 96 96l0 224c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-64-448 0 0 64c0 17.7-14.3 32-32 32S0 465.7 0 448L0 64C0 46.3 14.3 32 32 32zm80 160a64 64 0 1 1 128 0 64 64 0 1 1 -128 0z"/></svg> : </span><?= $row_itinarary['stay'] ?></li>
                        <li class="col-md-12 mg_tp_10 mg_bt_10"><span><svg xmlns="http://www.w3.org/2000/svg" height="12" width="12" viewBox="0 0 512 512"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(0, 0, 0)" d="M63.9 14.4C63.1 6.2 56.2 0 48 0s-15.1 6.2-16 14.3L17.9 149.7c-1.3 6-1.9 12.1-1.9 18.2 0 45.9 35.1 83.6 80 87.7L96 480c0 17.7 14.3 32 32 32s32-14.3 32-32l0-224.4c44.9-4.1 80-41.8 80-87.7 0-6.1-.6-12.2-1.9-18.2L223.9 14.3C223.1 6.2 216.2 0 208 0s-15.1 6.2-15.9 14.4L178.5 149.9c-.6 5.7-5.4 10.1-11.1 10.1-5.8 0-10.6-4.4-11.2-10.2L143.9 14.6C143.2 6.3 136.3 0 128 0s-15.2 6.3-15.9 14.6L99.8 149.8c-.5 5.8-5.4 10.2-11.2 10.2-5.8 0-10.6-4.4-11.1-10.1L63.9 14.4zM448 0C432 0 320 32 320 176l0 112c0 35.3 28.7 64 64 64l32 0 0 128c0 17.7 14.3 32 32 32s32-14.3 32-32l0-448c0-17.7-14.3-32-32-32z"/></svg> : </span><?= $row_itinarary['meal_plan'] ?></li>
                      </ul>
                    </div>
                  </div>
                </section>
                <?php } ?>
              </div>
            </div>
          </div>
        </section>
  


      <!-- Inclusion -->
      <section class="print_sec main_block side_pad mg_tp_30">
        <div class="row">
          <?php if($sq_pckg['inclusions']!= ' '){?>
            <div class="col-md-6">
              <div class="section_heding">
                <h2>Inclusions</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['inclusions'] ?>
              </div>
            </div>
          <?php } ?> 


          <!-- Exclusion -->
          <?php if($sq_pckg['exclusions']!= ' '){?>
            <div class="col-md-6">
              <div class="section_heding">
                <h2>Exclusions</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['exclusions'] ?>
              </div>
            </div>
          <?php } ?> 

          <!-- Note -->
          <?php if($sq_pckg['note']!= ''){?>
            <div class="col-md-12">
              <div class="section_heding">
                <h2>Note</h2>
                <div class="section_heding_img">
                  <img src="<?php echo BASE_URL.'images/heading_border.png'; ?>" class="img-responsive">
                </div>
              </div>
              <div class="print_text_bolck">
                <?= $sq_pckg['note'] ?>
              </div>
            </div>
          <?php } ?> 

        </div>
      </section>
  </body>
</html>