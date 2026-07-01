<?php

include "constants.php";

$userId = $_SESSION['login_id'];

// // Step 1: Get all user pages & forms
$sql = "SELECT fbp.page_id, r.fb_access_token, fbp.form_id 
        FROM facebook_pages fbp 
        JOIN roles r ON r.id = fbp.user_id 
        WHERE fbp.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "❌ No connected Facebook page or form found.";
    exit;
}

$successCount = 0;

while ($row = $result->fetch_assoc()) {
    $pageId = $row['page_id'];
    $accessToken = $row['fb_access_token'];
    $formId = $row['form_id'];

    if (!$pageId || !$accessToken || !$formId) {
        continue;
    }

    // Step 2: Get leads from this form
    $leadsUrl = "https://graph.facebook.com/" . FB_GRAPH_VERSION . "/$formId/leads?access_token=$accessToken";
    $response = @file_get_contents($leadsUrl);
    $leads = json_decode($response, true);

    if (!empty($leads['data'])) {
        foreach ($leads['data'] as $lead) {
            $lead_id = $lead['id'];
            $created_time = $lead['created_time'];
            $fieldData = json_encode($lead['field_data']); // store for DB
            $fieldDataArray = $lead['field_data']; // for processing



            $check = $conn->prepare("SELECT * FROM enquiry_master WHERE lead_id = ?");
            $check->bind_param("s", $lead_id);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows === 0) {
                // Insert new lead
                // -----------------------------------
                $login_id = $_SESSION['login_id'];
                $branch_admin_id = $_SESSION['branch_admin_id'];
                $financial_year_id = $_SESSION['financial_year_id'];
                $enquiry_type = 'Package Booking';
                $enquiry = 'Strong';
                $name = '';
                $mobile_no = '';
                $landline_no = '';
                $country_code = '+91';
                $email_id = '';
                $location = ' ';
                $assigned_emp_id = 2;
                $enquiry_specification = '';
                $enquiry_date = date('Y-m-d');
                $followup_date = date('Y-m-d H:i');
                $reference_id = '13';
                $enquiry_content = '';
                $customer_name = '';
                $user_id = '0';
                $lead_id = $lead_id;
                $pageId = $pageId;
                $formId = $formId;
                // --------------------------------------------
                $enquiry_content_array = [];

                foreach ($fieldDataArray as $field) {
                    if (!is_array($field) || !isset($field['name'])) {
                        continue; // skip if invalid structure
                    }

                    $key = strtoupper($field['name']);
                    $value = isset($field['values'][0]) ? $field['values'][0] : '';

                    switch ($key) {
                        case 'FULL_NAME':
                            $name = $value;
                            $customer_name = $value;
                            break;
                        case 'EMAIL':
                            $email_id = $value;
                            break;
                        case 'PHONE':
                            $mobile_no = $value;
                            $landline_no = $value;
                            break;
                        case '0':
                            $enquiry_content_array[] = ['name' => 'travel_from_date', 'value' => $value];
                            break;
                        case '1':
                            $enquiry_content_array[] = ['name' => 'tour_name', 'value' => $value];
                            break;
                        case '2':
                            $enquiry_content_array[] = ['name' => 'total_adult', 'value' => $value];
                            break;
                        default:
                            $enquiry_content_array[] = ['name' => $key, 'value' => $value];
                            break;
                    }
                }
                // Add any default fields to enquiry content (optional)
                $enquiry_content_array[] = ['name' => 'children_with_bed', 'value' => '0'];
                $enquiry_content_array[] = ['name' => 'children_without_bed', 'value' => '0'];
                $enquiry_content_array[] = ['name' => 'total_infant', 'value' => '0'];
                $enquiry_content_array[] = ['name' => 'total_single_person', 'value' => '0'];
                $enquiry_content_array[] = ['name' => 'total_members', 'value' => '0'];
                $enquiry_content_array[] = ['name' => 'hotel_type', 'value' => 'Standard'];

                $enquiry_content = json_encode($enquiry_content_array);
                // --------------------------------------------
                $sq_max_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(enquiry_id) AS max FROM enquiry_master"));

                $enquiry_id = $sq_max_id['max'] + 1;

                $sq_enquiry = mysqli_query($conn, "
                INSERT INTO enquiry_master (
                    enquiry_id, login_id, branch_admin_id, financial_year_id, enquiry_type, enquiry, name, mobile_no, 
                    landline_no, country_code, email_id, location, assigned_emp_id, enquiry_specification, 
                    enquiry_date, followup_date, reference_id, enquiry_content, customer_name, user_id, 
                    lead_id, page_id, form_id
                ) VALUES (
                    '$enquiry_id', '$login_id', '$branch_admin_id', '$financial_year_id', '$enquiry_type', '$enquiry', 
                    '$name', '$mobile_no', '$landline_no', '$country_code', '$email_id', '$location', '$assigned_emp_id', 
                    '$enquiry_specification', '$enquiry_date', '$followup_date', '$reference_id', '$enquiry_content', 
                    '$customer_name', '$user_id', '$lead_id', '$pageId', '$formId'
                )
            ");
                // For notification count
                $result_emp = mysqli_query($conn, "SELECT notification_count FROM emp_master WHERE emp_id='$assigned_emp_id'");

                if ($result_emp && mysqli_num_rows($result_emp) > 0) {
                    $row_emp = mysqli_fetch_assoc($result_emp);
                    $notification_count = (int)$row_emp['notification_count'] + 1;

                    mysqli_query($conn, "UPDATE emp_master SET notification_count='$notification_count' WHERE emp_id='$assigned_emp_id'");
                } else {
                    // If employee doesn't exist, insert with count = 1
                    $notification_count = 1;

                    mysqli_query($conn, "INSERT INTO emp_master (emp_id, notification_count) VALUES ('$assigned_emp_id', '$notification_count')");
                }


                // --------------------------

                // Get max entry_id
                $sq_max = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(entry_id) AS max FROM enquiry_master_entries"));
                $entry_id = $sq_max['max'] + 1;

                // Insert into enquiry_master_entries
                $sq_followup = mysqli_query($conn, "INSERT INTO enquiry_master_entries (entry_id, enquiry_id, followup_reply, followup_status, followup_type, 
followup_date, followup_stage, created_at ) VALUES ('$entry_id', '$enquiry_id', '', 'Active', '', '$followup_date', '$enquiry', '$enquiry_date') ");

                // Update enquiry_master with new entry_id
                $sq_entryid = mysqli_query($conn, "UPDATE enquiry_master 
    SET entry_id = '$entry_id' WHERE enquiry_id = '$enquiry_id'");

                // --------------------------
                $successCount++;
            }
        }
    }
}

$redirectUrl = $_POST['redirect_back'] ?? 'dashboard.php'; // fallback page

if ($successCount > 0) {
    $_SESSION['flash_msg'] = "✅ $successCount new leads fetched and saved!";
} else {
    $_SESSION['flash_msg'] = "No New Lead found to process. Please try again later.";
}

header("Location: $redirectUrl");
exit;
