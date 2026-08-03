<style>
  .app_dual_button input[type="radio"]{
display: none;
}
.app_dual_button{
padding: 6px 6px;
border: 1px solid <?= $theme_color ?>;
margin-right: -5px;
background: #fff;
margin-bottom: 0;
cursor: pointer;
font-weight: 300;
font-size: 14px;
}
.app_dual_button label{
margin:0;
}
.app_dual_button.active{
background: <?= $theme_color ?>;
color: #fff;
}
.app_dual_button:first-child{
border-top-left-radius:20px;
border-bottom-left-radius:20px;
}
.app_dual_button:last-child{
border-top-right-radius:20px;
border-bottom-right-radius:20px;
}
label input.labelauty:checked + label{
<!-- background-color: rgba(0, 0, 0, 0.42); -->
color: #ffffff;
}
input.labelauty + label, input.labelauty:checked + label, input.labelauty:checked:not([disabled]) + label:hover{
background-color:<?= $theme_color ?>;
}
#tbl_package_tour_quotation_dynamic_costing .quotation-package-costing-separator,
#quotation_pp_costing_container .quotation-package-costing-separator {
    border: 0;
    border-top: 2px solid #e0e0e0;
    margin: 22px 0;
}
</style>
<form id="frm_tab4" novalidate method="post" action="javascript:void(0);" onsubmit="return false;">

    <div class="app_panel">

        <div class="container" style="width:100% !important;">
            <div class="row">
                <div class="col-md-12">
                <div class="row text-center text_left_sm_xs mg_bt_10">	
                    <?php
                    $is_group_costing = (intval($sq_quotation['costing_type']) != 2);
                    ?>
                             <label for="group_costing" class="app_dual_button mg_bt_10 <?= $is_group_costing ? 'active' : '' ?>">
                                 <input type="radio" id="group_costing" name="costing_tab" <?= $is_group_costing ? 'checked' : '' ?> onchange="costing_reflect()">
                                 &nbsp;&nbsp;Group Costing
                             </label>    
                             <label for="perperson_costing" class="app_dual_button mg_bt_10 <?= !$is_group_costing ? 'active' : '' ?>">
                                 <input type="radio" id="perperson_costing" name="costing_tab" <?= !$is_group_costing ? 'checked' : '' ?> onchange="costing_reflect()">
                                 &nbsp;&nbsp;Per Person Costing
                             </label>
                           </div>
                        </div>
                        <!-- Group Costing -->
                        <div id="group_costing_tab" class="costing_section main_block mg_bt_20">
                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12" >
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <legend >Land Cost</legend>
                                                    <div class="row mg_bt_20_sm_xs">
                                                        <div class="col-xs-12">
                                                            <div class="table-responsive">
                                                                <div id="tbl_package_tour_quotation_dynamic_costing"
                                                                    name="tbl_package_tour_quotation_dynamic_costing" class="table no-marg border_0"
                                                                    disabled>
                                                                    <?php
                                                                    $count = 0;
                                                                    $sq_q_costing = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' ");
                                                                    while ($row_q_costing = mysqli_fetch_assoc($sq_q_costing)) {

                                                                        $count++;

                                                                        $add_class1 = '';
                                                                        if ($role == 'B2b') {
                                                                            $add_class1 = "hidden";
                                                                        } else {
                                                                            $add_class1 = "text";
                                                                        }
                                                                        $basic_cost = $row_q_costing['basic_amount'];
                                                                        $service_charge = $row_q_costing['service_charge'];
                                                                        $bsmValues = json_decode($row_q_costing['bsmValues']);
                                                                        $service_tax_amount = 0;
                                                                        if ($row_q_costing['service_tax_subtotal'] !== 0.00 && ($row_q_costing['service_tax_subtotal']) !== '') {
                                                                            $service_tax_subtotal1 = explode(',', $row_q_costing['service_tax_subtotal']);
                                                                            for ($i = 0; $i < sizeof($service_tax_subtotal1); $i++) {
                                                                                $service_tax = explode(':', $service_tax_subtotal1[$i]);
                                                                                $service_tax_amount = $service_tax_amount + $service_tax[2];
                                                                            }
                                                                        }

                                                                        foreach ($bsmValues[0] as $key => $value) {
                                                                            switch ($key) {
                                                                                case 'basic':
                                                                                    $basic_cost = ($value != "") ? $basic_cost + $service_tax_amount : $basic_cost;
                                                                                    $inclusive_b = $value;
                                                                                    break;
                                                                                case 'service':
                                                                                    $service_charge = ($value != "") ? $service_charge + $service_tax_amount : $service_charge;
                                                                                    $inclusive_s = $value;
                                                                                    break;
                                                                            }
                                                                        }
                                                                        $readonly = isset($inclusive_d) ? 'readonly' : '';
                                                                        if($bsmValues[0]->tax_apply_on == '1') {
                                                                            $tax_apply_on = 'Basic Amount';
                                                                        }
                                                                        else if($bsmValues[0]->tax_apply_on == '2') { 
                                                                            $tax_apply_on = 'Service Charge';
                                                                        }
                                                                        else if($bsmValues[0]->tax_apply_on == '3') { 
                                                                            $tax_apply_on = 'Total';
                                                                        }else{
                                                                            $tax_apply_on = '';
                                                                        }
                                                                    ?>
                                                                    <?php if ($count > 1) { ?>
                                                                    <hr class="quotation-package-costing-separator">
                                                                    <?php } ?>
                                                                    <div class="quotation-group-costing-row mg_bt_20">
                                                                    <div>
                                                                        <div class="header_btn hidden" style="display:none;"><small>&nbsp;</small><input class="css-checkbox" id="chk_costing<?= $count ?>" type="checkbox" checked disabled><span class="css-label" for="chk_costing<?= $count ?>"></span></div>

                                                                        <div class="header_btn hidden" style="display:none;">
                                                                            <small>&nbsp;</small><input maxlength="15" value="1" type="text" name="username" placeholder="Sr. No." class="form-control" disabled /><span>SR.NO</span></div>

                                                                       <div style="display: grid; grid-template-columns: 150px auto;  gap: 15px">
                                                                       <div><small>&nbsp;</small><span>Package Type</span><input type="text" id="package_type-<?= $count ?>" name="package_type-" placeholder="Package Type" title="Package Type" style="width:150px" value="<?= $row_q_costing['package_type'] ?>" readonly></div>
                                                                       
                                                                       <div>
                                                                       <div style="display: grid; grid-template-columns: repeat(7, 1fr);  gap: 15px">
                                                                       <div ><small>&nbsp;</small><span>Hotel Cost</span><input type="text"
                                                                               id="tour_cost-<?= $count ?>" name="tour_cost"
                                                                               placeholder="Hotel Cost" title="Hotel Cost"
                                                                               onchange="validate_balance(this.id);quotation_cost_calculate1(this.id);"
                                                                               value="<?php echo $row_q_costing['tour_cost']; ?>"
                                                                               ></div>

                                                                       <div ><small>&nbsp;</small><span>Transport Cost</span><input type="text"
                                                                               id="transport_cost-<?= $count ?>" name="transport_cost"
                                                                               placeholder="Transport Cost" title="Transport Cost"
                                                                               onchange="validate_balance(this.id);quotation_cost_calculate1(this.id)"
                                                                               value="<?php echo $row_q_costing['transport_cost']; ?>"
                                                                               ></div>

                                                                       <div ><small>&nbsp;</small><span>Activity Cost</span><input type="text"
                                                                               id="excursion_cost-<?= $count ?>" name="excursion_cost"
                                                                               onchange="quotation_cost_calculate1(this.id); validate_balance(this.id)"
                                                                               placeholder="Activity Cost" title="Activity Cost"
                                                                               value="<?= $row_q_costing['excursion_cost'] ?>"
                                                                               ></div>

                                                                       <div ><small id="basic_show-"
                                                                               style="color:#000000">&nbsp;</small><span>Basic
                                                                               Amount</span><input type="<?= $add_class1 ?>"
                                                                               id="basic_amount-<?= $count ?>" name="basic_amount" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'true');validate_balance(this.id)"
                                                                               placeholder="Basic Amount" title="Basic Amount" 
                                                                               value="<?= $row_q_costing['basic_amount'] ?>" readonly></div>

                                                                       <div ><small id="service_show-"
                                                                               style="color:#000000">&nbsp;</small><span>Service charge</span><input type="<?= $add_class1 ?>"
                                                                               id="service_charge-<?= $count ?>" name="service_charge"
                                                                               onchange="get_business(this.id,'false');quotation_cost_calculate1(this.id); validate_balance(this.id)"  placeholder="Service charge" title="Service charge" value="<?= $row_q_costing['service_charge'] ?>"></div>

                                                                       <div ><small>&nbsp;</small> <span>Discount In</span><select title="Discount In" id="discount_in-<?= $count ?>" name="discount_in-" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'true');">
                                                                           <?php
                                                                           $discount_in_val = trim((string) ($row_q_costing['discount_in'] ?? ''));
                                                                           if ($discount_in_val !== 'Percentage' && $discount_in_val !== 'Flat') {
                                                                             $discount_in_val = 'Percentage';
                                                                           }
                                                                           ?>
                                                                           <option value="Percentage" <?= $discount_in_val === 'Percentage' ? 'selected' : '' ?>>Percentage</option>
                                                                           <option value="Flat" <?= $discount_in_val === 'Flat' ? 'selected' : '' ?>>Flat</option>
                                                                           </select></div>

                                                                       <div ><small>&nbsp;</small><span>Discount</span><input type="<?= $add_class1 ?>" id="discount_amt-<?= $count ?>" name="discount_amt-" onchange="quotation_cost_calculate1(this.id); get_business(this.id,'false');validate_balance(this.id)" placeholder="Discount" title="Discount" value="<?= $row_q_costing['discount'] ?>" ></div>
                                                                       </div>

                                                                      <div style="display: grid; grid-template-columns: repeat(7, 1fr);  gap: 15px ;margin-top: 15px;">
                                                                      <div ><small id="tax_apply_show-" style="color:#000000">&nbsp;</small><span>Tax Apply On</span><select title="Tax Apply On" id="atax_apply_on-<?= $count ?>" name="atax_apply_on-<?= $count ?>" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'false');" >
                                                                       <?php $tax_apply_on_val = isset($bsmValues[0]->tax_apply_on) ? (string) $bsmValues[0]->tax_apply_on : ''; ?>
                                                                               <option value="">*Tax Apply On</option>
                                                                               <option value="1" <?= $tax_apply_on_val === '1' ? 'selected' : '' ?>>Basic Amount</option>
                                                                               <option value="2" <?= $tax_apply_on_val === '2' ? 'selected' : '' ?>>Service Charge</option>
                                                                               <option value="3" <?= $tax_apply_on_val === '3' ? 'selected' : '' ?>>Total</option>
                                                                           </select></div>
                                                                       
                                                                       <div ><small id="tax_show-" style="color:#000000">&nbsp;</small><span>Select Tax</span><select title="Select Tax" id="tax_value1-<?= $count ?>" name="tax_value1-<?= $count ?>" class="form-control" onchange="quotation_cost_calculate1(this.id);get_business(this.id,'false');" >
                                                                           <?php $tax_value_val = isset($bsmValues[0]->tax_value) ? (string) $bsmValues[0]->tax_value : ''; ?>
                                                                           <option value="">*Select Tax</option>
                                                                           <?php
                                                                           $sq_tax = mysqlQuery("SELECT * FROM `tax_master` where status='Active' and reflection='Income'");
                                                                           while ($row_tax = mysqli_fetch_assoc($sq_tax)) {
                                                                             $tax_string = $row_tax['name1'] . ':(' . $row_tax['amount1'] . '%):(' . $row_tax['ledger1'] . ')';
                                                                             $tax_string .= ($row_tax['name2'] != '') ? '+' . $row_tax['name2'] . ':(' . $row_tax['amount2'] . '%):(' . $row_tax['ledger2'] . ')' : '';
                                                                             $tax_selected = ($tax_value_val !== '' && $tax_value_val === $tax_string) ? 'selected' : '';
                                                                           ?>
                                                                             <option value="<?= htmlspecialchars($tax_string, ENT_QUOTES) ?>" <?= $tax_selected ?>><?= htmlspecialchars($tax_string) ?></option>
                                                                           <?php } ?>
                                                                           </select></div>

                                                                       <div ><small>&nbsp;</small><span>Tax Amount</span><input type="text"
                                                                               id="service_tax_subtotal-<?= $count ?>" name="service_tax_subtotal"
                                                                               readonly placeholder="Tax Amount" title="Tax Amount"
                                                                               value="<?= $row_q_costing['service_tax_subtotal'] ?>"
                                                                               ></div>
                                                                      
                                                                        <div >
                                                                           <small id="tcs_tax_show-" style="color:#000000">&nbsp;</small><span>TCS</span>
                                                                           <select title="TCS" id="tcs_tax-<?= $count ?>" name="tcs_tax-<?= $count ?>" class="form-control" >
                                                                               <option value="">*TCS Tax</option>
                                                                               <option value="2" <?php if($bsmValues[0]->tcsper==2) { echo "selected"; } ?> >2% TCS</option>
                                                                               <option value="20" <?php if($bsmValues[0]->tcsper==20) { echo "selected"; } ?>>20% TCS</option>
                                                                           </select>
                                                                       </div>

                                                                <div >
                                                                           <small id="tcs_tax_show-" style="color:#000000">&nbsp;</small><span>TCS</span>
                                                                            <input type="number" name="tcs-<?= $count ?>" id="tcs1-<?= $count ?>" readonly class="text-right"
                                                                               placeholder="TCS" title="TCS" value="<?= $bsmValues[0]->tcsvalue ?>" >
                                                                               
                                                                       </div>        
                                                                       
                                                                       
                                                                       <div  style="display:none;"> <span>TDSss</span>
                                                                           <small id="tcs_tax_show-" style="color:#000000">&nbsp;</small>
                                                                            <input type="number" name="tds-<?= $count ?>" id="tds" readonly class="text-right"
                                                                               placeholder="TDS" title="TDS" value="0.00" >
                                                                              
                                                                       </div>
                                                                       
                                                                       <div ><small>&nbsp;</small><span>Total Cost</span><input type="text"
                                                                               id="total_tour_cost-<?= $count ?>"
                                                                               class="amount_feild_highlight text-right" name="total_tour_cost"
                                                                               placeholder="Total Cost" title="Total Cost"
                                                                               value="<?= $row_q_costing['total_tour_cost'] ?>"
                                                                               readonly></div>

                                                                       <div ><small>&nbsp;</small><input type="text"
                                                                               id="package_name1-<?= $count ?>" name="package_name1"
                                                                               placeholder="Package Name" title="Package Name"
                                                                               value="<?php echo '0'; ?>" style="display: none" readonly>
                                                                       </div>
                                                                           <div ><input type="hidden"
                                                                               value="<?= $bsmValues[0]->tax_apply_on ?>" id="atax_apply_on-<?= $count ?>"></div>

                                                                       <div ><input type="hidden" id="costing_entry_id-<?= $count ?>"
                                                                               value="<?= $row_q_costing['id'] ?>"></div>

                                                                           </div>
                                                                      </div>
                                                                       </div>
                                                                       </div>
                                                                    </div>

                                                                    <?php

                                                                    }

                                                                    ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mg_tp_30">
                                            <div class="col-xs-12" >
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <legend >Travel Cost</legend>
                                                    <!-- Other costs -->
                                                    <div class="row">
                                                        <div class="col-md-2  col-xs-12 mg_bt_10">
                                                            <span>Flight Cost</span>
                                                            <input type="text" id="flight_cost1" value="<?php echo $sq_quotation['flight_cost']; ?>"
                                                                name="flight_cost" placeholder="Flight Cost" title="Flight Cost"
                                                                onchange="validate_balance(this.id)">
                                                        </div>
                                                        <div class="col-md-2  col-xs-12 mg_bt_10">
                                                            <span>Train Cost</span>
                                                            <input type="text" id="train_cost1" name="train_cost"
                                                                value="<?php echo $sq_quotation['train_cost']; ?>" placeholder="Train Cost"
                                                                title="Train Cost" onchange="validate_balance(this.id)">
                                                        </div>
                                                        <div class="col-md-2  mg_bt_10">
                                                            <span>Cruise Cost</span>
                                                            <input type="text" id="cruise_cost1" name="cruise_cost1" placeholder="Cruise Cost"
                                                                value="<?php echo $sq_quotation['cruise_cost']; ?>" title="Cruise Cost"
                                                                onchange="validate_balance(this.id)">
                                                        </div>
                                                        <div class="col-md-2  col-xs-12 mg_bt_10">
                            <span>Visa Cost</span>
                            <input type="text" id="visa_cost1" value="<?php echo $sq_quotation['visa_cost']; ?>"
                                name="visa_cost" placeholder="Visa Cost" title="Visa Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-2  mg_bt_10">
                            <span>Guide Cost</span>
                            <input type="text" id="guide_cost1" name="guide_cost1" placeholder="Guide Cost"
                                value="<?php echo $sq_quotation['guide_cost']; ?>" title="Guide Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-2  mg_bt_10">
                            <span>Miscellaneous Cost</span>
                            <input type="text" id="misc_cost1" name="misc_cost1" placeholder="Miscellaneous Cost"
                                value="<?php echo $sq_quotation['misc_cost']; ?>" title="Miscellaneous Cost"
                                onchange="validate_balance(this.id)">
                        </div>
                        <div class="col-md-4  mg_bt_10">
                            <span>Miscellaneous Description</span>
                            <textarea id="other_desc1" name="other_desc1" placeholder="Miscellaneous Description" title="Miscellaneous Description"><?php echo $sq_quotation['other_desc']; ?></textarea>
                        </div>
                        <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10">
                        <span>Currency</span>

                    <select name="currency_code1" id="currency_code1" title="Currency" style="width:100%"
                        data-toggle="tooltip" required>
                        <?php
						$sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $sq_quotation['currency_code']));
						?>
                        <option value="<?= $sq_currencyd['id'] ?>"><?= $sq_currencyd['currency_code'] ?></option>
                        <option value=''>*Select Currency</option>
                        <?php
						$sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
						while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
						?>
                        <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                        </div>
                        <!-- Per Person Costing -->
                        <div id="per_person_costing_tab" class="costing_section main_block mg_bt_20" style="display: none;">
                              <?php
                                     ensure_pp_costing_package_type_column();
                                     // Load PP rows keyed by package_type + pax_type
                                     $pp_by_package = array();
                                     $pp_legacy = array();
                                     $sq_pp = mysqlQuery("SELECT * FROM package_quotation_pp_costing WHERE quotation_id='$quotation_id'");
                                     if ($sq_pp) {
                                         while ($row = mysqli_fetch_assoc($sq_pp)) {
                                             $ptype_key = trim((string) (isset($row['package_type']) ? $row['package_type'] : ''));
                                             if ($ptype_key === '') {
                                                 $pp_legacy[$row['pax_type']] = $row;
                                             } else {
                                                 if (!isset($pp_by_package[$ptype_key])) {
                                                     $pp_by_package[$ptype_key] = array();
                                                 }
                                                 $pp_by_package[$ptype_key][$row['pax_type']] = $row;
                                             }
                                         }
                                     }

                                     // Package list from costing entries (same as Group Costing multi rows)
                                     $pp_package_list = array();
                                     $sq_cost_pp = mysqlQuery("select * from package_tour_quotation_costing_entries where quotation_id='$quotation_id' order by sort_order, id");
                                     while ($row_ce = mysqli_fetch_assoc($sq_cost_pp)) {
                                         $pp_package_list[] = $row_ce;
                                     }
                                     if (!count($pp_package_list)) {
                                         $pp_package_list[] = array('package_type' => 'NA', 'id' => '');
                                     }
                                     // If legacy PP has no package_type, attach to first package
                                     if (count($pp_legacy) && count($pp_package_list)) {
                                         $first_type = trim((string) $pp_package_list[0]['package_type']);
                                         if ($first_type !== '' && empty($pp_by_package[$first_type])) {
                                             $pp_by_package[$first_type] = $pp_legacy;
                                         }
                                     }
                                     $sq_cost_count = count($pp_package_list);
                                     $currency_code_selected = isset($sq_quotation['currency_code']) ? $sq_quotation['currency_code'] : '';
                              ?>
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <legend>Land Cost</legend>
                                                    <input type="hidden" id="sq_ppcost_count" value="<?= $sq_cost_count ?>" />
                                                    <div id="quotation_pp_costing_container">
                                                    <?php
                                                    $pp_total = count($pp_package_list);
                                                    for ($ppi = 0; $ppi < $pp_total; $ppi++) {
                                                        $row_ce = $pp_package_list[$ppi];
                                                        $package_type = isset($row_ce['package_type']) ? $row_ce['package_type'] : 'NA';
                                                        $entry_id = isset($row_ce['id']) ? $row_ce['id'] : '';
                                                        $suffix = ($pp_total <= 1) ? '' : ('-' . ($ppi + 1));
                                                        $pp_pkg = isset($pp_by_package[$package_type]) ? $pp_by_package[$package_type] : array();
                                                        if ($ppi > 0) {
                                                            echo '<hr class="quotation-package-costing-separator">';
                                                        }
                                                        include __DIR__ . '/pp_costing_package_block.php';
                                                    }
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                        </div>
                </div>
        </div>
      </div>

                 

            <div class="row  mg_tp_20 text-center row  text-center mg_bt_30">
                <div class="col-xs-12">
                    <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab3()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
                    &nbsp;&nbsp;
                    <button class="btn btn-sm btn-success" type="button" id="btn_quotation_update"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Update</button>
                </div>
            </div>
         
    </div>
