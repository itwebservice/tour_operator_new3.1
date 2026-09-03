<?php
/**
 * One Per-Person costing block for a package type (update screen).
 * Expects: $pp_pkg (pax_type => row), $package_type, $suffix ('' | '-1' | '-2'), $entry_id, $currency_code_selected
 */
if (!isset($pp_pkg) || !is_array($pp_pkg)) {
	$pp_pkg = array();
}
$suffix = isset($suffix) ? (string) $suffix : '';
$package_type = isset($package_type) ? (string) $package_type : '';
$entry_id = isset($entry_id) ? $entry_id : '';
$currency_code_selected = isset($currency_code_selected) ? (string) $currency_code_selected : '';

if (!function_exists('pp_update_field_val')) {
	function pp_update_field_val($pp_pkg, $ptype, $field)
	{
		return isset($pp_pkg[$ptype][$field]) ? $pp_pkg[$ptype][$field] : '';
	}
}
if (!function_exists('pp_update_money_input')) {
	/** Money input with data-saved-amount so JS can restore DB value after currency/tariff glitches */
	function pp_update_money_input($id, $name, $value, $extra_attrs = '')
	{
		$val = ($value === '' || $value === null) ? '' : $value;
		$saved = ($val === '' || $val === null) ? '' : htmlspecialchars((string) $val, ENT_QUOTES);
		$val_attr = htmlspecialchars((string) $val, ENT_QUOTES);
		$type = (strpos($extra_attrs, 'type=') !== false) ? '' : 'type="number"';
		$class = (strpos($extra_attrs, 'class=') !== false) ? '' : 'class="form-control"';
		return '<input ' . $type . ' ' . $class . ' id="' . htmlspecialchars($id, ENT_QUOTES) . '" name="' . htmlspecialchars($name, ENT_QUOTES) . '" value="' . $val_attr . '" data-saved-amount="' . $saved . '" data-default-amount="' . $saved . '" ' . $extra_attrs . '>';
	}
}
if (!function_exists('pp_update_tax_apply')) {
	function pp_update_tax_apply($pp_pkg, $ptype)
	{
		return function_exists('pp_tax_apply_on_to_ui')
			? pp_tax_apply_on_to_ui(pp_update_field_val($pp_pkg, $ptype, 'tax_apply_on'))
			: '1';
	}
}
if (!function_exists('pp_update_tcs')) {
	function pp_update_tcs($pp_pkg, $ptype)
	{
		return function_exists('pp_tcs_to_ui')
			? pp_tcs_to_ui(pp_update_field_val($pp_pkg, $ptype, 'tcs'))
			: '1';
	}
}

