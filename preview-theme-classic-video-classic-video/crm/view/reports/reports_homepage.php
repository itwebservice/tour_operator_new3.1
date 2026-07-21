<?php
include "../../model/model.php";
/*======******Header******=======*/
require_once('../layouts/admin_header.php');
$role = $_SESSION['role'];
$branch_admin_id = $_SESSION['branch_admin_id'];
$q = "select * from branch_assign where link='reports/reports_homepage.php'";
$sq_count = mysqli_num_rows(mysqlQuery($q));
$sq = mysqli_fetch_assoc(mysqlQuery($q));
$branch_status = ($sq_count > 0 && $sq['branch_status'] !== NULL && isset($sq['branch_status'])) ? $sq['branch_status'] : 'no';
?>
<input type="hidden" id="branch_status" name="branch_status" value="<?= $branch_status ?>">
<?= begin_panel('Sales Reports', 95) ?> <span style="font-size: 15px;font-weight: 400;color: #006d6d;margin-left: 15px;" id="span_report_name"></span>
<!--  -->
<!-- Main Menu End -->
<div class="col-xs-12 mg_tp_20">
  <div id="div_report_content" class="main_block">
  </div>
</div>

</div>
<?= end_panel() ?>

<script type="text/javascript">
  $(function() {
    $("a").on("click", function() {
      if ($(this).parent('li').attr('class') == "dropdown active") {
        $("li.active").removeClass("active");
      } else {
        $("li.active").removeClass("active");
        $(this).parent('li').addClass("active");
      }
    });
  });

  $(function() {
    $("span").on("click", function() {
      $("li.active").removeClass("active");
      $(this).closest('li.dropdown').addClass("active");
    });
  });


  function show_report_reflect(report_name) {

    //Tour Report
    // if (report_name == "Complete Tour Summary") {
      url = 'filters/common_reports_filters.php';
    // }
    $.post(url, {}, function(data) {
      $(".dropdown_menu").addClass('hidden');
      $("li.active").removeClass("active");
      $('#div_report_content').html(data);
      setTimeout(
        function() {
          $(".dropdown_menu").removeClass('hidden');
        }, 500);
    });
  }
  show_report_reflect('Complete Tour Summary');

  function travelers_booking_reflect(tour_group_id, tour_id, id) {
    var tour_id = document.getElementById(tour_id).value;
    var tour_group_id = document.getElementById(tour_group_id).value;

    $("#" + id).html('<option value="">Select Booking ID</option>');

    $.get("reports_content/group_tour/booking_tickets/cancelled_traveler_reflect.php", {
      tour_id: tour_id,
      tour_group_id: tour_group_id
    }, function(data) {
      $("#" + id).html(data);
    });
  }
</script>
<?php
/*======******Footer******=======*/
require_once('../layouts/admin_footer.php');
?>