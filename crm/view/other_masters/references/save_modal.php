<?php

include "../../../model/model.php";

?>

<form id="frm_save">

<div class="modal fade" id="save_modal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">

  <div class="modal-dialog" role="document">

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        <h4 class="modal-title" id="myModalLabel">Add Reference</h4>

      </div>

      <div class="modal-body">

        

          <div class="row">

            <div class="col-sm-6 mg_bt_10">
              <label for="reference">Reference</label>
              <input type="text" id="reference" name="reference" onchange="fname_validate(this.id);" placeholder="*Reference" title="Reference">

            </div>

             <div class="col-sm-6 mg_bt_10">
              <label for="active_flag">Status</label>
              <select name="active_flag" id="active_flag" title="*Status" class="form-control" style="width:100%" required>

                <option value="Active">Active</option>

                <option value="Inactive">Inactive</option>

              </select>

            </div>

          </div>

          <div class="row mg_tp_10">
            <div class="col-xs-12 text-center">
              <button class="btn btn-sm btn-success" id="btn_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
            </div>
          </div>



      </div>      

    </div>

  </div>

</div>

</form>

<script>

$('#save_modal').modal('show');

$('#frm_save').validate({

    rules:{

            reference : { required : true },
            active_flag : { required : true }

    },

    submitHandler:function(form){



        var reference = $('#reference').val();

        var status = $('#active_flag').val();



        $('#btn_save').button('loading');



        $.ajax({

          type:'post',

          url:base_url()+'controller/other_masters/references/save_reference.php',

          data:{ reference : reference, status : status},

          success:function(result){

              $('#btn_save').button('reset');

              var msg = result.split('--');


              if(msg[0]!="error"){
                $('#save_modal').modal('hide');
                msg_alert(result);
                list_reflect();
              }
              else{
                error_msg_alert(msg[1]);
                $('#btn_save').button('reset');
              }

          }

        });







    }

});

</script>

<script src="<?= BASE_URL ?>js/app/footer_scripts.js"></script>