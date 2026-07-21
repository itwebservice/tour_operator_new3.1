<?php
include_once("../../../model/model.php");
$financial_year_id = $_SESSION['financial_year_id'];

$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
?>

<div class="dashboard_table dashboard_table_panel main_block">
    <div class="col-md-12 no-pad table_verflow">
        <div class="table-responsive">
            <table class="table table-hover" style="margin: 0 !important;border: 0;">
                <thead>
                    <tr class="table-heading-row">
                        <th>S_No.</th>
                        <th>Enquiry_No</th>
                        <th>Customer_Name</th>
                        <th>Tour_Type</th>
                        <th>Tour_Name</th>
                        <th>Mobile</th>
                        <th>Followup_D/T</th>
                        <th>Allocate_To</th>
                        <th>Followup_Type</th>
                        <th>History</th>
                        <th>Followup</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $count = $offset;
                $rightnow = date('Y-m-d');

                // 1. Fetch relevant enquiries
                $query = "SELECT * FROM enquiry_master WHERE status != 'Disabled'";
               
                $query .= " ORDER BY enquiry_id DESC";
                $all_enquiries = mysqlQuery($query);

                $enquiry_data = [];
                $enquiry_ids = [];

                while ($row = mysqli_fetch_assoc($all_enquiries)) {
                    $enquiry_data[$row['enquiry_id']] = $row;
                    $enquiry_ids[] = $row['enquiry_id'];
                }

                // 2. Fetch latest entries for each enquiry
                // $latest_entries = [];
                // if (!empty($enquiry_ids)) {
                //     $enq_id_str = implode(",", $enquiry_ids);
                //     $filter = "AND followup_status IN ('Active', 'In-Followup')";
                //     if ($from_date != '') {
                //         $from_date_db = get_datetime_db($from_date);
                //         $to_date_db = get_datetime_db($to_date);
                //         $filter .= " AND followup_date BETWEEN '$from_date_db' AND '$to_date_db'";
                //     } else{

                //         // bydefault last ,today and next day entries showing
                //           $yesterday = date('Y-m-d', strtotime('-1 day'));
                //         //   $tomorrow  = date('Y-m-d', strtotime('+1 day'));

                //         $tomorrow  = date('Y-m-d 23:59:59', strtotime('+1 day'));

                //           $filter .= " AND followup_date BETWEEN '$yesterday' AND '$tomorrow'";

                //     }
                  
                //     $entry_query = "
                //         SELECT t1.*
                //         FROM enquiry_master_entries t1
                //         INNER JOIN (
                //             SELECT enquiry_id, MAX(entry_id) AS max_id 
                //             FROM enquiry_master_entries 
                //             WHERE enquiry_id IN ($enq_id_str) 
                //             $filter 
                //             GROUP BY enquiry_id
                //         ) t2 ON t1.enquiry_id = t2.enquiry_id AND t1.entry_id = t2.max_id
                //     ";
                //     $result = mysqlQuery($entry_query);
                //     while ($row = mysqli_fetch_assoc($result)) {
                //         $latest_entries[$row['enquiry_id']] = $row;
                //     }
                // }
// new changes


