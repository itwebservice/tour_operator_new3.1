<?php
include '../../../model/model.php';


$query = mysqli_fetch_assoc(mysqlQuery("SELECT services FROM `b2c_settings` where setting_id='1'"));
// $services = ($query['services'] != '' && $query['services'] != 'null') ? json_decode($query['services'],true) : [];
// var_dump($query);

$services_json = trim($query['services']);
$services_json = stripslashes($services_json); // Remove escaped slashes
$services_json = preg_replace('/[\x00-\x1F\x7F]/', '', $services_json); // Remove control characters

$services = (!empty($services_json) && $services_json !== 'null') ? json_decode($services_json, true) : [];

if (json_last_error() !== JSON_ERROR_NONE) {
    // echo "JSON Decode Error: " . json_last_error_msg();
    // die(); // Stop execution for debugging
}
// var_dump($services);

?>
<form id="section_hotels">
    <legend>Define Services</legend>
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-excel btn-sm" title="Note : For saving services keep checkbox selected!"><i class="fa fa-question-circle"></i></button>
            <button type="button" class="btn btn-excel btn-sm" onclick="addRow('tbl_services')" title="Add Row"><i
                    class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-pdf btn-sm" onclick="deleteRow1('tbl_services');" title="Delete Row"><i
                    class="fa fa-trash"></i></button>
        </div>
    </div>

    <div class="row mg_bt_20">
        <div class="col-md-12">
               


                <table id="tbl_services" name="tbl_services" class="table border_0 table-hover no-marg">
                    <?php
                    if (sizeof($services) == 0) { ?>
                    <tr>
                        <td><input id="chk_city1" type="checkbox" checked></td>
                        <td><input maxlength="15" value="1" type="text" name="no" placeholder="Sr. No."
                                class="form-control" disabled /></td>
                        <td><textarea name="service_name-1" id="service_name-1" title="Service Name" class="form-control city_name"  onchange="validate_services_1(this.id);" rows="5"
                                 style="width:100%; height:70px;">
                    </textarea></td>
                        <td><textarea id='description-1' name='description-1' class="form-control" title="Description"  onchange="validate_services(this.id);" rows="5" style="width:100%; height:70px;" >
                               
                    </textarea></td>
                    </tr>
                    <?php
                    }
                       else {
                            foreach ($services as $i => $service) {
                                $service_name = htmlspecialchars($service['service_name'] ?? '');
                                $description = htmlspecialchars($service['description'] ?? '');
                        ?>
                                <tr>
                                    <td><input id="chk_city1<?= $i ?>_u" type="checkbox" checked></td>
                                    <td><input maxlength="15" value="<?= ($i + 1) ?>" type="text" name="no" placeholder="Sr. No."
                                            class="form-control" disabled /></td>
                                    <td><textarea name="service_name-1<?= $i ?>_u" id="service_name-1<?= $i ?>_u" class="city_name"
                                            style="width:100%; height:70px;" title="Service Name"  onchange="validate_services_1(this.id);" rows="5"><?= $service_name ?></textarea>
                                    </td>
                                    <td><textarea id='description-1<?= $i ?>_u' name='description-1<?= $i ?>_u' class="form-control"
                                            title="Description" onchange="validate_services(this.id);" rows="5" style="width:100%; height:70px;"  ><?= $description ?></textarea></td>
                                </tr>
                        <?php }
                        } ?>
                </table>


            <script>
            //city_lzloading('.city_name');
            </script>
        </div>
    </div>
    <div class="row mg_tp_20">
        <div class="col-xs-12 text-center">
            <button class="btn btn-sm btn-success" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
        </div>
    </div>
</form>
<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>


function deleteRow1(tableID) {
    try {
        var table = document.getElementById(tableID);
        var rowCount = table.rows.length;

        for (var i = rowCount - 1; i >= 0; i--) { // Loop in reverse to prevent skipping rows
            var row = table.rows[i];
            var chkbox = row.cells[0].childNodes[0];

            if (chkbox && chkbox.checked) {
                if (rowCount <= 1) {
                    error_msg_alert("Cannot delete all the rows.");
                    return; // Stop function execution
                }
                table.deleteRow(i);
                rowCount--;
            }
        }

        // ** Reset Row Numbers ** //
        resetRowNumbers(tableID);
    } catch (e) {
        alert(e);
    }

    
}

// ** Function to Reset Row Numbers ** //
function resetRowNumbers(tableID) {
    var table = document.getElementById(tableID);
    for (var i = 0; i < table.rows.length; i++) {
        table.rows[i].cells[1].innerText = i + 1; // Assuming the row number is in the 2nd column (index 1)
    }
}