</form>
<?= end_panel(); ?>
<script>
function getQuotationEditorContent(textareaId) {
    var $target = $('#' + textareaId);
    if (!$target.length) {
        return '';
    }
    if ($target.data('wysiwyg')) {
        return $target.wysiwyg('getContent') || '';
    }
    var iframe = document.getElementById(textareaId + '-wysiwyg-iframe');
    if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
        return iframe.contentWindow.document.body.innerHTML || '';
    }
    return $target.val() || '';
}

function getInclusionsExclusionsForQuotation() {
    if ($('#inclusions_ai').length) {
        return {
            inclusions: getQuotationEditorContent('inclusions_ai'),
            exclusions: getQuotationEditorContent('exclusions_ai')
        };
    }
    return {
        inclusions: getQuotationEditorContent('inclusions1'),
        exclusions: getQuotationEditorContent('exclusions1')
    };
}
function get_business(id, flag, change = false) {
    var offset = id.split('-');
    get_auto_values('quotation_date', 'basic_amount-' + offset[1], 'payment_mode', 'service_charge-' + offset[1],'markup', 'update', flag, 'markup', 'discount_amt-'+ offset[1], offset[1], change);
}

function switch_to_tab3() {
    $('#tab4_head').removeClass('active');
    $('#tab3_head').addClass('active');
    $('.bk_tab').removeClass('active');
    $('#tab3').addClass('active');
    $('html, body').animate({
        scrollTop: $('.bk_tab_head').offset().top
    }, 200);
}

