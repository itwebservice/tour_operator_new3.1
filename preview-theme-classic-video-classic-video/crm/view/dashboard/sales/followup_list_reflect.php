<?php
include_once("../../../model/model.php");

$login_id = $_SESSION['login_id'];
$financial_year_id = $_SESSION['financial_year_id'];
$emp_id = $_SESSION['emp_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;
$count = 0;
$rightnow = date('Y-m-d');

$where = "status != 'Disabled' AND assigned_emp_id = '$emp_id'";

// if ($from_date != '' && $to_date != '') {
//     $from_date_db = get_datetime_db($from_date);
//     $to_date_db = get_datetime_db($to_date);
//     $where .= " AND enquiry_id IN (
//         SELECT enquiry_id FROM enquiry_master_entries
//         WHERE followup_date BETWEEN '$from_date_db' AND '$to_date_db'
//         AND followup_status IN ('Active', 'In-Followup')
//     )";
// } else {

//      $yesterday = date('Y-m-d', strtotime('-1 day'));
                    
//                         $tomorrow  = date('Y-m-d 23:59:59', strtotime('+1 day'));

//     $where .= " AND enquiry_id IN (
//         SELECT enquiry_id FROM enquiry_master_entries
//         WHERE followup_date BETWEEN '$yesterday' AND '$tomorrow'
//         AND followup_status IN ('Active', 'In-Followup')
//     )";
// }


if ($from_date != '' && $to_date != '') {
    $from_date_db = get_datetime_db($from_date);
    $to_date_db   = get_datetime_db($to_date);
    $where .= " AND enquiry_id IN (
        SELECT enquiry_id
        FROM enquiry_master_entries e1
        WHERE e1.entry_id = (
            SELECT e2.entry_id
            FROM enquiry_master_entries e2
            WHERE e2.enquiry_id = e1.enquiry_id
              AND e2.followup_status != 'Dropped' AND e2.followup_status IN ('Active', 'In-Followup')
            ORDER BY e2.entry_id DESC
            LIMIT 1
        )
        AND e1.followup_date BETWEEN '$from_date_db' AND '$to_date_db'
    )";
} else {
    // Yesterday to Tomorrow
    $yesterday = date('Y-m-d 00:00:00', strtotime('-1 day'));
    $tomorrow  = date('Y-m-d 23:59:59', strtotime('+1 day'));
    $where .= " AND enquiry_id IN (
        SELECT enquiry_id
        FROM enquiry_master_entries e1
        WHERE e1.entry_id = (
            SELECT e2.entry_id
            FROM enquiry_master_entries e2
            WHERE e2.enquiry_id = e1.enquiry_id
              AND e2.followup_status != 'Dropped' AND e2.followup_status IN ('Active', 'In-Followup')
            ORDER BY e2.entry_id DESC
            LIMIT 1
        )
        AND e1.followup_date BETWEEN '$yesterday' AND '$tomorrow'
    )";
}


// Count total
$count_query = "SELECT COUNT(*) as total FROM enquiry_master WHERE $where";
$count_result = mysqli_fetch_assoc(mysqlQuery($count_query));
$total_records = $count_result['total'];
$total_pages = ceil($total_records / $limit);

// Fetch enquiries
$query = "SELECT * FROM enquiry_master WHERE $where ORDER BY enquiry_id DESC LIMIT $offset, $limit";
$sq_enquiries = mysqlQuery($query);
?>

