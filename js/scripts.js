$(function() {

  

  $('form').attr('autocomplete', 'off');

  $('input').attr('autocomplete', 'off');



  //Lazy Load the cities

  // city_lzloading('#hotel_city_filter,#activities_city_filter,#city,#ffrom_city_filter,#fto_city_filter');

});
$(document).ready(function () {
    // Partner Slider
    $(".pageSlider").owlCarousel({
      items: 1,
      loop:true,
      dots: true,
      smartSpeed: 800, // Duration of the transition
      easing: "easeInOutQuad", // Custom easing function for jQuery Easing
      animateOut: "fadeOut", // Fade out animation
      animateIn: "fadeIn",
      autoplay: true,
      autoplayTimeout: 3000,
      autoplayHoverPause: true,
    });
  
    $(".cardSlider").owlCarousel({
      items: 4,
      nav: true,
      dots: false,
      margin: 24,
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 2,
        },
        768: {
          items: 3,
        },
        960: {
          items: 4,
        },
      },
    });
  
    // Partner Slider
    $(".partnerSlider").owlCarousel({
      items: 8,
      dots: false,
      autoplay: true,
      responsive: {
        0: {
          items: 1,
        },
        420: {
          items: 2,
        },
        560: {
          items: 3,
        },
        768: {
          items: 4,
        },
        960: {
          items: 6,
        },
        1024: {
          items: 8,
        },
      },
    });
  
    // Partner Slider
    $(".cta-slider").owlCarousel({
      items: 1,
      dots: true,
    });
  
    // Select 2
    $(".js-advanceSelect").select2();
  
    // Calendar
    jQuery(".js-calendar-date")
      .datetimepicker({
        format: "m/d/Y",
        timepicker:false,
        minDate: new Date() // Disable past dates
      });
  
      jQuery(".js-calendar-dateTime")
      .datetimepicker({
        format: "m/d/Y H:i",
        minDate: new Date() // Disable past dates
      });

  });
  function multicityrenderInputs(multicityIndex) {
    // Select 2
    $(".js-advanceSelect").select2();
  
    // Calendar
    if(multicityIndex>0)
    {
        var lastmulticityIndex=0;
        var departureDate = jQuery('input[name="multicity['+lastmulticityIndex+'][departureDate]"]').val();

        $(".js-calendar-date-"+multicityIndex)
          .datetimepicker({
            format: "m/d/Y",
            timepicker:false,
            minDate: departureDate // Disable past dates
          });
    }
    else
    {
        $(".js-calendar-date-"+multicityIndex)
          .datetimepicker({
            format: "m/d/Y",
            timepicker:false,
            minDate: new Date() // Disable past dates
          });
    }
  }
  
  function blockSpecialChar(e) {
    var k = e.keyCode;
  
    return (
      (k > 64 && k < 91) ||
      (k > 96 && k < 123) ||
      k == 8 ||
      k == 32 ||
      (k >= 48 && k <= 57)
    );
  }
  
  // Compare best low cost with price-range filter minmax values
  
  function compare(best_lowest_cost, fromRange_cost, toRange_cost) {
    if (
      parseFloat(best_lowest_cost) >= parseFloat(fromRange_cost) &&
      parseFloat(best_lowest_cost) <= parseFloat(toRange_cost)
    )
      return 1;
  }
  
  // JQuery range slider
  
  function reinit(bestlow_cost, besthigh_cost) {
    var randno = "slider_" + new Date().getTime();
  
    $(".slider-input").attr({
      id: randno,
  
      min: bestlow_cost,
  
      max: besthigh_cost,
    });
  
    $(".sliderr-input").jRange("disable");
  
    setTimeout(() => {
      var valueText =
        parseFloat(bestlow_cost).toFixed(2) +
        "," +
        parseFloat(besthigh_cost).toFixed(2);
  
      $("#" + randno).val(valueText);
  
      var rangeMinValue = document.getElementById(randno).min;
  
      var rangeMaxValue = document.getElementById(randno).max;
  
      var rangeStep = $("#" + randno).data("step");
  
      $("#" + randno).jRange({
        from: rangeMinValue,
  
        to: rangeMaxValue,
  
        step: rangeStep,
  
        showLabels: true,
  
        isRange: true,
  
        width: 210,
  
        showScale: true,
  
        onbarclicked: function () {
          passSliderValue();
        },
  
        ondragend: function () {
          passSliderValue();
        },
      });
    }, 1000);
  }
  
  //Generate uuid for each item
  
  function uuidv4() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (c) {
      var r = (Math.random() * 16) | 0,
        v = c == "x" ? r : (r & 0x3) | 0x8;
  
      return v.toString(16);
    });
  }
  
  function pagination_load(
    dataset,
    columns,
    bg_stat = false,
    footer_string = false,
    pg_length = 20,
    table_id
  ) {
    //1. dataset,2.columns titles,3.if want bg color,4.if want footer,5.manual pagelength change
  
    var html = "";
  
    var dataset_main = JSON.parse(dataset);
  
    if (bg_stat) {
      var table_data = [];
  
      var bg = [];
  
      $.each(dataset_main, function (i, item) {
        table_data.push(dataset_main[i].data); // keeping different arrays for data and background color
  
        bg.push(dataset_main[i].bg);
      });
  
      table_data = JSON.parse(JSON.stringify(table_data));
    } else {
      var table_data = JSON.parse(dataset);
    }
  
    if (footer_string) {
      table_data.pop();
  
      if ($.trim($("#" + table_id + " tfoot").html())) {
        document.getElementById(table_id).deleteTFoot();
      }
  
      for (
        var i = 0;
        i <
        parseInt(
          dataset_main[dataset_main.length - 1].footer_data["total_footers"]
        );
        i++
      ) {
        html +=
          "<td colspan='" +
          dataset_main[dataset_main.length - 1].footer_data["col" + i] +
          "'>" +
          dataset_main[dataset_main.length - 1].footer_data["namecol" + i] +
          " : " +
          dataset_main[dataset_main.length - 1].footer_data["foot" + i] +
          "</td>";
      }
  
      html = "<tfoot><tr>" + html + "</tr></tfoot>";
    }
  
    if ($.fn.DataTable.isDataTable("#" + table_id)) {
      $("#" + table_id)
        .DataTable()
        .clear()
        .destroy(); // for managin error
    }
  
    var table = $("#" + table_id).DataTable({
      data: table_data,
  
      pageLength: pg_length,
  
      columns: columns,
  
      searching: true,
  
      createdRow: function (row, data, dataIndex) {
        // adds bg color for every invalid point
  
        if (bg) {
          $(row).addClass(bg[dataIndex]);
        }
      },
    });
  
    $("#" + table_id).append(html);
  }
  
  //City Dropdown Lazy Loading
  
  function city_lzloading(element) {

    var base_url = $("#crm_base_url").val();
  
    url = base_url + "/view/load_data/generic_city_loading.php";

    $(element).select2({
      placeholder: "City Name",
  
      ajax: {
        url: url,
  
        dataType: "json",
  
        type: "GET",
  
        data: function (params) {
          return { term: params.term, page: params.page || 0 };
        },
  
        processResults: function (data) {
          let more = data.pagination;
  
          return {
            results: data.results,
  
            pagination: {
              more: more.more,
            },
          };
        },
      },
    });
  }
  
  //Selected currency rates
  
  function get_currency_rates(to, from) {
    var cache_currencies = JSON.parse($("#cache_currencies").val());
  
    var to_currency =
      cache_currencies.find((el) => el.currency_id === to) !== undefined
        ? cache_currencies.find((el) => el.currency_id === to)
        : "0";
  
    var from_currency =
      cache_currencies.find((el) => el.currency_id === from) !== undefined
        ? cache_currencies.find((el) => el.currency_id === from)
        : "0";
  
    return to_currency.currency_rate + "-" + from_currency.currency_rate;
  }
  
