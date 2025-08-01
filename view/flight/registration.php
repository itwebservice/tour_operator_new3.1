<?php
require_once('../../config.php');
session_start();
$first_name = $emailshow = $middle_name = $last_name = $type= $email = $password = $contact_no = $contact_noshow = $pan_no = $company_name = $gst_no = $state = $address = $address2 = $city = $pincode = $country = $state =  "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize isValid to true
    $isValid = true;

    // Sanitizing and Validating inputs
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $type = trim($_POST['type']);
    $emailshow = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = $encrypt_decrypt->fnEncrypt($email, $secret_key);
    $password = $_POST['password'];
    $contact_no = trim($_POST['contact_no']);
    $contact_noshow = trim($_POST['contact_no']);
    $contact_no = $encrypt_decrypt->fnEncrypt($contact_no, $secret_key);
    $pan_no = trim($_POST['pan_no']);
    $company_name = trim($_POST['company_name']);
    $gst_no = trim($_POST['gst_no']);
    $state = $_POST['state'];
    $address = trim($_POST['address']);
    $address2 = trim($_POST['address2']);
    $city = trim($_POST['city']);
    $pincode = trim($_POST['pincode']);
    $country = $_POST['country'];
echo "kndvvk";
    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($contact_no)) {
        $_SESSION['message'] = "Please Fill Mandatory fields";
        $_SESSION['message_type'] = "warning";
        $isValid = false;
    } else {
        // Additional validation if customer type is 'corporate'
        if ($type == "corporate") {
            // Check if company name and GST number are provided
            if (empty($company_name)) {
                $_SESSION['message'] = "Company name is required for corporate customers.";
                $_SESSION['message_type'] = "warning";
                $isValid = false; // Stop further processing if validation fails
            }
            if (empty($gst_no)) {
                $_SESSION['message'] = "GST number is required for corporate customers.";
                $_SESSION['message_type'] = "warning";
                $isValid = false; // Stop further processing if validation fails
            }
        }

        if ($isValid) {
            // Check if email already exists using mysqli_query
            $sql_check_email = "SELECT * FROM customer_master WHERE email_id = '$email'";
            $result_check_email = mysqli_query($connection, $sql_check_email);

            if (mysqli_num_rows($result_check_email) > 0) {
                $_SESSION['message'] = "Error: This email is already registered.";
                $_SESSION['message_type'] = "danger";
                $isValid = false;
            } else {
                // Hash password securely (using bcrypt)
                $hashed_password = md5($password);
                
                // Check if mobile number already exists using mysqli_query
                $sql_check_mobile = "SELECT * FROM customer_master WHERE contact_no = '$contact_no'";
                $result_check_mobile = mysqli_query($connection, $sql_check_mobile);

                if (mysqli_num_rows($result_check_mobile) > 0) {
                    $_SESSION['message'] = "Error: This mobile number is already registered.";
                    $_SESSION['message_type'] = "danger";
                    $isValid = false;
                } else {
                    // Get last customer ID and increment using mysqli_query
                    $sql_last_id = "SELECT MAX(customer_id) AS last_id FROM customer_master";
                    $result_last_id = mysqli_query($connection, $sql_last_id);
                    $last_id = (mysqli_num_rows($result_last_id) > 0) ? mysqli_fetch_assoc($result_last_id)['last_id'] : 0;
                    $new_customer_id = $last_id + 1;

                    // Insert data into customer_master table using mysqli_query
                   $sql_insert = "INSERT INTO customer_master (customer_id, branch_admin_id, type, first_name, middle_name, last_name, email_id, password, contact_no, pan_no, company_name, gst_no, state_id, address, address2, city, pincode, country) 
                                   VALUES ($new_customer_id, '1', '$type', '$first_name', '$middle_name', '$last_name', '$email', '$hashed_password', '$contact_no', '$pan_no', '$company_name', '$gst_no', '$state', '$address', '$address2', '$city', '$pincode', '$country')";
                    if (mysqli_query($connection, $sql_insert)) {
                        $_SESSION['message'] = "Registration successfully completed. You can login now.";
                        $_SESSION['message_type'] = "success";
                        header("Location: login.php");
                    } else {
                        $_SESSION['message'] = "Error: Could not complete registration. Please try again.";
                        $_SESSION['message_type'] = "danger";
                    }
                }
            }
        }
    }
}


// Query to get the data from the 'state_master' table
$stateResult = mysqli_query($connection, "SELECT * FROM state_master");

// Initialize an array to store states
$states = [];
while ($row = mysqli_fetch_assoc($stateResult)) {
    // Append each state to the array
    $states[] = [
        'id' => $row['id'],
        'state_name' => $row['state_name']
    ];
}

