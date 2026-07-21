<?php

include 'config.php';

$service = $_GET['service'];

//Include header

include 'layouts/header2.php';
$_SESSION['page_type'] = 'career';

$career = mysqli_fetch_all(mysqlQuery("SELECT * FROM `b2c_career` WHERE active_flag='0'"), MYSQLI_ASSOC);

?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

    <div class="container">

        <div class="row">

            <div class="col-md-7 col-12">



                <!-- *** Search Head **** -->

                <div class="searchHeading">

                    <span class="pageTitle mb-0">Careers</span>

                </div>

                <!-- *** Search Head End **** -->

            </div>



            <div class="col-md-5 col-12 c-breadcrumbs">

                <ul>

                    <li>

                        <a href="<?= BASE_URL_B2C ?>">Home</a>

                    </li>

                    <li class="st-active">

                        <a href="javascript:void(0)">Careers</a>

                    </li>

                </ul>

            </div>



        </div>

    </div>

</div>

<!-- ********** Component :: Page Title End ********** -->


<!-- Contact Section Start -->

<section class="ts-contact-section">

    <div class="container">

        <div class="ts-section-subtitle-content">

            <h2 class="ts-section-subtitle">Careers</h2>

            <span class="ts-section-subtitle-icon"><img src="images/traveler.png" alt="traveler" classimg-fluid></span>

        </div>

        <h2 class="ts-section-title">CURRENT OPENINGS</h2>

        <div class="row">

            <div class="col col-12 col-md-6 col-lg-8">

                <div class="ts-careers-content">

                    <div id="accordion" class="ts-accordion">

                        <?php if (!empty($career)): ?>
                            <?php foreach ($career as $index => $job): ?>
                                <div class="card">
                                    <div class="card-header" id="heading<?= $index ?>">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?= $index ?>"
                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                                aria-controls="collapse<?= $index ?>">
                                                Opening - <?= $index + 1 ?> (<?= htmlspecialchars($job['position']) ?>)
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="collapse<?= $index ?>" class="collapse <?= $index === 0 ? 'show' : '' ?>"
                                        aria-labelledby="heading<?= $index ?>" data-parent="#accordion">
                                        <div class="card-body">
                                            <h3>Position: <?= htmlspecialchars($job['position']) ?></h3>
                                            <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                                            <p><strong>Job Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                                            <p><strong>Description:</strong> <?php echo '<div class="career-div" style="padding-left: 20px;">' . strip_tags($job['job_description'], '<p><b><strong><ul><li><span>') . '</div>'; ?></p>
                                            <p><strong>Skills:</strong> <?php echo '<div class="career-div" style="padding-left: 20px;">' . strip_tags($job['skills'], '<p><b><strong><ul><li><span>') . '</div>'; ?></p>
                                            <p><strong>Benefits:</strong> <?php echo '<div class="career-div" style="padding-left: 20px;">' . strip_tags($job['benefits'], '<p><b><strong><ul><li><span>') . '</div>'; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center font-weight-bold text-danger">No Openings</p>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <div class="col col-12 col-md-6 col-lg-4">

                <div class="ts-careers-apply-content">

                    <h3 class="ts-careers-apply-title">APPLY NOW</h3>

                    <form id="career_form" class="needs-validation" novalidate>

                        <div class="form-row">

                            <div class="form-group col-md-12">

                                <input type="text" class="form-control" id="inputName" name="inputName" placeholder="*Enter Name" onkeypress="return blockSpecialChar(event)" required>

                            </div>

                            <div class="form-group col-md-12">

                                <input type="number" class="form-control" id="inputPhone" name="inputPhone" placeholder="*Enter Phone" required>

                            </div>

                            <div class="form-group col-md-12">

                                <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="*Enter Email ID" required>

                            </div>

                            <div class="form-group col-md-12">

                                <input type="text" class="form-control" id="inputPos" name="inputPos" placeholder="*Enter Position" required>

                            </div>

                            <div class="form-group col-md-12">

                                <!-- <input type="file" class="form-control" id="inputFile" name="inputFile"> -->

                                <div class="div-upload">

                                    <div id="hotel_btn1" class="upload-button1"><span>Upload Resume</span></div>

                                    <span id="id_proof_status"></span>

                                    <ul id="files"></ul>

                                    <input type="hidden" id="inputFile_url" name="inputFile_url">

                                </div>

                            </div>

                        </div>

                        <div class="text-center">

                            <button type="submit" class="btn btn-primary" id="career_form_send">Submit</button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- Contact Section End -->