function upload_price_struct() {
    var btnUpload = $('#price_structure1');
    if (!btnUpload.length || typeof AjaxUpload === 'undefined') {
        return;
    }
    $(btnUpload).find('span').text('Price Structure');

    new AjaxUpload(btnUpload, {
        action: '../upload_price_structure.php',
        name: 'uploadfile',
        onSubmit: function(file, ext) {
            if (!(ext && /^(xlsx|docx|xls)$/.test(ext))) {
                error_msg_alert('Only Excel or word files are allowed');
                return false;
            }
            $(btnUpload).find('span').text('Uploading...');
        },
        onComplete: function(file, response) {
            if (response === "error") {
                error_msg_alert("File is not uploaded.");
                $(btnUpload).find('span').text('Upload');
                return false;
            } else {
                $(btnUpload).find('span').text('Uploaded');
                success_msg_alert('File is uploaded!');
                $("#upload_url1").val(response);
            }
        }
    });
}


function quotation_cost_calculate1(id) {
    var offset = id.split('-');
    var quotation_cost = 0;
    var tour_cost = $('#tour_cost-' + offset[1]).val();
    var transport_cost = $('#transport_cost-' + offset[1]).val();
    var excursion_cost = $('#excursion_cost-' + offset[1]).val();
    var service_tax = $('#service_tax-' + offset[1]).val();
	var discount_in = $('#discount_in-' + offset[1]).val();
	var discount_amt = $('#discount_amt-' + offset[1]).val();

    if (tour_cost == '') {
        tour_cost = 0;
    }
    if (transport_cost == '') {
        transport_cost = 0;
    }
    if (excursion_cost == '') {
        excursion_cost = 0;
    }
	if (discount_amt == '') {
		discount_amt = 0;
	}

    var sub_total = parseFloat(tour_cost) + parseFloat(transport_cost) + parseFloat(excursion_cost);
    $('#basic_amount-' + offset[1]).val(sub_total.toFixed(2));

    if (id != 'basic_amount-' + offset[1]) {
        $('#basic_amount-' + offset[1]).trigger('change');
    }

    var service_charge = $('#service_charge-' + offset[1]).val();
    var service_tax_subtotal = $('#service_tax_subtotal-' + offset[1]).val();
    if (service_charge == '') {
        service_charge = 0;
    }
	var service_tax_amount = 0;
	if (parseFloat(service_tax_subtotal) !== 0.0 && service_tax_subtotal !== '' && typeof service_tax_subtotal != 'undefined') {
		var service_tax_subtotal1 = service_tax_subtotal.split(',');
		for (var i = 0; i < service_tax_subtotal1.length; i++) {
			var service_tax = service_tax_subtotal1[i].split(':');
			service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
		}
	}

	var discountable_amt = parseFloat(service_charge);
	if(discount_in == 'Percentage'){
		var discount = parseFloat(discountable_amt) * parseFloat(discount_amt) / 100;
	}
	else{
		var discount = (service_charge != 0) ? parseFloat(discount_amt) : 0;
	}
	var after_discount_amt = parseFloat(discountable_amt) - parseFloat(discount);
    customTcsTax(offset[1]);

    var tcs_amt = $('#tcs1-' + offset[1]).val();
    if (tcs_amt == '') {
        tcs_amt = 0;
    }

    var total_amt = parseFloat(sub_total) + parseFloat(service_tax_amount) + parseFloat(after_discount_amt) + parseFloat(tcs_amt);
    $('#total_tour_cost-' + offset[1]).val(total_amt.toFixed(2));
}