function error_msg_alert(message, base_url = '') {

  if (base_url == '') {

      var base_url1 = $('#base_url').val() + 'Tours_B2B/notification_modal.php';

  } else {

      var base_url1 = base_url + 'notification_modal.php';

  }
$('#site_alert').html('');
$('#site_alert').removeClass();
  $('#site_alert').addClass('error');
  $('#site_alert').vialert({
    type: 'danger',
    title: 'Error',
    message: message,
    delay: 3000
  });

}



function success_msg_alert(message, base_url = '') {

  if (base_url == '') {

      var base_url1 = $('#base_url').val() + 'Tours_B2B/notification_modal.php';

  } else {

      var base_url1 = base_url + 'notification_modal.php';

  }

$('#site_alert').html('');
$('#site_alert').removeClass();
  $('#site_alert').addClass('success');
  $('#site_alert').vialert({
    type: 'danger',
    title: 'Error',
    message: message,
    delay: 3000
  });

}
  //Get Date
  
  function get_to_date1(from_date, to_date) {
    var from_date1 = $("#" + from_date).val();
  
    if (from_date1 != "") {
      var edate = from_date1.split("-");
  
      e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
  
      var currentDate = new Date(
        new Date(e_date).getTime() + 24 * 60 * 60 * 1000
      );
  
      var day = currentDate.getDate();
  
      var month = currentDate.getMonth() + 1;
  
      var year = currentDate.getFullYear();
  
      if (day < 10) {
        day = "0" + day;
      }
  
      if (month < 10) {
        month = "0" + month;
      }
  
      $("#" + to_date).val(day + "-" + month + "-" + year);
    } else {
      $("#" + to_date).val("");
    }
  }
  
  //function for valid date tariff
  
  function validate_validDate1(from, to) {
    var base_url = $("#crm_base_url").val();
  
    var from_date = $("#" + from).val();
  
    var to_date = $("#" + to).val();
  
    var edate = from_date.split("-");
  
    e_date = new Date(edate[2], edate[1] - 1, edate[0]).getTime();
  
    var edate1 = to_date.split("-");
  
    e_date1 = new Date(edate1[2], edate1[1] - 1, edate1[0]).getTime();
  
    var from_date_ms = new Date(e_date).getTime();
  
    var to_date_ms = new Date(e_date1).getTime();
  
    if (from_date_ms > to_date_ms) {
      error_msg_alert("Date should not be greater than valid to date", base_url);
  
      $("#" + from).css({ border: "1px solid red" });
  
      document.getElementById(from).value = "";
  
      $("#" + from).focus();
  
      g_validate_status = false;
  
      return false;
    } else {
      $("#" + from).css({ border: "1px solid #ddd" });
  
      return true;
    }
  
    return true;
  }
  
  
