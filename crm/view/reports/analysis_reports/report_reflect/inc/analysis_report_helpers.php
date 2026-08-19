<?php
/**
 * Shared helpers for Analysis → Statistic Reports.
 */
if (!function_exists('analysis_report_new_status_sql')) {
	function analysis_report_new_status_sql($column = 'enquiry_master_entries.followup_status')
	{
		return $column . " IN ('New','Active')";
	}
}

if (!function_exists('analysis_report_converted_status_sql')) {
	function analysis_report_converted_status_sql($enquiry_id_column = 'enquiry_master.enquiry_id')
	{
		return "(enquiry_master_entries.followup_status = 'Converted' OR $enquiry_id_column IN (
			SELECT DISTINCT ptqm.enquiry_id
			FROM package_tour_booking_master ptbm
			INNER JOIN package_tour_quotation_master ptqm ON ptbm.quotation_id = ptqm.quotation_id
			WHERE ptbm.delete_status='0' AND ptqm.enquiry_id > 0
		))";
	}
}

if (!function_exists('analysis_report_date_between_sql')) {
	function analysis_report_date_between_sql($column, $fromdate, $todate)
	{
		if (!empty($fromdate) && !empty($todate)) {
			return " AND DATE($column) BETWEEN '$fromdate' AND '$todate'";
		}
		return '';
	}
}

if (!function_exists('analysis_report_b2b_customer_names')) {
	function analysis_report_b2b_customer_names($customer_id)
	{
		$customer_id = (int) $customer_id;
		$row_cust = mysqli_fetch_assoc(mysqlQuery(
			"SELECT company_name, first_name, middle_name, last_name FROM customer_master WHERE customer_id='$customer_id'"
		));
		if (!$row_cust) {
			return array('company_name' => '', 'full_name' => '');
		}
		return array(
			'company_name' => trim($row_cust['company_name'] ?? ''),
			'full_name' => trim(preg_replace('/\s+/', ' ', trim(
				($row_cust['first_name'] ?? '') . ' ' . ($row_cust['middle_name'] ?? '') . ' ' . ($row_cust['last_name'] ?? '')
			))),
		);
	}
}

if (!function_exists('analysis_report_b2b_portal_quotation_count')) {
	function analysis_report_b2b_portal_quotation_count($customer_id, $fromdate = null, $todate = null)
	{
		$customer_id = (int) $customer_id;
		$sq_reg = mysqli_fetch_assoc(mysqlQuery("SELECT register_id FROM b2b_registration WHERE customer_id='$customer_id'"));
		if (empty($sq_reg['register_id'])) {
			return 0;
		}
		$query = "SELECT register_id FROM b2b_quotations WHERE register_id='{$sq_reg['register_id']}'";
		$query .= analysis_report_date_between_sql('created_at', $fromdate, $todate);
		return mysqli_num_rows(mysqlQuery($query));
	}
}

if (!function_exists('analysis_report_b2b_package_quotation_count')) {
	function analysis_report_b2b_package_quotation_count($customer_id, $fromdate = null, $todate = null)
	{
		$customer_id = (int) $customer_id;
		$names = analysis_report_b2b_customer_names($customer_id);
		$name_filters = array();
		if ($names['company_name'] !== '') {
			$name_filters[] = "e.name = '" . addslashes($names['company_name']) . "'";
		}
		if ($names['full_name'] !== '') {
			$name_filters[] = "e.name = '" . addslashes($names['full_name']) . "'";
		}
		$name_sql = $name_filters ? (' OR ' . implode(' OR ', $name_filters)) : '';

		$query = "SELECT COUNT(DISTINCT q.quotation_id) AS cnt
			FROM package_tour_quotation_master q
			LEFT JOIN package_tour_booking_master b ON b.quotation_id = q.quotation_id AND b.delete_status='0'
			LEFT JOIN enquiry_master e ON q.enquiry_id = e.enquiry_id
			WHERE (b.customer_id='$customer_id'" . $name_sql . ")";
		$query .= analysis_report_date_between_sql('q.created_at', $fromdate, $todate);
		$row = mysqli_fetch_assoc(mysqlQuery($query));
		return (int) ($row['cnt'] ?? 0);
	}
}

if (!function_exists('analysis_report_b2b_quotation_count')) {
	function analysis_report_b2b_quotation_count($customer_id, $fromdate = null, $todate = null)
	{
		return analysis_report_b2b_portal_quotation_count($customer_id, $fromdate, $todate)
			+ analysis_report_b2b_package_quotation_count($customer_id, $fromdate, $todate);
	}
}

if (!function_exists('analysis_report_b2b_portal_booking_count')) {
	function analysis_report_b2b_portal_booking_count($customer_id, $fromdate = null, $todate = null)
	{
		$customer_id = (int) $customer_id;
		$query = "SELECT customer_id FROM b2b_booking_master WHERE customer_id='$customer_id' AND status=''";
		$query .= analysis_report_date_between_sql('created_at', $fromdate, $todate);
		return mysqli_num_rows(mysqlQuery($query));
	}
}

if (!function_exists('analysis_report_b2b_package_booking_count')) {
	function analysis_report_b2b_package_booking_count($customer_id, $fromdate = null, $todate = null)
	{
		$customer_id = (int) $customer_id;
		$query = "SELECT booking_id FROM package_tour_booking_master WHERE customer_id='$customer_id' AND delete_status='0'";
		$query .= analysis_report_date_between_sql('booking_date', $fromdate, $todate);
		return mysqli_num_rows(mysqlQuery($query));
	}
}

if (!function_exists('analysis_report_b2b_booking_count')) {
	function analysis_report_b2b_booking_count($customer_id, $fromdate = null, $todate = null)
	{
		return analysis_report_b2b_portal_booking_count($customer_id, $fromdate, $todate)
			+ analysis_report_b2b_package_booking_count($customer_id, $fromdate, $todate);
	}
}
