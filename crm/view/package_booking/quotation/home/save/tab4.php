<style>
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
                             <label for="group_costing" class="app_dual_button mg_bt_10 active">
                                 <input type="radio" id="group_costing" name="costing_tab" checked onchange="costing_reflect()">
                                 &nbsp;&nbsp;Group Costing
                             </label>    
                             <label for="perperson_costing" class="app_dual_button mg_bt_10">
                                 <input type="radio" id="perperson_costing" name="costing_tab"  onchange="costing_reflect()">
                                 &nbsp;&nbsp;Per Person Costing
                             </label>
                           </div>
                        </div>
                        <!-- Group Costing -->
                        <div id="group_costing_tab" class="costing_section main_block mg_bt_20">
                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12">
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <legend>Land Cost</legend>
                                                    <div>
                                                        <div>
                                                            <div>
                                                                <div id="tbl_package_tour_quotation_dynamic_costing" name="tbl_package_tour_quotation_dynamic_costing" class="table border_0 no-marg">
                                                                    <div class="quotation-group-costing-row mg_bt_20">
                                                                    <div style="display: grid;  grid-template-columns: 150px  auto;   gap: 15px;">
                                                                        <div class="header_btn " style="display:none;"><input class="css-checkbox" id="chk_costing1" type="checkbox" checked disabled><span class="css-label" for="chk_costing1"> </span>
                                                                        </div>
                                                                        
                                                                        <div class="header_btn " style="display:none;">
                                                                            <small>&nbsp;</small><input type="text" maxlength="15" value="1" name="username" placeholder="Sr. No." class="form-control" disabled />
                                                                        </div>

                                                                        <div class="" ><small>&nbsp;</small><span>Package Type</span><input type="text" id="package_type-" name="package_type-" placeholder="Package Type" title="Package Type"  readonly></div>

                                                                        <div>
                                                                        <div style="display: grid; grid-template-columns: repeat(7, 1fr);  gap: 15px">                                                                        
                                                                        <div class=" "><small>&nbsp;</small><span>Hotel Cost</span><input type="number" id="tour_cost-" name="tour_cost-" placeholder="Hotel Cost" title="Hotel Cost" value="0" onchange="quotation_cost_calculate(this.id);" ></div>

                                                                        <div class=" " ><small>&nbsp;</small><span>Transport Cost</span><input type="number" id="transport_cost1-" name="transport_cost1-" placeholder="Transport Cost" title="Transport Cost" onchange="quotation_cost_calculate(this.id);"  value="0"></div>

                                                                        <?php
                                                                        $add_class1 = '';
                                                                        if ($role == 'B2b') {
                                                                            $add_class1 = "hidden";
                                                                        } else {
                                                                            $add_class1 = "number";
                                                                        } ?>
                                                                        <div class="" ><small>&nbsp;</small><span>Activity Cost</span><input type="number" id="excursion_cost-" name="excursion_cost-" onchange="quotation_cost_calculate(this.id);" placeholder="Activity Cost" title="Activity Cost"  value="0"></div>

                                                                        <div class="" ><small id="basic_show-" style="color:#000000">&nbsp;</small><span>Basic Amount</span><input type="<?= $add_class1 ?>" id="basic_amount-" name="basic_amount-" onchange="get_business(this.id,'true');;" placeholder="Basic Amount" title="Basic Amount"  readonly></div>

                                                                        <div class="" ><small id="service_show-" style="color:#000000">&nbsp;</small><span>Service charge</span><input type="<?= $add_class1 ?>" id="service_charge-" name="service_charge-" onchange="get_business(this.id,'false');quotation_cost_calculate(this.id); " value="0.00" placeholder="Service charge" title="Service charge" ></div>

                                                                        <div class="" >
                                                                            <small id="discount_in_show-">&nbsp;</small>
                                                                            <span>Discount In</span>
                                                                            <select title="Discount In" id="discount_in-" name="discount_in-" class="form-control" onchange="get_business(this.id,'true');quotation_cost_calculate(this.id);" >
                                                                                <option value="Percentage">Percentage</option>
                                                                                <option value="Flat">Flat</option>
                                                                            </select> 
                                                                        </div>

                                                                        <div class="" ><small>&nbsp;</small><span>Discount</span><input type="<?= $add_class1 ?>" id="discount_amt-" name="discount_amt-" onchange="get_business(this.id,'true');quotation_cost_calculate(this.id); " placeholder="Discount" title="Discount" ></div>
                                                                        </div>

                                                                        <div style="display: grid; grid-template-columns: repeat(7, 1fr);   gap: 15px; margin-top: 15px;">
                                                                        <div class="" >
                                                                            <small id="tax_apply_show-" style="color:#000000">&nbsp;</small>
                                                                            <span>Tax Apply On</span>
                                                                            <select title="Tax Apply On" id="tax_apply_on-" name="tax_apply_on-" class="form-control" onchange="get_business(this.id,'true');" >
                                                                                <option value="">*Tax Apply On</option>
                                                                                <option value="1">Basic Amount</option>
                                                                                <option value="2">Service Charge</option>
                                                                                <option value="3">Total</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class=" " >
                                                                            <small id="tax_show-" style="color:#000000">&nbsp;</small>
                                                                            <span>Select Tax</span>
                                                                            <select title="Select Tax" id="tax_value-" name="tax_value-" class="form-control" onchange="get_business(this.id,'true');" >
                                                                                <option value="">*Select Tax</option>
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                            </select>
                                                                        </div>

                                                                        <div class=" " ><small>&nbsp;</small><span>Tax Amount</span><input type="text" id="service_tax_subtotal-" name="service_tax_subtotal-" readonly placeholder="Tax Amount" title="Tax Amount" ></div>

                                                                        <div class="" >
                                                                            <small id="tcs_tax_show-" style="color:#000000">&nbsp;</small>
                                                                            <span>TCS</span>
                                                                            <select title="TCS" id="tcs_tax-" name="tcs_tax-" class="form-control" >
                                                                                <option value="0">*TCS Tax</option>
                                            									<option value="2">2% TCS</option>
                                            									<option value="20">20% TCS</option>
                                                                            </select>
                                                                        </div>

                                                                        <div class="" >
                                                                            <small id="tcs1_show-" style="color:#000000">&nbsp;</small>
                                                                            <span>TCS</span>
                                                                             <input type="number" name="tcs-" id="tcs1-" readonly class="text-right"
                                                                                placeholder="TCS" title="TCS" value="0.00" >                                                                                
                                                                        </div>        

                                                                        
                                                                        <div class=" "  style="display:none; ">
                                                                            <small id="tds_show-" style="color:#000000">&nbsp;</small>
                                                                            <span>TDS</span>
                                                                             <input type="hidden" name="tds-" id="tds-" readonly class="text-right"
                                                                                placeholder="TDS" title="TDS" value="0.00" >
                                                                        </div>
                                                                        
                                                                        <div class=" " >
                                                                            <small>&nbsp;</small>
                                                                            <span>Total Cost</span>
                                                                            <input type="text" id="total_tour_cost-" class="amount_feild_highlight text-right" name="total_tour_cost-" placeholder="Total Cost" title="Total Cost"  readonly>
                                                                         </div>

                                                                        <div class="header_btn hidden" style="display:none; ">
                                                                            <small>&nbsp;</small><input type="text" id="package_name1-" name="package_name1-" placeholder="Package Name" title="Package Name" style="display:none;" readonly>
                                                                        </div>

                                                                        <div  class="header_btn hidden " style="display:none;">
                                                                            <small>&nbsp;</small><input type="text" id="package_id1-" name="package_id1-" placeholder="Package ID" title="Package ID" style="display:none;">
                                                                        </div>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                           </div>
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                        </div>

                                        <div class="row mg_tp_30">
                                            <div class="col-xs-12">
                                                <div class="panel panel-default panel-body app_panel_style">
                                                <legend>Travel Cost</legend>
                                                    <!-- Other costs -->
                                                    <div class="row ">
                                                        <div class="col-md-2  mg_bt_10">
                                                            <span>Flight Cost</span>
                                                            <input type="text" id="flight_cost" name="flight_cost" placeholder="Flight Cost" title="Flight Cost" onchange="">
                                                        </div>
                                                        <div class="col-md-2  col-xs-12 mg_bt_10">
                                                            <span>Train Cost</span>
                                                            <input type="text" id="train_cost" name="train_cost" placeholder="Train Cost" title="Train Cost" onchange="">
                                                        </div>
                                                        <div class="col-md-2  mg_bt_10">
                                                            <span>Cruise Cost</span>
                                                            <input type="text" id="cruise_cost" name="cruise_cost" placeholder="Cruise Cost" title="Cruise Cost" onchange="">
                                                        </div>
                                                        <div class="col-md-2  mg_bt_10">
                                                                  <span>Visa Cost</span>
                                                                  <input type="text" id="visa_cost" name="visa_cost" placeholder="Visa Cost" title="Visa Cost" onchange="">
                                                                  </div>
                                                                <div class="col-md-2  mg_bt_10">
                                                                    <span>Guide Cost</span>
                                                                    <input type="text" id="guide_cost" name="guide_cost" placeholder="Guide Cost" title="Guide Cost" onchange="">
                                                                </div>
                                                                <div class="col-md-2  mg_bt_10">
                                                                    <span>Miscellaneous Cost</span>
                                                                    <input type="text" id="misc_cost" name="misc_cost" placeholder="Miscellaneous Cost" title="Miscellaneous Cost" onchange="">
                                                                </div>
                                                                <div class="col-md-4  mg_bt_10">
                                                                    <span>Miscellaneous Description</span>
                                                                    <textarea id="other_desc" name="other_desc" placeholder="Miscellaneous Description" title="Miscellaneous Description"></textarea>
                                                                </div>
                                                                <div class="col-md-2 col-sm-6 col-xs-12 mg_bt_10 ">
                                                                    <span>Currency</span>
                            <select name="currency_code" id="currency_code" title="Currency" style="width:100%" data-toggle="tooltip" required>
                                <?php
                                $sq_app_setting = mysqli_fetch_assoc(mysqlQuery("select currency from app_settings"));
                                if ($sq_app_setting['currency'] != '0') {

                                    $sq_currencyd = mysqli_fetch_assoc(mysqlQuery("SELECT `id`,`currency_code` FROM `currency_name_master` WHERE id=" . $sq_app_setting['currency']));
                                ?>
                                    <option value="<?= $sq_currencyd['id'] ?>"><?= $sq_currencyd['currency_code'] ?>
                                    </option>
                                <?php } ?>
                                <option value=''>*Select Currency</option>
                                <?php
                                $sq_currency = mysqlQuery("select * from currency_name_master order by currency_code");
                                while ($row_currency = mysqli_fetch_assoc($sq_currency)) {
                                ?>
                                    <option value="<?= $row_currency['id'] ?>"><?= $row_currency['currency_code'] ?>
                                    </option>
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
                                        <div class="row mg_tp_10">
                                            <div class="col-xs-12">
                                                <div class="panel panel-default panel-body app_panel_style">
                                                    <legend>Land Cost</legend>
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="table-responsive">
                                                                <table id="tbl_adult_child_head" name="tbl_adult_child_head" class="table border_0 no-marg">
                                                                    <tr>
                                                                        <td class="col-md-1 " style="padding-left: 0 !important;"><span>Package Type</span></th>
                                                                        <td class="col-md-3 " style="padding-left: 0 !important;"><span>Currency</span></th>
                                                                    </tr>
                                                                    
                                                                </table>
                                                            </div>
                                                            <div id="quotation_pp_costing_container">
                                                            <div class="quotation-pp-costing-row mg_bt_20" data-pp-suffix="">
                                                            <!-- Adult & child cost -->
                                                            <div class="row">
                                                                <div class="col-xs-12">
                                                                    <div class="table-responsive">
                                                                       <table id="tbl_package_tour_quotation_adult_child" name="tbl_package_tour_quotation_adult_child" class="table border_0 no-marg">
                                                                            <tr>
                                                                                <td class="col-md-3" style="padding-left: 0 !important; padding-top: 0 !important;"><input type="text" id="ppackage_type1" name="ppackage_type1" placeholder="Package Type" title="Package Type" readonly></td>
                                                                                <td class="col-md-3" style="padding-left: 0 !important; padding-top: 0 !important;">
                                                                                         <select name="currency_code_pp" id="currency_code_pp" title="Currency" style="width:100%" data-toggle="tooltip">
                                                                                          <option value="">*Select Currency</option>
                                                                                          <option value="1">USD</option>
                                                                                          <option value="2">EUR</option>
                                                                                          <option value="3">GBP</option>
                                                                                          <option value="4">INR</option>
                                                                                          <option value="5">AUD</option>
                                                                                          <option value="6">CAD</option>
                                                                                          <option value="7">CHF</option>
                                                                                          <option value="8">CNY</option>
                                                                                          <option value="9">JPY</option>
                                                                                         </select>                       
                                                                                </td>
                                                                                
                                                                                <td><input type="text" onchange=";" id="adult_cost" name="adult_cost" placeholder="Adult Cost" title="Adult Cost" style="display:none;"></td>
                                                                                <td><input type="text" onchange=";" id="child_with" name="child_with" placeholder="Child with Bed Cost" title="Child with Bed Cost" style="display:none;"></td>
                                                                                <td><input type="text" onchange=";" id="child_without" name="child_without" placeholder="Child w/o Bed Cost" title="Child w/o Bed Cost"style="display:none;"></td>
                                                                                </td>
                                                                                <td><input type="text" onchange=";" id="infant_cost" name="infant_cost" placeholder="Infant Cost" title="Infant Cost"style="display:none;"></td>
                                                                                <td><input type="hidden" id="pacakge_id2" name="pacakge_id2" placeholder="Package Id" title="Package Id"></td>
                                                                                <td><input type="hidden" id="adult_landcost" name="adult_landcost" ></td>
                                                                                <td><input type="hidden" id="cweb_landcost" name="cweb_landcost"></td>
                                                                                <td><input type="hidden" id="cwnb_landcost" name="cwnb_landcost"></td>
                                                                                <td><input type="hidden" id="infant_landcost" name="infant_landcost"></td>
                                                                       
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                            </div>

                                                                    <div class="costing-content-wp">
                                                                  <div class="costing-card-wp">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="costing-card">
                                                                <div class="costing-card-icon">
                                                                    <i class="fa fa-regular fa-user icon"></i>
                                                                </div>
                                                                <div class="costing-card-detail" >
                                                                    <p class="costing-card-label">Adult<br> (Double Sharing)</p>
                                                                </div>
                                                            </div>
                                                            <div class="costing-card-table" style="display: block;">
                                                                <table class="table table-bordered costing-table " id="" name="">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Components</th>
                                                                            <th>Cost</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                         <tr data-type="hotel">
                                                                            <td>Hotel</td>
                                                                            <td  class="price"><input type="number" id="adult_hotel_pp" name="adult_hotel_pp"  placeholder="Hotel" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="transfer">
                                                                            <td>Transfer</td>
                                                                            <td  class="price"><input type="number" id="adult_transfer_pp" name="transfer_pp"  placeholder="Transfer" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="activity">
                                                                            <td>Activity</td>
                                                                            <td  class="price"><input type="number" id="adult_activity_pp" name="adult_activity_pp"  placeholder="Activity" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td data-type="land_cost">Land Cost <br/> <span>(Hotel+Transfer+Activity)</span></td>
                                                                            <td  class="price">
                                                                                <input type="text" onchange=";" id="adult_land_cost_pp" name="adult_land_cost_pp" placeholder="Adult Cost" title="Adult Cost">
                                                                            </td>
                                                                        </tr>
                                                                        <tr data-type="service_charge">
                                                                            <td>Service Charges </td>
                                                                            <td  class="price"><input type="number" id="adult_service_charge_pp" name="adult_service_charge_pp" placeholder="Service Charges" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="discount">
                                                                            <td>Discount In</td>
                                                                            <td ><select name="adult_discount_in_pp" id="adult_discount_in_pp"  class="form-control">
                                                                                     <option value="1">Percentage</option>
                                                                                     <option value="2">Flat</option>
                                                                                </select></td>
                                                                        </tr>
                                                                        <tr data-type="discount_amount">
                                                                            <td  class="price">Discount </td>
                                                                            <td ><input type="number"  id="adult_discount_amount_pp" name="adult_discount_amount_pp" placeholder="Discount Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="flight_acost">
                                                                            <td>Flight Cost </td>
                                                                            <td  class="price"><input type="number" id="adult_flight_pp" name="adult_flight_pp" placeholder="Flight Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                       
                                                                        <tr data-type="train_acost">
                                                                            <td>Train Cost </td>
                                                                            <td  class="price"><input type="number" id="adult_train_pp" name="adult_train_pp"  placeholder="Train Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Cruise Cost </td>
                                                                            <td  class="price"><input type="number"id="adult_cruise_pp" name="adult_cruise_pp"  placeholder="Cruise Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="visa_acost">
                                                                            <td>Visa Cost </td>
                                                                            <td  class="price"><input type="number" id="adult_visa_pp" name="adult_visa_pp"  placeholder="Visa Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="guide_acost">
                                                                            <td>Guide Cost </td> 
                                                                            <td  class="price"><input type="number" id="adult_guide_pp" name="adult_guide_pp"  placeholder="Guide Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="miscellaneous_acost">
                                                                            <td>Miscellaneous Cost </td>
                                                                            <td  class="price"><input type="number" id="adult_misc_pp" name="adult_misc_pp"  placeholder="Miscellaneous Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="tax_apply_on">
                                                                            <td>Tax Apply On</td>
                                                                            <td ><select id="adult_tax_apply_on_pp" name="adult_tax_apply_on_pp"  class="form-control">
                                                                                    <option value="1">Tax Apply On</option>
                                                                                     <option value="2">Basic Amount</option>
                                                                                     <option value="3">Service Charge</option>
                                                                                     <option value="4">Total</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tax">
                                                                            <td>Select Tax</td>
                                                                            <td ><select id="adult_select_tax_pp" name="adult_select_tax_pp" class="form-control">
                                                                                    <option value="">*Select Tax</option>
                                                                                    
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                                   
                                                                                </select></td>
                                                                                <input type="hidden" name="tax_id" />
                                                                        </tr>
                                                                          <tr data-type="tax_value">
                                                                            <td>Tax Amount </td>
                                                                            <td  class="price"><input type="number" id="adult_tax_amount_pp" name="adult_tax_amount_pp"  placeholder="Tax Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                             <tr data-type="tcs">
                                                                            <td>TCS</td>
                                                                            <td ><select id="adult_select_tcs_pp" name="adult_select_tcs_pp"  class="form-control">
                                                                                     <option value="1">*TCS Tax</option>
                                                                                     <option value="2">2% TCS</option>
                                                                                     <option value="3">20% TCS</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tcs_value">
                                                                            <td>TCS Amount </td>
                                                                            <td  class="price"><input type="number" id="adult_tcs_amount_pp" name="adult_tcs_amount_pp"  placeholder="TCS" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="total">
                                                                            <td>Total Cost </td>
                                                                            <td  class="price"><input type="number" id="adult_total_amount_pp" name="adult_total_amount_pp" placeholder="Total Cost" title="" class="form-control totalcost-input" ></td>
                                                                        </tr>
                                                                       
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="costing-card">
                                                                <div class="costing-card-icon">
                                                                    <i class="fa fa-regular fa-user icon"></i>
                                                                </div>
                                                                <div class="costing-card-detail" >
                                                                   <p class="costing-card-label">CWEB<br> (Child With Extra Bed)</p>
                                                                </div>
                                                            </div>
                                                           <div class="costing-card-table" style="display: block;">
                                                                <table class="table table-bordered costing-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Components</th>
                                                                            <th>Cost</th>
                                                                        </tr>
                                                                    </thead>
                                                                      <tbody>
                                                                         <tr data-type="hotel">
                                                                            <td>Hotel</td>
                                                                            <td  class="price"><input type="number" id="cweb_hotel_pp" name="cweb_hotel_pp"  placeholder="Hotel" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="transfer">
                                                                            <td>Transfer</td>
                                                                            <td  class="price"><input type="number" id="cweb_transfer_pp" name="transfer_pp"  placeholder="Transfer" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="activity">
                                                                            <td>Activity</td>
                                                                            <td  class="price"><input type="number" id="cweb_activity_pp" name="cweb_activity_pp"  placeholder="Activity" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td data-type="land_cost">Land Cost <br/> <span>(Hotel+Transfer+Activity)</span></td>
                                                                            <td  class="price">
                                                                                <input type="text" onchange=";" id="cweb_land_cost_pp" name="cweb_land_cost_pp" placeholder="Adult Cost" title="Adult Cost">
                                                                            </td>
                                                                        </tr>
                                                                        <tr data-type="service_charge">
                                                                            <td>Service Charges </td>
                                                                            <td  class="price"><input type="number" id="cweb_service_charge_pp" name="cweb_service_charge_pp" placeholder="Service Charges" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="discount">
                                                                            <td>Discount In</td>
                                                                            <td ><select name="cweb_discount_in_pp" id="cweb_discount_in_pp"  class="form-control">
                                                                                     <option value="1">Percentage</option>
                                                                                     <option value="2">Flat</option>
                                                                                </select></td>
                                                                        </tr>
                                                                        <tr data-type="discount_amount">
                                                                            <td  class="price">Discount </td>
                                                                            <td ><input type="number"  id="cweb_discount_amount_pp" name="cweb_discount_amount_pp" placeholder="Discount Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="flight_acost">
                                                                            <td>Flight Cost </td>
                                                                            <td  class="price"><input type="number" id="cweb_flight_pp" name="cweb_flight_pp" placeholder="Flight Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                       
                                                                        <tr data-type="train_acost">
                                                                            <td>Train Cost </td>
                                                                            <td  class="price"><input type="number" id="cweb_train_pp" name="cweb_train_pp"  placeholder="Train Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Cruise Cost </td>
                                                                            <td  class="price"><input type="number"id="cweb_cruise_pp" name="cweb_cruise_pp"  placeholder="Cruise Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="visa_acost">
                                                                            <td>Visa Cost </td>
                                                                            <td  class="price"><input type="number" id="cweb_visa_pp" name="cweb_visa_pp"  placeholder="Visa Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="guide_acost">
                                                                            <td>Guide Cost </td> 
                                                                            <td  class="price"><input type="number" id="cweb_guide_pp" name="cweb_guide_pp"  placeholder="Guide Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="miscellaneous_acost">
                                                                            <td>Miscellaneous Cost </td>
                                                                            <td  class="price"><input type="number" id="cweb_misc_pp" name="cweb_misc_pp"  placeholder="Miscellaneous Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="tax_apply_on">
                                                                            <td>Tax Apply On</td>
                                                                            <td ><select id="cweb_tax_apply_on_pp" name="cweb_tax_apply_on_pp"  class="form-control">
                                                                                     <option value="1">Tax Apply On</option>
                                                                                     <option value="2">Basic Amount</option>
                                                                                     <option value="3">Service Charge</option>
                                                                                     <option value="4">Total</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tax">
                                                                            <td>Select Tax</td>
                                                                            <td ><select id="cweb_select_tax_pp" name="cweb_select_tax_pp" class="form-control">
                                                                                    <option value="">*Select Tax</option>
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                                   
                                                                                </select></td>
                                                                        </tr>
                                                                          <tr data-type="tax_value">
                                                                            <td>Tax Amount </td>
                                                                            <td  class="price"><input type="number" id="cweb_tax_amount_pp" name="cweb_tax_amount_pp"  placeholder="Tax Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                             <tr data-type="tcs">
                                                                            <td>TCS</td>
                                                                            <td ><select id="cweb_select_tcs_pp" name="cweb_select_tcs_pp"  class="form-control">
                                                                                     <option value="1">*TCS Tax</option>
                                                                                     <option value="2">2% TCS</option>
                                                                                     <option value="3">20% TCS</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tcs_value">
                                                                            <td>TCS Amount </td>
                                                                            <td  class="price"><input type="number" id="cweb_tcs_amount_pp" name="cweb_tcs_amount_pp"  placeholder="TCS" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="total">
                                                                            <td>Total Cost </td>
                                                                            <td  class="price"><input type="number" id="cweb_total_amount_pp" name="cweb_total_amount_pp" placeholder="Total Cost" title="" class="form-control totalcost-input" ></td>
                                                                        </tr>
                                                                       
                                                                    </tbody>
                                                                    
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="costing-card">
                                                                <div class="costing-card-icon">
                                                                    <i class="fa fa-regular fa-user icon"></i>
                                                                </div>
                                                                <div class="costing-card-detail" >
                                                                   
                                                                    <p class="costing-card-label">CWNB<br> (Child With No Bed)</p>
                                                                </div>
                                                            </div>
                                                            <div class="costing-card-table" style="display: block;">
                                                                <table class="table table-bordered costing-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Components</th>
                                                                            <th>Cost</th>
                                                                        </tr>
                                                                    </thead>
                                                                      <tbody>
                                                                         <tr data-type="hotel">
                                                                            <td>Hotel</td>
                                                                            <td  class="price"><input type="number" id="cwnb_hotel_pp" name="cwnb_hotel_pp"  placeholder="Hotel" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="transfer">
                                                                            <td>Transfer</td>
                                                                            <td  class="price"><input type="number" id="cwnb_transfer_pp" name="transfer_pp"  placeholder="Transfer" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="activity">
                                                                            <td>Activity</td>
                                                                            <td  class="price"><input type="number" id="cwnb_activity_pp" name="cwnb_activity_pp"  placeholder="Activity" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td data-type="land_cost">Land Cost <br/> <span>(Hotel+Transfer+Activity)</span></td>
                                                                            <td  class="price">
                                                                                <input type="text" onchange=";" id="cwnb_land_cost_pp" name="cwnb_land_cost_pp" placeholder="Adult Cost" title="Adult Cost">
                                                                            </td>
                                                                        </tr>
                                                                        <tr data-type="service_charge">
                                                                            <td>Service Charges </td>
                                                                            <td  class="price"><input type="number" id="cwnb_service_charge_pp" name="cwnb_service_charge_pp" placeholder="Service Charges" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="discount">
                                                                            <td>Discount In</td>
                                                                            <td ><select name="cwnb_discount_in_pp" id="cwnb_discount_in_pp"  class="form-control">
                                                                                     <option value="1">Percentage</option>
                                                                                     <option value="2">Flat</option>
                                                                                </select></td>
                                                                        </tr>
                                                                        <tr data-type="discount_amount">
                                                                            <td  class="price">Discount </td>
                                                                            <td ><input type="number"  id="cwnb_discount_amount_pp" name="cwnb_discount_amount_pp" placeholder="Discount Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="flight_acost">
                                                                            <td>Flight Cost </td>
                                                                            <td  class="price"><input type="number" id="cwnb_flight_pp" name="cwnb_flight_pp" placeholder="Flight Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                       
                                                                        <tr data-type="train_acost">
                                                                            <td>Train Cost </td>
                                                                            <td  class="price"><input type="number" id="cwnb_train_pp" name="cwnb_train_pp"  placeholder="Train Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Cruise Cost </td>
                                                                            <td  class="price"><input type="number"id="cwnb_cruise_pp" name="cwnb_cruise_pp"  placeholder="Cruise Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="visa_acost">
                                                                            <td>Visa Cost </td>
                                                                            <td  class="price"><input type="number" id="cwnb_visa_pp" name="cwnb_visa_pp"  placeholder="Visa Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="guide_acost">
                                                                            <td>Guide Cost </td> 
                                                                            <td  class="price"><input type="number" id="cwnb_guide_pp" name="cwnb_guide_pp"  placeholder="Guide Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="miscellaneous_acost">
                                                                            <td>Miscellaneous Cost </td>
                                                                            <td  class="price"><input type="number" id="cwnb_misc_pp" name="cwnb_misc_pp"  placeholder="Miscellaneous Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="tax_apply_on">
                                                                            <td>Tax Apply On</td>
                                                                            <td ><select id="cwnb_tax_apply_on_pp" name="cwnb_tax_apply_on_pp"  class="form-control">
                                                                                     <option value="1">Tax Apply On</option>
                                                                                     <option value="2">Basic Amount</option>
                                                                                     <option value="3">Service Charge</option>
                                                                                     <option value="4">Total</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tax">
                                                                            <td>Select Tax</td>
                                                                            <td ><select id="cwnb_select_tax_pp" name="cwnb_select_tax_pp" class="form-control">
                                                                                    <option value="">*Select Tax</option>
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                                   
                                                                                </select></td>
                                                                        </tr>
                                                                          <tr data-type="tax_value">
                                                                            <td>Tax Amount </td>
                                                                            <td  class="price"><input type="number" id="cwnb_tax_amount_pp" name="cwnb_tax_amount_pp"  placeholder="Tax Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                             <tr data-type="tcs">
                                                                            <td>TCS</td>
                                                                            <td ><select id="cwnb_select_tcs_pp" name="cwnb_select_tcs_pp"  class="form-control">
                                                                                     <option value="1">*TCS Tax</option>
                                                                                     <option value="2">2% TCS</option>
                                                                                     <option value="3">20% TCS</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tcs_value">
                                                                            <td>TCS Amount </td>
                                                                            <td  class="price"><input type="number" id="cwnb_tcs_amount_pp" name="cwnb_tcs_amount_pp"  placeholder="TCS" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="total">
                                                                            <td>Total Cost </td>
                                                                            <td  class="price"><input type="number" id="cwnb_total_amount_pp" name="cwnb_total_amount_pp" placeholder="Total Cost" title="" class="form-control totalcost-input" ></td>
                                                                        </tr>
                                                                       
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-3">
                                                            <div class="costing-card">
                                                                <div class="costing-card-icon">
                                                                    <i class="fa fa-regular fa-user icon"></i>
                                                                </div>
                                                                <div class="costing-card-detail">
                                                                    
                                                                    <p class="costing-card-label">Infant</p>
                                                                </div>
                                                            </div>
                                                          <div class="costing-card-table" style="display: block;">
                                                                <table class="table table-bordered costing-table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Components</th>
                                                                            <th>Cost</th>
                                                                        </tr>
                                                                    </thead>
                                                                          <tbody>
                                                                         <tr data-type="hotel">
                                                                            <td>Hotel</td>
                                                                            <td  class="price"><input type="number" id="infant_hotel_pp" name="infant_hotel_pp"  placeholder="Hotel" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="transfer">
                                                                            <td>Transfer</td>
                                                                            <td  class="price"><input type="number" id="infant_transfer_pp" name="transfer_pp"  placeholder="Transfer" title="" class="form-control" ></td>
                                                                        </tr>

                                                                        <tr data-type="activity">
                                                                            <td>Activity</td>
                                                                            <td  class="price"><input type="number" id="infant_activity_pp" name="infant_activity_pp"  placeholder="Activity" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td data-type="land_cost">Land Cost <br/> <span>(Hotel+Transfer+Activity)</span></td>
                                                                            <td  class="price">
                                                                                <input type="text" onchange=";" id="infant_land_cost_pp" name="infant_land_cost_pp" placeholder="Adult Cost" title="Adult Cost">
                                                                            </td>
                                                                        </tr>
                                                                        <tr data-type="service_charge">
                                                                            <td>Service Charges </td>
                                                                            <td  class="price"><input type="number" id="infant_service_charge_pp" name="infant_service_charge_pp" placeholder="Service Charges" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="discount">
                                                                            <td>Discount In</td>
                                                                            <td ><select name="infant_discount_in_pp" id="infant_discount_in_pp"  class="form-control">
                                                                                     <option value="1">Percentage</option>
                                                                                     <option value="2">Flat</option>
                                                                                </select></td>
                                                                        </tr>
                                                                        <tr data-type="discount_amount">
                                                                            <td  class="price">Discount </td>
                                                                            <td ><input type="number"  id="infant_discount_amount_pp" name="infant_discount_amount_pp" placeholder="Discount Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="flight_acost">
                                                                            <td>Flight Cost </td>
                                                                            <td  class="price"><input type="number" id="infant_flight_pp" name="infant_flight_pp" placeholder="Flight Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                       
                                                                        <tr data-type="train_acost">
                                                                            <td>Train Cost </td>
                                                                            <td  class="price"><input type="number" id="infant_train_pp" name="infant_train_pp"  placeholder="Train Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Cruise Cost </td>
                                                                            <td  class="price"><input type="number"id="infant_cruise_pp" name="infant_cruise_pp"  placeholder="Cruise Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="visa_acost">
                                                                            <td>Visa Cost </td>
                                                                            <td  class="price"><input type="number" id="infant_visa_pp" name="infant_visa_pp"  placeholder="Visa Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="guide_acost">
                                                                            <td>Guide Cost </td> 
                                                                            <td  class="price"><input type="number" id="infant_guide_pp" name="infant_guide_pp"  placeholder="Guide Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                        <tr data-type="miscellaneous_acost">
                                                                            <td>Miscellaneous Cost </td>
                                                                            <td  class="price"><input type="number" id="infant_misc_pp" name="infant_misc_pp"  placeholder="Miscellaneous Cost" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="tax_apply_on">
                                                                            <td>Tax Apply On</td>
                                                                            <td ><select id="infant_tax_apply_on_pp" name="infant_tax_apply_on_pp"  class="form-control">
                                                                                     <option value="1">Tax Apply On</option>
                                                                                     <option value="2">Basic Amount</option>
                                                                                     <option value="3">Service Charge</option>
                                                                                     <option value="4">Total</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tax">
                                                                            <td>Select Tax</td>
                                                                            <td ><select id="infant_select_tax_pp" name="infant_select_tax_pp" class="form-control">
                                                                                    <option value="">*Select Tax</option>
                                                                                <?php get_tax_dropdown('Income') ?>
                                                                                   
                                                                                </select></td>
                                                                        </tr>
                                                                          <tr data-type="tax_value">
                                                                            <td>Tax Amount </td>
                                                                            <td  class="price"><input type="number" id="infant_tax_amount_pp" name="infant_tax_amount_pp"  placeholder="Tax Amount" title="" class="form-control" ></td>
                                                                        </tr>
                                                                             <tr data-type="tcs">
                                                                            <td>TCS</td>
                                                                            <td ><select id="infant_select_tcs_pp" name="infant_select_tcs_pp"  class="form-control">
                                                                                     <option value="1">*TCS Tax</option>
                                                                                     <option value="2">2% TCS</option>
                                                                                     <option value="3">20% TCS</option>
                                                                                </select></td>
                                                                        </tr>
                                                                           <tr data-type="tcs_value">
                                                                            <td>TCS Amount </td>
                                                                            <td  class="price"><input type="number" id="infant_tcs_amount_pp" name="infant_tcs_amount_pp"  placeholder="TCS" title="" class="form-control" ></td>
                                                                        </tr>
                                                                           <tr data-type="total">
                                                                            <td>Total Cost </td>
                                                                            <td  class="price"><input type="number" id="infant_total_amount_pp" name="infant_total_amount_pp" placeholder="Total Cost" title="" class="form-control totalcost-input" ></td>
                                                                        </tr>
                                                                       
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                            </div><!-- /.quotation-pp-costing-row -->
                                                            </div><!-- /#quotation_pp_costing_container -->

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                         
                        </div>
                </div>
            </div>

         

            <div class="row">
                <div class="col-xs-12">

                 
                    <div class="row mg_tp_20 text-center mg_bt_30">
                        <div class="col-md-12">
                            <button class="btn btn-info btn-sm ico_left" type="button" onclick="switch_to_tab3()"><i class="fa fa-arrow-left"></i>&nbsp;&nbsp;Previous</button>
                            &nbsp;&nbsp;
                            <button class="btn btn-sm btn-success" type="button" id="btn_quotation_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="login_id" name="login_id" value="<?= $login_id ?>">
                <input type="hidden" id="upload_url" name="upload_url" value="">
            </div>
        </div>
    </div>
</form>
<?= end_panel() ?>

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


function get_hotel_cost() {

        var hotel_id_arr = [];
        var room_cat_arr = [];
        var check_in_arr = [];
        var check_out_arr = [];
        var total_nights_arr = [];
        var total_rooms_arr = [];
        var extra_bed_arr = [];
        var meal_plan_arr = [];
        var checked_arr = [];
        var package_id_arr = [];
        var child_with_bed = $('#children_with_bed').val();
        var child_without_bed = $('#children_without_bed').val();
        var adult_count = $('#total_adult').val();
        // alert(adult_count);
        adult_count = (adult_count == '') ? 0 : adult_count;
        child_without_bed = (child_without_bed == '') ? 0 : child_without_bed;
        child_with_bed = (child_with_bed == '') ? 0 : child_with_bed;

        var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
        var rowCount = table.rows.length;
        for (var i = 0; i < rowCount; i++) {

            var row = table.rows[i];
            var hotel_id = row.cells[4].childNodes[0].value;
            var room_category = row.cells[5].childNodes[0].value;
            var check_in = row.cells[6].childNodes[0].value;
            var check_out = row.cells[7].childNodes[0].value;
            var total_nights = row.cells[9].childNodes[0].value;
            var total_rooms = row.cells[10].childNodes[0].value;
            var extra_bed = row.cells[11].childNodes[0].value;
            var package_id = row.cells[14].childNodes[0].value;
            var meal_plan = row.cells[16].childNodes[0].value;

            hotel_id_arr.push(hotel_id);
            room_cat_arr.push(room_category);
            check_in_arr.push(check_in);
            check_out_arr.push(check_out);
            total_nights_arr.push(total_nights);
            total_rooms_arr.push(total_rooms);
            extra_bed_arr.push(extra_bed);
            meal_plan_arr.push(meal_plan);
            package_id_arr.push(package_id);
            checked_arr.push(row.cells[0].childNodes[0].checked);
        }
        var base_url = $('#base_url').val();
        $.ajax({
            type: 'post',
            url: base_url + 'view/package_booking/quotation/home/hotel/get_hotel_cost.php',
            data: {
                hotel_id_arr: hotel_id_arr,
                check_in_arr: check_in_arr,
                check_out_arr: check_out_arr,
                room_cat_arr: room_cat_arr,
                total_nights_arr: total_nights_arr,
                total_rooms_arr: total_rooms_arr,
                extra_bed_arr: extra_bed_arr,
                child_with_bed: child_with_bed,
                child_without_bed: child_without_bed,
                adult_count: adult_count,
                package_id_arr: package_id_arr,
                checked_arr: checked_arr,
                meal_plan_arr: meal_plan_arr
            },
            success: function(result) {

                var hotel_arr = JSON.parse(result);
                var pp_arr = [];
                if (hotel_arr.length === 0) {

                    for (var i = 0; i < rowCount; i++) {

                        var row = table.rows[i];
                        row.cells[13].childNodes[0].value = 0;
                    }
                } else {

                    for (var i = 0; i < hotel_arr.length; i++) {

                        var row = table.rows[i];
                        
                        if (row.cells[0].childNodes[0].checked) {
console.log("hotel_cost"+hotel_arr[i]);
                            // row.cells[13].childNodes[0].value = hotel_arr[i]['hotel_cost'];
                            let input = row.cells[13].querySelector('input');

if (input) {
    input.value = hotel_arr[i]['hotel_cost'];
}
                            pp_arr.push({
                                'hotel_cost': hotel_arr[i]['hotel_cost'],
                                'adult_cost': hotel_arr[i]['adult_cost'],
                                'child_with_bed': hotel_arr[i]['child_with_bed'],
                                'child_without_bed': hotel_arr[i]['child_without_bed'],
                                'infant_cost': hotel_arr[i]['infant_cost'] || 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'flag': hotel_arr[i]['flag'],
                                'package_type': (typeof quotationGetHotelRowPackageType === 'function'
                                    ? quotationGetHotelRowPackageType(row)
                                    : (row.cells[2].childNodes[0].value)),
                                'checked': true
                            });
                        } else {
                            row.cells[13].childNodes[0].value = 0;
                            pp_arr.push({
                                'hotel_cost':0,
                                'adult_cost': 0,
                                'child_with_bed': 0,
                                'child_without_bed': 0,
                                'infant_cost': 0,
                                'package_id': hotel_arr[i]['package_id'],
                                'package_type': (typeof quotationGetHotelRowPackageType === 'function'
                                    ? quotationGetHotelRowPackageType(row)
                                    : (row.cells[2].childNodes[0].value)),
                                'checked': false
                            });
                        }
                        // Don't force checkboxes to be checked - let user control them
                        // $(row.cells[0].childNodes[0]).prop('checked', true) /* .trigger('change') */ ;
                        $(row.cells[2].childNodes[0]).prop('disabled', true);
                        hideHotelPackage(row.cells[2].childNodes[0].value);
                    }
                }
                
                //Tab-4 Per person costing
                $('#hotel_pp_costing').val(JSON.stringify(pp_arr));
              
            }
        });
    }

    function upload_price_struct() {
        var btnUpload = $('#price_structure');
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
                } else {
                    $(btnUpload).find('span').text('Uploaded');
                    success_msg_alert('File is uploaded!');
                    $("#upload_url").val(response);
                }
            }
        });
    }

    function get_business(id, flag, change = false) {

        var offset = id.split('-');
        // console.log("offset" + offset[1]);
        get_auto_values('quotation_date', 'basic_amount-' + offset[1], 'payment_mode', 'service_charge-' + offset[1],
            'markup', 'save', flag, 'markup', 'discount_amt-'+ offset[1], offset[1], change);
    }

    function switch_to_tab3() {
        if (typeof quotationSaveTab4CostingState === 'function') {
            quotationSaveTab4CostingState();
        }
        $('#tab4_head').removeClass('active');
        $('#tab3_head').addClass('active');
        $('.bk_tab').removeClass('active');
        $('#tab3').addClass('active');
        $('html, body').animate({
            scrollTop: $('.bk_tab_head').offset().top
        }, 200);
    }

    function resetQuotationSaveState() {
        window.quotationSaveInProgress = false;
        $('#btn_quotation_save').prop('disabled', false);
        try { $('#btn_quotation_save').button('reset'); } catch (e) {}
    }

    function getExcursionRowValue(row, cellIndex) {
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

    function initQuotationSaveForm() {
        if ($('#currency_code').length && !$('#currency_code').data('select2')) {
            $('#currency_code').select2();
        }
        upload_price_struct();

        if ($('#frm_tab4').data('validator')) {
            return;
        }

        $('#frm_tab4').on('submit', function(e) {
            e.preventDefault();
        });

    $('#frm_tab4').validate({

        rules: {



        },
        
        invalidHandler: function(event, validator) {
            console.log("QUOTATION SAVE: Form validation failed");
            console.log("Validation errors:", validator.numberOfInvalids());
            console.log("Invalid fields:", validator.invalid);
            resetQuotationSaveState();
        },

        submitHandler: function(form) {
            
            console.log("QUOTATION SAVE: Form submission started");
            
            // Prevent double submission
            if (window.quotationSaveInProgress) {
                console.log("QUOTATION SAVE: Already in progress, preventing double submission");
                return false;
            }
            window.quotationSaveInProgress = true;
            
            // Add timeout to reset flag in case of issues
            setTimeout(function() {
                if (window.quotationSaveInProgress) {
                    console.log("QUOTATION SAVE: Timeout reached, resetting flag");
                    resetQuotationSaveState();
                }
            }, 30000); // 30 second timeout
            
            $('#btn_quotation_save').prop('disabled', true);
            var login_id = $("#login_id").val();

            var emp_id = $("#emp_id").val();

            var enquiry_id = $("#enquiry_id").val();

            var tour_name = $('#tour_name').val();

            var from_date = $('#from_date').val();

            var to_date = $('#to_date').val();

            var total_days = $('#total_days').val();

            var customer_name = $('#customer_name').val();

            var user_id = 0;
            if($('#user_dropdown').html() != ''){
                user_id = $('#user_id').val();
            }

            var email_id = $('#email_id').val();
            var mobile_no = $('#mobile_no').val();
			var country_code = $('#country_code').val();

            var total_adult = $('#total_adult').val();

            var total_infant = $('#total_infant').val();

            var total_passangers = $('#total_passangers').val();

            var children_without_bed = $('#children_without_bed').val();

            var children_with_bed = $('#children_with_bed').val();

            var quotation_date = $('#quotation_date').val();

            var booking_type = $('#booking_type').val();

            var train_cost = $('#train_cost').val();

            var flight_cost = $('#flight_cost').val();

            var cruise_cost = $('#cruise_cost').val();

            var visa_cost = $('#visa_cost').val();
            var branch_admin_id = $('#branch_admin_id1').val();
            var financial_year_id = $('#financial_year_id').val();
            //Per person travel costing
            var flight_acost = $('#flight_acost').val();
            var flight_ccost = $('#flight_ccost').val();
            var flight_icost = $('#flight_icost').val();
            var train_acost = $('#train_acost').val();
            var train_ccost = $('#train_ccost').val();
            var train_icost = $('#train_icost').val();
            var cruise_acost = $('#cruise_acost').val();
            var cruise_ccost = $('#cruise_ccost').val();
            var cruise_icost = $('#cruise_icost').val();
            var other_desc = $('#other_desc').val();

            var guide_cost = $('#guide_cost').val();
            var misc_cost = $('#misc_cost').val();
            var costing_type = getQuotationCostingType();

            //Train Information

            var train_from_location_arr = [];

            var train_to_location_arr = [];

            var train_class_arr = [];

            var train_arrival_date_arr = [];

            var train_departure_date_arr = [];





            var table = document.getElementById("tbl_package_tour_quotation_dynamic_train");

            var rowCount = table.rows.length;



            for (var i = 0; i < rowCount; i++)

            {

                var row = table.rows[i];



                if (row.cells[0].childNodes[0].checked)

                {

                    var train_from_location1 = row.cells[2].childNodes[0].value;

                    var train_to_location1 = row.cells[3].childNodes[0].value;

                    var train_class = row.cells[4].childNodes[0].value;

                    var train_departure_date = row.cells[5].childNodes[0].value;

                    var train_arrival_date = row.cells[6].childNodes[0].value;







                    if (train_from_location1 == "")

                    {

                        error_msg_alert('Enter train from location in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;

                    }



                    if (train_to_location1 == "")

                    {

                        error_msg_alert('Enter train to location in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;

                    }
                    train_from_location_arr.push(train_from_location1);

                    train_to_location_arr.push(train_to_location1);

                    train_class_arr.push(train_class);

                    train_arrival_date_arr.push(train_arrival_date);

                    train_departure_date_arr.push(train_departure_date);



                }

            }



            //Plane Information  
            var plane_from_city_arr = [];
            var plane_to_city_arr = [];
            var plane_from_location_arr = [];

            var plane_to_location_arr = [];

            var airline_name_arr = [];

            var plane_class_arr = [];

            var arraval_arr = [];

            var dapart_arr = [];



            var table = document.getElementById("tbl_package_tour_quotation_dynamic_plane");

            var rowCount = table.rows.length;



            for (var i = 0; i < rowCount; i++)

            {

                var row = table.rows[i];



                if (row.cells[0].childNodes[0].checked)

                {

                    var plane_from_location1 = row.cells[2].childNodes[0].value;
                    var plane_to_location1 = row.cells[3].childNodes[0].value;
                    var airline_name = row.cells[4].childNodes[0].value;
                    var plane_class = row.cells[5].childNodes[0].value;
                    var dapart1 = row.cells[6].childNodes[0].value;
                    var arraval1 = row.cells[7].childNodes[0].value;
                    var plane_from_city = row.cells[8].childNodes[0].value;
                    var plane_to_city = row.cells[9].childNodes[0].value;



                    if (plane_from_location1 == "")

                    {

                        error_msg_alert('Enter from sector in row' + (i + 1));
                        resetQuotationSaveState();

                        return false;

                    }



                    if (plane_to_location1 == "")

                    {

                        error_msg_alert('Enter to sector in row' + (i + 1));
                        resetQuotationSaveState();

                        return false;

                    }




                    if (arraval1 == "")

                    {

                        error_msg_alert('Arrival Date time is required in row:' + (i + 1));
                        resetQuotationSaveState();

                        return false;

                    }

                    if (dapart1 == "")

                    {

                        error_msg_alert("Daparture Date time is required in row:" + (i + 1));
                        resetQuotationSaveState();

                        return false;

                    }


                    plane_from_city_arr.push(plane_from_city);
                    plane_to_city_arr.push(plane_to_city);
                    plane_from_location_arr.push(plane_from_location1);

                    plane_to_location_arr.push(plane_to_location1);

                    airline_name_arr.push(airline_name);

                    plane_class_arr.push(plane_class);

                    arraval_arr.push(arraval1);

                    dapart_arr.push(dapart1);



                }

            }

            //Cruise Information
            var cruise_departure_date_arr = [];
            var cruise_arrival_date_arr = [];
            var route_arr = [];
            var cabin_arr = [];
            var sharing_arr = [];

            var table = document.getElementById("tbl_dynamic_cruise_quotation");
            var rowCount = table.rows.length;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {
                    var cruise_from_date = row.cells[2].childNodes[0].value;
                    var cruise_to_date = row.cells[3].childNodes[0].value;
                    var route = row.cells[4].childNodes[0].value;
                    var cabin = row.cells[5].childNodes[0].value;
                    var sharing = row.cells[6].childNodes[0].value;

                    if (cruise_from_date == "") {
                        error_msg_alert('Enter cruise departure datetime in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }

                    if (cruise_to_date == "") {
                        error_msg_alert('Enter cruise departure datetime  in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                    if (route == "") {
                        error_msg_alert('Enter route in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                    if (cabin == "") {
                        error_msg_alert('Enter cabin in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                    cruise_departure_date_arr.push(cruise_from_date);
                    cruise_arrival_date_arr.push(cruise_to_date);
                    route_arr.push(route);
                    cabin_arr.push(cabin);
                    sharing_arr.push(sharing);

                }
            }

            //Hotel Information
            var package_type_arr = [];
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
            var hotel_meal_plan_arr = [];

            var table = document.getElementById("tbl_package_tour_quotation_dynamic_hotel");
            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {

                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {

                    var package_type = row.cells[2].childNodes[0].value;
                    var city_name = row.cells[3].childNodes[0].value;
                    var hotel_id = row.cells[4].childNodes[0].value;
                    var hotel_cat = row.cells[5].childNodes[0].value;
                    var check_in = row.cells[6].childNodes[0].value;
                    var check_out = row.cells[7].childNodes[0].value;
                    var hotel_type = row.cells[8].childNodes[0].value;
                    var hotel_stay_days1 = row.cells[9].childNodes[0].value;
                    var total_rooms = row.cells[10].childNodes[0].value;
                    var extra_bed = row.cells[11].childNodes[0].value;
                    var package_name1 = row.cells[12].childNodes[0].value;
                    var hotel_cost = row.cells[13].childNodes[0].value;
                    var package_id1 = row.cells[14].childNodes[0].value;
                    var extra_bed_cost = row.cells[15].childNodes[0].value;
                    var meal_plan = row.cells[16].childNodes[0].value;

                    if (city_name == "") {
                        error_msg_alert('Select hotel city in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                    if (hotel_id == "") {
                        error_msg_alert('Enter hotel in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }

                    if (hotel_stay_days1 == "") {
                        error_msg_alert('Enter hotel total days in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }

                    package_type_arr.push(package_type);
                    city_name_arr.push(city_name);
                    hotel_name_arr.push(hotel_id);
                    hotel_cat_arr.push(hotel_cat);
                    check_in_arr.push(check_in);
                    check_out_arr.push(check_out);
                    hotel_stay_days_arr.push(hotel_stay_days1);
                    hotel_type_arr.push(hotel_type);
                    total_rooms_arr.push(total_rooms);
                    extra_bed_arr.push(extra_bed);
                    package_name_arr.push(package_name1);
                    hotel_cost_arr.push(hotel_cost);
                    extra_bed_cost_arr.push(extra_bed_cost);
                    hotel_meal_plan_arr.push(meal_plan);
                }
            }

            //Transport Information
            var vehicle_name_arr = [];
            var start_date_arr = [];
            var pickup_arr = [];
            var drop_arr = [];
            var vehicle_count_arr = [];
            var transport_cost_arr1 = [];
            var package_name_arr1 = [];
            var pickup_type_arr = [];
            var drop_type_arr = [];
            var end_date_arr = [];
            var service_duration_arr = [];
            var table = document.getElementById("tbl_package_tour_quotation_dynamic_transport");

            var rowCount = table.rows.length;
            for (var i = 0; i < rowCount; i++) {

                var row = table.rows[i];
                if (row.cells[0].childNodes[0].checked) {
                    var transport_id = row.cells[2].childNodes[0].value;
                    var travel_date = row.cells[3].childNodes[0].value;
                    var end_date = row.cells[4].childNodes[0].value;
                    var service_duration = row.cells[7].childNodes[0].value;
                    var vehicle_count = row.cells[8].childNodes[0].value;
                    var transport_cost = row.cells[9].childNodes[0].value;
                    var pname = row.cells[10].childNodes[0].value;
                    var pid = row.cells[11].childNodes[0].value;

                    var pickup = row.cells[5].childNodes[0].value;
                    var drop = row.cells[6].childNodes[0].value;
                    var pickup_type = $("option:selected", $("#" + row.cells[5].childNodes[0].id)).parent()
                        .attr('value');
                    var drop_type = $("option:selected", $("#" + row.cells[6].childNodes[0].id)).parent().attr(
                        'value');

                    if (transport_id == "") {
                        error_msg_alert('Select Transport Vehicle in row' + (i + 1));
                        resetQuotationSaveState();
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (travel_date == "") {
                        error_msg_alert('Enter Travel date in row' + (i + 1));
                        resetQuotationSaveState();
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (pickup == "") {
                        error_msg_alert('Select pickup location in row' + (i + 1));
                        resetQuotationSaveState();
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    if (drop == "") {
                        error_msg_alert('Select drop location in row' + (i + 1));
                        resetQuotationSaveState();
                        $('.accordion_content').removeClass("indicator");
                        $('#tbl_package_tour_quotation_dynamic_transport').parent('div').closest(
                            '.accordion_content').addClass("indicator");
                        return false;
                    }
                    vehicle_name_arr.push(transport_id);
                    start_date_arr.push(travel_date);
                    end_date_arr.push(end_date);
                    pickup_arr.push(pickup);
                    drop_arr.push(drop);
                    vehicle_count_arr.push(vehicle_count);
                    transport_cost_arr1.push(transport_cost);
                    package_name_arr1.push(pname);
                    pickup_type_arr.push(pickup_type);
                    drop_type_arr.push(drop_type);
                    service_duration_arr.push(service_duration);
                }
            }

            var table = document.getElementById("tbl_package_tour_quotation_dynamic_excursion");
            var rowCount = table ? table.rows.length : 0;
            var exc_date_arr_e = [];
            var city_name_arr_e = [];
            var excursion_name_arr = [];
            var transfer_option_arr = [];
            var adult_arr = [];
            var chwb_arr = [];
            var chwob_arr = [];
            var infant_arr = [];
            var excursion_amt_arr = [];
            var vehicle_id_arr_e = [];
            var vehicles_arr = [];

            for (var e = 0; e < rowCount; e++) {
                var row = table.rows[e];
                var $checkbox = $(row.cells[0]).find('input[type="checkbox"]');
                if (!$checkbox.prop('checked')) {
                    continue;
                }
                var exc_date = getExcursionRowValue(row, 2);
                var city_name = getExcursionRowValue(row, 3);
                var excursion_name = getExcursionRowValue(row, 4);
                var transfer_option = getExcursionRowValue(row, 5);
                var adults = getExcursionRowValue(row, 6) || 0;
                var chwb = getExcursionRowValue(row, 7) || 0;
                var chwob = getExcursionRowValue(row, 8) || 0;
                var infant = getExcursionRowValue(row, 9) || 0;
                var excursion_amount = getExcursionRowValue(row, 10) || 0;
                var vehicle_id = getExcursionRowValue(row, 15);
                var vehicles = getExcursionRowValue(row, 16) || 0;

                if (exc_date == "") {
                    error_msg_alert('Select Activity date in row' + (e + 1));
                    resetQuotationSaveState();
                    return false;
                }
                if (city_name == "") {
                    error_msg_alert('Select Activity city in row' + (e + 1));
                    resetQuotationSaveState();
                    return false;
                }
                if (excursion_name == "") {
                    error_msg_alert('Select Activity name in row' + (e + 1));
                    resetQuotationSaveState();
                    return false;
                }
                if (transfer_option == "") {
                    error_msg_alert('Select Transfer option in row' + (e + 1));
                    resetQuotationSaveState();
                    return false;
                }
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
            }

            //Costing Information  
            var tour_cost_arr = [];
            var transport_cost_arr = [];
            var excursion_cost_arr = [];
            var basic_amount_arr = [];
            var service_charge_arr = [];
            var service_tax_subtotal_arr = [];
            var tcs_arr = [];
            var tcsvalue_arr = [];
            var total_tour_cost_arr = [];
            var package_name_arr2 = [];
            var package_type_c_arr = [];
            var discount_in_arr = [];
            var discount_arr = [];

            var costingEntries = (typeof collectGroupCostingEntries === 'function')
                ? collectGroupCostingEntries()
                : ((typeof quotationCollectGroupCostingEntries === 'function')
                    ? quotationCollectGroupCostingEntries()
                    : []);

            if (!costingEntries || !costingEntries.length) {
                error_msg_alert('Please enter land costing details before saving.');
                resetQuotationSaveState();
                return false;
            }

            // Per Person mode: validate PP tax fields (not hidden Group Costing tax dropdowns)
            if (costing_type == 2) {
                var ppPackages = (typeof quotationCollectPpCostingEntries === 'function')
                    ? quotationCollectPpCostingEntries()
                    : [];
                if (!ppPackages.length) {
                    error_msg_alert('Please enter Per Person costing details before saving.');
                    resetQuotationSaveState();
                    return false;
                }
                var adult_count_v = parseInt($('#total_adult').val(), 10) || 0;
                var cweb_count_v = parseInt($('#children_with_bed').val(), 10) || 0;
                var cwnb_count_v = parseInt($('#children_without_bed').val(), 10) || 0;
                var infant_count_v = parseInt($('#total_infant').val(), 10) || 0;
                var paxRequired = {
                    adult: adult_count_v > 0,
                    cweb: cweb_count_v > 0,
                    cwnb: cwnb_count_v > 0,
                    infant: infant_count_v > 0
                };
                for (var pi = 0; pi < ppPackages.length; pi++) {
                    var pkgLabel = ppPackages[pi].package_type || ('Package ' + (pi + 1));
                    var rows = ppPackages[pi].rows || [];
                    for (var ri = 0; ri < rows.length; ri++) {
                        var prow = rows[ri];
                        if (!prow || !paxRequired[prow.type]) {
                            continue;
                        }
                        // value "1" is the placeholder "Tax Apply On" in PP dropdowns
                        if (!prow.tax_apply_on || prow.tax_apply_on === '1') {
                            error_msg_alert('Select Tax Apply On for ' + prow.type + ' (' + pkgLabel + ')');
                            resetQuotationSaveState();
                            return false;
                        }
                        if (!prow.tax_value) {
                            error_msg_alert('Select Tax for ' + prow.type + ' (' + pkgLabel + ')');
                            resetQuotationSaveState();
                            return false;
                        }
                    }
                }
            }

            for (var i = 0; i < costingEntries.length; i++) {
                var entry = costingEntries[i];
                var package_type_c = entry.package_type_c;
                var tour_cost = entry.tour_cost;
                var transport_cost = entry.transport_cost;
                var excursion_cost = entry.excursion_cost;
                var basic_cost = entry.basic_cost;
                var service_tax = entry.service_tax;
                var discount_in = entry.discount_in;
                var discount = entry.discount;
                var tax_apply_on = entry.tax_apply_on;
                var tax_value = entry.tax_value;
                var service_tax_subtotal = entry.service_tax_subtotal;
                var tcs = entry.tcs;
                var tcsvalue = entry.tcsvalue;
                var total_tour_cost = entry.total_tour_cost;
                var package_name3 = entry.package_name3;
                var pkg_id = entry.pkg_id;

                if (tour_cost == "") {
                    error_msg_alert('Select Hotel cost in row' + (i + 1));
                    resetQuotationSaveState();
                    return false;
                }
                // Group Costing tax is required only when Group Costing tab is active
                if (costing_type != 2) {
                    if (tax_apply_on == "") {
                        error_msg_alert('Select Tax Apply On in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                    if (tax_value == "") {
                        error_msg_alert('Select Tax in row' + (i + 1));
                        resetQuotationSaveState();
                        return false;
                    }
                }
                tour_cost_arr.push(tour_cost);
                transport_cost_arr.push(transport_cost);
                excursion_cost_arr.push(excursion_cost);
                basic_amount_arr.push(basic_cost);
                service_charge_arr.push(service_tax);
                discount_in_arr.push(discount_in);
                discount_arr.push(discount);
                service_tax_subtotal_arr.push(service_tax_subtotal);
                tcs_arr.push(tcs);
                tcsvalue_arr.push(tcsvalue);
                total_tour_cost_arr.push(total_tour_cost);
                package_name_arr2.push(package_name3);
                package_type_c_arr.push(package_type_c);
            }
            
            //BSM value Costing Information  
            var bsmValues = (typeof collectGroupCostingBsmValues === 'function')
                ? collectGroupCostingBsmValues()
                : [];
            //Adult & Child Costing Information  
            var c_package_id_arr = [];
            var adult_cost_arr = [];
            var infant_cost_arr = [];
            var child_with_arr = [];
            var child_without_arr = [];

            adult_cost_arr.push($('#adult_cost').val() || '');
            child_with_arr.push($('#child_with').val() || '');
            child_without_arr.push($('#child_without').val() || '');
            infant_cost_arr.push($('#infant_cost').val() || '');
            c_package_id_arr.push($('#pacakge_id2').val() || '');

            var package_id_arr1 = [];
            var incl_arr = [];
            var excl_arr = [];

            $('input[name="custom_package"]:checked').each(function() {

                package_id_arr1.push($(this).val());
                var package_id = $(this).val();
                //Incl & Excl
                var table = document.getElementById("dynamic_table_incl" + package_id);
                var rowCount = table.rows.length;
                for (var i = 0; i < rowCount; i++) {
                    var row = table.rows[i];
                    var inclusion = $('#inclusions' + package_id).val();
                    var exclusion = $('#exclusions' + package_id).val();

                    incl_arr.push(inclusion);
                    excl_arr.push(exclusion);
                }
            });

            var is_ai_quotation = sessionStorage.getItem('is_ai_quotation') === '1' ? '1' : ($('#is_ai_quotation').val() || '0');
            var dest_id = sessionStorage.getItem('quotation_dest_id') || $('#quotation_dest_id').val() || $('#dest_name').val() || '';
            if (is_ai_quotation === '1' && package_id_arr1.length === 0) {
                package_id_arr1 = ['0'];
            }

            // Get itinerary data from sessionStorage (saved in tab2)
            var itineraryData = sessionStorage.getItem('itinerary_data');
            var attraction_arr = [];
            var program_arr = [];
            var stay_arr = [];
            var meal_plan_arr = [];
            var day_image_arr = [];
            var package_p_id_arr = [];

            if (itineraryData) {
                var data = JSON.parse(itineraryData);
                attraction_arr = data.attraction_arr || [];
                program_arr = data.program_arr || [];
                stay_arr = data.stay_arr || [];
                meal_plan_arr = data.meal_plan_arr || [];
                day_image_arr = data.day_image_arr || [];
                package_p_id_arr = data.package_p_id_arr || [];
                
                console.log("Using stored itinerary data:", {
                    attraction_arr: attraction_arr,
                    program_arr: program_arr,
                    stay_arr: stay_arr,
                    meal_plan_arr: meal_plan_arr,
                    day_image_arr: day_image_arr,
                    package_p_id_arr: package_p_id_arr
                });
                
                // Debug: Check if arrays have data
                console.log("Itinerary data counts - attractions: " + attraction_arr.length + ", programs: " + program_arr.length + ", stays: " + stay_arr.length);
            } else {
                console.log("No stored itinerary data found, collecting from tables...");
                // Fallback: collect from tables if no stored data
                for (var j = 0; j < package_id_arr1.length; j++) {
                    var table = document.getElementById("dynamic_table_list_p_" + package_id_arr1[j]);
                    if (!table) {
                        console.error("Table not found: dynamic_table_list_p_" + package_id_arr1[j]);
                        continue;
                    }
                    var rowCount = table.rows.length;
                    for (var i = 0; i < rowCount; i++) {
                        var row = table.rows[i];
                        if (row.cells[0].childNodes[0] && row.cells[0].childNodes[0].checked) {
                            var attraction = row.cells[2].childNodes[0] ? row.cells[2].childNodes[0].value : '';
                            var program = row.cells[3].childNodes[0] ? row.cells[3].childNodes[0].value : '';
                            var stay = row.cells[4].childNodes[0] ? row.cells[4].childNodes[0].value : '';
                            var meal_plan = row.cells[5].childNodes[0] ? row.cells[5].childNodes[0].value : '';
                            var package_id1 = row.cells[7].childNodes[0] ? row.cells[7].childNodes[0].value : '';

                            if (program == "") {
                                error_msg_alert('Daywise program is mandatory in row' + (i + 1));
                                resetQuotationSaveState();
                                return false;
                            }
                            
                            attraction_arr.push(attraction);
                            program_arr.push(program);
                            stay_arr.push(stay);
                            meal_plan_arr.push(meal_plan);
                            
                            // Get image data for this row
                            var img = '';
                            var existingImgInput = row.querySelector('input[id^="existing_image_path_"]');
                            if (existingImgInput) {
                                img = existingImgInput.value || '';
                            }
                            day_image_arr.push(img);
                            
                            package_p_id_arr.push(package_id1);
                        }
                    }
                }
            }

            if (is_ai_quotation === '1' && incl_arr.length === 0) {
                var inclContent = getQuotationEditorContent('inclusions_ai');
                var exclContent = getQuotationEditorContent('exclusions_ai');
                if (!inclContent && !exclContent && itineraryData) {
                    try {
                        var storedInclExcl = JSON.parse(itineraryData);
                        inclContent = storedInclExcl.inclusions || '';
                        exclContent = storedInclExcl.exclusions || '';
                    } catch (e) {}
                }
                incl_arr = [inclContent];
                excl_arr = [exclContent];
            }

            console.log("Final itinerary arrays:", {
                attraction_arr: attraction_arr,
                program_arr: program_arr,
                stay_arr: stay_arr,
                meal_plan_arr: meal_plan_arr,
                day_image_arr: day_image_arr,
                package_p_id_arr: package_p_id_arr
            });

            // ==============================
// PER PERSON COSTING (SAVE)
// ==============================

        var pp_costing_arr = [];
        if (typeof quotationCollectPpCostingEntries === 'function') {
            pp_costing_arr = quotationCollectPpCostingEntries();
        } else {
            function getPP(prefix) {
                return {
                    type: prefix,
                    hotel: +$('#' + prefix + '_hotel_pp').val() || 0,
                    transfer: +$('#' + prefix + '_transfer_pp').val() || 0,
                    activity: +$('#' + prefix + '_activity_pp').val() || 0,
                    land_cost: +$('#' + prefix + '_land_cost_pp').val() || 0,
                    service_charge: +$('#' + prefix + '_service_charge_pp').val() || 0,
                    discount_in: $('#' + prefix + '_discount_in_pp').val(),
                    discount_amount: +$('#' + prefix + '_discount_amount_pp').val() || 0,
                    flight: +$('#' + prefix + '_flight_pp').val() || 0,
                    train: +$('#' + prefix + '_train_pp').val() || 0,
                    cruise: +$('#' + prefix + '_cruise_pp').val() || 0,
                    visa: +$('#' + prefix + '_visa_pp').val() || 0,
                    guide: +$('#' + prefix + '_guide_pp').val() || 0,
                    misc: +$('#' + prefix + '_misc_pp').val() || 0,
                    tax_apply_on: $('#' + prefix + '_tax_apply_on_pp').val(),
                    tax_value: $('#' + prefix + '_select_tax_pp').val(),
                    tax_amount: +$('#' + prefix + '_tax_amount_pp').val() || 0,
                    tcs: $('#' + prefix + '_select_tcs_pp').val(),
                    tcs_amount: +$('#' + prefix + '_tcs_amount_pp').val() || 0,
                    total: +$('#' + prefix + '_total_amount_pp').val() || 0
                };
            }
            pp_costing_arr = [{
                package_type: $('#ppackage_type1').val() || '',
                package_id: $('#pacakge_id2').val() || '',
                rows: [getPP('adult'), getPP('cweb'), getPP('cwnb'), getPP('infant')]
            }];
        }

            // Get the temporary quotation ID from sessionStorage
            var temp_quotation_id = sessionStorage.getItem('temp_quotation_id');
            console.log("Using temporary quotation ID:", temp_quotation_id);

            var price_str_url = $("#upload_url").val();
            var pckg_daywise_url = $('#pckg_daywise_url').val();
            var currency_code = $('#currency_code').val() || $('#currency_code_pp').val();
            var discount = $('#discount').val();
            var base_url = $('#base_url').val();

            $("#vi_confirm_box").vi_confirm_box({
                callback: function(result) {
                    if (result == "yes") {
                        $('#btn_quotation_save').button('loading');
                        $('#btn_quotation_save').prop('disabled', false);
                        $.ajax({

                            type: 'post',

                            url: base_url +
                                'controller/package_tour/quotation/quotation_save.php',

                            data: {
                                enquiry_id: enquiry_id,
                                tour_name: tour_name,
                                from_date: from_date,
                                to_date: to_date,
                                total_days: total_days,
                                customer_name: customer_name,user_id:user_id,
                                email_id: email_id,
                                mobile_no: mobile_no,
                                country_code:country_code,
                                total_adult: total_adult,
                                total_infant: total_infant,
                                total_passangers: total_passangers,
                                children_without_bed: children_without_bed,
                                children_with_bed: children_with_bed,
                                quotation_date: quotation_date,
                                booking_type: booking_type,
                                train_cost: train_cost,
                                flight_cost: flight_cost,
                                visa_cost: visa_cost,
                                train_from_location_arr: train_from_location_arr,
                                train_to_location_arr: train_to_location_arr,
                                train_class_arr: train_class_arr,
                                train_arrival_date_arr: train_arrival_date_arr,
                                train_departure_date_arr: train_departure_date_arr,
                                plane_from_city_arr: plane_from_city_arr,
                                plane_to_city_arr: plane_to_city_arr,
                                plane_from_location_arr: plane_from_location_arr,
                                plane_to_location_arr: plane_to_location_arr,
                                airline_name_arr: airline_name_arr,
                                plane_class_arr: plane_class_arr,
                                arraval_arr: arraval_arr,
                                dapart_arr: dapart_arr,
                                cruise_departure_date_arr: cruise_departure_date_arr,
                                cruise_arrival_date_arr: cruise_arrival_date_arr,
                                route_arr: route_arr,
                                cabin_arr: cabin_arr,
                                sharing_arr: sharing_arr,
                                package_type_arr: package_type_arr,
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
                                meal_plan_arr:meal_plan_arr,
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
                                service_duration_arr:service_duration_arr,
                                tour_cost_arr: tour_cost_arr,
                                excursion_cost_arr: excursion_cost_arr,
                                adult_arr: adult_arr,
                                chwb_arr: chwb_arr,
                                chwob_arr: chwob_arr,
                                infant_arr: infant_arr,
                                vehicles_arr:vehicles_arr,
                                basic_amount_arr: basic_amount_arr,
                                service_charge_arr: service_charge_arr,
                                service_tax_subtotal_arr: service_tax_subtotal_arr,
                                tcs_arr: tcs_arr,
                                tcsvalue_arr: tcsvalue_arr,
                                total_tour_cost_arr: total_tour_cost_arr,
                                package_name_arr2: package_name_arr2,
                                transport_cost_arr1: transport_cost_arr1,
                                transport_cost_arr: transport_cost_arr,
                                package_id_arr: package_id_arr1,
                                login_id: login_id,
                                emp_id: emp_id,
                                city_name_arr_e: city_name_arr_e,
                                excursion_name_arr: excursion_name_arr,
                                exc_date_arr_e: exc_date_arr_e,
                                transfer_option_arr: transfer_option_arr,
                                excursion_amt_arr: excursion_amt_arr,
                                vehicle_id_arr_e: vehicle_id_arr_e,
                                guide_cost: guide_cost,
                                cruise_cost: cruise_cost,
                                misc_cost: misc_cost,
                                attraction_arr: attraction_arr,
                                program_arr: program_arr,
                                stay_arr: stay_arr,
                                hotel_meal_plan_arr: hotel_meal_plan_arr,
                                day_image_arr: day_image_arr,
                                package_p_id_arr: package_p_id_arr,
                                branch_admin_id: branch_admin_id,
                                c_package_id_arr: c_package_id_arr,
                                package_type_c_arr: package_type_c_arr,
                                discount_in_arr:discount_in_arr,
                                discount_arr:discount_arr,
                                adult_cost_arr: adult_cost_arr,
                                infant_cost_arr: infant_cost_arr,
                                child_with_arr: child_with_arr,
                                child_without_arr: child_without_arr,
                                price_str_url: price_str_url,
                                incl_arr: incl_arr,
                                excl_arr: excl_arr,
                                financial_year_id: financial_year_id,
                                pckg_daywise_url: pckg_daywise_url,
                                costing_type: costing_type,
                                bsmValues: bsmValues,
                                currency_code: currency_code,
                                discount: discount,
                                flight_acost: flight_acost,
                                flight_ccost: flight_ccost,
                                flight_icost: flight_icost,
                                train_acost: train_acost,
                                train_ccost: train_ccost,
                                train_icost: train_icost,
                                cruise_acost: cruise_acost,
                                cruise_ccost: cruise_ccost,
                                cruise_icost: cruise_icost,
                                other_desc: other_desc,
                                pp_costing_arr: JSON.stringify(pp_costing_arr),
                                temp_quotation_id: temp_quotation_id,
                                is_ai_quotation: is_ai_quotation,
                                dest_id: dest_id
                            },
                            success: function(message) {
                                console.log("QUOTATION SAVE: Response received");
                                window.quotationSaveInProgress = false; // Reset flag
                                $('#btn_quotation_save').button('reset');
                                $('#btn_quotation_save').prop('disabled', false);
                                var msg = message.split('--');
                                if (msg[0] == "error") {
                                    error_msg_alert(msg[1]);
                                } else {
                                    // Extract quotation ID from success message for image uploads
                                    console.log("DEBUG: Success message:", message);
                                    var quotationIdMatch = message.match(/Quotation ID:\s*(\d+)/i);
                                    var quotationId = quotationIdMatch ? quotationIdMatch[1] : null;
                                    console.log("DEBUG: Extracted quotation ID:", quotationId);
                                    
                                    // Try alternative patterns if first one fails
                                    if (!quotationId) {
                                        var altMatch1 = message.match(/quotation\s+(\d+)/i);
                                        var altMatch2 = message.match(/(\d+)/);
                                        console.log("DEBUG: Alternative matches - pattern1:", altMatch1, "pattern2:", altMatch2);
                                        quotationId = altMatch1 ? altMatch1[1] : (altMatch2 ? altMatch2[1] : null);
                                        console.log("DEBUG: Final quotation ID:", quotationId);
                                    }
                                    
                                    // Collect stored images from itinerary interface
                                    var storedImages = collectStoredImages();
                                    console.log("Collected " + storedImages.length + " stored images for upload");
                                    
                                    // Upload itinerary images if any exist
                                    if (storedImages && storedImages.length > 0) {
                                        if (quotationId) {
                                            console.log("DEBUG: Uploading " + storedImages.length + " itinerary images for quotation " + quotationId);
                                            uploadItineraryImages(quotationId, storedImages);
                                        } else {
                                            console.error("DEBUG: Could not extract quotation ID from message:", message);
                                            // Try alternative ID extraction methods
                                            var altMatch = message.match(/(\d+)/);
                                            if (altMatch) {
                                                console.log("DEBUG: Using alternative quotation ID:", altMatch[1]);
                                                uploadItineraryImages(altMatch[1], storedImages);
                                            } else {
                                                console.error("DEBUG: No quotation ID found, cannot upload images");
                                                alert("Images could not be uploaded - quotation ID not found");
                                            }
                                        }
                                    } else {
                                        console.log("DEBUG: No images to upload");
                                    }
                                    
                                    $('#vi_confirm_box').vi_confirm_box({

                                        false_btn: false,

                                        message: message,

                                        true_btn_text: 'Ok',

                                        callback: function(data1) {

                                            if (data1 == "yes") {

                                                $('#btn_quotation_save')
                                                    .button('reset');
                                                $('#quotation_save_modal')
                                                    .modal('hide');
                                                $('#btn_quotation_save')
                                                    .prop('disabled',
                                                        false);
                                                window.location.href =
                                                    base_url +
                                                    'view/package_booking/quotation/home/index.php';
                                            }
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        console.log("QUOTATION SAVE: User cancelled, resetting flag");
                        window.quotationSaveInProgress = false; // Reset flag
                        $('#btn_quotation_save').button('reset');
                        $('#btn_quotation_save').prop('disabled', false);
                    }
                }
            });
        }
    });

        $('#btn_quotation_save').off('click.quotationSave').on('click.quotationSave', function(e) {
            e.preventDefault();
            $('#frm_tab4').submit();
        });
    }

    $(document).ready(function() {
        initQuotationSaveForm();
    });
    
    
// $(document).on("change","#tcs_tax-",function() {
//     customTcsTax(id);
// });

$(document).on("change", "[id^=tcs_tax-]", function() {
    var id = $(this).attr('id'); // Get the ID of the element that triggered the change event
    customTcsTax(id); // Pass this ID to the customTcsTax function
});

function customTcsTax(id)
{

    var suffix = (typeof quotationCostingFieldSuffix === 'function')
        ? quotationCostingFieldSuffix(id)
        : ('-' + (id.split('-')[1] || ''));
    if (suffix === '-') {
        suffix = '-';
    }

    var tcs_tax=$("#tcs_tax" + suffix).val();
    //alert(tcs_tax);
    //console.log(tcs_tax);
    if(tcs_tax!=='')
    {
       var  subtotal=$("#basic_amount" + suffix).val();
       var  servicecharge=$("#service_charge" + suffix).val();
       var txt_actual_tour_cost1=$("#total_tour_cost" + suffix).val();
       var discount1=$('#discount_amt' + suffix).val() || 0;
      


          // Get the discount type
          var discountIn = $("#discount_in" + suffix).val();
        
        // Calculate discount if it's in percentage
        if (discountIn === "Percentage") {
            discount1 = (discount1 / 100) * servicecharge;
        }
        // alert( discount1);
       var  service_tax_amount=0;
       var  tax_subtotal=$("#service_tax_subtotal" + suffix).val();
       var service_tax_subtotal1 = tax_subtotal.split(',');
	   for (var i = 0; i < service_tax_subtotal1.length; i++) {
		    var service_tax = service_tax_subtotal1[i].split(':');
		    service_tax_amount = parseFloat(service_tax_amount) + parseFloat(service_tax[2]);
            service_tax_amount1=parseFloat(service_tax_amount ) - parseFloat(discount1);
	   }
       var tcsamount=parseFloat(parseFloat(service_tax_amount1)+parseFloat(subtotal)+parseFloat(servicecharge))*parseFloat(tcs_tax)/100;

    //    var tcsamount=parseFloat(txt_actual_tour_cost1)*parseFloat(tcs_tax)/100;
       var totalTcs=$("#tcs1" + suffix).val();
       if(totalTcs=='')
       {
        totalTcs=0;   
       }
       $("#tcs1" + suffix).val(tcsamount.toFixed(2));
       txt_actual_tour_cost1=parseFloat(txt_actual_tour_cost1)-parseFloat(totalTcs);
       var txt_actual_tour_cost1total=parseFloat(tcsamount)+parseFloat(txt_actual_tour_cost1);
    //    $("#total_tour_cost" + suffix).val(Math.round(txt_actual_tour_cost1total).toFixed(2));

     $("#total_tour_cost" + suffix).val(txt_actual_tour_cost1total.toFixed(2));
    }
    else
    {
        var totalTcs=$("#tcs1" + suffix).val();
        $("#tcs1" + suffix).val(0.00);
        var txt_actual_tour_cost1=$("#total_tour_cost" + suffix).val();
        var txt_actual_tour_cost1total=parseFloat(txt_actual_tour_cost1)-parseFloat(totalTcs);
        // $("#total_tour_cost" + suffix).val(Math.round(txt_actual_tour_cost1total).toFixed(2));


         $("#total_tour_cost" + suffix).val(txt_actual_tour_cost1total.toFixed(2));
    }    

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

// Function to upload itinerary images after quotation is saved
function uploadItineraryImages(quotationId, images) {
    var base_url = $('#base_url').val();
    var uploadPromises = [];
    
    console.log("Starting upload of " + images.length + " images for quotation " + quotationId);
    
    images.forEach(function(imageData, index) {
        // Extract simple day number from offset (handles both "1" and "18_1" formats)
        var dayNumber = imageData.offset || imageData.day_number;
        if (typeof dayNumber === 'string' && dayNumber.includes('_')) {
            dayNumber = dayNumber.split('_').pop(); // Extract "1" from "18_1"
        }
        
        console.log("DEBUG: Uploading image for day", dayNumber, "from offset", imageData.offset);
        
        var formData = new FormData();
        formData.append('image', imageData.file);
        formData.append('quotation_id', quotationId);
        formData.append('package_id', imageData.package_id);
        formData.append('day_number', dayNumber);
        
        console.log("Uploading image for day " + dayNumber + ", package " + imageData.package_id + ", file: " + imageData.file.name);
        
        var promise = $.ajax({
            url: base_url + 'controller/package_tour/quotation/upload_itinerary_image.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log("Image upload response for day " + imageData.day_number + ":", response);
                if (response && response.success) {
                    console.log("Image uploaded successfully: " + response.image_url);
                    // Mark as uploaded using the correct offset key
                    var offsetKey = imageData.offset || dayNumber;
                    if (window.quotationImages && window.quotationImages[offsetKey]) {
                        window.quotationImages[offsetKey].uploaded = true;
                        window.quotationImages[offsetKey].image_url = response.image_url;
                    }
                } else {
                    console.error("Image upload failed: " + (response ? response.message : 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error("Image upload AJAX error for day " + imageData.day_number + ":", {
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
            }
        });
        
        uploadPromises.push(promise);
    });
    
    // Wait for all uploads to complete
    Promise.all(uploadPromises).then(function() {
        console.log("All itinerary images uploaded successfully");
        // Clear the stored images
        window.quotationImages = [];
    }).catch(function(error) {
        console.error("Some image uploads failed:", error);
    });
}

function calculateCostingCards(forceHotelFromTariff) {

    let hotelData = $('#hotel_pp_costing').val();
    let travelData = $("#travel_pp_costing").val();

    // ===== PAX COUNT =====
    let adult_count  = +$('#total_adult').val() || 0;
    let cwnb_count   = +$('#children_without_bed').val() || 0;
    let cweb_count   = +$('#children_with_bed').val() || 0;
    let infant_count = +$('#total_infant').val() || 0;

    let total_number = adult_count + cwnb_count + cweb_count + infant_count;
    if (total_number === 0) total_number = 1;

    // ===== TRANSPORT PER PERSON =====
    let transport_pp = 0;
    try {
        if (travelData) {
            let pp_arr_travelData = JSON.parse(travelData);
            if (pp_arr_travelData && pp_arr_travelData[0]) {
                transport_pp = (parseFloat(pp_arr_travelData[0]['total_cost']) / total_number) || 0;
            }
        }
    } catch (e) {
        transport_pp = 0;
    }

    var $ppRows = $('#quotation_pp_costing_container .quotation-pp-costing-row');

    function setHotelTransfer($row, suffix, hotelVals) {
        function setVal(baseId, value, force) {
            var $el = $row.find('#' + (typeof quotationPpFieldId === 'function' ? quotationPpFieldId(baseId, suffix) : (baseId + (suffix || ''))));
            if (!$el.length) {
                $el = $('#' + baseId + (suffix || ''));
            }
            if (!$el.length) return;
            if (force || !$el.val() || $el.val() == 0) {
                $el.val(value);
            }
        }
        setVal('adult_hotel_pp', hotelVals.adult || 0, forceHotelFromTariff);
        setVal('cweb_hotel_pp', hotelVals.cweb || 0, forceHotelFromTariff);
        setVal('cwnb_hotel_pp', hotelVals.cwnb || 0, forceHotelFromTariff);
        setVal('infant_hotel_pp', hotelVals.infant || 0, forceHotelFromTariff);
        setVal('adult_transfer_pp', transport_pp.toFixed(2), false);
        setVal('cweb_transfer_pp', transport_pp.toFixed(2), false);
        setVal('cwnb_transfer_pp', transport_pp.toFixed(2), false);
        setVal('infant_transfer_pp', transport_pp.toFixed(2), false);
    }

    // ===== COMMON FUNCTION =====
    function calculateCard($row, type, count, suffix) {
        var sid = function (base) {
            return '#' + (typeof quotationPpFieldId === 'function' ? quotationPpFieldId(base, suffix) : (base + (suffix || '')));
        };
        var $find = function (base) {
            var $el = $row && $row.length ? $row.find(sid(base)) : $(sid(base));
            return $el.length ? $el : $(sid(base));
        };

        if (count === 0) {
            $find(type + '_hotel_pp').val(0);
            $find(type + '_transfer_pp').val(0);
            $find(type + '_activity_pp').val(0);
            $find(type + '_land_cost_pp').val(0);
            $find(type + '_service_charge_pp').val(0);
            $find(type + '_tax_amount_pp').val(0);
            $find(type + '_tcs_amount_pp').val(0);
            $find(type + '_total_amount_pp').val(0);
            return;
        }

        let hotel    = +$find(type + '_hotel_pp').val() || 0;
        let transfer = +$find(type + '_transfer_pp').val() || 0;
        let activity = +$find(type + '_activity_pp').val() || 0;

        let land_cost = hotel + transfer + activity;
        $find(type + '_land_cost_pp').val(land_cost.toFixed(2));

        let service_charge = land_cost * 0.10;
        $find(type + '_service_charge_pp').val(service_charge.toFixed(2));

        let discount_type = $find(type + '_discount_in_pp').val();
        let discount_val  = +$find(type + '_discount_amount_pp').val() || 0;

        let discount_amount = (discount_type == "1")
            ? (service_charge * discount_val) / 100
            : discount_val;

        if (discount_amount > service_charge) discount_amount = service_charge;

        let final_service_charge = service_charge + 0 - discount_amount;

        let flight = +$find(type + '_flight_pp').val() || 0;
        let train  = +$find(type + '_train_pp').val() || 0;
        let cruise = +$find(type + '_cruise_pp').val() || 0;
        let visa   = +$find(type + '_visa_pp').val() || 0;
        let guide  = +$find(type + '_guide_pp').val() || 0;
        let misc   = +$find(type + '_misc_pp').val() || 0;

        let extra_cost = flight + train + cruise + visa + guide + misc;
        let base_amount = land_cost + final_service_charge + extra_cost;

        let tax_text = $find(type + '_select_tax_pp').find('option:selected').text();
        let tax_match = tax_text.match(/\d+(\.\d+)?/);
        let tax_percent = tax_match ? parseFloat(tax_match[0]) : 0;

        let tax_apply_on = $find(type + '_tax_apply_on_pp').val();

        let tax_base = 0;
        if (tax_apply_on == "2") tax_base = land_cost;
        else if (tax_apply_on == "3") tax_base = final_service_charge;
        else if (tax_apply_on == "4") tax_base = base_amount;

        let tax_amount = (tax_base * tax_percent) / 100;
        $find(type + '_tax_amount_pp').val(tax_amount.toFixed(2));

        let tcs_val = $find(type + '_select_tcs_pp').val();
        let tcs_percent = (tcs_val == "2") ? 5 : (tcs_val == "3") ? 20 : 0;

        let tcs_amount = ((base_amount + tax_amount) * tcs_percent) / 100;
        $find(type + '_tcs_amount_pp').val(tcs_amount.toFixed(2));

        let total = base_amount + tax_amount + tcs_amount;
        $find(type + '_total_amount_pp').val(total.toFixed(2));
    }

    var packageHotelArr = [];
    if (typeof quotationBuildHotelPerPersonArrFromPpCosting === 'function') {
        packageHotelArr = quotationBuildHotelPerPersonArrFromPpCosting();
    } else if (hotelData) {
        try {
            var pp_arr = JSON.parse(hotelData);
            if (pp_arr && pp_arr[0]) {
                packageHotelArr = [{
                    adult_cost: pp_arr[0]['adult_cost'] || 0,
                    cwb_cost: pp_arr[0]['child_with_bed'] || 0,
                    cwob_cost: pp_arr[0]['child_without_bed'] || 0,
                    infant_cost: pp_arr[0]['infant_cost'] || 0,
                    type: pp_arr[0]['package_type'] || ''
                }];
            }
        } catch (e) {}
    }

    $ppRows = $('#quotation_pp_costing_container .quotation-pp-costing-row');
    if (!$ppRows.length) {
        $ppRows = $();
        // legacy single block
        var hotelVals = packageHotelArr[0] || {};
        setHotelTransfer($(document), '', {
            adult: hotelVals.adult_cost || 0,
            cweb: hotelVals.cwb_cost || 0,
            cwnb: hotelVals.cwob_cost || 0,
            infant: hotelVals.infant_cost || 0
        });
        calculateCard(null, 'adult', adult_count, '');
        calculateCard(null, 'cweb', cweb_count, '');
        calculateCard(null, 'cwnb', cwnb_count, '');
        calculateCard(null, 'infant', infant_count, '');
        return;
    }

    $ppRows.each(function (idx) {
        var $row = $(this);
        var suffix = $row.attr('data-pp-suffix') || '';
        var hotelVals = packageHotelArr[idx] || packageHotelArr[0] || {};
        if (hotelVals.type) {
            $row.find('#' + (typeof quotationPpFieldId === 'function' ? quotationPpFieldId('ppackage_type1', suffix) : ('ppackage_type1' + suffix))).val(hotelVals.type);
        }
        setHotelTransfer($row, suffix, {
            adult: hotelVals.adult_cost || 0,
            cweb: hotelVals.cwb_cost || 0,
            cwnb: hotelVals.cwob_cost || 0,
            infant: hotelVals.infant_cost || 0
        });
        calculateCard($row, 'adult', adult_count, suffix);
        calculateCard($row, 'cweb', cweb_count, suffix);
        calculateCard($row, 'cwnb', cwnb_count, suffix);
        calculateCard($row, 'infant', infant_count, suffix);
    });
}
$(document).on('input change', '.costing-table input, .costing-table select', function () {
    calculateCostingCards();
});

// =========================
// EVENTS
// =========================
$(document).ready(function () {

    console.log("READY RUNNING");

    quotation_cost_calculate('tour_cost-');
    if (typeof quotationBuildHotelPerPersonArrFromPpCosting === 'function'
        && typeof quotationPopulatePpCostingFromHotels === 'function') {
        var initPp = quotationBuildHotelPerPersonArrFromPpCosting();
        if (initPp.length) {
            quotationPopulatePpCostingFromHotels(initPp, {});
        }
    }
    calculateCostingCards();

  
    $(document).on('keyup change',
        '#adult_cost, #child_with, #child_without, ' +
        '#flight_acost, #train_acost, #cruise_acost, ' +
        '#flight_ccost, #train_ccost, #cruise_ccost, ' +
        '#visa_cost, #guide_cost, #misc_cost, #total_pax',
        function () {
            console.log("INPUT CHANGED");
            quotation_cost_calculate('tour_cost-');
            calculateCostingCards();
        }
    );

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
		if (typeof quotationBuildHotelPerPersonArrFromPpCosting === 'function'
			&& typeof quotationPopulatePpCostingFromHotels === 'function') {
			var ppHotels = quotationBuildHotelPerPersonArrFromPpCosting();
			if (ppHotels.length) {
				var travelData = $("#travel_pp_costing").val();
				var transport_pp = 0;
				try {
					if (travelData) {
						var td = JSON.parse(travelData);
						var adult_count  = +$('#total_adult').val() || 0;
						var cwnb_count   = +$('#children_without_bed').val() || 0;
						var cweb_count   = +$('#children_with_bed').val() || 0;
						var infant_count = +$('#total_infant').val() || 0;
						var total_number = adult_count + cwnb_count + cweb_count + infant_count;
						if (total_number === 0) total_number = 1;
						if (td && td[0]) {
							transport_pp = (parseFloat(td[0]['total_cost']) / total_number) || 0;
						}
					}
				} catch (e) {}
				quotationPopulatePpCostingFromHotels(ppHotels, { transport_pp: transport_pp });
			}
		}
		calculateCostingCards(true);
	}

}
costing_reflect();


</script>