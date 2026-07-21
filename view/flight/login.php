<?php
require_once('../../config.php');

// include_once('encrypt_decrypt.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (!empty($email) && !empty($password)) {
        $hashed_password = md5($password); // Hash the password (use bcrypt for stronger security in production)
        $email = $encrypt_decrypt->fnEncrypt($email, $secret_key);

        // Check if the user exists in the database
        $sql = "SELECT * FROM customer_master WHERE email_id = '$email' AND password = '$hashed_password'";
        $result = $connection->query($sql);

        if ($result && $result->num_rows > 0) {
            // Fetch user data from the database
            $user = $result->fetch_assoc();
            
            $_SESSION['user_logged_in'] = true;
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];;
           // Redirect to the page user came from
    if (isset($_SESSION['redirect_after_login'])) {
        $redirectUrl = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);  // Clear the redirect URL after use
        header("Location: $redirectUrl");
        exit();
    } else {
        // If no redirect URL is set, redirect to a default page (e.g., homepage)
        header('Location: ../../index.php');
        exit();
    }
            
            
        } else {
            // Invalid credentials
            $_SESSION['message'] = "Invalid email or password.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        // Missing fields
        $_SESSION['message'] = "All fields are required.";
        $_SESSION['message_type'] = "warning";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Page Title -->
  <title>Client Login</title>

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
              <span class="pageTitle m0">Client Login</span>
            </div>
            <!-- *** Search Head End **** -->
          </div>

          <div class="col-md-5 col-12 c-breadcrumbs">
            <ul>
              <li>
                <a href="#">Home</a>
              </li>
              <li class="st-active">
                <a href="#">Client Login</a>
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
                  Login
                </h2>


                <div class="accordion c-userProfileAccord" id="accordionExample">
				
                 <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?=$_SESSION['message_type']?> alert-dismissible fade show" role="alert">
                <?=$_SESSION['message']?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                <?php endif; ?>
                  <!-- ** Accord 4 ** -->
                  <div class="card">
                    <div  aria-labelledby="headingChangePassword">
                      <div class="card-body">
                       <form id="loginForm" action="login.php" method="post">  
                        <div class="row">
                          <div class="col-md-4 col-sm-6 col-12">
                            <div class="formField">
                              <input type="email" class="txtBox" name="email" placeholder="Email Id" />
                            </div>
                          </div>
                          <div class="col-md-4 col-sm-6 col-12">
                            <div class="formField">
                              <input type="password" class="txtBox" name="password" placeholder="Password" />
                            </div>
                          </div>
                          <div class="col-12 text-center">
                            <button type="submit" class="c-button ">Login</button>
                          </div>
                          <div class="col-12">
                           <p style="font-size: 15px;font-weight: 500;">Don't have an account? <a href="registration.php" style="color:#fdb714">Register</a></p>
                          </div>
                        </div>
                       </form>     
                      </div>
                    </div>
                  </div>
                  <!-- ** Accord 4 End ** -->

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
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/jquery-ui.1.10.4.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/popper.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/bootstrap-4.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/datatables.min.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/theme-scripts.js"></script>
  <script type="text/javascript" src="<?= BASE_URL_B2C ?>js2/scripts.js"></script>
</body>

</html>