<style>
    .career-div ol,
    .career-div ul {
        list-style: disc;
    }
</style>

<?php include 'layouts/footer2.php'; ?>
<script type="text/javascript" src="js2/scripts.js"></script>

<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>

<script>
    upload_pic_attch();

    function upload_pic_attch()

    {



        var base_url = $('#crm_base_url').val();

        var btnUpload = $('#hotel_btn1');

        $(btnUpload).find('span').text('*Upload Resume');

        $("#inputFile_url").val('');

        new AjaxUpload(btnUpload, {



            action: 'upload_resume.php',

            name: 'uploadfile',

            onSubmit: function(file, ext) {



                if (!(ext && /^(txt|pdf|doc|docx)$/.test(ext))) {

                    error_msg_alert('Only Text,PDF or word files are allowed', base_url);

                    return false;

                }



                $(btnUpload).find('span').text('Uploading...');



            },



            onComplete: function(file, response) {



                if (response === "error") {



                    error_msg_alert("File is not uploaded.", base_url);



                    $(btnUpload).find('span').text('*Upload Resume');



                } else



                {



                    if (response == "error1")



                    {



                        $(btnUpload).find('span').text('*Upload Resume');



                        error_msg_alert('Maximum size exceeds', base_url);



                        return false;



                    } else



                    {



                        $(btnUpload).find('span').text('Uploaded');

                        $("#inputFile_url").val(response);



                    }



                }



            }



        });



    }

    // Example starter JavaScript for disabling form submissions if there are invalid fields

    (function() {

        'use strict';

        window.addEventListener('load', function() {

            // Fetch all the forms we want to apply custom Bootstrap validation styles to

            var forms = document.getElementsByClassName('needs-validation');

            // Loop over them and prevent submission

            var validation = Array.prototype.filter.call(forms, function(form) {

                form.addEventListener('submit', function(event) {

                    if (form.checkValidity() === false) {

                        event.preventDefault();

                        event.stopPropagation();

                    }

                    form.classList.add('was-validated');

                }, false);

            });

        }, false);

    })();



    $(function() {

        $('#career_form').validate({

            rules: {

                // inputName: { required: true },

                // inputEmail: { required: true },

                // inputPhone: { required: true },

                // inputFile_url: { required: true },

                // inputPos : { required: true }

            },

            submitHandler: function(form) {



                $('#career_form_send').prop('disabled', true);



                var crm_base_url = $('#crm_base_url').val();

                var base_url = $('#base_url').val();

                var name = $('#inputName').val();

                var email = $('#inputEmail').val();

                var phone = $('#inputPhone').val();

                var file = $('#inputFile_url').val();

                var pos = $('#inputPos').val();

                if (name == '' || email == '' || phone == '' || file == '' || pos == '') {

                    if (file == '') {

                        error_msg_alert('Upload your resume please!', base_url);

                        $('#career_form_send').prop('disabled', false);

                        return false;

                    } else {

                        $('#career_form_send').prop('disabled', false);

                        return false;

                    }

                }

                $('#career_form_send').button('loading');

                $.ajax({

                    type: 'post',

                    url: crm_base_url + 'controller/b2c_settings/b2c/career_form_mail.php',

                    data: {

                        name: name,

                        email: email,

                        phone: phone,

                        file: file,

                        pos: pos

                    },

                    success: function(result) {

                        $('#career_form_send').prop('disabled', false);

                        $('#career_form_send').button('reset');

                        success_msg_alert(result, base_url);

                        setTimeout(() => {

                            $('#inputName').val('');

                            $('#inputEmail').val('');

                            $('#inputPhone').val('');

                            $('#inputFile_url').val('');

                            $('#inputPos').val('');

                            return false;

                        }, 1000);

                    }

                });

            }

        });

    });
</script>