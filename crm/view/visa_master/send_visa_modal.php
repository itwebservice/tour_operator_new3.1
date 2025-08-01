<?php
include "../../model/model.php";
$entry_id = $_POST['entry_id'];
?>

<style>
.bootstrap-tagsinput {
  display: flex !important;
  flex-wrap: wrap !important;
  width: 100% !important;
  min-height: 40px !important;
  padding: 6px !important;
  border: 1px solid rgb(204, 204, 204);
  box-shadow: none;
  background: transparent;
}

.bootstrap-tagsinput .tag {
  margin: 4px 4px 0 0;
  padding: 5px 10px;
  font-size: 13px;
  background-color:rgb(139, 188, 237);
  color: #fff;
  border-radius: 3px;
  display: inline-block;
  white-space: nowrap;
}
.bootstrap-tagsinput input {
  width: auto !important;
  max-width: 200px;
  min-width: 100px;
  /* border: 1px solid rgb(204, 204, 204); */
  outline: none;
  background: transparent;
}
</style>

<div class="modal fade" id="visa_send_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Send Visa Information</h4>
      </div>
      <div class="modal-body">
        <form id="frm_visa_send">
          <input type="hidden" value="<?= $entry_id ?>" id="entry_id" name="entry_id">
          <div class="row mg_bt_10">
            <div class="col-md-12 col-sm-12">
              <span><input type="checkbox" id="mail" name="mail">&nbsp;&nbsp;Enter Multiple Email Id's with comma!</span>
            </div>
          </div>
          <div class="row mg_bt_20">
            <div class="col-md-12 col-sm-12 mg_bt_10">
              <input type="text" name="email_id" class="form-control" title="Email ID" placeholder="Email ID" id="email_id" data-role="tagsinput" onchange="validate_email(this.id)"  style="width:100%; min-height:40px; padding:6px; display:flex; flex-wrap:wrap; 1px solid rgb(204, 204, 204); background-color:#fff; line-height:1.4; overflow-y:auto; border-radius:4px;">
              <input type="hidden" id="cust_data" name="cust_data" value='<?= get_customer_hint_1('enq', 'yes') ?>'>
              <div id="email-suggestions" style="position: absolute;background: rgb(255 255 255);border: 1px solid rgb(204, 204, 204);top: 39px;width: 520px;height: 32px;z-index: 999; display: none;"></div>
            </div>
          </div>
          <div class="row mg_bt_10">
            <div class="col-md-12 col-sm-12">
              <span><input type="checkbox" id="whatsapp" name="whatsapp">&nbsp;&nbsp;Select checkbox to send on whatsapp!</span>
            </div>
          </div>
          <div class="row mg_bt_30">
            <div class="col-sm-4 col-xs-12">
              <select name="country_code" id="country_code" style="width:100%;" class="form-control">
                <?= get_country_code() ?>
              </select>
            </div>
            <div class="col-sm-8 col-xs-12">
              <input type="number" id="cust_contact_no" name="cust_contact_no" maxlength="15" onchange="mobile_validate(this.id)" placeholder="Mobile No" title="Mobile No">
              
            </div>
          </div>
          <div class="row text-center">
            <div class="col-md-12">
              <button class="btn btn-sm btn-success" id="btn_visa_send"><i class="fa fa-paper-plane-o"></i>&nbsp;&nbsp;Send</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
<style>
    .ui-autocomplete-input {
        min-width: 300px !important; /* or use percentages for responsive sizing */
      }
