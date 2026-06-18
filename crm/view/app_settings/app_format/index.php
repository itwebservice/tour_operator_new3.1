<?php
include "../../../model/model.php";
$sq_settings = mysqli_fetch_assoc(mysqlQuery("select * from app_settings"));
$sq_settings_g = mysqli_fetch_assoc(mysqlQuery("select * from generic_count_master"));

$quot_format_labels = array(
  1  => 'Option-1',
  2  => 'Option-2',
  3  => 'Option-3',
  4  => 'Option-4',
  5  => 'Option-5',
  6  => 'Option-6',
  7  => 'Option-7',
  8  => 'Option-8',
  9  => 'Portrait Standard',
  10 => 'Portrait Advanced',
);
$qf_val = (int) $sq_settings['quot_format'];
$quot_format = isset($quot_format_labels[$qf_val]) ? $quot_format_labels[$qf_val] : 'Option-1';
?>

<form id="app_format_info" class="mg_tp_30">
  <div class="row mg_tp_30">
    <div class="col-md-6">
      <div class="panel panel-default panel-body app_panel_style feildset-panel ">
        <legend>Color Setting</legend>
        <div class="col-md-10 col-md-offset-2">
          <button class="btn btn-info btn-sm ico_left" data-toggle="tooltip" data-placement="bottom" title="Setting" id="theme_color_scheme_save_modal_btn" onclick="theme_color_scheme_save_modal();btnDisableEnable(this.id)"><i class="fa fa-cog"></i><span class="">&nbsp;&nbsp;Change</span></button>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="panel panel-default panel-body app_panel_style feildset-panel ">
        <legend>Invoice Setting</legend>
        <div class="col-sm-8 col-md-8 mg_bt_10">

          <select name="invoice_format_list" id="invoice_format_list" title="Invoice Format List">
            <?php if ($sq_settings_g['invoice_format'] == '4') { ?>
              <option value="4">Creative</option>
            <?php } else { ?>
              <option value="<?= $sq_settings_g['invoice_format'] ?>"><?= $sq_settings_g['invoice_format'] ?></option>
            <?php } ?>
            <option value="Standard">Standard</option>
            <option value="Regular">Regular</option>
            <option value="Advance">Advance</option>
          </select>
          <!-- <small>Note : Bydefault Standard Format is used.</small> -->
        </div>
        <div class="col-sm-4 col-md-4">
          <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Bydefault Standard Format is used."><i class="fa fa-question-circle"></i></button>
          <button class="btn btn-info btn-sm ico_left" data-toggle="tooltip" data-placement="bottom" title="View" id="display_modal_invoive_btn" onclick="display_modal_invoive();btnDisableEnable(this.id)"><i class="fa fa-eye"></i><span class="">&nbsp;&nbsp;View</span></button>
        </div>
      </div>
    </div>
  </div>
  <!-- Quotatio nformat settings -->
  <div class="row mg_tp_30">
    <div class="col-md-12 mg_tp_30">
      <div class="panel panel-default panel-body app_panel_style feildset-panel ">
        <legend>Quotation Format & Image Setting</legend>
        <div class="col-sm-6 col-md-3 mg_bt_10">
          <select name="format_list" id="format_list" title="Quotation Format List" onchange="display_images(this.id);">
            <?php if ($qf_val != 0) { ?>
              <option value="<?= $qf_val ?>"><?= $quot_format ?></option>
            <?php } ?>
            <option value="1">Option-1</option>
            <option value="2">Option-2</option>
            <option value="3">Option-3</option>
            <option value="4">Option-4</option>
            <option value="5">Option-5</option>
            <option value="6">Option-6</option>
            <option value="7">Option-7</option>
            <option value="8">Option-8</option>
            <option value="9">Portrait Standard</option>
            <option value="10">Portrait Advanced</option>
          </select>
        </div>
        <div class="col-sm-6 col-md-3 mg_bt_10">
          <select style="width:100%;" name="" id="destination_format_filter" title="Destination" onchange="display_images('format_list');">

            <?= get_destinations_option($sq_settings['format_dest_id']) ?>
          </select>
          <?php
          if (!empty($sq_settings['format_dest_id'])) {
          ?>
            <script>
              $('#destination_format_filter').trigger('change');
            </script>
          <?php
          }
          ?>
        </div>
        <div class="col-md-6 no-pad">
          <div class="col-md-6 text-left">
            <button type="button" data-toggle="tooltip" class="btn btn-excel" title="Bydefault Portrait Standard Format is used."><i class="fa fa-question-circle"></i></button>
            <a class="btn btn-info btn-sm ico_left" data-toggle="tooltip" data-placement="bottom" title="View" href="javascript:void(0)" onclick="display_modal('format_list')"><i class="fa fa-eye"></i><span class="">&nbsp;&nbsp;View</span></a>
            <button type="button" class="btn btn-info btn-sm ico_left" data-toggle="tooltip" data-placement="bottom" title="Upload" onclick="upload_modal()"><i class="fa fa-upload"></i><span class="">&nbsp;&nbsp;Upload</span></button>
          </div>
          <div class="col-md-6 text-right">
            <button class="btn btn-sm btn-success" id="format_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
          </div>
        </div>
        <div class="col-md-12">
          <div class="panel panel-default panel-body mg_tp_20 main_block">
            <div class="row">
              <div class="col-md-12 no-pad">
                <div id="div_list" class="loader_parent"></div>
                <div id="upload_modal_div"></div>
              </div>
            </div>

            <!-- ============================ Dipti -->
            <div class="row">
              <div class="col-md-12 no-pad">
                <?php
                include_once(__DIR__ . '/../../../model/app_settings/print_html/quotation_html/generic_builder_config.php');
                $gqb_config = gqb_get_config();
                $testimonials = isset($gqb_config['testimonials']) ? $gqb_config['testimonials'] : array();

                if (empty($testimonials)) {
                  $testimonials = array(
                    array('name' => 'Customer 1', 'designation' => 'Traveller', 'review' => 'Excellent travel experience and smooth arrangements.', 'photo' => ''),
                    array('name' => 'Customer 2', 'designation' => 'Traveller', 'review' => 'The trip was well planned and managed professionally.', 'photo' => ''),
                    array('name' => 'Customer 3', 'designation' => 'Traveller', 'review' => 'Hotels, transport and itinerary were very well organized.', 'photo' => ''),
                    array('name' => 'Customer 4', 'designation' => 'Traveller', 'review' => 'Great support from the team throughout the journey.', 'photo' => ''),
                    array('name' => 'Customer 5', 'designation' => 'Traveller', 'review' => 'A memorable holiday with wonderful service.', 'photo' => ''),
                    array('name' => 'Customer 6', 'designation' => 'Traveller', 'review' => 'Everything was handled perfectly from start to finish.', 'photo' => '')
                  );
                }
                ?>

                <div class="panel panel-default panel-body mg_tp_20">
                  <!-- <legend>Customer Testimonials</legend> -->
                  <h4 class="text-left">
                    Customer Testimonials
                  </h4>

                  <div style="padding-top:15px;">
                    <div id="testimonial_rows">
                      <?php foreach ($testimonials as $i => $t) { ?>
                        <div class="row testimonial_row mg_bt_10">
                          <div class="col-md-2">
                            <input type="text" class="form-control testimonial_name" placeholder="Name" value="<?= htmlspecialchars($t['name'] ?? '') ?>">
                          </div>
                          <div class="col-md-2">
                            <input type="text" class="form-control testimonial_designation" placeholder="Designation" value="<?= htmlspecialchars($t['designation'] ?? '') ?>">
                          </div>
                          <div class="col-md-4">
                            <input type="text" class="form-control testimonial_review" placeholder="Review" value="<?= htmlspecialchars($t['review'] ?? '') ?>">
                          </div>
                          <div class="col-md-3">
                            <!-- <input type="text" class="form-control testimonial_photo" placeholder="Photo URL" value="<?= htmlspecialchars($t['photo'] ?? '') ?>"> -->
                            <input type="hidden" class="testimonial_photo" value="<?= htmlspecialchars($t['photo'] ?? '') ?>">

                            <div class="div-upload">
                              <div class="upload-button1 testimonial_upload_btn">
                                <span><?= !empty($t['photo']) ? 'Uploaded' : 'Image' ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm qb_remove_testm"><i class="fa fa-trash"></i></button>
                          </div>
                        </div>
                      <?php } ?>
                    </div>

                    <button type="button" class="btn btn-info btn-sm" onclick="add_testimonial_row()">+ Add Testimonial</button>
                  </div>
                </div>
                <!-- =========================================== -->
              </div>
            </div>

            <?php
            $social_links = isset($gqb_config['social_links']) ? $gqb_config['social_links'] : array();
            ?>

            <div class="panel panel-default panel-body mg_tp_20">
              <h4 class="text-left">Social Media Links</h4>

              <div style="padding-top:15px;">
                <div class="row mg_bt_10">
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="facebook_link" placeholder="Facebook URL" value="<?= htmlspecialchars($social_links['facebook'] ?? '') ?>">
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="instagram_link" placeholder="Instagram URL" value="<?= htmlspecialchars($social_links['instagram'] ?? '') ?>">
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="linkedin_link" placeholder="LinkedIn URL" value="<?= htmlspecialchars($social_links['linkedin'] ?? '') ?>">
                  </div>
                </div>

                <div class="row mg_bt_10">
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="youtube_link" placeholder="YouTube URL" value="<?= htmlspecialchars($social_links['youtube'] ?? '') ?>">
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="twitter_link" placeholder="Twitter / X URL" value="<?= htmlspecialchars($social_links['twitter'] ?? '') ?>">
                  </div>
                  <div class="col-md-4">
                    <input type="text" class="form-control" id="whatsapp_link" placeholder="WhatsApp URL" value="<?= htmlspecialchars($social_links['whatsapp'] ?? '') ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

