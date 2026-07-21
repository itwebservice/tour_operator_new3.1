<?php
include_once("../../../model/model.php");
$financial_year_id = $_SESSION['financial_year_id'];
$emp_id = $_SESSION['emp_id'];
$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$q = "select * from branch_assign where link='attractions_offers_enquiry/enquiry/index.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status1 = ($sq_count >0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="dashboard_table dashboard_table_panel main_block">
    <div class="col-md-12 no-pad table_verflow">
    <div class="table-responsive">
        <table class="table table-hover enquiryTable" style="margin: 0 !important;border: 0;">
        <thead>
            <tr class="table-heading-row">
            <th>S_No.</th>
            <th>enquiry_id</th>
            <th>Customer_Name</th>
            <th>Tour_type</th>
            <th>Tour_Name</th>
            <th>Mobile</th>
            <th>Followup_D/T&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Allocate_To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Followup_Type</th>
            <th>History</th>
            <th>Followup_Update</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $count = 0;
            $rightnow = date('Y-m-d');
            $from_date_db = $from_date ? get_datetime_db($from_date) : null;
            $to_date_db = $to_date ? get_datetime_db($to_date) : null;

            // Build base enquiry query with filters
            $query = "SELECT em.*, cu.name AS cust_user_name, emp.first_name, emp.last_name
                    FROM enquiry_master em
                    LEFT JOIN customer_users cu ON em.user_id = cu.user_id
                    LEFT JOIN emp_master emp ON em.assigned_emp_id = emp.emp_id
                    WHERE em.status != 'Disabled'";

            if ($branch_status1 == 'yes') {
                if ($role == 'Branch Admin' || $role == 'Accountant' || $role_id > 7) {
                    $query .= " AND em.branch_admin_id = '$branch_admin_id'";
                } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                    $query .= " AND em.assigned_emp_id = '$emp_id' AND em.branch_admin_id = '$branch_admin_id'";
                }
            } elseif ($role != 'Admin' && $role != 'Branch Admin' && $role_id != '7' && $role_id < '7') {
                $query .= " AND em.assigned_emp_id = '$emp_id'";
            }

            $sq_enquiries = mysqlQuery($query);

            // Pre-fetch latest enquiry_master_entries for all enquiry_ids
            $enquiry_ids = [];
            while ($row = mysqli_fetch_assoc($sq_enquiries)) {
                $enquiry_ids[] = $row['enquiry_id'];
                $enquiries_data[$row['enquiry_id']] = $row;
            }

            // Reset pointer to loop again after fetching all enquiry_ids
            if (count($enquiry_ids) == 0) {
                // No enquiries to process
                return;
            }

            // Fetch latest entries for all enquiries at once
            $ids_str = implode(',', array_map('intval', $enquiry_ids));

            $entries_query = "
                SELECT eme1.*
                FROM enquiry_master_entries eme1
                INNER JOIN (
                    SELECT enquiry_id, MAX(entry_id) AS max_entry_id
                    FROM enquiry_master_entries
                    WHERE enquiry_id IN ($ids_str)
                    GROUP BY enquiry_id
                ) eme2 ON eme1.entry_id = eme2.max_entry_id
                WHERE 
                    followup_status IN ('Active', 'In-Followup')";

            if ($from_date_db && $to_date_db) {
                $entries_query .= " AND followup_date BETWEEN '$from_date_db' AND '$to_date_db'";
            } else {


                $yesterday = date('Y-m-d', strtotime('-1 day'));
                    
                        $tomorrow  = date('Y-m-d 23:59:59', strtotime('+1 day'));

                $entries_query .= " AND followup_date BETWEEN '$yesterday'  AND '$tomorrow'";
            }

            $entries_result = mysqlQuery($entries_query);

            // Map latest entries by enquiry_id for quick access
            $latest_entries = [];
            while ($entry = mysqli_fetch_assoc($entries_result)) {
                $latest_entries[$entry['enquiry_id']] = $entry;
            }

            // Fetch all enquiry_master_entries grouped by enquiry_id to find statuses in bulk
            $status_query = "
                SELECT enquiry_id, followup_status, followup_type, followup_date
                FROM enquiry_master_entries
                WHERE enquiry_id IN ($ids_str)
                ORDER BY enquiry_id, entry_id DESC";

            $status_result = mysqlQuery($status_query);
            $status_data = [];
            while ($status_row = mysqli_fetch_assoc($status_result)) {
                $eid = $status_row['enquiry_id'];
                if (!isset($status_data[$eid])) $status_data[$eid] = [];
                $status_data[$eid][] = $status_row;
            }

            // Loop through enquiries and output rows
            foreach ($enquiries_data as $enquiry_id => $row) {
                if (!isset($latest_entries[$enquiry_id])) continue; // no latest entry with required status

                $entry = $latest_entries[$enquiry_id];

                $count++;

                // Decode enquiry content once
                $enquiry_content_arr = json_decode($row['enquiry_content'], true);
                $tour_name = 'NA';
                if (in_array($row['enquiry_type'], ['Package Booking', 'Group Booking'])) {
                    foreach ($enquiry_content_arr as $content_item) {
                        if ($content_item['name'] === 'tour_name') {
                            $tour_name = $content_item['value'];
                            break;
                        }
                    }
                }

                // Determine background color based on followup_status
                // Fetch the latest non-Dropped followup_status entry if exists
                $bg = '';
                if (isset($status_data[$enquiry_id])) {
                    foreach ($status_data[$enquiry_id] as $status_entry) {
                        if ($status_entry['followup_status'] != 'Dropped') {
                            $fs = $status_entry['followup_status'];
                            $bg = ($fs == 'Converted') ? 'success' : ($fs == 'Active' ? 'warning' : ($fs == 'Dropped' ? 'danger' : ''));
                            break;
                        }
                    }
                }

                // Determine followup_date to display
                $followup_date1 = $entry['followup_date'];

                // Determine status and back_color for table cell
                $status = $entry['followup_type'] ?: 'Not Done';
                $back_color = $entry['followup_type'] ? 'background: #40dbbc !important' : 'background: #ffc674 !important';

                $cust_user_name = $row['cust_user_name'] ? " ({$row['cust_user_name']})" : '';

                // Extract year from enquiry_date for get_enquiry_id()
                $year = explode('-', $row['enquiry_date'])[0];

                ?>
                <tr class="<?= $bg; ?>">
                    <td><?= $count; ?></td>
                    <td><?= get_enquiry_id($enquiry_id, $year); ?></td>
                    <td><?= htmlspecialchars($row['name'] . $cust_user_name); ?></td>
                    <td><?= htmlspecialchars($row['enquiry_type']); ?></td>
                    <td><?= htmlspecialchars($tour_name); ?></td>
                    <td><?= htmlspecialchars($row['mobile_no']); ?></td>
                    <td><?= get_datetime_user($followup_date1); ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><div style="<?= $back_color ?>" class="table_side_widget_text widget_blue_text table_status"><?= htmlspecialchars($status); ?></div></td>
                    <td><button class="btn btn-info btn-sm" id="history-<?= $count ?>" onclick="display_history('<?= $enquiry_id ?>','<?= $count ?>');" title="History"><i class="fa fa-history"></i></button></td>
                    <td><button class="btn btn-info btn-sm" id="followup-<?= $count ?>" onclick="Followup_update('<?= $enquiry_id ?>','<?= $count ?>');" title="Update Followup" target="_blank"><i class="fa fa-reply-all"></i></button></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        </table>
    </div> 
</div>
<!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> -->

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
    $('.enquiryTable').DataTable({
      "pageLength": 10,
      "lengthChange": true,
      "ordering": true,
      "searching": false
    });
  });
</script>