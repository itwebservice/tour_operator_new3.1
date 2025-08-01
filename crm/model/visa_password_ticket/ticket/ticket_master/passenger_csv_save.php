<?php 

class passenger_csv_save{

public function passenger_csv_save1()
{
    $cust_csv_dir = $_POST['cust_csv_dir'];
    $selected_portal = $_POST['selected_portal'];    
    $pass_info_arr = array();
    $cust_csv_dir = explode('uploads', $cust_csv_dir);
    $cust_csv_dir = CSV_READ_URL.'uploads'.$cust_csv_dir[1];
    begin_t();
    $count = 1;
    $handle = fopen($cust_csv_dir, "r");
    if($selected_portal == "Tripjack"){
        if(empty($handle) === false) {
            while(($data = fgetcsv($handle,0, ",")) !== FALSE){
                if($count == 1) { $count++; continue; }
                if($count>0){                
                $arr = array(
                    'passenger_name' => $data[5],
                    'gds_pnr' => $data[2],
                    'pn' => $data[2],
                    'total_fair_amount' => $data[4],
                    'departure_or_arrival' => $data[6], 
                    'flight_travel_date' => $data[7],
                    'flight_duration' => $data[8],
                    'main_ticket' => $data[9],
                    'search_type' => $data[12],
                    );
                array_push($pass_info_arr, $arr);
                }
                $count++;
            }
            fclose($handle);
        }
    }elseif($selected_portal == "TBO-Train"){
         if(empty($handle) === false) {
            while(($data = fgetcsv($handle,0, ",")) !== FALSE){
                if($count == 1) { $count++; continue; }
                if($count>0){                
                $arr = array(
                    'flight_type' => $data[0],
                    'flight_date' => $data[1], 
                    'passenger_name' => $data[3],    
                    'flight_carrier' => $data[4],               
                    'ticket_no' => $data[5],
                    'flight_pnr' => $data[6],
                    'flight_sector' => $data[7],
                    'flight_no_with_operator' => $data[8],
                    'fair_amount' => $data[9],
                  
                    );
                array_push($pass_info_arr, $arr);
                }
                $count++;
            }
            fclose($handle);
        }
    }elseif($selected_portal == "Amadeus"){
         if(empty($handle) === false) {
            while(($data = fgetcsv($handle,0, ",")) !== FALSE){
                if($count == 1) { $count++; continue; }
                if($count>0){                
                $arr = array(                    
                    'flight_pnr_no' => $data[0],
                    'passenger_name' => $data[1],
                    'ticket_no' => $data[2],
                    'airline_code' => $data[3],
                    'flight_no_with_operator' => $data[4],
                    'flight_from' => $data[6],
                    'flight_to' => $data[7],
                    'flight_d_date' => $data[8],
                    'flight_d_time' => $data[9],
                    'flight_a_date' => $data[10],
                    'flight_a_time' => $data[11],
                    'flight_status' => $data[12],
                    'fair_amount' => $data[13],
                );
                array_push($pass_info_arr, $arr);
                }
                $count++;
            }
            fclose($handle);
        }
    }elseif($selected_portal == "Galileo"){
         if(empty($handle) === false) {
            while(($data = fgetcsv($handle,0, ",")) !== FALSE){
                if($count == 1) { $count++; continue; }
                if($count>0){                
                $arr = array(   
                    'flight_pnr_no' => $data[1],                
                    'passenger_name' => $data[2],
                    'airline_code' => $data[3],
                    'flight_no_with_operator' => $data[4],    
                    'flight_d_date' => $data[5],
                    'flight_d_time' => $data[6],
                    'flight_a_date' => $data[7],
                    'flight_a_time' => $data[8], 
                    'flight_from' => $data[9],
                    'flight_to' => $data[10],   
                    'flight_class' => $data[11],               
                    'ticket_no' => $data[12], 
                    'fair_amount' => $data[13],    
                    'flight_status' => $data[16],      
                );
                array_push($pass_info_arr, $arr);
                }
                $count++;
            }
            fclose($handle);
        }
    }
echo json_encode($pass_info_arr);
}

}
?>