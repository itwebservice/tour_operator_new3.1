<?php
if ($sq_req['quotation_for'] == "DMC") : 
    $dmc_entries = $sq_req['dmc_entries'];
    $dmc_entries_arr = json_decode($dmc_entries, true);

    // Check if data exists
    if (!empty($dmc_entries_arr) && count($dmc_entries_arr) > 0) : 
?>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="profile_box main_block">
                <h3 class="editor_title">Hotel Details</h3>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered no-marg">
                                <thead>
                                    <tr class="table-heading-row">
                                        <th>S_No.</th>
                                        <th>Hotel_Category</th>
                                        <th>Check_In</th>
                                        <th>Check_Out</th>
                                        <th>Total_Rooms</th>
                                        <th>Room_Category</th>
                                        <th>Meal_Plan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($dmc_entries_arr as $hotel) { 
                                        $arr = [];
                                        foreach ($hotel as $entry) {
                                            $arr[$entry['name']] = $entry['value'];
                                        }

                                        // Ensure hotel_id exists before displaying
                                        if (!empty($arr['hotel_id'])) {
                                            ?>
                                            <tr>
                                                <td><?= $count ?></td>
                                                <td><?= $arr['hotel_id'] ?></td>
                                                <td><?= get_date_user($arr['check_in_date']); ?></td>
                                                <td><?= get_date_user($arr['check_out_date']); ?></td>
                                                <td><?= $arr['total_rooms'] ?></td>
                                                <td><?= $arr['room_type'] ?></td>
                                                <td><?= !empty($arr['meal_plan']) ? $arr['meal_plan'] : 'N/A'; ?></td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
    endif; // End of check for empty $dmc_entries_arr
endif; // End of check for "DMC" 
?>