function get_hotel_listing_page(hotel_id){
    
  var base_url = $('#crm_base_url').val();
  var b2c_base_url = $('#base_url').val();
  var hotel_array = [];

  var today = new Date();
  today.setDate(today.getDate());
  var day = today.getDate();
  var month = today.getMonth() + 1;
  var year = today.getFullYear();
  if (day < 10) {
      day = '0' + day;
  }
  if (month < 10) {
      month = '0' + month;
  }
  var today_date = month + "/" + day + "/" + year;

  var tomm = new Date();
  tomm.setDate(tomm.getDate() + 1);
  var day = tomm.getDate();
  var month = tomm.getMonth() + 1
  var year = tomm.getFullYear();
  if (day < 10) {
      day = '0' + day;
  }
  if (month < 10) {
      month = '0' + month;
  }
  var tomm_date = month + "/" + day + "/" + year;

  var final_arr = [];
  final_arr.push({
      rooms : {
          room     : parseInt(1),
          adults   : parseInt(2),
          child    : parseInt(0),
          childAge : []
      }
  });

  hotel_array.push({
      'city_id': '',
      'hotel_id': hotel_id,
      'check_indate': today_date,
      'check_outdate': tomm_date,
      'star_category_arr': [],
      'final_arr': JSON.stringify(final_arr)
  });
  $.post(base_url + 'controller/hotel/b2c_search_session_save.php', { hotel_array: hotel_array }, function(data) {
      window.location.href = b2c_base_url + 'view/hotel/hotel-listing.php';
  });
}

function get_act_listing_page(activity_id){
  
  var base_url = $('#crm_base_url').val();
  var b2c_base_url = $('#base_url').val();
  var activity_array = [];

  var today = new Date();
  today.setDate(today.getDate());
  var day = today.getDate();
  var month = today.getMonth() + 1;
  var year = today.getFullYear();
  if (day < 10) {
      day = '0' + day;
  }
  if (month < 10) {
      month = '0' + month;
  }
  var today_date = month + "/" + day + "/" + year;
  activity_array.push({
      'activity_city_id': '',
      'activities_id': activity_id,
      'checkDate': today_date,
      'adult': parseInt(1),
      'child': parseInt(0),
      'infant': parseInt(0)
  });
  $.post(base_url + 'controller/b2b_excursion/b2b/search_session_save.php', { activity_array: activity_array }, function(data) {
      window.location.href = b2c_base_url + 'view/activities/activities-listing.php';
  });
}