</style>
<script>
  $('#visa_send_modal').modal('show');
  $('#country_code').select2();
  
    
  
  $(function() {
    
    
    var keyEnter = '';
    $(document).on('keydown', function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            keyEnter = 'Yes';
        }
    });
      
      
      
    $('#frm_visa_send').validate({
      submitHandler: function(form) {
          
        $('#btn_visa_send').prop('disabled', true);
        var base_url = $('#base_url').val();
        var entry_id = $('#entry_id').val();
        var mail = document.getElementById('mail').checked;
        var email_id = $('#email_id').val();
        var whatsapp = document.getElementById('whatsapp').checked;
        var country_code = $('#country_code').val();
        var contact_no = $('#cust_contact_no').val();
        if (mail == true && email_id == '') {
          error_msg_alert("Enter email id!");
          $('#btn_visa_send').prop('disabled', false);
          $('#btn_visa_send').button('reset');
          return false;
        }
        if (whatsapp == true && country_code == '') {
          error_msg_alert("Select country code!");
          $('#btn_visa_send').prop('disabled', false);
          $('#btn_visa_send').button('reset');
          return false;
        }
        if (whatsapp == true && contact_no == '') {
          error_msg_alert("Enter mobile no!");
          $('#btn_visa_send').prop('disabled', false);
          $('#btn_visa_send').button('reset');
          return false;
        }
        if (mail === false && whatsapp === false) {
        //if($('.bootstrap-tagsinput span').text()){
          error_msg_alert("Send information on mail or on whatsapp atleast!");
          $('#btn_visa_send').prop('disabled', false);
          $('#btn_visa_send').button('reset');
          return false;
        }
        var msg = '';
        $('#vi_confirm_box').vi_confirm_box({
          callback: function(data1) {
            if (data1 == "yes") {
              if (mail === true) {

                $('#btn_visa_send').prop('disabled', true);
                $('#btn_visa_send').button('loading');
                $.ajax({
                  type: 'post',
                  url: base_url + 'controller/visa_master/visa_email_send.php',
                  data: {
                    entry_id: entry_id,
                    email_id: email_id
                  },
                  success: function(message) {
                    msg_alert(message);
                    $('#btn_visa_send').button('reset');
                    $('#btn_visa_send').prop('disabled', false);
                    $('#visa_send_modal').modal('hide');
                  }
                });
              }
              if (whatsapp === true) {
                $('#btn_visa_send').prop('disabled', true);
                $('#btn_visa_send').button('loading');
                var wcontact_no = country_code + contact_no;
                whatsapp_send(wcontact_no, entry_id, 'visa_send_modal');
                $('#btn_visa_send').button('reset');
                $('#btn_visa_send').prop('disabled', false);
              }
            } else {
              $('#btn_visa_send').button('reset');
              $('#btn_visa_send').prop('disabled', false);
            }
          }
        });
      }
    });
  });

  function whatsapp_send(contact_no, entry_id, visa_send_modal) {
    var base_url = $('#base_url').val();
    $.post(base_url + 'controller/visa_master/visa_whatsapp.php', {
      contact_no: contact_no,
      entry_id: entry_id
    }, function(data) {
      $('#' + visa_send_modal).modal('hide');
      window.open(data);
    });
  }
  // -------------------------- jquery end here
  $("#email_id").tagsinput('items');
  $(document).ready(function() {
    const $input = $('#email_id').siblings('.bootstrap-tagsinput').find('input');
    $('.bootstrap-tagsinput input').attr('id', 'targetInput');
    $('#targetInput').attr('size', '100');
    $("#targetInput").autocomplete({
        source: JSON.parse($('#cust_data').val()),
        select: function(event, ui) {
            var base_url = $('#base_url').val();

            $('#cust_contact_no').val(ui.item.contact_no);
            var country_code = ui.item.country_code;
            $('#country_code').prepend($('<option value=' + ui.item.country_id + '>' + country_code +
                '</option>'));
            document.getElementById('country_code').selectedIndex = "0";
            $('#country_code').trigger('change');

        }
    });
    $("#cust_contact_no").autocomplete({
        source: JSON.parse($('#mobile_data').val())
    });
    const $suggestionsBox = $('#email-suggestions');
    $input.on('input', function() {
      let inputVal = $(this).val().split(',').pop().trim();
      if (inputVal.length < 2) {
        $suggestionsBox.hide();
        return;
      }
      var base_url = $('#base_url').val();
      $.ajax({
        url: base_url + 'controller/visa_master/visa_email_suggestions.php',
        method: 'GET',
        data: {
          q: inputVal
        },
        success: function(data) {
          let suggestions = JSON.parse(data);
          let suggestionsHtml = '';

          if (suggestions.length > 0) {
            suggestions.forEach(item => {
              suggestionsHtml += `<div class="suggestion-item" style="padding: 5px; cursor: pointer;">${item.email_id}</div>`;
            });

            let offset = $input.offset();
            $suggestionsBox.css({
              top: offset.top + $input.outerHeight(),
              left: offset.left,
              width: $input.outerWidth()
            }).html(suggestionsHtml).show();
          } else {
            $suggestionsBox.hide();
          }
        }
      });
    });

    // On clicking a suggestion
    $(document).on('click', '.suggestion-item', function() {
      let email = $(this).text();
      $('#email_id').tagsinput('add', email);
      $('#email-suggestions').hide();
      $('#email_id').val(''); // clear input part
    });

    // Hide suggestions if clicked outside
    $(document).on('click', function(e) {
      if (!$(e.target).closest('#email_id, #email-suggestions').length) {
        $suggestionsBox.hide();
      }
    });
  });
  
    
</script>
<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?= BASE_URL ?>js/app/field_validation.js"></script>