?>

<!DOCTYPE html>
<html>

<head>
  <!-- Page Title -->
  <title>Client Registration</title>

  <!-- Meta Tags -->
  <meta charset="utf-8" />
  <meta name="keywords" content="HTML5 Template" />
  <meta name="description" content="iTours - Travel, Tour Booking HTML5 Template" />
  <meta name="author" content="iTours" />

  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

  <!-- Theme Styles -->
  <link rel="stylesheet" href="<?= BASE_URL_B2C ?>css2/bootstrap-4.min.css" />
  <link rel="stylesheet" href="<?= BASE_URL_B2C ?>css2/animate.min.css" />
  <link rel="stylesheet" href="<?= BASE_URL_B2C ?>css2/datatables.min.css" />
  <link id="main-style" rel="stylesheet" href="<?= BASE_URL_B2C ?>css2/itours-styles.css" />
  <link id="main-style" rel="stylesheet" href="<?= BASE_URL_B2C ?>css2/itours-components.css" />
  
    <style>
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
        .alert{
            font-size: 15px;
            font-weight: 500; 
        }
    </style>
</head>

<body>
  <div class="c-pageWrapper">


    <!-- ********** Component :: Page Title ********** -->
    <div class="c-pageTitleSect">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-12">

            <!-- *** Search Head **** -->
            <div class="searchHeading">
              <span class="pageTitle m0">Client Registration</span>
            </div>
            <!-- *** Search Head End **** -->
          </div>

          <div class="col-md-5 col-12 c-breadcrumbs">
            <ul>
              <li>
                <a href="#">Home</a>
              </li>
              <li class="st-active">
                <a href="#">New Client Registration</a>
              </li>
            </ul>
          </div>

        </div>
      </div>
    </div>
    <!-- ********** Component :: Page Title End ********** -->

    <!-- ********** Component :: User Profile ********** -->
    <div class="c-containerDark">
      <div class="container">
        <div class="row">

          <!-- ** User Profile Details ** -->
          <div class="col-md-12 col-12">

            <div class="c-userBlock st-details  tab-content">

              <!-- Tab 1 -->
              <div class="tabDetails tab-pane fade show active" id="home1" role="tabpanel">
                <h2 class="c-heading">
                  Registration
                </h2>
          
             <?php if (isset($_SESSION['message'])): ?>