// 2. Fetch latest follow-up entry for each enquiry (respecting date filters)
$latest_entries = [];
if (!empty($enquiry_ids)) {
    $enq_id_str = implode(",", $enquiry_ids);

    // Date filter range
    if ($from_date != '' && $to_date != '') {
        $from_date_db = get_datetime_db($from_date);
        $to_date_db   = get_datetime_db($to_date);
    } else {
        // Default yesterday → tomorrow
        $from_date_db = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $to_date_db   = date('Y-m-d 23:59:59', strtotime('+1 day'));
    }

    // This ensures: only the latest follow-up per enquiry is checked against date range
    $entry_query = "
        SELECT t1.*
        FROM enquiry_master_entries t1
        INNER JOIN (
            SELECT enquiry_id, MAX(entry_id) AS max_id
            FROM enquiry_master_entries
            WHERE enquiry_id IN ($enq_id_str)
              AND followup_status != 'Dropped' AND followup_status IN ('Active', 'In-Followup')
            GROUP BY enquiry_id
        ) t2 
          ON t1.enquiry_id = t2.enquiry_id 
         AND t1.entry_id = t2.max_id
        WHERE t1.followup_date BETWEEN '$from_date_db' AND '$to_date_db'
    ";

    $result = mysqlQuery($entry_query);
    while ($row = mysqli_fetch_assoc($result)) {
        $latest_entries[$row['enquiry_id']] = $row;
    }
}

                // 3. Filter only valid enquiries
                $valid_ids = array_keys($latest_entries);
                $total_records = count($valid_ids);
                $paged_ids = array_slice($valid_ids, $offset, $limit);

                // 4. Fetch employee and user names in bulk
                $emp_ids = array_column(array_intersect_key($enquiry_data, array_flip($paged_ids)), 'assigned_emp_id');
                $user_ids = array_column(array_intersect_key($enquiry_data, array_flip($paged_ids)), 'user_id');

                $emp_names = [];
                if (!empty($emp_ids)) {
                    $emp_str = implode(",", array_unique($emp_ids));
                    $emp_res = mysqlQuery("SELECT emp_id, first_name, last_name FROM emp_master WHERE emp_id IN ($emp_str)");
                    while ($row = mysqli_fetch_assoc($emp_res)) {
                        $emp_names[$row['emp_id']] = $row['first_name'] . ' ' . $row['last_name'];
                    }
                }

                $user_names = [];
                if (!empty($user_ids)) {
                    $user_str = implode(",", array_filter(array_unique($user_ids)));
                    if ($user_str != '') {
                        $user_res = mysqlQuery("SELECT user_id, name FROM customer_users WHERE user_id IN ($user_str)");
                        while ($row = mysqli_fetch_assoc($user_res)) {
                            $user_names[$row['user_id']] = $row['name'];
                        }
                    }
                }

                // 5. Render rows
                foreach ($paged_ids as $enquiry_id) {
                    
                    $row = $enquiry_data[$enquiry_id];
                    $followup = $latest_entries[$enquiry_id];

                    $cust_user_name = isset($user_names[$row['user_id']]) ? ' (' . $user_names[$row['user_id']] . ')' : '';
                    $date = $row['enquiry_date'];
                    $yr = explode("-", $date);
                    $year = $yr[0];
                    $tour_name = 'NA';

                    $enquiry_content = json_decode($row['enquiry_content'], true);
                    foreach ($enquiry_content as $entry) {
                        if ($entry['name'] == "tour_name") {
                            $tour_name = $entry['value'];
                        }
                    }

                    $followup_date1 = $followup['followup_date'];
                    $status = ($followup['followup_type'] != '') ? $followup['followup_type'] : 'Not Done';
                    $back_color = ($status != 'Not Done') ? 'background: #40dbbc !important' : 'background: #ffc674 !important';

                    $bg = ($followup['followup_status'] == 'Converted') ? "success" :
                          (($followup['followup_status'] == 'Dropped') ? "danger" :
                          (($followup['followup_status'] == 'Active') ? "warning" : ""));

                    $count++;
                    ?>
                    <tr class="<?= $bg ?>">
                        <td><?= $count ?></td>
                        <td><?= get_enquiry_id($row['enquiry_id'], $year) ?></td>
                        <td><?= $row['name'] . $cust_user_name ?></td>
                        <td><?= $row['enquiry_type'] ?></td>
                        <td><?= ($row['enquiry_type'] == 'Package Booking' || $row['enquiry_type'] == 'Group Booking') ? $tour_name : 'NA' ?></td>
                        <td><?= $row['mobile_no'] ?></td>
                        <td><?= get_datetime_user($followup_date1) ?></td>
                        <td><?= ($emp_names[$row['assigned_emp_id']] ?? 'Admin') ?></td>
                        <td><div style="<?= $back_color ?>" class="table_side_widget_text widget_blue_text table_status"><?= $status ?></div></td>
                        <td><button class="btn btn-info btn-sm" onclick="display_history('<?= $row['enquiry_id'] ?>');"><i class="fa fa-history"></i></button></td>
                        <td><button class="btn btn-info btn-sm" onclick="Followup_update('<?= $row['enquiry_id'] ?>');"><i class="fa fa-reply-all"></i></button></td>
                    </tr>
                <?php } ?>
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
            $total_pages = ceil($total_records / $limit);
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
                if ($start > 2) {
                    echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
            }

            for ($i = $start; $i <= $end; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='javascript:void(0)' onclick='followup_reflect($i)'>$i</a></li>";
            }

            if ($end < $total_pages) {
                if ($end < $total_pages - 1) {
                    echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                }
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