<div class="dashboard_table dashboard_table_panel main_block">
    <div class="col-md-12 no-pad table_verflow">
        <div class="table-responsive">
            <table class="table table-hover" style="margin: 0 !important;border: 0;">
                <thead>
                    <tr class="table-heading-row">
                        <th>S_No.</th>
                        <th>Enquiry_Id</th>
                        <th>Customer_Name</th>
                        <th>Tour_Type</th>
                        <th>Tour_Name</th>
                        <th>Mobile</th>
                        <th>Followup_D/T</th>
                        <th>Followup_Type</th>
                        <th>History</th>
                        <th>Followup_Update</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while($row = mysqli_fetch_assoc($sq_enquiries)){ 
                    $cust_user_name = '';
                    if($row['user_id'] != 0){ 
                        $row_user = mysqli_fetch_assoc(mysqlQuery("SELECT name FROM customer_users WHERE user_id ='$row[user_id]'"));
                        $cust_user_name = ' ('.$row_user['name'].')';
                    }

                    $assigned_emp_id = $row['assigned_emp_id'];
                    $sq_emp = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM emp_master WHERE emp_id='$assigned_emp_id'"));

                    $enquiry_content = json_decode($row['enquiry_content'], true);
                    $tour_name = 'NA';
                    foreach($enquiry_content as $content){
                        if(isset($content['name']) && $content['name'] == 'tour_name'){
                            $tour_name = $content['value'];
                        }
                    }

                    $enquiry_id = $row['enquiry_id'];
                    $status_entry = mysqli_fetch_assoc(mysqlQuery("SELECT * FROM enquiry_master_entries WHERE enquiry_id='$enquiry_id' AND followup_status != 'Dropped' ORDER BY entry_id DESC LIMIT 1"));

                    if($status_entry){
                        $count++;
                        $followup_date = $status_entry['followup_date'];
                        $followup_type = $status_entry['followup_type'];
                        $followup_status = $status_entry['followup_status'];

                        // Determine row background color
                        $bg = '';
                        $bg = ($followup_status == 'Converted') ? "success" : $bg;
                        $bg = ($followup_status == 'Dropped') ? "danger" : $bg;
                        $bg = ($followup_status == 'Active') ? "warning" : $bg;

                        $status = ($followup_type != '') ? $followup_type : 'Not Done';
                        $back_color = ($followup_type != '') ? 'background: #40dbbc !important' : 'background: #ffc674 !important';

                        ?>
                        <tr class="<?= $bg ?>">
                            <td><?= $count ?></td>
                            <td><?= get_enquiry_id($enquiry_id, $year) ?></td>
                            <td><?= $row['name'].$cust_user_name ?></td>
                            <td><?= $row['enquiry_type'] ?></td>
                            <td><?= $tour_name ?></td>
                            <td><?= $row['mobile_no'] ?></td>
                            <td><?= get_datetime_user($followup_date) ?></td>
                            <td><div style='<?= $back_color ?>' class="table_side_widget_text widget_blue_text table_status"><?= $status ?></div></td>
                            <td><button class="btn btn-info btn-sm" onclick="display_history('<?= $enquiry_id ?>');"><i class="fa fa-history"></i></button></td>
                            <td><button class="btn btn-info btn-sm" onclick="Followup_update('<?= $enquiry_id ?>');" title="Update Followup"><i class="fa fa-reply-all"></i></button></td>
                        </tr>
                <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="row text-center">
    <div class="col-md-12">
        <ul class="pagination justify-content-center">
        <?php
            $adjacents = 2;
            $start = ($page > $adjacents) ? $page - $adjacents : 1;
            $end = ($page < ($total_pages - $adjacents)) ? $page + $adjacents : $total_pages;

            if ($page > 1) {
                echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect(".($page - 1).")'>&laquo; Prev</a></li>";
            } else {
                echo "<li class='page-item disabled'><span class='page-link'>&laquo; Prev</span></li>";
            }

            if ($start > 1) {
                echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect(1)'>1</a></li>";
                if ($start > 2) echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
            }

            for ($i = $start; $i <= $end; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect($i)'>$i</a></li>";
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect($total_pages)'>$total_pages</a></li>";
            }

            if ($page < $total_pages) {
                echo "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect(".($page + 1).")'>Next &raquo;</a></li>";
            } else {
                echo "<li class='page-item disabled'><span class='page-link'>Next &raquo;</span></li>";
            }
        ?>
        </ul>
    </div>
</div>