function get_tours_data(dest_id, type) {



  var base_url = $('#crm_base_url').val();

  var b2c_base_url = $('#base_url').val();

  var currency = $('#currency').val();

  var tours_array = [];

  if (type == '1' || type == '2') {



      if (type == '1') {



          var tomorrow = new Date();

          tomorrow.setDate(tomorrow.getDate() + 10);

          var day = tomorrow.getDate();

          var month = tomorrow.getMonth() + 1

          var year = tomorrow.getFullYear();

          if (day < 10) {

              day = '0' + day;

          }

          if (month < 10) {

              month = '0' + month;

          }

          var date = month + "/" + day + "/" + year;



          tours_array.push({

              'dest_id': dest_id,

              'tour_id': '',

              'tour_date': date,

              'adult': parseInt(1),

              'child_wobed': parseInt(0),

              'child_wibed': parseInt(0),

              'extra_bed': parseInt(0),

              'infant': parseInt(0)

          });

      } else if (type == '2') {



          tours_array.push({

              'dest_id': dest_id,

              'tour_id': '',

              'tour_group_id': '',

              'adult': parseInt(1),

              'child_wobed': parseInt(0),

              'child_wibed': parseInt(0),

              'extra_bed': parseInt(0),

              'infant': parseInt(0)

          });

      }

      $.post(base_url + 'controller/custom_packages/search_session_save.php', { tours_array: tours_array, currency: currency }, function(data) {

          if (type == '1') {

              window.location.href = b2c_base_url + 'view/tours/tours-listing.php';

          } else if (type == '2') {

              window.location.href = b2c_base_url + 'view/group_tours/tours-listing.php';

          }

      });

  } else if (type == '3') {



      var hotel_array = [];



      var today = new Date();

      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;



      var tomm = new Date();

      tomm.setDate(tomm.getDate() + 1);

      var day = tomm.getDate();

      var month = tomm.getMonth() + 1

      var year = tomm.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var tomm_date = month + "/" + day + "/" + year;
      var final_arr = [];
      final_arr.push({
          rooms : {
              room     : parseInt(1),
              adults   : parseInt(2),
              child    : parseInt(0),
              childAge : []
          }
      });



      hotel_array.push({

          'city_id': '',

          'hotel_id': '',

          'check_indate': today_date,

          'check_outdate': tomm_date,

          'star_category_arr': [],

          'final_arr': JSON.stringify(final_arr)

      });

      $.post(base_url + 'controller/hotel/b2c_search_session_save.php', { hotel_array: hotel_array }, function(data) {

          window.location.href = b2c_base_url + 'view/hotel/hotel-listing.php';

      });

  } else if (type == '4') {

      var activity_array = [];

      var today = new Date();
      today.setDate(today.getDate());
      var day = today.getDate();
      var month = today.getMonth() + 1;
      var year = today.getFullYear();
      if (day < 10) {
          day = '0' + day;
      }
      if (month < 10) {
          month = '0' + month;
      }
      var today_date = month + "/" + day + "/" + year;
      activity_array.push({
          'activity_city_id': '',
          'activities_id': '',
          'checkDate': today_date,
          'adult': parseInt(1),
          'child': parseInt(0),
          'infant': parseInt(0)
      });
      $.post(base_url + 'controller/b2b_excursion/b2b/search_session_save.php', { activity_array: activity_array }, function(data) {
          window.location.href = b2c_base_url + 'view/activities/activities-listing.php';
      });
  }
  else if (type == '5') {

      var today = new Date();
      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;

      var pick_drop_array = [];
      pick_drop_array.push({

          'trip_type': 'oneway',

          'pickup_type': '',

          'pickup_from': '',

          'drop_type': '',

          'drop_to': '',

          'pickup_date': today_date,

          'return_date': '',

          'passengers': '1'

      })

      $.post(base_url + 'controller/b2b_transfer/b2b/search_session_save.php', { pick_drop_array: pick_drop_array }, function(data) {

          window.location.href = b2c_base_url + 'view/transfer/transfer-listing.php';

      });

  } else if (type == '6') {

      var visa_array = [];

      visa_array.push({

          'country_id': ''

      });

      $.post(base_url + 'controller/visa_master/search_session_save.php', { visa_array: visa_array }, function(data) {

          window.location.href = b2c_base_url + 'view/visa/visa-listing.php';

      });

  } else if (type == '7') {

      var today = new Date();

      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;

      var ferry_array = [];

      ferry_array.push({

          'from_city': '',

          'to_city': '',

          'travel_date': today_date,

          'adult': parseInt(1),

          'children': parseInt(0),

          'infant': parseInt(0)

      })

      $.post(base_url + 'controller/ferry/search_session_save.php', { ferry_array: ferry_array }, function(data) {

          window.location.href = b2c_base_url + 'view/ferry/ferry-listing.php';

      });

  }

}

  $(function () {
    $("#contact_us_form").validate({
      rules: {},
  
      submitHandler: function (form) {
        $("#contact_form_send").prop("disabled", true);
  
        var crm_base_url = $("#crm_base_url").val();
        var base_url = $("#base_url").val();
        var name = $("#inputName").val();
        var email_id = $("#inputEmail1").val();
        var phone = $("#inputPhone").val();
        var state = $("#inputState").val();
        var message = $("#inputMessage").val();
  
        if (
          name == "" ||
          email_id == "" ||
          phone == "" ||
          state == "" ||
          message == ""
        ) {
          $("#contact_form_send").prop("disabled", false);
          return false;
        }
  
        $("#contact_form_send").button("loading");
  
        $.post(
          crm_base_url + "controller/b2c_settings/b2c/contact_form_mail.php",
          {
            name: name,
            email_id: email_id,
            phone: phone,
            state: state,
            message: message,
          },
          function (result) {
            $("#contact_form_send").button("reset");
  
            $("#contact_form_send").prop("disabled", false);
  
            success_msg_alert(result, base_url);
            $("#inputName").val("");
  
            $("#inputEmail1").val("");
  
            $("#inputPhone").val("");
  
            $("#inputState").val("");
  
            $("#inputMessage").val("");
  
            setTimeout(() => {
              location.reload();
            }, 2000);
          }
        );
      },
    });
  });
  
  $(function () {
    $("#getInTouch_form").validate({
      rules: {},
  
      submitHandler: function (form) {
        $("#getInTouch_btn").prop("disabled", true);
  
        var crm_base_url = $("#crm_base_url").val();
  
        var base_url = $("#base_url").val();
  
        var name = $("#inputNamep").val();
  
        var email_id = $("#inputEmailp").val();
  
        var phone = $("#inputPhonep").val();
  
        var message = $("#inputMessagep").val();
  
        var package_name = $("#package_name").val();
  
        if (name == "" || email_id == "" || phone == "" || message == "") {
          $("#getInTouch_btn").prop("disabled", false);
  
          return false;
        }
  
        $("#getInTouch_btn").button("loading");
  
        $.post(
          crm_base_url + "controller/b2c_settings/b2c/contact_form_mail.php",
          {
            name: name,
            email_id: email_id,
            phone: phone,
            message: message,
            package_name: package_name,
          },
          function (result) {
            $("#getInTouch_btn").button("reset");
            $("#getInTouch_btn").prop("disabled", false);
  
            success_msg_alert(result, base_url);
  
            $("#inputNamep").val("");
            $("#inputEmailp").val("");
            $("#inputPhonep").val("");
            $("#inputMessagep").val("");
  
            setTimeout(() => {
              location.reload();
            }, 2000);
          }
        );
      },
    });
  });
  
