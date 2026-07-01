<?php
include "../../model/model.php";
/*======******Header******=======*/
require_once('../layouts/admin_header.php');
require_once('../../classes/tour_booked_seats.php');

$role = $_SESSION['role'];
$role_id = $_SESSION['role_id'];
$login_id = $_SESSION['login_id'];
$reminder_status = (isset($_SESSION['reminder_status'])) ? "true" : "false";
$getClient =  mysqli_fetch_array(mysqlQuery("select client_id from app_settings"))['client_id'];
$modules = json_decode(file_get_contents("modules.json"),true);

if(empty($modules))
{
    $modules = [];
}
?>
<?= begin_panel('Onboarding Status') ?>
  <style>
       
          #onboarding_data {display: flex;overflow-x: scroll;}

        .flow-item {
           
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .flow-item:last-child {
            border-bottom: none;
        }

        .flow-name {
            flex: 1;
            font-size: 14px;
            font-weight: bold;
            color: #333;
                display: ruby-text;
        }

        .status-circle {
           width: 40px;
    height: 40px;
            border-radius: 50%;
            border: 2px solid #333;
          margin: auto;
        }

        .status-done {
            background-color: #28a745;
            border-color: #28a745;
        }

        .status-pending {
            background-color: transparent;
            border-color: #ccc;
        }

        /* Progress bar */
        .progress-bar-container {
            width: 100%;
            height: 5px;
            background-color: #eee;
            border-radius: 5px;
            margin-top: 10px;
        }
      .cont{    color: green;
    font-weight: 800;}
      
    </style>
<div class="container-fluid">
	<div class="row">
								<div class="col-md-12 col-sm-12 mg_bt_10">
								    <div id='onboarding_data'></div>
								    
								</div>
							
							</div>
							</div>

<?= end_panel() ?>
<!-- Onboarding Status end -->
<?php
/*======******Footer******=======*/
require_once('../layouts/admin_footer.php');
?>

<script>
  function get_onboarding(type) {
    $.post('../dashboard/onboarding/onboarding_flow.php', {}, function(data) {
        // Assuming `data` is the JSON response.
        const onboardingData = JSON.parse(data);  // Parse the JSON response
        const container = $('#onboarding_data');
        container.empty(); // Clear any existing content

        // Process each flow and generate HTML dynamically
        onboardingData.forEach(item => {
            const flowItem = $('<div class="flow-item"></div>');

            // Flow name
            const flowName = $('<div class="flow-name"></div>').text(item.flow_name);

            // Status circle
            const statusCircle = $('<div class="cont status-circle"></div>');
            if (item.status === "Done") {
                statusCircle.addClass('status-done');
            }else if (item.status === "NA") {
                statusCircle.removeClass('status-circle');
                 statusCircle.text('This version is not Subscribed');
            } else {
                statusCircle.addClass('status-pending');
            }

           
            // Append all components to the flow item
            flowItem.append(flowName).append(statusCircle);

            // Append to the main container
            container.append(flowItem);
        });
    });
}

	get_onboarding('onboarding');
</script>