function initQuotationUpdateForm() {
    if ($('#currency_code1').length && !$('#currency_code1').data('select2')) {
        $('#currency_code1').select2();
    }
    upload_price_struct();

    if ($('#frm_tab4').data('validator')) {
        return;
    }

$('#frm_tab4').validate({

    rules: {

        currency_code1: {
            required: true
        }

    },

    submitHandler: function(form, e) {

        e.preventDefault();

        function getRowInputValue(row, cellIndex) {
            if (!row || !row.cells || !row.cells[cellIndex]) {
                return '';
            }
            var input = row.cells[cellIndex].querySelector('input, select, textarea');
            if (!input) {
                return '';
            }
            var $input = $(input);
            if ($input.data('select2')) {
                return $input.val() || '';
            }
            return input.value || '';
        }

        function getRowCheckboxChecked(row, cellIndex) {
            if (!row || !row.cells || !row.cells[cellIndex]) {
                return 'false';
            }
            var checkbox = row.cells[cellIndex].querySelector('input[type="checkbox"]');
            return (checkbox && checkbox.checked) ? 'true' : 'false';
        }

        function getTableRows(tableId) {
            var table = document.getElementById(tableId);
            return (table && table.rows) ? table.rows : [];
        }

        function collectUpdateCostingEntries() {
            var el = document.getElementById('tbl_package_tour_quotation_dynamic_costing');
            if (!el) {
                return [];
            }
            if (el.tagName === 'DIV') {
                var entries = [];
                $(el).find('[id^="tour_cost-"]').each(function() {
                    var suffix = this.id.replace('tour_cost-', '');
                    if (!suffix) {
                        return;
                    }
                    var checkbox = document.getElementById('chk_costing' + suffix);
                    if (checkbox && !checkbox.checked) {
                        return;
                    }
                    entries.push({
                        package_type_c: $('#package_type-' + suffix).val() || '',
                        tour_cost: $('#tour_cost-' + suffix).val() || '',
                        transport_cost: $('#transport_cost-' + suffix).val() || '',
                        excursion_cost: $('#excursion_cost-' + suffix).val() || '',
                        basic_amount: $('#basic_amount-' + suffix).val() || '',
                        service_charge: $('#service_charge-' + suffix).val() || '',
                        discount_in: $('#discount_in-' + suffix).val() || '',
                        discount: $('#discount_amt-' + suffix).val() || '',
                        tax_apply_on: $('#atax_apply_on-' + suffix).val() || '',
                        tax_value: $('#tax_value1-' + suffix).val() || '',
                        service_tax_subtotal: $('#service_tax_subtotal-' + suffix).val() || '',
                        total_tour_cost: $('#total_tour_cost-' + suffix).val() || '',
                        package_name3: $('#package_name1-' + suffix).val() || '',
                        costing_id: $('#costing_entry_id-' + suffix).val() || ''
                    });
                });
                return entries;
            }
            var rows = getTableRows('tbl_package_tour_quotation_dynamic_costing');
            var tableEntries = [];
            for (var r = 0; r < rows.length; r++) {
                var row = rows[r];
                tableEntries.push({
                    package_type_c: getRowInputValue(row, 2),
                    tour_cost: getRowInputValue(row, 3),
                    transport_cost: getRowInputValue(row, 4),
                    excursion_cost: getRowInputValue(row, 5),
                    basic_amount: getRowInputValue(row, 6),
                    service_charge: getRowInputValue(row, 7),
                    discount_in: getRowInputValue(row, 8),
                    discount: getRowInputValue(row, 9),
                    tax_apply_on: getRowInputValue(row, 10),
                    tax_value: getRowInputValue(row, 11),
                    service_tax_subtotal: getRowInputValue(row, 12),
                    total_tour_cost: getRowInputValue(row, 16),
                    package_name3: getRowInputValue(row, 17),
                    costing_id: getRowInputValue(row, 19)
                });
            }
            return tableEntries;
        }

        function collectUpdateCostingBsmValues() {
            var el = document.getElementById('tbl_package_tour_quotation_dynamic_costing');
            if (!el) {
                return [];
            }
            if (el.tagName === 'DIV') {
                var bsmValues = [];
                $(el).find('[id^="tour_cost-"]').each(function() {
                    var suffix = this.id.replace('tour_cost-', '');
                    if (!suffix) {
                        return;
                    }
                    var checkbox = document.getElementById('chk_costing' + suffix);
                    if (checkbox && !checkbox.checked) {
                        return;
                    }
                    bsmValues.push([{
                        "basic": 'basic',
                        "service": 'service',
                        'tax_apply_on': $('#atax_apply_on-' + suffix).val() || '',
                        'tax_value': $('#tax_value1-' + suffix).val() || '',
                        'tcsper': $('#tcs_tax-' + suffix).val() || '',
                        'tcsvalue': $('#tcs1-' + suffix).val() || ''
                    }]);
                });
                return bsmValues;
            }
            var rows = getTableRows('tbl_package_tour_quotation_dynamic_costing');
            var bsmValues = [];
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                if (getRowCheckboxChecked(row, 0) === 'true') {
                    bsmValues.push([{
                        "basic": 'basic',
                        "service": 'service',
                        'tax_apply_on': getRowInputValue(row, 10),
                        'tax_value': getRowInputValue(row, 11),
                        'tcsper': getRowInputValue(row, 13),
                        'tcsvalue': getRowInputValue(row, 14)
                    }]);
                }
            }
            return bsmValues;
        }

        function resetQuotationUpdateState() {
            window.quotationUpdateInProgress = false;
            $('#btn_quotation_update').prop('disabled', false);
            try { $('#btn_quotation_update').button('reset'); } catch (e) {}
        }
        if (window.quotationUpdateInProgress) {
            console.log("QUOTATION UPDATE: Already in progress, preventing double submission");
            return false;
        }
        window.quotationUpdateInProgress = true;

        $('#btn_quotation_update').prop('disabled', true);
        var quotation_id = $('#quotation_id1').val();

        var enquiry_id = $('#enquiry_id12').val();

        var package_id = $('#is_ai_quotation').val() === '1' ? '0' : $('#package_id1').val();
        var is_ai_quotation = $('#is_ai_quotation').val() || '0';
        var dest_id = $('#quotation_dest_id').val() || $('#dest_name').val() || $('#dest_name_hidden').val() || '';
        var tour_name = $('#tour_name12').val();
        var from_date = $('#from_date12').val();

        var to_date = $('#to_date12').val();

        var total_days = $('#total_days12').val();

        var customer_name = $('#customer_name12').val();
        var user_id = 0;
        if($('#s_user_id').val() != 0){
            user_id = $('#user_id_u').val();
        }

        var email_id = $('#email_id12').val();
        var mobile_no = $('#mobile_no12').val();
		var country_code = $('#country_code1').val();

        var total_adult = $('#total_adult12').val();

        var total_infant = $('#total_infant12').val();

        var total_passangers = $('#total_passangers12').val();

        var children_without_bed = $('#children_without_bed12').val();

        var children_with_bed = $('#children_with_bed12').val();

        var quotation_date = $('#quotation_date').val();
        var active_flag = $('#active_flag1').val();
        var booking_type = $('#booking_type2').val();

        var train_cost = $('#train_cost1').val();

        var flight_cost = $('#flight_cost1').val();
        var cruise_cost = $('#cruise_cost1').val();
        var visa_cost = $('#visa_cost1').val();
        var guide_cost = $('#guide_cost1').val();
        var misc_cost = $('#misc_cost1').val();
        var price_str_url = $("#upload_url1").val();
        var currency_code = $('#currency_code1').val();

        // Try to get data from stored tab2 form data first
        var checked_programe_arr = [];
        var day_count_arr = [];
        var attraction_arr = [];
        var program_arr = [];
        var stay_arr = [];
        var meal_plan_arr = [];
        var day_image_arr = [];
        var package_p_id_arr = [];

        if (window.tab2FormData) {
            console.log("UPDATE TAB4: Using stored tab2 form data:", window.tab2FormData);
            checked_programe_arr = window.tab2FormData.checked_programe_arr || [];
            day_count_arr = window.tab2FormData.day_count_arr || [];
            attraction_arr = window.tab2FormData.attraction_arr || [];
            program_arr = window.tab2FormData.program_arr || [];
            stay_arr = window.tab2FormData.stay_arr || [];
            meal_plan_arr = window.tab2FormData.meal_plan_arr || [];
            day_image_arr = window.tab2FormData.day_image_arr || [];
            package_p_id_arr = window.tab2FormData.package_p_id_arr || [];
        } else {
            var storedTab2Data = sessionStorage.getItem('tab2_form_data');
            if (storedTab2Data) {
                try {
                    window.tab2FormData = JSON.parse(storedTab2Data);
                    checked_programe_arr = window.tab2FormData.checked_programe_arr || [];
                    day_count_arr = window.tab2FormData.day_count_arr || [];
                    attraction_arr = window.tab2FormData.attraction_arr || [];
                    program_arr = window.tab2FormData.program_arr || [];
                    stay_arr = window.tab2FormData.stay_arr || [];
                    meal_plan_arr = window.tab2FormData.meal_plan_arr || [];
                    day_image_arr = window.tab2FormData.day_image_arr || [];
                    package_p_id_arr = window.tab2FormData.package_p_id_arr || [];
                } catch (parseError) {
                    console.error("UPDATE TAB4: Error parsing stored tab2 data:", parseError);
                }
            }
        }

        if (!program_arr.length) {
            console.log("UPDATE TAB4: No stored tab2 data, reading from visible itinerary table");
            var table = null;
            if ($('#is_ai_quotation').val() === '1') {
                table = document.getElementById("dynamic_table_list_update");
            } else {
                var packageTableId = $('input[name="custom_package"]:checked').val() || $('#img_package_id').val() || $('#package_id1').val();
                if (packageTableId) {
                    table = document.getElementById("dynamic_table_list_p_" + packageTableId);
                }
                if (!table) {
                    table = document.getElementById("dynamic_table_list_update");
                }
            }

            if (!table) {
                error_msg_alert('Itinerary data not found. Please open the Package tab and click Next before updating.');
                window.quotationUpdateInProgress = false;
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }

            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var checkbox = row.querySelector('input[type="checkbox"]');
                var checked_programe = checkbox ? checkbox.checked : false;
                var attractionInput = row.querySelector('input[id*="special_attaraction"]');
                var programTextarea = row.querySelector('textarea[id*="day_program"]');
                var stayInput = row.querySelector('input[id*="overnight_stay"]');
                var mealPlanSelect = row.querySelector('select[id*="meal_plan"]');
                var packageIdInput = row.querySelector('input[name="package_id_n"]');

                var attraction = attractionInput ? attractionInput.value : '';
                var program = programTextarea ? programTextarea.value : '';
                var stay = stayInput ? stayInput.value : '';
                var meal_plan = mealPlanSelect ? mealPlanSelect.value : '';
                var package_id1 = packageIdInput ? packageIdInput.value : ($('#is_ai_quotation').val() === '1' ? $('#quotation_refer_id').val() : $('#package_id1').val());

                checked_programe_arr.push(checked_programe);
                day_count_arr.push(i + 1);
                attraction_arr.push(attraction);
                program_arr.push(program);
                stay_arr.push(stay);
                meal_plan_arr.push(meal_plan);
            
            // Get image data for this row - check both new uploads and existing images
            var img = '';
            var imageInput = row.querySelector('input[id^="day_image_"]');
            var imageKey = imageInput && imageInput.id ? imageInput.id.replace('day_image_', '') : String(i + 1);
            
            console.log("UPDATE TAB4: Processing image for row", i, "with imageKey", imageKey);
            
            // First check if we have a new image uploaded via previewDayImage
            if (window.quotationImages && window.quotationImages[imageKey]) {
                var imageData = window.quotationImages[imageKey];
                console.log("UPDATE TAB4: Found new image data for imageKey", imageKey, imageData);
                
                if (imageData.image_url) {
                    img = imageData.image_url;
                    console.log("UPDATE TAB4: Using existing uploaded image URL for imageKey", imageKey, ":", img);
                }
            } else if (window.quotationImages && window.quotationImages[i + 1]) {
                var imageDataLegacy = window.quotationImages[i + 1];
                if (imageDataLegacy.image_url) {
                    img = imageDataLegacy.image_url;
                }
            } else {
                // Fallback to existing image from hidden input
                var existingImgInput = row.querySelector('input[id^="existing_image_path_"]');
                img = existingImgInput ? existingImgInput.value : '';
                console.log("UPDATE TAB4: Using existing image for imageKey", imageKey, ":", img);
            }
            
            console.log("UPDATE TAB4: Final image for row", i, ":", img);
            day_image_arr.push(img || '');
            
            package_p_id_arr.push(package_id1);
            }
        }

        //Train Information
        var train_status_arr = [];
        var train_from_location_arr = [];
        var train_to_location_arr = [];
        var train_class_arr = [];
        var train_arrival_date_arr = [];
        var train_departure_date_arr = [];
        var train_id_arr = [];

        var trainRows = getTableRows("tbl_package_tour_quotation_dynamic_train");
        for (var i = 0; i < trainRows.length; i++) {
            var row = trainRows[i];
            var status = getRowCheckboxChecked(row, 0);
            var train_from_location1 = getRowInputValue(row, 2);
            var train_to_location1 = getRowInputValue(row, 3);
            var train_class = getRowInputValue(row, 4);
            var train_departure_date = getRowInputValue(row, 5);
            var train_arrival_date = getRowInputValue(row, 6);
            var train_id = getRowInputValue(row, 7);

            train_status_arr.push(status);
            train_from_location_arr.push(train_from_location1);
            train_to_location_arr.push(train_to_location1);
            train_class_arr.push(train_class);
            train_arrival_date_arr.push(train_arrival_date);
            train_departure_date_arr.push(train_departure_date);
            train_id_arr.push(train_id);
        }

        //Plane Information
        var plane_status_arr = [];
        var plane_from_city_arr = [];
        var plane_to_city_arr = [];
        var plane_from_location_arr = [];
        var plane_to_location_arr = [];
        var airline_name_arr = [];
        var plane_class_arr = [];
        var arraval_arr = [];
        var dapart_arr = [];
        var plane_id_arr = [];

        var planeRows = getTableRows("tbl_package_tour_quotation_dynamic_plane");
        for (var i = 0; i < planeRows.length; i++) {
            var row = planeRows[i];

            var status = getRowCheckboxChecked(row, 0);
            var plane_from_location1 = getRowInputValue(row, 2);
            var plane_to_location1 = getRowInputValue(row, 3);
            var airline_name = getRowInputValue(row, 4);
            var plane_class = getRowInputValue(row, 5);
            var dapart1 = getRowInputValue(row, 6);
            var arraval1 = getRowInputValue(row, 7);
            var plane_from_city = getRowInputValue(row, 8);
            var plane_to_city = getRowInputValue(row, 9);
            var plane_id = getRowInputValue(row, 10);

            plane_status_arr.push(status);
            plane_from_city_arr.push(plane_from_city);
            plane_to_city_arr.push(plane_to_city);
            plane_from_location_arr.push(plane_from_location1);
            plane_to_location_arr.push(plane_to_location1);
            airline_name_arr.push(airline_name);
            plane_class_arr.push(plane_class);
            arraval_arr.push(arraval1);
            dapart_arr.push(dapart1);
            plane_id_arr.push(plane_id);
        }
        //Cruise Information
        var cruise_status_arr = [];
        var cruise_departure_date_arr = [];
        var cruise_arrival_date_arr = [];
        var route_arr = [];
        var cabin_arr = [];
        var sharing_arr = [];
        var c_entry_id_arr = [];

        var cruiseRows = getTableRows("tbl_dynamic_cruise_quotation");
        for (var i = 0; i < cruiseRows.length; i++) {
            var row = cruiseRows[i];

            var status = getRowCheckboxChecked(row, 0);
            var cruise_from_date = getRowInputValue(row, 2);
            var cruise_to_date = getRowInputValue(row, 3);
            var route = getRowInputValue(row, 4);
            var cabin = getRowInputValue(row, 5);
            var sharing = getRowInputValue(row, 6);
            var c_entry_id = getRowInputValue(row, 7);

            if (c_entry_id == '') {
                c_entry_id = 0;
            }
            cruise_status_arr.push(status);
            cruise_departure_date_arr.push(cruise_from_date);
            cruise_arrival_date_arr.push(cruise_to_date);
            route_arr.push(route);
            cabin_arr.push(cabin);
            sharing_arr.push(sharing);
            c_entry_id_arr.push(c_entry_id);
        }

        //Hotel Information
        var package_type_arr = [];
        var hotel_status_arr = [];
        var city_name_arr = [];
        var hotel_name_arr = [];
        var hotel_cat_arr = [];
        var check_in_arr = [];
        var check_out_arr = [];
        var hotel_stay_days_arr = [];
        var hotel_type_arr = [];
        var package_name_arr = [];
        var total_rooms_arr = [];
        var hotel_cost_arr = [];
        var extra_bed_cost_arr = [];
        var extra_bed_arr = [];
        var hotel_id_arr = [];
        var hotel_meal_plan_arr = [];

        var hotelRows = getTableRows("tbl_package_tour_quotation_dynamic_hotel_update");
        for (var i = 0; i < hotelRows.length; i++) {

            var row = hotelRows[i];
            var status = getRowCheckboxChecked(row, 0);
            var package_type = getRowInputValue(row, 2);
            var city_name = getRowInputValue(row, 3);
            var hotel_id = getRowInputValue(row, 4);
            var hotel_cat = getRowInputValue(row, 5);
            var check_in = getRowInputValue(row, 6);
            var checkout = getRowInputValue(row, 7);
            var hotel_type = getRowInputValue(row, 8);
            var hotel_stay_days1 = getRowInputValue(row, 9);
            var total_rooms = getRowInputValue(row, 10);
            var extra_bed = getRowInputValue(row, 11);
            var package_name1 = getRowInputValue(row, 12);
            var hotel_cost = getRowInputValue(row, 13);
            var package_id1 = getRowInputValue(row, 14);
            var extra_bed_cost = getRowInputValue(row, 15);
            var meal_plan = getRowInputValue(row, 16);
            var hotel_id1 = getRowInputValue(row, 17);

            hotel_status_arr.push(status);
            package_type_arr.push(package_type);
            city_name_arr.push(city_name);
            hotel_name_arr.push(hotel_id);
            hotel_cat_arr.push(hotel_cat);
            hotel_stay_days_arr.push(hotel_stay_days1);
            hotel_type_arr.push(hotel_type);
            total_rooms_arr.push(total_rooms);
            extra_bed_arr.push(extra_bed);
            package_name_arr.push(package_name1);
            hotel_cost_arr.push(hotel_cost);
            extra_bed_cost_arr.push(extra_bed_cost);
            hotel_id_arr.push(hotel_id1);
            check_in_arr.push(check_in);
            check_out_arr.push(checkout);
            hotel_meal_plan_arr.push(meal_plan);
        }

        //Transport Information
        var transport_status_arr = [];
        var vehicle_name_arr = [];
        var start_date_arr = [];
        var end_date_arr = [];
        var pickup_arr = [];
        var drop_arr = [];
        var vehicle_count_arr = [];
        var transport_cost_arr1 = [];
        var package_name_arr1 = [];
        var pickup_type_arr = [];
        var drop_type_arr = [];
        var transport_id_arr = [];
        var service_duration_arr = [];

        var transportRows = getTableRows("tbl_package_tour_quotation_dynamic_transport_u");
        for (var i = 0; i < transportRows.length; i++) {

            var row = transportRows[i];
            var status = getRowCheckboxChecked(row, 0);
            var transport_id1 = getRowInputValue(row, 2);
            var travel_date = getRowInputValue(row, 3);
            var end_date = getRowInputValue(row, 4);
            var pickup = getRowInputValue(row, 5);
            var drop = getRowInputValue(row, 6);
            var pickup_type = getRowInputValue(row, 12);
            var drop_type = getRowInputValue(row, 13);
            if (!pickup_type && pickup && pickup.indexOf('-') !== -1) {
                pickup_type = pickup.split('-')[0];
            }
            if (!drop_type && drop && drop.indexOf('-') !== -1) {
                drop_type = drop.split('-')[0];
            }
            var service_duration = getRowInputValue(row, 7);
            var vehicle_count = getRowInputValue(row, 8);
            var transport_cost = getRowInputValue(row, 9);
            var pname = getRowInputValue(row, 10);
            var package_id1 = getRowInputValue(row, 11);
            var transport_id = getRowInputValue(row, 14);

            transport_status_arr.push(status);
            vehicle_name_arr.push(transport_id1);
            start_date_arr.push(travel_date);
            end_date_arr.push(end_date);
            pickup_arr.push(pickup);
            drop_arr.push(drop);
            vehicle_count_arr.push(vehicle_count);
            transport_cost_arr1.push(transport_cost);
            package_name_arr1.push(pname);
            pickup_type_arr.push(pickup_type);
            drop_type_arr.push(drop_type);
            transport_id_arr.push(transport_id);
            service_duration_arr.push(service_duration);

            if (status === 'true' && transport_id1 && !pickup) {
                error_msg_alert('Select pickup location in transport row ' + (i + 1));
                window.quotationUpdateInProgress = false;
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
            if (status === 'true' && transport_id1 && !drop) {
                error_msg_alert('Select drop-off location in transport row ' + (i + 1));
                window.quotationUpdateInProgress = false;
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
        }
        //Activity Info
        var excRows = getTableRows("tbl_package_tour_quotation_dynamic_excursion");

        var exc_status_arr = [];
        var exc_date_arr_e = [];
        var city_name_arr_e = [];
        var excursion_name_arr = [];
        var transfer_option_arr = [];
        var adult_arr = [];
        var chwb_arr = [];
        var chwob_arr = [];
        var infant_arr = [];
        var excursion_amt_arr = [];
        var excursion_id_arr = [];
        var vehicle_id_arr_e = [];
        var vehicles_arr = [];

        for (var e = 0; e < excRows.length; e++) {
            var row = excRows[e];

            var status = getRowCheckboxChecked(row, 0);
            var exc_date = getRowInputValue(row, 2);
            var city_name = getRowInputValue(row, 3);
            var excursion_name = getRowInputValue(row, 4);
            var transfer_option = getRowInputValue(row, 5);
            var adults = getRowInputValue(row, 6);
            var chwb = getRowInputValue(row, 7);
            var chwob = getRowInputValue(row, 8);
            var infant = getRowInputValue(row, 9);
            var excursion_amount = getRowInputValue(row, 10);
            var vehicle_id = getRowInputValue(row, 15);
            var vehicles = getRowInputValue(row, 16);
            var excursion_id = getRowInputValue(row, 17);
            exc_status_arr.push(status);
            exc_date_arr_e.push(exc_date);
            city_name_arr_e.push(city_name);
            excursion_name_arr.push(excursion_name);
            transfer_option_arr.push(transfer_option);
            excursion_amt_arr.push(excursion_amount);
            adult_arr.push(adults);
            chwb_arr.push(chwb);
            chwob_arr.push(chwob);
            infant_arr.push(infant);
            vehicle_id_arr_e.push(vehicle_id);
            vehicles_arr.push(vehicles);
            excursion_id_arr.push(excursion_id);

            if (status === 'true' && city_name && !excursion_name) {
                error_msg_alert('Select activity name in row ' + (e + 1));
                window.quotationUpdateInProgress = false;
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
            if (status === 'true' && excursion_name && !city_name) {
                error_msg_alert('Select activity city in row ' + (e + 1));
                window.quotationUpdateInProgress = false;
                $('#btn_quotation_update').prop('disabled', false);
                return false;
            }
        }

        //Costing Information
        var tour_cost_arr = [];
        var transport_cost_arr = [];
        var excursion_cost_arr = [];
        var basic_amount_arr = [];
        var service_charge_arr = [];
        var service_tax_subtotal_arr = [];
        var total_tour_cost_arr = [];
        var package_name_arr2 = [];
        var costing_id_arr = [];
        var package_type_c_arr = [];
        var discount_in_arr = [];
        var discount_arr = [];

        var costingEntries = collectUpdateCostingEntries();
        if (!costingEntries.length) {
            error_msg_alert('Please enter land costing details before updating.');
            resetQuotationUpdateState();
            return false;
        }

        var costing_type_pre = getQuotationCostingType();
        if (costing_type_pre == 2) {
            var paxRequired = {
                adult: (parseInt($('#total_adult12').val(), 10) || 0) > 0,
                cweb: (parseInt($('#children_with_bed12').val(), 10) || 0) > 0,
                cwnb: (parseInt($('#children_without_bed12').val(), 10) || 0) > 0,
                infant: (parseInt($('#total_infant12').val(), 10) || 0) > 0
            };
            var ppTypes = ['adult', 'cweb', 'cwnb', 'infant'];
            var $ppRows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
            if (!$ppRows.length) {
                $ppRows = $(document);
            }
            var taxOk = true;
            $ppRows.each(function (pkgIdx) {
                var suffix = $(this).attr('data-pp-suffix') || '';
                var pkgLabel = $(this).attr('data-package-type') || ('Package ' + (pkgIdx + 1));
                for (var pti = 0; pti < ppTypes.length; pti++) {
                    var ptype = ppTypes[pti];
                    if (!paxRequired[ptype]) {
                        continue;
                    }
                    var taxApply = $('#' + ptype + '_tax_apply_on_pp_update' + suffix).val();
                    var taxVal = $('#' + ptype + '_select_tax_pp_update' + suffix).val();
                    if (!taxApply || taxApply === '1') {
                        error_msg_alert('Select Tax Apply On for ' + ptype + ' (' + pkgLabel + ')');
                        taxOk = false;
                        return false;
                    }
                    if (!taxVal) {
                        error_msg_alert('Select Tax for ' + ptype + ' (' + pkgLabel + ')');
                        taxOk = false;
                        return false;
                    }
                }
            });
            if (!taxOk) {
                resetQuotationUpdateState();
                return false;
            }
        }

        for (var i = 0; i < costingEntries.length; i++) {

            var entry = costingEntries[i];
            var package_type_c = entry.package_type_c;
            var tour_cost = entry.tour_cost;
            var transport_cost = entry.transport_cost;
            var excursion_cost = entry.excursion_cost;
            var basic_amount = entry.basic_amount;
            var service_charge = entry.service_charge;
            var discount_in = entry.discount_in;
            var discount = entry.discount;
            var tax_apply_on = entry.tax_apply_on;
            var tax_value = entry.tax_value;
            var service_tax_subtotal = entry.service_tax_subtotal;
            var total_tour_cost = entry.total_tour_cost;
            var package_name3 = entry.package_name3;
            var costing_id = entry.costing_id;

            if (tour_cost == "") {
                error_msg_alert('Select Tour cost in row' + (i + 1));
                resetQuotationUpdateState();
                return false;
            }
            if (costing_type_pre != 2) {
                if (tax_apply_on == "") {
                    error_msg_alert('Select Tax Apply On in row' + (i + 1));
                    resetQuotationUpdateState();
                    return false;
                }
                if (tax_value == "") {
                    error_msg_alert('Select Tax in row' + (i + 1));
                    resetQuotationUpdateState();
                    return false;
                }
            }

            package_type_c_arr.push(package_type_c);
            tour_cost_arr.push(tour_cost);
            transport_cost_arr.push(transport_cost);
            excursion_cost_arr.push(excursion_cost);
            basic_amount_arr.push(basic_amount);
            service_charge_arr.push(service_charge);
            discount_in_arr.push(discount_in);
            discount_arr.push(discount);
            service_tax_subtotal_arr.push(service_tax_subtotal);
            total_tour_cost_arr.push(total_tour_cost);
            package_name_arr2.push(package_name3);
            costing_id_arr.push(costing_id);
        }
        var bsmValues = collectUpdateCostingBsmValues();
        // PP hotel costs per package (hidden fields on each .quotation-pp-costing-row)
        var adult_cost_arr = [];
        var infant_cost_arr = [];
        var child_with_arr = [];
        var child_without_arr = [];
        var entry_id_arr = [];
        var $ppCostRows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
        if ($ppCostRows.length) {
            $ppCostRows.each(function () {
                var suffix = $(this).attr('data-pp-suffix') || '';
                // Prefer live card hotel values; fall back to hidden package costs
                adult_cost_arr.push($('#adult_hotel_pp_update' + suffix).val() || $('#adult_cost' + suffix).val() || 0);
                infant_cost_arr.push($('#infant_hotel_pp_update' + suffix).val() || $('#infant_cost' + suffix).val() || 0);
                child_with_arr.push($('#cweb_hotel_pp_update' + suffix).val() || $('#child_with' + suffix).val() || 0);
                child_without_arr.push($('#cwnb_hotel_pp_update' + suffix).val() || $('#child_without' + suffix).val() || 0);
                entry_id_arr.push($('#pp_entry_id' + suffix).val() || '');
            });
        } else {
            // Legacy fallback: adult_cost11, adult_cost12, ...
            var sq_ppcost_count = parseInt($('#sq_ppcost_count').val(), 10) || 0;
            for (var i = 1; i <= sq_ppcost_count; i++) {
                adult_cost_arr.push($('#adult_cost1' + i).val() || 0);
                infant_cost_arr.push($('#infant_cost1' + i).val() || 0);
                child_with_arr.push($('#child_with1' + i).val() || 0);
                child_without_arr.push($('#child_without1' + i).val() || 0);
                entry_id_arr.push($('#entry_id1' + i).val() || '');
            }
        }
        //Per person travel costing
        var flight_acost = $('#flight_acost1').val();
        var flight_ccost = $('#flight_ccost1').val();
        var flight_icost = $('#flight_icost1').val();
        var train_acost = $('#train_acost1').val();
        var train_ccost = $('#train_ccost1').val();
        var train_icost = $('#train_icost1').val();
        var cruise_acost = $('#cruise_acost1').val();
        var cruise_ccost = $('#cruise_ccost1').val();
        var cruise_icost = $('#cruise_icost1').val();
        var other_desc = $('#other_desc1').val();

        var costing_type = getQuotationCostingType();
        var pp_costing_arr = (typeof quotationCollectPpCostingEntries === 'function')
            ? quotationCollectPpCostingEntries({ mode: 'update' })
            : [];
        var inclExclData = getInclusionsExclusionsForQuotation();
        var inclusions = inclExclData.inclusions;
        var exclusions = inclExclData.exclusions;
        var image_url_id = $('#image_url_id').val();
        var pckg_daywise_url = $('#pckg_daywise_url').val();
        var image_url = $('#delete_image_url').val();
        var discount = $('#discount1').val();
        var updated_url = pckg_daywise_url + image_url;
        var base_url = $('#base_url').val();

        $("#vi_confirm_box").vi_confirm_box({
            callback: function(result) {
                if (result == "yes") {
                    $('#btn_quotation_update').button('loading');
                    $('#btn_quotation_update').prop('disabled', false);
                    
                    // Debug: Log the data being sent
                    console.log("DEBUG: Sending quotation update data:");
                    console.log("attraction_arr:", attraction_arr);
                    console.log("program_arr:", program_arr);
                    console.log("day_count_arr:", day_count_arr);
                    console.log("day_image_arr:", day_image_arr);
                    console.log("checked_programe_arr:", checked_programe_arr);
                    console.log("window.quotationImages:", window.quotationImages);
                    
                    // Additional debugging for image data
                    console.log("DEBUG: day_image_arr length:", day_image_arr.length);
                    for (var idx = 0; idx < day_image_arr.length; idx++) {
                        console.log("DEBUG: day_image_arr[" + idx + "]:", day_image_arr[idx]);
                    }
                    $.ajax({

                        type: 'post',
                        url: base_url +
                            'controller/package_tour/quotation/quotation_update.php',
                        data: {
                            quotation_id: quotation_id,
                            package_id: package_id,
                            tour_name: tour_name,
                            from_date: from_date,
                            to_date: to_date,
                            total_days: total_days,
                            customer_name: customer_name,user_id:user_id,
                            email_id: email_id,
                            mobile_no: mobile_no,country_code:country_code,
                            total_adult: total_adult,
                            total_infant: total_infant,
                            total_passangers: total_passangers,
                            children_without_bed: children_without_bed,
                            children_with_bed: children_with_bed,
                            quotation_date: quotation_date,
                            active_flag: active_flag,
                            booking_type: booking_type,
                            train_cost: train_cost,
                            flight_cost: flight_cost,
                            cruise_cost: cruise_cost,
                            visa_cost: visa_cost,
                            train_from_location_arr: train_from_location_arr,
                            train_to_location_arr: train_to_location_arr,
                            train_class_arr: train_class_arr,
                            train_arrival_date_arr: train_arrival_date_arr,
                            train_departure_date_arr: train_departure_date_arr,
                            train_id_arr: train_id_arr,
                            plane_from_city_arr: plane_from_city_arr,
                            plane_to_city_arr: plane_to_city_arr,
                            plane_from_location_arr: plane_from_location_arr,
                            plane_to_location_arr: plane_to_location_arr,
                            plane_id_arr: plane_id_arr,
                            airline_name_arr: airline_name_arr,
                            plane_class_arr: plane_class_arr,
                            arraval_arr: arraval_arr,
                            dapart_arr: dapart_arr,
                            cruise_departure_date_arr: cruise_departure_date_arr,
                            cruise_arrival_date_arr: cruise_arrival_date_arr,
                            route_arr: route_arr,
                            cabin_arr: cabin_arr,
                            sharing_arr: sharing_arr,
                            c_entry_id_arr: c_entry_id_arr,
                            city_name_arr: city_name_arr,
                            hotel_name_arr: hotel_name_arr,
                            hotel_cat_arr: hotel_cat_arr,
                            hotel_type_arr: hotel_type_arr,
                            hotel_stay_days_arr: hotel_stay_days_arr,
                            package_name_arr: package_name_arr,
                            total_rooms_arr: total_rooms_arr,
                            hotel_cost_arr: hotel_cost_arr,
                            extra_bed_arr: extra_bed_arr,
                            extra_bed_cost_arr: extra_bed_cost_arr,
                            hotel_id_arr: hotel_id_arr,
                            hotel_meal_plan_arr:hotel_meal_plan_arr,
                            check_in_arr: check_in_arr,
                            check_out_arr: check_out_arr,
                            vehicle_name_arr: vehicle_name_arr,
                            start_date_arr: start_date_arr,
                            end_date_arr: end_date_arr,
                            pickup_arr: pickup_arr,
                            drop_arr: drop_arr,
                            vehicle_count_arr: vehicle_count_arr,
                            transport_cost_arr1: transport_cost_arr1,
                            package_name_arr1: package_name_arr1,
                            pickup_type_arr: pickup_type_arr,
                            drop_type_arr: drop_type_arr,
                            tour_cost_arr: tour_cost_arr,
                            basic_amount_arr: basic_amount_arr,
                            service_charge_arr: service_charge_arr,
                            service_tax_subtotal_arr: service_tax_subtotal_arr,
                            total_tour_cost_arr: total_tour_cost_arr,
                            package_name_arr2: package_name_arr2,
                            transport_cost_arr: transport_cost_arr,
                            costing_id_arr: costing_id_arr,
                            package_type_c_arr: package_type_c_arr,
                            enquiry_id: enquiry_id,
                            transport_id_arr: transport_id_arr,
                            service_duration_arr:service_duration_arr,
                            city_name_arr_e: city_name_arr_e,
                            excursion_name_arr: excursion_name_arr,
                            exc_date_arr_e: exc_date_arr_e,
                            transfer_option_arr: transfer_option_arr,
                            excursion_amt_arr: excursion_amt_arr,
                            vehicle_id_arr_e: vehicle_id_arr_e,
                            excursion_id_arr: excursion_id_arr,
                            excursion_cost_arr: excursion_cost_arr,
                            vehicles_arr:vehicles_arr,
                            guide_cost: guide_cost,
                            misc_cost: misc_cost,
                            adult_cost: adult_cost_arr,
                            infant_cost: infant_cost_arr,
                            child_with: child_with_arr,
                            child_without: child_without_arr,
                            entry_id_arr: entry_id_arr,
                            price_str_url: price_str_url,
                            attraction_arr: attraction_arr,
                            program_arr: program_arr,
                            stay_arr: stay_arr,
                            meal_plan_arr: meal_plan_arr,
                            day_image_arr: day_image_arr,
                            package_p_id_arr: package_p_id_arr,
                            inclusions: inclusions,
                            exclusions: exclusions,
                            checked_programe_arr: checked_programe_arr,
                            day_count_arr: day_count_arr,
                            costing_type: costing_type,
                            train_status_arr: train_status_arr,
                            plane_status_arr: plane_status_arr,
                            cruise_status_arr: cruise_status_arr,
                            hotel_status_arr: hotel_status_arr,
                            transport_status_arr: transport_status_arr,
                            exc_status_arr: exc_status_arr,
                            updated_url: updated_url,
                            image_url_id: image_url_id,
                            bsmValues: bsmValues,
                            currency_code: currency_code,
                            package_type_arr: package_type_arr,
                            discount_in_arr:discount_in_arr,discount_arr:discount_arr,
                            adult_arr: adult_arr,
                            chwb_arr: chwb_arr,
                            chwob_arr: chwob_arr,
                            infant_arr: infant_arr,
                            discount: discount,
                            flight_acost : flight_acost,flight_ccost:flight_ccost,flight_icost:flight_icost,train_acost:train_acost,train_ccost:train_ccost,train_icost:train_icost,                            cruise_acost:cruise_acost,cruise_ccost:cruise_ccost,cruise_icost:cruise_icost,other_desc:other_desc,
                            is_ai_quotation: is_ai_quotation,
                            dest_id: dest_id,
                            pp_costing_arr: JSON.stringify(pp_costing_arr)
                        },

                        success: function(message) {
                            console.log("DEBUG: Quotation update response:", message);
                            console.log("QUOTATION UPDATE: Response received");
                            window.quotationUpdateInProgress = false; // Reset flag
                            $('#btn_quotation_update').button('reset');
                            $('#btn_quotation_update').prop('disabled', false);
                            var msg = message.split('--');
                            if (msg[0] == "error") {
                                error_msg_alert(msg[1]);
                                $('#btn_quotation_update').prop('disabled', false);
                            } else {
                                // Extract quotation ID from success message for image uploads
                                console.log("DEBUG: Update success message:", message);
                                var quotationIdMatch = message.match(/Quotation ID:\s*(\d+)/i);
                                var quotationId = quotationIdMatch ? quotationIdMatch[1] : quotation_id;
                                console.log("DEBUG: Using quotation ID for image upload:", quotationId);
                                
                                // Collect stored images from itinerary interface
                                var storedImages = collectStoredImages();
                                console.log("DEBUG: Collected " + storedImages.length + " stored images for upload");
                                
                                // Upload itinerary images if any exist
                                if (storedImages && storedImages.length > 0) {
                                    console.log("DEBUG: Uploading " + storedImages.length + " itinerary images for quotation " + quotationId);
                                    uploadItineraryImages(quotationId, storedImages);
                                } else {
                                    console.log("DEBUG: No images to upload");
                                }
                                $('#vi_confirm_box').vi_confirm_box({
                                    false_btn: false,
                                    message: message,
                                    true_btn_text: 'Ok',
                                    callback: function(data1) {
                                        $('#btn_quotation_update').prop(
                                            'disabled', false);
                                        if (data1 == "yes") {
                                            $('#btn_quotation_update')
                                                .button('reset');
                                            $('#btn_quotation_update')
                                                .prop('disabled',
                                                false);
                                            $('#quotation_update_modal')
                                                .modal('hide');
                                            window.location.href =
                                                base_url +
                                                'view/package_booking/quotation/home/index.php';
                                        } else {
                                            $('#btn_quotation_update')
                                                .button('reset');
                                            $('#btn_quotation_update')
                                                .prop('disabled',
                                                false);
                                        }
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("DEBUG: AJAX Error - Status:", status, "Error:", error);
                            console.error("DEBUG: Response Text:", xhr.responseText);
                            console.log("QUOTATION UPDATE: AJAX failed, resetting flag");
                            window.quotationUpdateInProgress = false; // Reset flag on error
                            $('#btn_quotation_update').button('reset');
                            $('#btn_quotation_update').prop('disabled', false);
                            error_msg_alert("Failed to update quotation: " + error);
                        }
                    });
                } else {
                    console.log("QUOTATION UPDATE: User cancelled, resetting flag");
                    window.quotationUpdateInProgress = false; // Reset flag
                    $('#btn_quotation_update').button('reset');
                    $('#btn_quotation_update').prop('disabled', false);
                }
            }
        });
    }
});

    $('#btn_quotation_update').off('click.quotationUpdate').on('click.quotationUpdate', function(e) {
        e.preventDefault();
        $('#frm_tab4').submit();
    });
}

$(document).ready(function() {
    initQuotationUpdateForm();
});

// $(document).on("change","#tcs_tax",function() {
//     customTcsTax();
// });


$(document).on("change", "[id^=tcs_tax-]", function() {
    var rowId = $(this).attr("id").split("-")[1]; // Extract dynamic row ID
    customTcsTax(rowId);
});


$(document).on("change", "[id^=basic_amount-]", function() {
    var rowId = $(this).attr("id").split("-")[1]; // Extract dynamic row ID
    customTcsTax(rowId);
});




// function customTcsTax() {
//     var tcs_tax = $("#tcs_tax").val(); // Get the TCS tax percentage
//     var service_charge = parseFloat($('#service_charge-1').val()) || 0; // Get the service charge
//     var basic_amount = parseFloat($('#basic_amount-1').val()) || 0; // Get the basic amount
//     var discount_amt = parseFloat($('#discount_amt-1').val()) || 0; // Get the discount amount
//     var discount_in = $('#discount_in-1').val(); // Get the discount type

//     // Calculate the final service charge after applying the discount
//     if (discount_in === 'Percentage') {
//         discount_amt = (service_charge * discount_amt) / 100;
//     }
//     var final_service_charge = service_charge - discount_amt;

//     if (tcs_tax !== '') {
//         var service_tax_amount = 0;
//         var tax_subtotal = $("#service_tax_subtotal-1").val() || '0:0:0'; // Get the service tax subtotal

//         // Calculate the total service tax amount
//         var service_tax_subtotal1 = tax_subtotal.split(',');
//         for (var i = 0; i < service_tax_subtotal1.length; i++) {
//             var service_tax = service_tax_subtotal1[i].split(':');
//             service_tax_amount += parseFloat(service_tax[2]) || 0;
//         }

//         // Calculate TCS using the adjusted service charge
//         var tcsamount = (service_tax_amount + final_service_charge + basic_amount) * parseFloat(tcs_tax) / 100;

//         // Get previous TCS amount
//         var totalTcs = parseFloat($("#tcs1-").val()) || 0;

//         // Update the TCS field
//         $("#tcs1-").val(tcsamount.toFixed(2));

//         // Update the total tour cost
//         var txt_actual_tour_cost1 = parseFloat($("#total_tour_cost-1").val()) || 0;
//         txt_actual_tour_cost1 -= totalTcs;
//         var txt_actual_tour_cost1total = tcsamount + txt_actual_tour_cost1;
//         $("#total_tour_cost-1").val(Math.round(txt_actual_tour_cost1total).toFixed(2));
//     } else {
//         // If no TCS is applicable, reset TCS and adjust total cost
//         var totalTcs = parseFloat($("#tcs1-").val()) || 0;
//         $("#tcs1-").val(0.00);
//         var txt_actual_tour_cost1 = parseFloat($("#total_tour_cost-1").val()) || 0;
//         var txt_actual_tour_cost1total = txt_actual_tour_cost1 - totalTcs;
//         $("#total_tour_cost-1").val(txt_actual_tour_cost1total.toFixed(2));
//     }
// }




// function customTcsTax(rowId) {
//     var tcs_tax = $("#tcs_tax-" + rowId).val(); // Get the TCS tax percentage
//     var service_charge = parseFloat($('#service_charge-' + rowId).val()) || 0; // Get the service charge
//     var basic_amount = parseFloat($('#basic_amount-' + rowId).val()) || 0; // Get the basic amount
//     var discount_amt = parseFloat($('#discount_amt-' + rowId).val()) || 0; // Get the discount amount
//     var discount_in = $('#discount_in-' + rowId).val(); // Get the discount type

//     // Calculate the final service charge after applying the discount
//     if (discount_in === 'Percentage') {
//         discount_amt = (service_charge * discount_amt) / 100;
//     }
//     var final_service_charge = service_charge - discount_amt;

//     var tcs_field = $("#tcs1-" + rowId);
//     var total_tour_cost_field = $("#total_tour_cost-" + rowId);

//     // Get the previous TCS amount before updating
//     var prev_tcs_amount = parseFloat(tcs_field.val()) || 0;

//     if (tcs_tax !== '') {
//         var service_tax_amount = 0;
//         var tax_subtotal = $("#service_tax_subtotal-" + rowId).val() ;
//         // || '0:0:0'; // Get the service tax subtotal

//         // Calculate the total service tax amount
//         var service_tax_subtotal1 = tax_subtotal.split(',');
//         for (var i = 0; i < service_tax_subtotal1.length; i++) {
//             var service_tax = service_tax_subtotal1[i].split(':');
//             service_tax_amount += parseFloat(service_tax[2]) ;
//             // || 0;
//         }

//         // Calculate new TCS amount
//         var new_tcs_amount = (service_tax_amount + final_service_charge + basic_amount) * parseFloat(tcs_tax) / 100;

//         // Update TCS field
//         tcs_field.val(new_tcs_amount.toFixed(2));

//         // Update total tour cost: First, subtract previous TCS, then add the new one
//         var total_tour_cost = parseFloat(total_tour_cost_field.val()) ;
//         // || 0;
//         total_tour_cost = total_tour_cost - prev_tcs_amount + new_tcs_amount;
//         total_tour_cost_field.val(Math.round(total_tour_cost).toFixed(2));

//     } else {
//         // If no TCS is selected, remove the previous TCS amount from total cost
//         var total_tour_cost = parseFloat(total_tour_cost_field.val()) ;
//         // || 0;
//         total_tour_cost -= prev_tcs_amount; // Subtract previous TCS amount
//         tcs_field.val("0.00"); // Reset TCS field
//         total_tour_cost_field.val(Math.round(total_tour_cost).toFixed(2));
//     }
// }

function customTcsTax(rowId) {
    // alert("hi");
    var tcs_tax = parseFloat($("#tcs_tax-" + rowId).val()) || 0; // Get TCS tax percentage
    var service_charge = parseFloat($('#service_charge-' + rowId).val()) || 0; // Get service charge
    var basic_amount = parseFloat($('#basic_amount-' + rowId).val()) || 0; // Get basic amount
    var discount_amt = parseFloat($('#discount_amt-' + rowId).val()) || 0; // Get discount amount
    var discount_in = $('#discount_in-' + rowId).val(); // Get discount type

    // Apply discount if applicable
    if (discount_in === 'Percentage') {
        discount_amt = (service_charge * discount_amt) / 100;
    }
    var final_service_charge = service_charge - discount_amt;

    var tcs_field = $("#tcs1-" + rowId);
    var total_tour_cost_field = $("#total_tour_cost-" + rowId);

    // Get the previous TCS amount before updating
    var prev_tcs_amount = parseFloat(tcs_field.val()) || 0;

    // alert(prev_tcs_amount);
    // Calculate the total service tax amount
    var service_tax_amount = 0;
    var tax_subtotal = $("#service_tax_subtotal-" + rowId).val() || '0:0:0';
    var service_tax_subtotal1 = tax_subtotal.split(',');

    for (var i = 0; i < service_tax_subtotal1.length; i++) {
        var service_tax = service_tax_subtotal1[i].split(':');
        service_tax_amount += parseFloat(service_tax[2]) || 0;
    }

    // Calculate total tour cost (excluding TCS for now)
    var total_tour_cost = basic_amount + final_service_charge + service_tax_amount;

    
    if (tcs_tax > 0) {
        // Calculate new TCS amount based on the updated total
        var new_tcs_amount = (total_tour_cost * tcs_tax) / 100;

        // Update TCS field
        tcs_field.val(new_tcs_amount.toFixed(2));

        // Adjust total tour cost: remove previous TCS, add new one
        total_tour_cost = total_tour_cost  + new_tcs_amount;
    } else {
        // If no TCS is selected, remove the previous TCS amount from total cost
        // total_tour_cost -= prev_tcs_amount; // Subtract previous TCS amount
        tcs_field.val("0.00"); // Reset TCS field
    }

    // Ensure no NaN values
    total_tour_cost = isNaN(total_tour_cost) ? 0 : total_tour_cost;

    // Update total tour cost field
    total_tour_cost_field.val(total_tour_cost.toFixed(2));
}

// Function to collect all stored images from itinerary interface
function collectStoredImages() {
    var storedImages = [];
    
    console.log("DEBUG: Checking window.quotationImages:", window.quotationImages);
    
    if (window.quotationImages) {
        console.log("DEBUG: quotationImages object exists, checking properties...");
        for (var offset in window.quotationImages) {
            console.log("DEBUG: Checking offset", offset, ":", window.quotationImages[offset]);
            if (window.quotationImages[offset] && !window.quotationImages[offset].uploaded) {
                console.log("DEBUG: Adding image for offset", offset, "to upload list");
                storedImages.push(window.quotationImages[offset]);
            }
        }
    } else {
        console.log("DEBUG: window.quotationImages does not exist");
    }
    
    console.log("DEBUG: Collected " + storedImages.length + " stored images for upload");
    console.log("DEBUG: Stored images details:", storedImages);
    return storedImages;
}

// Function to upload itinerary images after quotation is updated
function uploadItineraryImages(quotationId, images) {
    var base_url = $('#base_url').val();
    var uploadPromises = [];

    images.forEach(function(imageData) {
        // If this is a replacement, first delete the old image
        if (imageData.is_replacement) {
            console.log("This is a replacement image for day " + imageData.day_number + ", deleting old image first");
            
            // Find the existing image URL for this day
            var existingImageUrl = $('#saved_image_' + imageData.offset + ' img').attr('src');
            if (existingImageUrl) {
                // Extract the relative path from the full URL
                var relativePath = existingImageUrl.replace(base_url, '');
                
                // Delete the old image first
                var deletePromise = $.ajax({
                    type: 'POST',
                    url: base_url + 'controller/package_tour/quotation/delete_itinerary_image.php',
                    data: {
                        quotation_id: quotationId,
                        package_id: imageData.package_id,
                        day_number: imageData.day_number,
                        image_url: relativePath
                    }
                });
                
                // Chain the delete operation before upload
                uploadPromises.push(deletePromise.then(function(deleteResponse) {
                    console.log("Old image deleted successfully:", deleteResponse);
                    return uploadSingleImage(imageData, quotationId, base_url);
                }).catch(function(error) {
                    console.log("Error deleting old image, proceeding with upload anyway:", error);
                    return uploadSingleImage(imageData, quotationId, base_url);
                }));
            } else {
                // No existing image, just upload
                uploadPromises.push(uploadSingleImage(imageData, quotationId, base_url));
            }
        } else {
            // Regular upload (not a replacement)
            uploadPromises.push(uploadSingleImage(imageData, quotationId, base_url));
        }
    });

    // Wait for all uploads to complete
    Promise.all(uploadPromises).then(function() {
        console.log("All images uploaded successfully");
    }).catch(function(error) {
        console.error("Some images failed to upload:", error);
    });
}

// Helper function to upload a single image
function uploadSingleImage(imageData, quotationId, base_url) {
    var formData = new FormData();
    formData.append('quotation_id', quotationId);
    formData.append('package_id', imageData.package_id);
    formData.append('day_number', imageData.day_number);
    formData.append('image', imageData.file);
    
    console.log("Uploading image for day " + imageData.day_number + ", package " + imageData.package_id + ", file: " + imageData.file.name);
    
    return $.ajax({
        url: base_url + 'controller/package_tour/quotation/upload_itinerary_image.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log("Image upload successful for offset " + imageData.offset + ":", response);
            // Mark as uploaded
            if (window.quotationImages && window.quotationImages[imageData.offset]) {
                window.quotationImages[imageData.offset].uploaded = true;
                window.quotationImages[imageData.offset].image_url = response;
            }
            
            // Refresh the image preview with the new URL
            if (typeof window.refreshImageAfterUpload === 'function') {
                console.log("TAB4: Calling refreshImageAfterUpload for offset", imageData.offset);
                window.refreshImageAfterUpload(imageData.offset, response);
            } else {
                console.log("TAB4: refreshImageAfterUpload function not found, using fallback");
                // Fallback: manually update the image
                var previewImg = $('#preview_img_' + imageData.offset);
                var previewDiv = $('#day_image_preview_' + imageData.offset);
                
                if (previewImg.length && previewDiv.length) {
                    // Add cache-busting parameter to ensure new image loads
                    var cacheBuster = '?t=' + new Date().getTime();
                    var imageUrl = response + cacheBuster;
                    
                    previewImg.attr('src', imageUrl);
                    $('#existing_image_path_' + imageData.offset).val(response);
                    previewDiv.show();
                    $('.upload-btn-' + imageData.offset).hide();
                    console.log("Image preview updated for offset", imageData.offset, "with URL:", imageUrl);
                } else {
                    console.log("TAB4: Preview elements not found for offset", imageData.offset);
                }
            }
            
            // Force refresh all images after this upload
            setTimeout(function() {
                if (typeof window.refreshAllImagesAfterUpload === 'function') {
                    console.log("TAB4: Calling refreshAllImagesAfterUpload");
                    window.refreshAllImagesAfterUpload();
                }
            }, 500);
        },
        error: function(xhr, status, error) {
            console.error("Image upload failed for offset " + imageData.offset + ":", error);
            alert("Failed to upload image for day " + imageData.day_number + ". Please try again.");
        }
    });
}

    // Load form data from sessionStorage when tab4 loads
    $(document).ready(function() {
        var storedData = sessionStorage.getItem('tab2_form_data');
        if (storedData && !window.tab2FormData) {
            try {
                var formData = JSON.parse(storedData);
                console.log("TAB4: Loading stored form data:", formData);
                window.tab2FormData = formData;
            } catch (e) {
                console.error("TAB4: Error parsing stored form data:", e);
            }
        }
    });

function calculateCostingCardsUpdate(options) {
    options = options || {};
    // On user typing: keep their input (incl. clearing zeros). Only recalc service charge
    // when land components change or on initial/forced refresh.
    var recalcServiceCharge = (options.recalcServiceCharge === true);
    var $active = $(document.activeElement);

    // ===== TOTAL PAX (update form uses *12 ids from Tab1) =====
    let total_adult = +$('#total_adult12').val() || +$('#total_adult').val() || 0;
    let cwb = +$('#children_with_bed12').val() || +$('#children_with_bed').val() || 0;
    let cnb = +$('#children_without_bed12').val() || +$('#children_without_bed').val() || 0;
    let infant = +$('#total_infant12').val() || +$('#total_infant').val() || 0;

    function numFrom($el) {
        var v = $el.val();
        if (v === '' || v === null || typeof v === 'undefined') {
            return 0;
        }
        var n = parseFloat(v);
        return isNaN(n) ? 0 : n;
    }

    // Never overwrite the field the user is currently typing in
    function writeIfNotFocused($el, value) {
        if (!$el || !$el.length) {
            return;
        }
        if ($active && $active.length && $el[0] === $active[0]) {
            return;
        }
        $el.val(value);
    }

    function calculateBlock(prefix, pax_count, suffix) {
        suffix = suffix || '';
        if (pax_count === 0) {
            // Keep loaded values visible; only clear when user changes inputs and pax is zero
            return;
        }

        var sid = function (base) {
            return '#' + base + suffix;
        };
        var $serviceEl = $(sid(prefix + '_service_charge_pp_update'));
        var serviceFocused = ($active.length && $serviceEl.length && $serviceEl[0] === $active[0]);

        // ===== BASIC (empty string stays empty in the field; treat as 0 for math only) =====
        let hotel = numFrom($(sid(prefix + '_hotel_pp_update')));
        let transfer = numFrom($(sid(prefix + '_transfer_pp_update')));
        let activity = numFrom($(sid(prefix + '_activity_pp_update')));

        let land_cost = hotel + transfer + activity;
        writeIfNotFocused($(sid(prefix + '_land_cost_pp_update')), land_cost.toFixed(2));

        let service_charge;
        // Only auto-fill SC from business rules when hotel/transfer/activity change,
        // and never while the user is editing the service charge field itself.
        if (recalcServiceCharge && !serviceFocused) {
            service_charge = (typeof get_pp_service_charge_from_business_rules === 'function')
                ? get_pp_service_charge_from_business_rules(land_cost, 'update')
                : 0;
            $serviceEl.val(Number(service_charge).toFixed(2));
        }
        service_charge = numFrom($serviceEl);

        // ===== DISCOUNT =====
        let discount_type = $(sid(prefix + '_discount_in_pp_update')).val();
        let discount_input = numFrom($(sid(prefix + '_discount_amount_pp_update')));

        let discount = 0;
        if (discount_type == 1) {
            discount = (service_charge * discount_input) / 100;
        } else {
            discount = discount_input;
        }
        if (discount > service_charge) {
            discount = service_charge;
        }

        let service_after_discount = service_charge - discount;

        // ===== OTHER COSTS =====
        let flight = numFrom($(sid(prefix + '_flight_pp_update')));
        let train = numFrom($(sid(prefix + '_train_pp_update')));
        let cruise = numFrom($(sid(prefix + '_cruise_pp_update')));
        let visa = numFrom($(sid(prefix + '_visa_pp_update')));
        let guide = numFrom($(sid(prefix + '_guide_pp_update')));
        let misc = numFrom($(sid(prefix + '_misc_pp_update')));

        let other_cost =
            flight + train + cruise + visa + guide + misc;

        // ===== TAX =====
        let tax_apply_on = $(sid(prefix + '_tax_apply_on_pp_update')).val();
        let tax_percent = 0;

        let tax_text = $(sid(prefix + '_select_tax_pp_update') + ' option:selected').text();
        let match = tax_text.match(/(\d+(\.\d+)?)%/);
        if (match) tax_percent = parseFloat(match[1]);

        let tax_base = 0;

        if (tax_apply_on == 2) {
            tax_base = land_cost;
        } else if (tax_apply_on == 3) {
            tax_base = service_after_discount;
        } else if (tax_apply_on == 4) {
            tax_base = land_cost + service_after_discount + other_cost;
        }

        let tax_amount = (tax_base * tax_percent) / 100;
        writeIfNotFocused($(sid(prefix + '_tax_amt_pp_update')), tax_amount.toFixed(2));

        // ===== TCS =====
        let tcs_percent = 0;
        let tcs_val = $(sid(prefix + '_select_tcs_pp_update')).val();

        if (tcs_val == 2) tcs_percent = 5;
        if (tcs_val == 3) tcs_percent = 20;

        let subtotal =
            land_cost +
            service_after_discount +
            other_cost +
            tax_amount;

        let tcs_amount = (subtotal * tcs_percent) / 100;
        writeIfNotFocused($(sid(prefix + '_tcs_amount_pp_update')), tcs_amount.toFixed(2));

        // ===== FINAL TOTAL =====
        let final_total = subtotal + tcs_amount;

        writeIfNotFocused($(sid(prefix + '_total_amount_pp_update')), final_total.toFixed(2));
    }

    var $ppRows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
    if ($ppRows.length) {
        $ppRows.each(function () {
            var suffix = $(this).attr('data-pp-suffix') || '';
            calculateBlock('adult', total_adult, suffix);
            calculateBlock('cweb', cwb, suffix);
            calculateBlock('cwnb', cnb, suffix);
            calculateBlock('infant', infant, suffix);
        });
    } else {
        calculateBlock('adult', total_adult, '');
        calculateBlock('cweb', cwb, '');
        calculateBlock('cwnb', cnb, '');
        calculateBlock('infant', infant, '');
    }
}

$(document).on('input change keyup', '#quotation_pp_costing_container .costing-table input, #quotation_pp_costing_container .costing-table select', function () {
    var id = (this.id || '');
    // Service charge must NOT be overwritten when typing in the SC field.
    // Only rebuild SC from business rules when hotel / transfer / activity change.
    var isLandComponent = /_(hotel|transfer|activity)_pp_update/.test(id);
    calculateCostingCardsUpdate({
        recalcServiceCharge: isLandComponent
    });
});
$(document).ready(function () {
    // Restore saved tax dropdown selections for every package block
    function restorePpTaxSelect($tax) {
        var selected = $tax.attr('data-selected') || '';
        if (!selected) {
            return;
        }
        $tax.val(selected);
        if ($tax.val() !== selected) {
            var matched = false;
            $tax.find('option').each(function () {
                var optVal = $(this).attr('value') || '';
                var optText = $.trim($(this).text() || '');
                if (optVal === selected || optText === selected) {
                    $(this).prop('selected', true);
                    matched = true;
                    return false;
                }
            });
            if (!matched) {
                $tax.append($('<option></option>').attr('value', selected).text(selected).prop('selected', true));
            }
        }
    }
    $('#quotation_pp_costing_container select[id*="_select_tax_pp_update"]').each(function () {
        restorePpTaxSelect($(this));
    });
    // Initial load: keep saved service charges (do not overwrite with rules)
    calculateCostingCardsUpdate({ recalcServiceCharge: false });
});
function getQuotationCostingType()
{
	var selectedId = $('input[name="costing_tab"]:checked').attr('id');
	return (selectedId === 'perperson_costing') ? 2 : 1;
}

function costing_reflect()
{
	var id = $('input[name="costing_tab"]:checked').attr('id');
	$('label[for="group_costing"], label[for="perperson_costing"]').removeClass('active');
	$('label[for="' + id + '"]').addClass('active');
	if(id=="group_costing"){
		$('#group_costing_tab').show();
		$('#per_person_costing_tab').hide();
	}
	if(id=="perperson_costing"){
		$('#group_costing_tab').hide();
		$('#per_person_costing_tab').show();
	}

}
costing_reflect();
</script>