<div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
    <?= $_SESSION['message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif; ?>
                <div class="accordion c-userProfileAccord" id="accordionExample">
             <form id="registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" onsubmit="return validateForm()">
    <div class="card">
        <div class="card-header" id="headingPersonalInformaiton">
            <button class="btnShow collapsed" type="button" data-toggle="collapse" data-target="#collapsePersonalInformaiton" aria-expanded="false" aria-controls="collapsePersonalInformaiton">
                <span>Personal Information</span>
            </button>
        </div>
        <div id="collapsePersonalInformaiton" class="collapse show" aria-labelledby="headingPersonalInformaiton">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="type">Customer Type <span class="required">*</span></label>
                           <select class="txtBox" name="type" id="type">
                <option value="">Select Type</option>
                <option value="b2b" <?php if ($type == 'b2b') echo 'selected'; ?>>Walk-in</option>
                <option value="corporate" <?php if ($type == 'corporate') echo 'selected'; ?>>Corporate</option>
            </select>
                            <div class="error-message" id="type-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <input type="text" class="txtBox" name="first_name" id="first_name"  value="<?php echo $first_name; ?>" placeholder="First Name" />
                            <div class="error-message" id="fname-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="middle_name">Middle Name <span class="required"></span></label>
                            <input type="text" class="txtBox" name="middle_name" id="middle_name" value="<?php echo $middle_name; ?>" placeholder="Middle Name" />
                            <div class="error-message" id="mname-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <input type="text" class="txtBox" name="last_name" id="last_name" value="<?php echo $last_name; ?>" placeholder="Last Name" />
                            <div class="error-message" id="lname-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" class="txtBox" name="email" id="email" value="<?php echo $emailshow; ?>" placeholder="Email ID" />
                            <div class="error-message" id="email-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="password">Password <span class="required">*</span></label>
                            <input type="password" class="txtBox" id="password" value="<?php echo $password; ?>" name="password" placeholder="Password" />
                            <div class="error-message" id="password-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="contact_no">Contact Number <span class="required">*</span></label>
                            <input type="number" class="txtBox" id="contact_no" name="contact_no" value="<?php echo $contact_noshow; ?>" placeholder="Contact Number" />
                            <div class="error-message" id="contact-error"></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="formField">
                            <label for="pan_no">Pan No <span class="required"></span></label>
                            <input type="text" class="txtBox" name="pan_no" id="pan_no" value="<?php echo $pan_no; ?>" placeholder="PAN Card No." />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card" id="company_name_group">
        <div class="card-header" id="headingProfileOverview">
            <button class="btnShow" type="button" data-toggle="collapse" data-target="#collapseProfileOverview" aria-expanded="true" aria-controls="collapseProfileOverview">
                <span>COMPANY INFORMATION</span>
            </button>
        </div>
        <div id="collapseProfileOverview" class="collapse show" aria-labelledby="headingProfileOverview">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="formField">
                            <label for="company_name">Company Name <span class="required"></span></label>
                            <input type="text" class="txtBox" name="company_name" id="company_name" value="<?php echo $company_name; ?>" placeholder="Company Name" />
                            <div class="error-message" id="company-name-error"></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-12">
                        <div class="formField">
                            <label for="gst_no">GST Number <span class="required"></span></label>
                            <input type="text" class="txtBox" name="gst_no" id="gst_no" value="<?php echo $gst_no; ?>" placeholder="GST Number" />
                            <div class="error-message" id="gst-no-error"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingAddressInformation">
            <button class="btnShow collapsed " type="button" data-toggle="collapse" data-target="#collapseAddressInformation" aria-expanded="false" aria-controls="collapseAddressInformation">
                <span>Address Information</span>
            </button>
        </div>
        <div id="collapseAddressInformation" class="collapse show" aria-labelledby="headingAddressInformation">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <div class="formField">
                            <label>Address Line 1</label>
                            <input type="text" name="address" value="<?php echo $address; ?>" class="txtBox" />
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="formField">
                            <label>Address Line 2</label>
                            <input type="text" name="address2" value="<?php echo $address2; ?>" class="txtBox" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="formField">
                            <label>City</label>
                            <input type="text" name="city"  value="<?php echo $city; ?>" class="txtBox" />
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="formField">
                            <label>Pincode</label>
                            <input type="text" name="pincode" value="<?php echo $pincode; ?>" class="txtBox" />
                        </div>
                    </div>
                    <input type="hidden" name="country" class="txtBox" value="IN" />
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="formField">
                            <label for="state">State</label>
                           <select class="txtBox" name="state" id="state">
                <option value="">Select State</option>
                <?php foreach ($states as $stateOption): ?>
                    <option value="<?php echo htmlspecialchars($stateOption['id']); ?>"
                        <?php if ($state == $stateOption['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($stateOption['state_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="c-button">Register</button>
</form>
                 <div class="mt-3">
                   <p style="font-size: 15px;font-weight: 500;">Already have an account? <a href="login.php" style="color:#fdb714">Login</a></p>
                 </div>
                </div>


              </div>
              <!-- Tab 1 End -->

              <!-- Tab 2 -->
              <div class="tabDetails tab-pane fade" id="home2" role="tabpanel">
                <h2 class="c-heading">
                  Booking Summery
                </h2>

                <!-- Table -->
                <div class="clearfix c-table st-dataTable">
                  <div class="clearfix">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="formField datepicker-wrap">
                          <label>Search By Date</label>
                          <input type="text" class="txtBox d-inline-block wAuto" placeholder="From Date" />
                          <input type="text" class="txtBox d-inline-block wAuto" placeholder="To Date" />
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="formField">
                          <label>Search By Booking ID</label>
                          <input type="text" class="txtBox customeSearchInput" placeholder="Booking ID" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th style="width: 10%;">Sr. No.</th>
                        <th style="width: 20%;">Booking ID</th>
                        <th style="width: 20%;">Booking Date</th>
                        <th style="width: 20%;">Booking Amount</th>
                        <th style="width: 20%;">Paid Amount</th>
                        <th style="width: 10%;">View</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>BOOK#6718</td>
                        <td>24/05/2020</td>
                        <td>INR 34000</td>
                        <td>INR 34000</td>
                        <td>
                          <button class="tableBtn" data-toggle="modal" data-target="#exampleModal">
                            <i class="icon itours-search"></i>
                          </button>
                        </td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>BOOK#2318</td>
                        <td>24/05/2020</td>
                        <td>INR 34000</td>
                        <td>INR 34000</td>
                        <td>
                          <button class="tableBtn" data-toggle="modal" data-target="#exampleModal">
                            <i class="icon itours-search"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Table End -->

                <!-- Colum totals -->
                <div class="c-tableINfo">
                  <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Amount</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total CANCEL AMOUNT</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Net Amount</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Paid Amount</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Colum totals End -->

              </div>
              <!-- Tab 2 End -->

              <!-- Tab 3 -->
              <div class="tabDetails tab-pane fade" id="home3" role="tabpanel">
                <h2 class="c-heading">
                  Account Ledger
                </h2>

                <!-- Table -->
                <div class="clearfix c-table st-dataTable">
                  <div class="clearfix">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="formField datepicker-wrap">
                          <label>Search By Date</label>
                          <input type="text" class="txtBox" placeholder="From Date" />
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="formField">
                          <label>Search By Keyword</label>
                          <input type="text" class="txtBox customeSearchInput" placeholder="Enter search keyword" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th style="width: 10%">Sr. No.</th>
                        <th style="width: 20%">Date</th>
                        <th style="width: 30%">Perticular</th>
                        <th style="width: 20%">Debit</th>
                        <th style="width: 20%">Credit</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>1</td>
                        <td>24/05/2020</td>
                        <td>A 1D array of options which will be used</td>
                        <td>INR 34000</td>
                        <td>INR 34000</td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>24/05/2020</td>
                        <td>A 1D array of options which will be used</td>
                        <td>INR 34000</td>
                        <td>INR 34000</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <!-- Table End -->

                <!-- Colum totals -->
                <div class="c-tableINfo">
                  <div class="row">
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Debit</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Credit</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-12">
                      <div class="infoCard">
                        <span class="lbl">Total Balance</span>
                        <span class="info">INR 34,00,000</span>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Colum totals End -->
              </div>
              <!-- Tab 3 End -->

              <!-- Tab 4 -->
              <div class="tabDetails tab-pane fade" id="home4" role="tabpanel">
                <h2 class="c-heading">
                  Prof Information
                </h2>
              </div>
              <!-- Tab 4 End -->

            </div>


          </div>
          <!-- ** User Profile Details End ** -->


        </div>
      </div>
    </div>
    <!-- ********** Component :: User Profile End ********** -->


    <div class="modal fade c-modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Booking ID: #BOOK987</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">Booking ID</span>
                  <span class="info">#book9876</span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">BOOKING DATE</span>
                  <span class="info">10/04/2020</span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">TOTAL AMOUNT</span>
                  <span class="info">INR 45,000</span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">CANCEL AMOUNT</span>
                  <span class="info">INR 45,000</span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">NET AMOUNT</span>
                  <span class="info">INR 45,000</span>
                </div>
              </div>
              <div class="col-md-6 col-12">
                <div class="readOnly_info">
                  <span class="lbl">PAID AMOUNT</span>
                  <span class="info">INR 45,000</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Javascript -->
  <!-- Javascript -->
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/jquery-3.4.1.min.js"></script>
  <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/jquery-ui.1.10.4.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/popper.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/bootstrap-4.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/datatables.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/theme-scripts.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/scripts.js"></script>
  <!-- Client-Side Validation (JavaScript) -->
<script>
function validateForm() {
    // Reset previous error messages
    let isValid = true;
    document.querySelectorAll('.error-message').forEach(function(errorDiv) {
        errorDiv.textContent = '';
    });

    // Get values of the input fields
    var fname = document.getElementById("first_name").value;
    var lname = document.getElementById("last_name").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var contact_no = document.getElementById("contact_no").value;
    var type = document.getElementById("type").value;

    // Check if first name is empty
    if (fname == "") {
        document.getElementById("fname-error").textContent = "First name is required.";
        isValid = false;
    }

    // Check if last name is empty
    if (lname == "") {
        document.getElementById("lname-error").textContent = "Last name is required.";
        isValid = false;
    }

    // Validate email
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (email == "") {
        document.getElementById("email-error").textContent = "Email is required.";
        isValid = false;
    } else if (!emailPattern.test(email)) {
        document.getElementById("email-error").textContent = "Please enter a valid email address.";
        isValid = false;
    }

    // Validate password
    if (password == "") {
        document.getElementById("password-error").textContent = "Password is required.";
        isValid = false;
    }

    // Validate contact number
    if (contact_no == "") {
        document.getElementById("contact-error").textContent = "Contact number is required.";
        isValid = false;
    }

    // Validate customer type
    if (type == "") {
        document.getElementById("type-error").textContent = "Please select a customer type.";
        isValid = false;
    }
    
    // Additional validation for "corporate" type (check company name and GST number)
    if (type == "corporate") {
        var company_name = document.getElementById("company_name").value;
        var gst_no = document.getElementById("gst_no").value;

        // Check if company name is empty
        if (company_name == "") {
            document.getElementById("company-name-error").textContent = "Company name is required for corporate customers.";
            isValid = false;
        }

        // Check if GST number is empty
        if (gst_no == "") {
            document.getElementById("gst-no-error").textContent = "GST number is required for corporate customers.";
            isValid = false;
        }
    }

    return isValid;  // If any validation fails, return false to prevent form submission
}
</script>
</body>

</html>