$("#single_tour_enq_form").validate({
    rules: {},

    submitHandler: function(form) {
        $("#getInTouch_btn").prop("disabled", true);
        var crm_base_url = $("#crm_base_url").val();

        var base_url = $("#base_url").val();

        var name = $("#inputNamep").val();

        var email_id = $("#inputEmailp").val();

        var phone = $("#inputPhonep").val();

        var package_name = $("#package_name").val();

        if (name == "" || email_id == "" || phone == "") {
            $("#getInTouch_btn").prop("disabled", false);
            return false;
        } 

        $("#getInTouch_btn").button("loading");

        $.post(
            crm_base_url + "controller/b2c_settings/b2c/single_tour_form_mail.php", {
                name: name,
                email_id: email_id,
                phone: phone,
                package_name: package_name,
            },
            function(result) {
                $("#getInTouch_btn").button("reset");
                $("#getInTouch_btn").prop("disabled", false);

                success_msg_alert(result, base_url);

                $("#inputNamep").val("");
                $("#inputEmailp").val("");
                $("#inputPhonep").val("");

                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        );
    },
});

  //Set selected currency in local/session storage
  
  function get_selected_currency() {
    var base_url = $("#base_url").val();
  
    var currency_id = $("#currency").val();
    
    //Set selected currency in php session also
  
    $.post(
      base_url + "view/set_currency_session.php",
      { currency_id: currency_id },
      function (data) {}
    );
  
    if (typeof Storage !== "undefined") {
      if (localStorage) {
        localStorage.setItem("global_currency", currency_id);
      } else {
        window.sessionStorage.setItem("global_currency", currency_id);
      }
    }
  
    // Call respective currency converter according active page url
  
    var current_page_url = document.URL;
  
    var tours_pageurl = base_url + "view/tours/tours-listing.php";
  
    if (
      current_page_url.split(base_url + "package_tours").length - 1 == 1 ||
      tours_pageurl == current_page_url
    ) {
      tours_page_currencies(current_page_url);
    }
  
    var tours_pageurl = base_url + "view/group_tours/tours-listing.php";
  
    if (
      current_page_url.split(base_url + "group_tours").length - 1 == 1 ||
      tours_pageurl == current_page_url
    ) {
      group_tours_page_currencies(current_page_url);
    }
  
    location.reload();
  }
  
  function clearRange() {
    var ans_arr = $("#price_rangevalues").val();
  
    ans_arr = ans_arr.split(",");
  
    if (ans_arr[0] == ans_arr[1]) {
      var bestlow_cost = 0;
  
      var besthigh_cost = ans_arr[0];
    } else if (parseFloat(ans_arr[1]) > parseFloat(ans_arr[0])) {
      var bestlow_cost = ans_arr[0];
  
      var besthigh_cost = ans_arr[1];
    } else {
      var bestlow_cost = ans_arr[1];
  
      var besthigh_cost = ans_arr[0];
    }
  
    $(".c-priceRange").next("div").remove();
  
    reinit(bestlow_cost, besthigh_cost);
  }
  
function postRedirect(url, data) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
  
    // Add all data as hidden inputs
    for (const key in data) {
      if (data.hasOwnProperty(key)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = data[key];
        form.appendChild(input);
      }
    }
  
    document.body.appendChild(form);
    form.submit();
  }
  function redirect_to_action_page(
    package_id,
    type,
    package_type,
    adult_count,
    child_wocount,
    child_wicount,
    extra_bed_count,
    infant_count,
    travel_date,
    group_id = ""
  ) {
    var base_url = $("#base_url").val();
  
    if (type == "2") {
      if (group_id == "0") {
        var len = $('input[name="chk_date"]:checked').length;
  
        if (len === 0) {
          error_msg_alert("Select atleast one tour date to proceed!", base_url);
  
          return false;
        }
  
        var id = $('input[name="chk_date"]:checked').attr("id");
  
        var group_id = id.split("-");
  
        group_id = group_id[1];
  
        travel_date = $('input[name="chk_date"]:checked').attr("value");
      } else {
        var total_pax =
          parseInt(adult_count) +
          parseInt(child_wocount) +
          parseInt(child_wicount) +
          parseInt(extra_bed_count) +
          parseInt(infant_count);
  
        var html = document.getElementById("aseats").innerHTML;
  
        var avail_seats = html.split(":");
  
        avail_seats = avail_seats[1];
  
        if (parseInt(total_pax) > parseInt(avail_seats)) {
          error_msg_alert(
            total_pax + " seat(s) not available for this tour!",
            base_url
          );
  
          return false;
        }
      }
    }


    const actionData =  {
        package_id: package_id,
        type: type,
        package_type:package_type,
        adult_count: adult_count,
        child_wocount: child_wocount,
        child_wicount: child_wicount,
        extra_bed_count: extra_bed_count,
        infant_count: infant_count,
        travel_date: travel_date,
        group_id: group_id
    };
    postRedirect(base_url + 'action.php', actionData);
  }
  
  function enq_to_action_page(type, item_id, enq_data) {
    var base_url = $("#base_url").val();
  
    if (type == "6") {
      var visa_type_arr = [];
  
      var input_name = "result_day-" + item_id;
  
      $("input[name=" + input_name + "]:checked").each(function () {
        visa_type_arr.push($(this).val());
      });
  
      if (visa_type_arr.length == 0) {
        error_msg_alert("Please select at least one visa type!", base_url);
  
        return false;
      }
  
      enq_data.push(visa_type_arr[0]);
    }
  
    enq_data = JSON.stringify(enq_data);
  
    window.location =
      base_url +
      "action.php?item_id=" +
      item_id +
      "&type=" +
      type +
      "&enq_data=" +
      enq_data;
  }