function addRow(tableID) {
    var table = document.getElementById(tableID);
    var rowCount = table.rows.length;

    // Check if the row count exceeds 6
    if (rowCount >= 6) {
        error_msg_alert("You cannot add more than 6 rows.");
        return false;
    }

    var row = table.insertRow(rowCount);
    var foo = { counter: rowCount + 1 }; // Adjust counter for new row

    row.insertCell(0).innerHTML = '<input type="checkbox" id="chk_city1' + foo.counter + '" checked>';
    row.insertCell(1).innerHTML = '<input maxlength="15" value="' + foo.counter + '" type="text" class="form-control" disabled>';
    row.insertCell(2).innerHTML = '<textarea name="service_name-' + foo.counter + '" id="service_name-' + foo.counter + '" class="form-control" onchange="validate_services_1(this.id);" rows="5" style="width:100%; height:70px;"></textarea>';
    row.insertCell(3).innerHTML = '<textarea id="description-' + foo.counter + '" name="description-' + foo.counter + '" class="form-control" onchange="validate_services(this.id);" rows="5" style="width:100%; height:70px;"></textarea>';


    $("input[type='checkbox']").labelauty({
        label: false,
        maximum_width: "20px"
    });

}




// $(document).on('change', '[id^=service_name-]', function() {
//    validate_services_1(this.id);
// });

// $(document).on('change', '[id^=description-]', function() {
//     validate_services(this.id);
// });


// $(function() {
//     $('#section_hotels').validate({
//         rules: {},
//         submitHandler: function(form) {

//             var base_url = $('#base_url').val();

          

//             var images_array = new Array();
//             var table = document.getElementById("tbl_services");
//             var rowCount = table.rows.length;
//             for (var i = 0; i < rowCount; i++) {
//                 var row = table.rows[i];
//                 var service_name = row.cells[2].childNodes[0].value;
//                 var description = row.cells[3].childNodes[0].value;

//                 if (row.cells[0].childNodes[0].checked) {
//                     if (service_name == "") {
//                         error_msg_alert("Enter Service Name at row " + (i + 1));
//                         return false;
//                     }
//                     if (description== "") {
//                         error_msg_alert("Enter Description at row " + (i + 1));
//                         return false;
//                     }



//         //             var flag1 = validate_services('description-1');
//         // if (!flag1) {
//         //   return false;
//         // }
//         // var flag2 = validate_services_1('service_name-1');
//         // if (!flag2) {
//         //   return false;
//         // }


//                     images_array.push({
//                         'service_name': service_name,
//                         'description': description
                         
//                     });
//                 }
//             }
//             // console.log(images_array);
//             $('#btn_save').button('loading');
//             $.ajax({
//                 type: 'post',
//                 url: base_url + 'controller/b2c_settings/cms_save.php',
//                 data: {
//                     section: '26',
//                     data: images_array
//                 },
//                 success: function(message) {
//                     $('#btn_save').button('reset');
//                     var data = message.split('--');
//                     if (data[0] == 'erorr') {
//                         error_msg_alert(data[1]);
//                     } else {
//                         success_msg_alert(data[1]);
//                         // reflect_data('3');
//                         // update_b2c_cache();
//                     }
//                 }
//             });
//         }
//     });
// });

$(function() {
    $('#section_hotels').validate({
        rules: {},
        submitHandler: function(form) {
            var base_url = $('#base_url').val();
            var images_array = [];
            var table = document.getElementById("tbl_services");
            var rowCount = table.rows.length;
            var hasError = false;

            for (var i = 0; i < rowCount; i++) {
                var row = table.rows[i];
                var service_name = row.cells[2].childNodes[0].value.trim();
                var description = row.cells[3].childNodes[0].value.trim();

                if (row.cells[0].childNodes[0].checked) {
                    if (service_name == "") {
                        error_msg_alert("Enter Service Name at row " + (i + 1));
                        hasError = true;
                    }
                    if (description == "") {
                        error_msg_alert("Enter Description at row " + (i + 1));
                        hasError = true;
                    }

                    // Validate service name and description
                    if (!validate_services_1(row.cells[2].childNodes[0].id)) {
                        hasError = true;
                    }
                    if (!validate_services(row.cells[3].childNodes[0].id)) {
                        hasError = true;
                    }

                    if (!hasError) {
                        images_array.push({
                            'service_name': service_name,
                            'description': description
                        });
                    }
                }
            }

            // Stop execution if there's an error
            if (hasError) {
                return false;
            }

            $('#btn_save').button('loading');
            $.ajax({
                type: 'post',
                url: base_url + 'controller/b2c_settings/cms_save.php',
                data: {
                    section: '26',
                    data: images_array
                },
                success: function(message) {
                    $('#btn_save').button('reset');
                    var data = message.split('--');
                    if (data[0] == 'error') {
                        error_msg_alert(data[1]);
                    } else {
                        success_msg_alert(data[1]);
                        // reflect_data('3');
                        // update_b2c_cache();
                    }
                }
            });
        }
    });
});


</script>