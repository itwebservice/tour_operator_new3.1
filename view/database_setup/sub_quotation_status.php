<?php
include "../../../model/model.php";
include "../../../model/database_setup/sub_quotation_setup.php";

$sub_quotation_setup = new sub_quotation_setup();
$status = $sub_quotation_setup->get_system_status();
$stats = $sub_quotation_setup->get_setup_stats();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sub-Quotation System Status</title>
    <link href="../../../css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-card {
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-success { background-color: #d4edda; border-left: 4px solid #28a745; }
        .status-warning { background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .status-danger { background-color: #f8d7da; border-left: 4px solid #dc3545; }
        .status-info { background-color: #d1ecf1; border-left: 4px solid #17a2b8; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Sub-Quotation System Status</h1>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-<?php echo $_GET['type'] ?? 'info'; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <!-- System Status -->
        <div class="status-card <?php echo $status['ready'] ? 'status-success' : 'status-warning'; ?>">
            <h3>System Status</h3>
            <p><strong>Overall Status:</strong> 
                <?php echo $status['ready'] ? '<span class="text-success">Ready</span>' : '<span class="text-warning">Not Ready</span>'; ?>
            </p>
            
            <h5>Sub-Quotation System:</h5>
            <p><strong>Fields Exist:</strong> 
                <?php echo $status['sub_quotation_fields_exist'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>'; ?>
            </p>
            <p><strong>Indexes Exist:</strong> 
                <?php echo $status['sub_quotation_indexes_exist'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>'; ?>
            </p>
            
            <h5>Itinerary Image System:</h5>
            <p><strong>Fields Exist:</strong> 
                <?php echo $status['itinerary_image_fields_exist'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>'; ?>
            </p>
            <p><strong>Indexes Exist:</strong> 
                <?php echo $status['itinerary_image_indexes_exist'] ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>'; ?>
            </p>
            
            <?php if (!empty($status['errors'])): ?>
                <div class="mt-3">
                    <h5>Errors:</h5>
                    <ul>
                        <?php foreach ($status['errors'] as $error): ?>
                            <li class="text-danger"><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics -->
        <div class="status-card status-info">
            <h3>System Statistics</h3>
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <h4><?php echo $stats['total_quotations'] ?? 0; ?></h4>
                        <p>Total Quotations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4><?php echo $stats['main_quotations'] ?? 0; ?></h4>
                        <p>Main Quotations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4><?php echo $stats['sub_quotations'] ?? 0; ?></h4>
                        <p>Sub-Quotations</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <h4><?php echo $stats['setup_date'] ?? 'N/A'; ?></h4>
                        <p>Setup Date</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Setup -->
        <div class="status-card status-info">
            <h3>Manual Actions</h3>
            <p>If the system is not ready, you can manually trigger the setup:</p>
            <form method="POST" action="sub_quotation_setup.php">
                <button type="submit" name="manual_setup" class="btn btn-primary">
                    Run Manual Setup
                </button>
            </form>
        </div>

        <!-- Database Structure Check -->
        <div class="status-card status-info">
            <h3>Database Structure</h3>
            <p>Check if the required fields exist in the database:</p>
            
            <h5>Sub-Quotation Fields (package_tour_quotation_master):</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Status</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sub_quotation_fields = ['is_sub_quotation', 'parent_quotation_id', 'quotation_id_display'];
                    foreach ($sub_quotation_fields as $field) {
                        $result = mysqlQuery("SHOW COLUMNS FROM package_tour_quotation_master LIKE '$field'");
                        $exists = mysqli_num_rows($result) > 0;
                        $field_info = $exists ? mysqli_fetch_assoc($result) : null;
                        ?>
                        <tr>
                            <td><?php echo $field; ?></td>
                            <td>
                                <?php if ($exists): ?>
                                    <span class="text-success">✓ Exists</span>
                                <?php else: ?>
                                    <span class="text-danger">✗ Missing</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $field_info ? $field_info['Type'] : 'N/A'; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            
            <h5>Itinerary Image Fields:</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Field Name</th>
                        <th>Status</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $itinerary_image_fields = [
                        'itinerary_master' => 'itinerary_image',
                        'custom_package_program' => 'day_image',
                        'package_quotation_program' => 'day_image'
                    ];
                    foreach ($itinerary_image_fields as $table => $field) {
                        $result = mysqlQuery("SHOW COLUMNS FROM $table LIKE '$field'");
                        $exists = mysqli_num_rows($result) > 0;
                        $field_info = $exists ? mysqli_fetch_assoc($result) : null;
                        ?>
                        <tr>
                            <td><?php echo $table; ?></td>
                            <td><?php echo $field; ?></td>
                            <td>
                                <?php if ($exists): ?>
                                    <span class="text-success">✓ Exists</span>
                                <?php else: ?>
                                    <span class="text-danger">✗ Missing</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $field_info ? $field_info['Type'] : 'N/A'; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Index Check -->
        <div class="status-card status-info">
            <h3>Database Indexes</h3>
            <p>Check if the required indexes exist:</p>
            
            <h5>Sub-Quotation Indexes (package_tour_quotation_master):</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Index Name</th>
                        <th>Status</th>
                        <th>Columns</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sub_quotation_indexes = ['idx_is_sub_quotation', 'idx_parent_quotation_id', 'idx_quotation_sub_quotation'];
                    foreach ($sub_quotation_indexes as $index) {
                        $result = mysqlQuery("SHOW INDEX FROM package_tour_quotation_master WHERE Key_name = '$index'");
                        $exists = mysqli_num_rows($result) > 0;
                        $index_info = $exists ? mysqli_fetch_assoc($result) : null;
                        ?>
                        <tr>
                            <td><?php echo $index; ?></td>
                            <td>
                                <?php if ($exists): ?>
                                    <span class="text-success">✓ Exists</span>
                                <?php else: ?>
                                    <span class="text-danger">✗ Missing</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $index_info ? $index_info['Column_name'] : 'N/A'; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            
            <h5>Itinerary Image Indexes:</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Table</th>
                        <th>Index Name</th>
                        <th>Status</th>
                        <th>Columns</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $itinerary_image_indexes = [
                        'itinerary_master' => 'idx_itinerary_image',
                        'custom_package_program' => 'idx_day_image',
                        'package_quotation_program' => 'idx_day_image'
                    ];
                    foreach ($itinerary_image_indexes as $table => $index) {
                        $result = mysqlQuery("SHOW INDEX FROM $table WHERE Key_name = '$index'");
                        $exists = mysqli_num_rows($result) > 0;
                        $index_info = $exists ? mysqli_fetch_assoc($result) : null;
                        ?>
                        <tr>
                            <td><?php echo $table; ?></td>
                            <td><?php echo $index; ?></td>
                            <td>
                                <?php if ($exists): ?>
                                    <span class="text-success">✓ Exists</span>
                                <?php else: ?>
                                    <span class="text-danger">✗ Missing</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $index_info ? $index_info['Column_name'] : 'N/A'; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="../dashboard/dashboard_main.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
