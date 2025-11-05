<?php
include "../../../../model/model.php";
include_once('../../../../view/vendor/inc/vendor_generic_functions.php');
?>

<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
    <select class="form-control" name="vendor_type" id="vendor_type" title="Supplier Type" onchange="vendor_type_data_load_p(this.value, 'div_vendor_type_content')">
        <option value="">*Supplier Type</option>
            <?php 
                    $sq_vendor = mysqlQuery("select * from vendor_type_master order by vendor_type");
                    while($row_vendor = mysqli_fetch_assoc($sq_vendor)){
            ?>
                <option value="<?= $row_vendor['vendor_type'] ?>"><?= $row_vendor['vendor_type'] ?></option>
            <?php
            }
            ?>
    </select>
</div>
                

<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
                <select id="estimate_id" class="form-control" name="estimate_id" style="width:100%" title="Supplier Estimate ID" onchange="get_purchase_outstanding();" required>
                      <option value="">*Supplier Estimate ID</option>
                      <?php
                      $sq_estimate = mysqlQuery("select * from vendor_estimate where delete_status='0' order by estimate_id desc");
                      while($row_estimate = mysqli_fetch_assoc($sq_estimate)){

                        $balance_amount = 0;
                        $sq_supplier_p = mysqli_fetch_assoc(mysqlQuery("select sum(payment_amount) as payment_amount from vendor_payment_master where estimate_id='$row_estimate[estimate_id]' and clearance_status!='Pending' AND clearance_status!='Cancelled'"));
                        $total_paid = $sq_supplier_p['payment_amount'];
                        $cancel_est = $row_estimate['cancel_amount'];
                        if($row_estimate['purchase_return'] == '1'){
                          $status = '(Cancelled)';
                          if($total_paid > 0){
                            if($cancel_est >0){
                              if($total_paid > $cancel_est){
                                $balance_amount = 0;
                              }else{
                                $balance_amount = $cancel_est - $total_paid;
                              }
                            }else{
                              $balance_amount = 0;
                            }
                          }
                          else{
                            $balance_amount = $cancel_est;
                          }
                        }else if($row_estimate['purchase_return'] == '2'){
                          $status = '(Cancelled)';
                          $cancel_estimate = (json_decode($row_estimate['cancel_estimate'])[0] === null ) ? 0 : json_decode($row_estimate['cancel_estimate'])[0]->net_total;
                          $balance_amount = (($row_estimate['net_total'] - floatval($cancel_estimate)) + $cancel_est) - $total_paid;
                        }
                        else{
                          $status = '';
                          $balance_amount = $row_estimate['net_total'] - $total_paid;
                        }
                      if($balance_amount > 0){
                        $vendor_type_val = get_vendor_name($row_estimate['vendor_type'], $row_estimate['vendor_type_id']);
                        $estimate_type_val = get_estimate_type_name($row_estimate['estimate_type'], $row_estimate['estimate_type_id']);
                        $date = $row_estimate['purchase_date'];
                        $yr = explode("-", $date);
                        $year = $yr[0];
                        ?>
                        <option value="<?= $row_estimate['estimate_id'] ?>"><?= get_vendor_estimate_id($row_estimate['estimate_id'],$year)." : ".$vendor_type_val."(".$row_estimate['vendor_type'].") : ".$estimate_type_val.' '.$status ?></option>
                        <?php
                        }
                      }
                      ?>
                  </select>
              </div>