$pax_defs = array(
	'adult' => 'Adult<br> (Double Sharing)',
	'cweb' => 'CWEB<br> (Child With Extra Bed)',
	'cwnb' => 'CWNB<br> (Child With No Bed)',
	'infant' => 'Infant',
);
?>
<div class="quotation-pp-costing-row mg_bt_20" data-pp-suffix="<?= htmlspecialchars($suffix) ?>" data-package-type="<?= htmlspecialchars($package_type) ?>">
	<div class="row mg_tp_10">
		<div class="col-md-3">
			<span>Package Type</span>
			<input type="text" id="ppackage_type1<?= $suffix ?>" name="ppackage_type1<?= $suffix ?>"
				placeholder="Package Type" title="Package Type"
				value="<?= htmlspecialchars($package_type) ?>" readonly>
		</div>
		<input type="hidden" id="pp_entry_id<?= $suffix ?>" name="pp_entry_id<?= $suffix ?>" value="<?= htmlspecialchars($entry_id) ?>">
		<input type="hidden" id="adult_cost<?= $suffix ?>" value="<?= htmlspecialchars(pp_update_field_val($pp_pkg,'adult','hotel_cost')) ?>">
		<input type="hidden" id="child_with<?= $suffix ?>" value="<?= htmlspecialchars(pp_update_field_val($pp_pkg,'cweb','hotel_cost')) ?>">
		<input type="hidden" id="child_without<?= $suffix ?>" value="<?= htmlspecialchars(pp_update_field_val($pp_pkg,'cwnb','hotel_cost')) ?>">
		<input type="hidden" id="infant_cost<?= $suffix ?>" value="<?= htmlspecialchars(pp_update_field_val($pp_pkg,'infant','hotel_cost')) ?>">
	</div>

	<div class="costing-content-wp">
		<div class="costing-card-wp mg_tp_10">
			<div class="row">
				<?php foreach ($pax_defs as $ptype => $plabel) {
					$tax_apply = pp_update_tax_apply($pp_pkg, $ptype);
					$tcs_ui = pp_update_tcs($pp_pkg, $ptype);
					$tax_value = pp_update_field_val($pp_pkg, $ptype, 'tax_value');
					$discount_in = (string) pp_update_field_val($pp_pkg, $ptype, 'discount_in');
					if ($discount_in === '') { $discount_in = '1'; }
				?>
				<div class="col-md-3">
					<div class="costing-card">
						<div class="costing-card-icon"><i class="fa fa-regular fa-user icon"></i></div>
						<div class="costing-card-detail"><p class="costing-card-label"><?= $plabel ?></p></div>
					</div>
					<div class="costing-card-table" style="display:block;">
						<table class="table table-bordered costing-table">
							<thead><tr><th>Components</th><th>Cost</th></tr></thead>
							<tbody>
								<tr data-type="hotel"><td>Hotel</td><td class="price"><?= pp_update_money_input($ptype.'_hotel_pp_update'.$suffix, $ptype.'_hotel_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'hotel_cost')) ?></td></tr>
								<tr data-type="transfer"><td>Transfer</td><td class="price"><?= pp_update_money_input($ptype.'_transfer_pp_update'.$suffix, $ptype.'_transfer_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'transfer_cost')) ?></td></tr>
								<tr data-type="activity"><td>Activity</td><td class="price"><?= pp_update_money_input($ptype.'_activity_pp_update'.$suffix, $ptype.'_activity_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'activity_cost')) ?></td></tr>
								<tr><td data-type="land_cost">Land Cost <br/><span>(Hotel+Transfer+Activity)</span></td><td class="price"><?= pp_update_money_input($ptype.'_land_cost_pp_update'.$suffix, $ptype.'_land_cost_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'land_cost'), 'type="text"') ?></td></tr>
								<tr data-type="service_charge"><td>Service Charges</td><td class="price"><?= pp_update_money_input($ptype.'_service_charge_pp_update'.$suffix, $ptype.'_service_charge_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'service_charge')) ?></td></tr>
								<tr data-type="discount"><td>Discount In</td><td>
									<select id="<?= $ptype ?>_discount_in_pp_update<?= $suffix ?>" name="<?= $ptype ?>_discount_in_pp_update<?= $suffix ?>" class="form-control">
										<option value="1" <?= $discount_in==='1'?'selected':'' ?>>Percentage</option>
										<option value="2" <?= $discount_in==='2'?'selected':'' ?>>Flat</option>
									</select>
								</td></tr>
								<tr data-type="discount_amount"><td class="price">Discount Amount</td><td><?= pp_update_money_input($ptype.'_discount_amount_pp_update'.$suffix, $ptype.'_discount_amount_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'discount_amount')) ?></td></tr>
								<tr data-type="flight_acost"><td>Flight Cost</td><td class="price"><?= pp_update_money_input($ptype.'_flight_pp_update'.$suffix, $ptype.'_flight_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'flight_cost')) ?></td></tr>
								<tr data-type="train_acost"><td>Train Cost</td><td class="price"><?= pp_update_money_input($ptype.'_train_pp_update'.$suffix, $ptype.'_train_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'train_cost')) ?></td></tr>
								<tr><td>Cruise Cost</td><td class="price"><?= pp_update_money_input($ptype.'_cruise_pp_update'.$suffix, $ptype.'_cruise_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'cruise_cost')) ?></td></tr>
								<tr data-type="visa_acost"><td>Visa Cost</td><td class="price"><?= pp_update_money_input($ptype.'_visa_pp_update'.$suffix, $ptype.'_visa_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'visa_cost')) ?></td></tr>
								<tr data-type="guide_acost"><td>Guide Cost</td><td class="price"><?= pp_update_money_input($ptype.'_guide_pp_update'.$suffix, $ptype.'_guide_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'guide_cost')) ?></td></tr>
								<tr data-type="miscellaneous_acost"><td>Miscellaneous Cost</td><td class="price"><?= pp_update_money_input($ptype.'_misc_pp_update'.$suffix, $ptype.'_misc_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'misc_cost')) ?></td></tr>
								<tr data-type="tax_apply_on"><td>Tax Apply On</td><td>
									<select id="<?= $ptype ?>_tax_apply_on_pp_update<?= $suffix ?>" name="<?= $ptype ?>_tax_apply_on_pp_update<?= $suffix ?>" class="form-control">
										<option value="1" <?= $tax_apply==='1'?'selected':'' ?>>Tax Apply On</option>
										<option value="2" <?= $tax_apply==='2'?'selected':'' ?>>Basic Amount</option>
										<option value="3" <?= $tax_apply==='3'?'selected':'' ?>>Service Charge</option>
										<option value="4" <?= $tax_apply==='4'?'selected':'' ?>>Total</option>
									</select>
								</td></tr>
								<tr data-type="tax"><td>Select Tax</td><td>
									<select id="<?= $ptype ?>_select_tax_pp_update<?= $suffix ?>" name="<?= $ptype ?>_select_tax_pp_update<?= $suffix ?>" class="form-control" data-selected="<?= htmlspecialchars($tax_value, ENT_QUOTES) ?>">
										<option value="">*Select Tax</option>
										<?php get_tax_dropdown('Income'); ?>
									</select>
								</td></tr>
								<tr data-type="tax_value"><td>Tax Amount</td><td class="price"><?= pp_update_money_input($ptype.'_tax_amt_pp_update'.$suffix, $ptype.'_tax_amt_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'tax_amount')) ?></td></tr>
								<tr data-type="tcs"><td>TCS</td><td>
									<select id="<?= $ptype ?>_select_tcs_pp_update<?= $suffix ?>" name="<?= $ptype ?>_select_tcs_pp_update<?= $suffix ?>" class="form-control">
										<option value="1" <?= $tcs_ui==='1'?'selected':'' ?>>*TCS Tax</option>
										<option value="2" <?= $tcs_ui==='2'?'selected':'' ?>>2% TCS</option>
										<option value="3" <?= $tcs_ui==='3'?'selected':'' ?>>20% TCS</option>
									</select>
								</td></tr>
								<tr data-type="tcs_value"><td>TCS Amount</td><td class="price"><?= pp_update_money_input($ptype.'_tcs_amount_pp_update'.$suffix, $ptype.'_tcs_amount_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'tcs_amount')) ?></td></tr>
								<tr data-type="total"><td>Total Cost</td><td class="price"><?= pp_update_money_input($ptype.'_total_amount_pp_update'.$suffix, $ptype.'_total_amount_pp_update'.$suffix, pp_update_field_val($pp_pkg,$ptype,'total_cost'), 'class="form-control totalcost-input"') ?></td></tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