</form>
<div id="invoice_format_image" class="main_block"></div>

<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>

<script>
  document.addEventListener('click', function(e) {
    if (e.target.closest('.qb_remove_testm')) {
      e.target.closest('.testimonial_row').remove();
    }
  });
</script>
<script type="text/javascript">
  $('#destination_format_filter').select2();

  function display_modal_invoive() {
    $('#display_modal_invoive_btn').button('loading');
    $('#display_modal_invoive_btn').prop('disabled', true);
    var base_url = $('#base_url').val();
    $.post(base_url + 'view/app_settings/basic_info/view/index.php', {}, function(data) {
      $('#invoice_format_image').html(data);
      $('#display_modal_invoive_btn').button('reset');
      $('#display_modal_invoive_btn').prop('disabled', false);
    });

  }

  function display_modal(format_list) {

    var format = $('#' + format_list).val();
    var base_url = $('#base_url').val();

    if (format == 2) {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Landscape-Standard-Pdf', '_blank');
      return false;
    } else if (format == 3) {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Landscape-Creative-Pdf', '_blank');
      return false;
    } else if (format == 4) {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Portrait-Creative-Pdf', '_blank');
      return false;
    } else if (format == 5) {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Portrait-Advanced-Pdf', '_blank');
      return false;
    } else if (format == 6) {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Landscape-Advanced-Pdf', '_blank');
      return false;
    } else {
      window.open('https://itourscloud.com/quotation_format_images/quot_pdf/Portiat-Standard-Pdf', '_blank');
      return false;
    }
  }

  function display_images(format_list) {
    var format = $('#' + format_list).val();
    var destination = $('#destination_format_filter').val();
    var base_url = $('#base_url').val();
    $.post(base_url + 'view/app_settings/app_format/display_images.php', {
      format: format,
      destination: destination
    }, function(data) {
      $('#div_list').html(data);
    });
  }

  function upload_modal() {

    var base_url = $('#base_url').val();
    $.post(base_url + 'view/app_settings/app_format/upload_img.php', {}, function(data) {
      $('#upload_modal_div').html(data);
    });
  }
  display_images('format_list');

  // ========================= Dipti
  function add_testimonial_row() {
    var html = `
    <div class="row testimonial_row mg_bt_10">
        <div class="col-md-2"><input type="text" class="form-control testimonial_name" placeholder="Name"></div>
        <div class="col-md-2"><input type="text" class="form-control testimonial_designation" placeholder="Designation"></div>
        <div class="col-md-4"><input type="text" class="form-control testimonial_review" placeholder="Review"></div>

        <div class="col-md-3">
  <input type="hidden" class="testimonial_photo">
  <div class="div-upload">
    <div class="upload-button1 testimonial_upload_btn"><span>Image</span></div>
    <span class="testimonial_upload_status"></span>
  </div>
</div>

        <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="$(this).closest('.testimonial_row').remove()">X</button></div>
    </div>`;
    $('#testimonial_rows').append(html);
  }

  function init_testimonial_uploads() {

    $('.testimonial_upload_btn').each(function() {

      if ($(this).data('upload-initialized') == 'yes') {
        return;
      }

      $(this).data('upload-initialized', 'yes');

      var btnUpload = $(this);
      var row = btnUpload.closest('.testimonial_row');

      new AjaxUpload(btnUpload, {
        action: 'app_format/upload_testimonial_img.php',
        name: 'uploadfile',

        onSubmit: function(file, ext) {
          if (!(ext && /^(png|jpeg|jpg)$/.test(ext))) {
            error_msg_alert('Only JPG,JPEG,PNG files are allowed');
            return false;
          }
          btnUpload.find('span').text('Uploading...');
        },

        onComplete: function(file, response) {
          var response1 = response.split('--');

          if (response1[0] == "error") {
            error_msg_alert(response1[1]);
            btnUpload.find('span').text('Image');
          } else {

            var base_url = $('#base_url').val();
            var oldPhoto = row.find('.testimonial_photo').val();

            if (oldPhoto != '' && oldPhoto.indexOf('uploads/testimonials/') != -1) {
              $.post(base_url + 'view/app_settings/app_format/delete_testimonial_img.php', {
                image: oldPhoto
              });
            }

            btnUpload.find('span').text('Uploaded');
            row.find('.testimonial_photo').val(response);
          }
        }
      });

    });
  }

  init_testimonial_uploads();
  // =======================================

  $(function() {
    $('#app_format_info').validate({
      rules: {
        app_version: {},
        app_email_id: {
          email: true
        },
      },

      submitHandler: function(form) {

        var base_url = $('#base_url').val();
        var invoice_format_list = $('#invoice_format_list').val();
        var quot_format = $('#format_list').val();
        var dest_id = $('#destination_format_filter').val();
        var img_arr1 = (function() {
          var a = '';
          $("input[name='image_check']:checked").each(function() {
            a += this.value + ',';
          });
          return a;
        })();

        var gallary_arr1 = img_arr1.split(",");
        var length = gallary_arr1.length - 1;
        if (length == 0 || length > 1) {
          error_msg_alert("Please select at least one image for Quotation cover!");
          return false;
        }

        // =========================== Dipti
        var testimonials = [];

        $('.testimonial_row').each(function() {
          testimonials.push({
            name: $(this).find('.testimonial_name').val(),
            designation: $(this).find('.testimonial_designation').val(),
            review: $(this).find('.testimonial_review').val(),
            photo: $(this).find('.testimonial_photo').val()
          });
        });

        var social_links = {
          facebook: $('#facebook_link').val(),
          instagram: $('#instagram_link').val(),
          linkedin: $('#linkedin_link').val(),
          youtube: $('#youtube_link').val(),
          twitter: $('#twitter_link').val(),
          whatsapp: $('#whatsapp_link').val()
        };
        console.log(social_links);
        // =======================================
        var image = gallary_arr1[0];
        $('#format_save').button('loading');
        $('#vi_confirm_box').vi_confirm_box({
          callback: function(data1) {
            if (data1 == "yes") {
              $.ajax({
                type: 'post',
                url: base_url + 'controller/app_settings/setting/app_format_info_save.php',
                data: {
                  invoice_format_list: invoice_format_list,
                  quot_format: quot_format,
                  image: image,
                  dest_id: dest_id,
                  testimonials: JSON.stringify(testimonials), //============= Dipti
                  social_links: JSON.stringify(social_links)
                },
                success: function(result) {
                  // msg_popup_reload(result);
                  success_msg_alert(result);
                  $('#format_save').button('reset');
                }
              });
            } else {
              $('#format_save').button('reset');
            }
          }
        });
        return false;
      }
    });
  });
</script>
<script src="<?= BASE_URL ?>js/ajaxupload.3.5.js"></script>