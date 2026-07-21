<?php
class quot_master{

	public function actions_qout_otp(){

        $name = $_POST['name'];
        $email_id = $_POST['email_id'];

        $six_digit_random_number = mt_rand(100000, 999999);
        $content = '
            <tr>
            <table width="85%" cellspacing="0" cellpadding="5" style="text-align: left;color: #888888;margin-top:20px; min-width: 100%;" role="presentation">
            <tr>
                <td colspan=2><b>Dear '.$name.',</b></td> 
            </tr>
            <tr></tr>
            <tr>
                <td colspan=2><b>A sign in attempt requires further verification because we did not recognize your device. To complete the download quotation, enter the verification code on the unrecognized device.</b></td> 
            </tr>
            </table>
            <table width="85%" cellspacing="0" cellpadding="5" style="text-align: left;color: #888888;border: 1px solid #888888;margin: 0px auto;margin-top:20px; min-width: 100%;" role="presentation">
            <tr><td style="text-align:left;border: 1px solid #888888;">Verification code</td>   <td style="text-align:left;border: 1px solid #888888;">'.$six_digit_random_number.'</td></tr>
            </table>
            </tr>';
        
        // Send email first (with timeout to prevent long delays)
        $subject = 'Please verify your device';
        global $model;
        
        // Set shorter timeout for email sending
        set_time_limit(15);
        
        // Send email before returning OTP to ensure it's sent
        // But use a shorter timeout so it doesn't block too long
        $email_sent = false;
        if (!empty($email_id)) {
            try {
                // Suppress any output from email function
                ob_start();
                $model->app_email_master($email_id, $content, $subject,'1');
                ob_end_clean();
                $email_sent = true;
            } catch (Exception $e) {
                error_log('OTP Email exception: ' . $e->getMessage());
                ob_end_clean();
            }
        }
        
        // Return OTP immediately after email attempt
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Type: text/plain');
        echo $six_digit_random_number;
        
        // If email wasn't sent, try again in background
        if (!$email_sent && !empty($email_id)) {
            // Allow script to continue even if client disconnects
            ignore_user_abort(true);
            set_time_limit(30);
            
            // Flush output to send OTP to client immediately
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                flush();
            }
            
            // Retry email in background
            try {
                $model->app_email_master($email_id, $content, $subject,'1');
            } catch (Exception $e) {
                error_log('OTP Email background retry failed: ' . $e->getMessage());
            }
        }
	}
    function quotation_save(){

        $currency = $_POST['currency'];
        $package_id= $_POST['package_id'];
        $name = $_POST['name'];
        $email_id = $_POST['email_id'];
        $city_place = $_POST['city_place'];
        $country_code = $_POST['country_code'];
        $phone = $_POST['phone'];
        $travel_from = $_POST['travel_from'];
        $travel_to = $_POST['travel_to'];
        $adults = $_POST['adults'];
        $chwb = $_POST['chwb'];
        $chwob = $_POST['chwob'];
        $extra_bed = $_POST['extra_bed'];
        $infant = $_POST['infant'];
        $package_typef= $_POST['package_typef'];
        $specification = $_POST['specification'];
        $type = $_POST['type'];
        $date = date('Y-m-d H:i');
        
        $contact = $country_code.$phone;
        $travel_from = get_date_db($travel_from);
        $travel_to = get_date_db($travel_to);

        $sq_max = mysqli_fetch_assoc(mysqlQuery("select max(entry_id) as max from b2c_quotations"));
        $entry_id = $sq_max['max']+1;
        
        $sq_query = mysqlQuery("INSERT INTO `b2c_quotations`(`entry_id`,`type`,`package_id`, `name`, `email`,`city`, `phone`, `travel_from_date`, `travel_to_date`, `adults`, `chwob`, `chwb`, `extra_bed`, `infant`, `package_type`, `specification`, `created_at`,`currency`) VALUES ('$entry_id','$type','$package_id','$name','$email_id','$city_place','$contact','$travel_from','$travel_to','$adults','$chwob','$chwb','$extra_bed','$infant','$package_typef','$specification','$date','$currency')");
        if($sq_query){
            echo $entry_id;
        }else{
            echo 'error';
        }
    }
    function delete_quot(){
        
        $quotation_id = $_POST['quotation_id'];
        $sq_query = mysqlQuery("update `b2c_quotations` set status='1' where entry_id='$quotation_id'");
        if($sq_query){
            echo 'Quotation has been deleted successfully!';
        }else{
            echo 'error';
        }
    }
}