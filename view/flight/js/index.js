
  var attendantModalType = 'oneway';

  function attendantModalLoader(type) {
    
    attendantModalType = type;

    var adult = jQuery('[data-x-val="'+attendantModalType+'-adult"]').val() ?? 0;
    var child = jQuery('[data-x-val="'+attendantModalType+'-child"]').val() ?? 0;
    var infant = jQuery('[data-x-val="'+attendantModalType+'-infant"]').val() ?? 0;
    var travelClass = jQuery('[data-x-val="'+attendantModalType+'-travelClass"]').val() ?? 'Economy';

    jQuery('[data-x-input="adult"]').val(adult);
    jQuery('[data-x-input="child"]').val(child);
    jQuery('[data-x-input="infant"]').val(infant);
    jQuery('[data-x-input="travelClass"][value="'+travelClass+'"]').prop('checked', true);
  }

  function attendantModalUpdater(){
    var base_url = $('#base_url').val();
    var adult = jQuery('[data-x-input="adult"]').val() ?? 0;
    var child = jQuery('[data-x-input="child"]').val() ?? 0;
    var infant = jQuery('[data-x-input="infant"]').val() ?? 0;
    var travelClass = jQuery('[data-x-input="travelClass"]:checked').val() ?? 0;
    
    let isValid = true; 
    var text = [];

    if(infant > 0 ){
      adult = adult > 0 ? adult : 1;
      text.push(infant+' Infant' + (infant > 1 ? 's' : ''));
    }

    if(child > 0){
      text.push(child+' Child' + (child > 1 ? 'ren' : ''));
    }

    if(adult > 0){
      text.push(adult+' Adult' + (adult > 1 ? 's' : ''));
    }
    var pass = parseInt(adult) + parseInt(child) + parseInt(infant);
    if(pass > 10){
      isValid = false;
      error_msg_alert('More than 10 travellers are not allowed!',base_url);
      return false;
    }
    if (isValid) {
      const modal = bootstrap.Modal.getInstance(document.getElementById('attendantModal'));
      modal.hide();
    }

    jQuery('[data-x-val="'+attendantModalType+'-pax-txt"]').html(text.reverse().join(', '));
    jQuery('[data-x-val="'+attendantModalType+'-travelClass-txt"]').html(travelClass)

    jQuery('[data-x-val="'+attendantModalType+'-adult"]').val(adult);
    jQuery('[data-x-val="'+attendantModalType+'-child"]').val(child);
    jQuery('[data-x-val="'+attendantModalType+'-infant"]').val(infant);
    jQuery('[data-x-val="'+attendantModalType+'-travelClass"]').val(travelClass);
  }
  window.addEventListener('load', function () {
    jQuery('[data-x-input="oneway-from"]').on('change', function () {
      jQuery('[data-x-val="oneway-from"]').html( jQuery('[data-x-input="oneway-from"] option:selected').data('title') );
    });

    jQuery('[data-x-input="oneway-to"]').on('change', function () {
      jQuery('[data-x-val="oneway-to"]').html( jQuery('[data-x-input="oneway-to"] option:selected').data('title') );
    });

    jQuery('[data-x-input="oneway-departureDate"]').on('change', function () {
      var dt = jQuery('[data-x-input="oneway-departureDate"]').val();
      var dateParts = dt.split("/");
      var dt = new Date(+dateParts[2],+dateParts[0]-1,+dateParts[1]);
      jQuery('[data-x-val="oneway-departureDate"]').html( ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][dt.getDay()] );
    });

    jQuery('[data-x-input="roundTrip-from"]').on('change', function () {
      jQuery('[data-x-val="roundTrip-from"]').html( jQuery('[data-x-input="roundTrip-from"] option:selected').data('title') );
    });

    jQuery('[data-x-input="roundTrip-to"]').on('change', function () {
      jQuery('[data-x-val="roundTrip-to"]').html( jQuery('[data-x-input="roundTrip-to"] option:selected').data('title') );
    });

    jQuery('[data-x-input="roundTrip-departureDate"]').on('change', function () {
      var dt = jQuery('[data-x-input="roundTrip-departureDate"]').val();
      var dateParts = dt.split("/");
      var dt = new Date(+dateParts[2],+dateParts[0]-1,+dateParts[1]);
      jQuery('[data-x-val="roundTrip-departureDate"]').html( ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][dt.getDay()] );
    });
    
    jQuery('[data-x-input="roundTrip-returnDate"]').on('change', function () {
      var dt = jQuery('[data-x-input="roundTrip-returnDate"]').val();
      var dateParts = dt.split("/");
      var dt = new Date(+dateParts[2],+dateParts[0]-1,+dateParts[1]);
      jQuery('[data-x-val="roundTrip-returnDate"]').html( ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][dt.getDay()] );
    });
  });

  var multicityIndex = 0;

  function addMultiCityRow(data = {}){
    var html = `<div class="row align-items-center mb-3" data-multicity-index="${multicityIndex}">
      <div class="col-md-11 col-sm-12">
        <div class="filterItemSection mb-0">
          <input type="hidden" name="multicity[${multicityIndex}][adult]" data-x-val="multicity-adult" value="1" />
          <input type="hidden" name="multicity[${multicityIndex}][child]" data-x-val="multicity-child" value="0" />
          <input type="hidden" name="multicity[${multicityIndex}][infant]" data-x-val="multicity-infant" value="0" />
          <input type="hidden" name="multicity[${multicityIndex}][travelClass]" data-x-val="multicity-travelClass" value="Economy" />

          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <span class="d-block fs-7 text-secondary mb-1">
                From*
              </span>
              <div class="c-advanceSelect transparent mb-1">
                <select class="js-advanceSelect" name="multicity[${multicityIndex}][from]" data-x-input="multicity${multicityIndex}-from">
                  <option value="" data-title="Please Select From">Select</option>
                  ${airportOptions}
                </select>
              </div>
              <span class="fs-8 fw-medium text-secondary" data-x-val="multicity${multicityIndex}-from">Please Select From</span>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <span class="d-block fs-7 text-secondary mb-1">
                To*
              </span>
              <div class="c-advanceSelect transparent mb-1">
                <select class="js-advanceSelect" name="multicity[${multicityIndex}][to]" data-x-input="multicity${multicityIndex}-to">
                  <option value="" data-title="Please Select To">Select</option>
                  ${airportOptions}
                </select>
              </div>
              <span class="fs-8 fw-medium text-secondary" data-x-val="multicity${multicityIndex}-to">Please Select To</span>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <span class="d-block fs-7 text-secondary mb-1">
                Departure*
              </span>
              <div class="c-calendar transparent mb-1">
                <div class="input-group date js-calendar-${multicityIndex}">
                  <input type="text" class="form-control js-calendar-date-${multicityIndex}" name="multicity[${multicityIndex}][departureDate]" data-x-input="multicity${multicityIndex}-departureDate" placeholder="mm/dd/yy" />
                  <span class="input-group-addon">
                    <i class="fa-sharp fa-solid fa-calendar-days"></i>
                  </span>
                </div>
              </div>
              <span class="fs-8 fw-medium text-secondary" data-x-val="multicity${multicityIndex}-departureDate">
                <?php echo date('l'); ?>
              </span>
            </div>
            ${multicityIndex == 0 ? `<div class="col-md-3 col-sm-6 col-xs-12"><span class="d-block fs-7 text-secondary mb-1">Traveller & Class*</span><div class="roomFilter mb-1" data-bs-toggle="modal" data-bs-target="#attendantModal" role="button" onclick="attendantModalLoader('multicity')"><span class="fs-6"><span data-x-val="multicity-pax-txt">1 Adult, 0 child</span><i class="fa-solid fa-users"></i></div><span class="fs-8 fw-medium text-secondary"><span data-x-val="multicity-travelClass-txt">Economy</span> Class</span></div>` : ''}


          </div>
        </div>
      </div>
      <div class="col-md-1 col-sm-12 mt-md-0 mt-2">
        <div class="d-flex flex-row gap-2 justify-content-center justify-content-md-start">
          ${multicityIndex == 0 ? '<button type="button" class="settingButton" onclick="addMultiCityRow()"><i class="fa-sharp fa-solid fa-plus"></i></button>' : ''}
          ${multicityIndex > 0 ? '<button type="button" class="settingButton" onclick="deleteMultiCityRow(this)"><i class="fa-sharp fa-solid fa-trash"></i></button>' : ''}
        </div>
      </div>
    </div>`;
    jQuery('#multicity-container-row').append(html);

    jQuery('[data-x-input="multicity'+multicityIndex+'-from"]').on('change', function () {
      var closestIndex = jQuery(this).closest('[data-multicity-index]').data('multicity-index');
      jQuery('[data-x-val="multicity'+closestIndex+'-from"]').html( jQuery('[data-x-input="multicity'+closestIndex+'-from"] option:selected').data('title') );
    });

    jQuery('[data-x-input="multicity'+multicityIndex+'-to"]').on('change', function () {
      var closestIndex = jQuery(this).closest('[data-multicity-index]').data('multicity-index');
      jQuery('[data-x-val="multicity'+closestIndex+'-to"]').html( jQuery('[data-x-input="multicity'+closestIndex+'-to"] option:selected').data('title') );
    });

    jQuery('[data-x-input="multicity'+multicityIndex+'-departureDate"]').on('change', function () {
      var closestIndex = jQuery(this).closest('[data-multicity-index]').data('multicity-index');
      var dt = jQuery('[data-x-input="multicity'+closestIndex+'-departureDate"]').val();
      var dateParts = dt.split("/");
      var dt = new Date(+dateParts[2],+dateParts[0]-1,+dateParts[1]);
      jQuery('[data-x-val="multicity'+closestIndex+'-departureDate"]').html( ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][dt.getDay()] );
    });
    multicityrenderInputs(multicityIndex);
    multicityIndex++;
  }

  function deleteMultiCityRow(initiator){
    jQuery(initiator).closest('[data-multicity-index]').remove();
  }

  addMultiCityRow();
  $(document).ready(function() {

    // Oneway form validation
    document.getElementById('oneway-container').addEventListener('submit', function (e) {
      const base_url = $('#base_url').val();
      const one_from_date = $('#onedepartureDate').val();   
      const onewayTrip_from = $('#oneway_from').val();   
      const onewayTrip_to = $('#oneway_to').val();           
      if (!onewayTrip_from || !onewayTrip_to) {
        e.preventDefault();
        error_msg_alert('Please select both from-to airport.', base_url);
        return;
      }
      if (!one_from_date) {
        e.preventDefault();
        error_msg_alert('Please select departure date.', base_url);
        return;
      }

    });
    // Roundtrip form validation
    document.getElementById('roundtrip-container').addEventListener('submit', function (e) {
      const base_url = $('#base_url').val();
      const from_date = $('#departureDate').val();   
      const to_date = $('#returnDate').val();   
      const roundTrip_from = $('#round_from').val();   
      const roundTrip_to = $('#round_to').val();           
      if (!roundTrip_from || !roundTrip_to) {
        e.preventDefault();
        error_msg_alert('Please select both from-to airport.', base_url);
        return;
      }
      if (!from_date || !to_date) {
        e.preventDefault();
        error_msg_alert('Please select departure and return dates.', base_url);
        return;
      }

      const fromParts = from_date.split('/');
      const toParts = to_date.split('/');

      const fromDateObj = new Date(fromParts[2], fromParts[0] - 1, fromParts[1]);
      const toDateObj = new Date(toParts[2], toParts[0] - 1, toParts[1]);

      if (toDateObj < fromDateObj) {
        e.preventDefault();
        error_msg_alert('Return date must be after departure date.!', base_url);
      }
    });

    document.getElementById('multicity-container').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent form by default
    let isValid = true;
    const base_url = $('#base_url').val(); // Optional for error_msg_alert
    let prevDate = null;

    $('#multicity-container-row .row[data-multicity-index]').each(function (i) {
      const index = $(this).data('multicity-index');
      const from = $(this).find('select[name="multicity[' + index + '][from]"]').val();
      const to = $(this).find('select[name="multicity[' + index + '][to]"]').val();
      const depDate = $(this).find('input[name="multicity[' + index + '][departureDate]"]').val();

      // Basic validation
      if (!from) {
        error_msg_alert(`Please select from airport at row ${i + 1}`, base_url);
        isValid = false;
        return false; // break .each
      }
      if (!to) {
        error_msg_alert(`Please select to airport at row ${i + 1}`, base_url);
        isValid = false;
        return false;
      }
      if (!depDate) {
        error_msg_alert(`Please select departure date at row ${i + 1}`, base_url);
        isValid = false;
        return false;
      }

      // Optional: check if dates are in ascending order
      const parts = depDate.split('/');
      const currentDate = new Date(parts[2], parts[0] - 1, parts[1]).getTime();
      if (prevDate && currentDate < prevDate) {
        error_msg_alert(`Departure at row ${i + 1} must be after previous row`, base_url);
        isValid = false;
        return false;
      }
      prevDate = currentDate;
    });

    if (isValid) {
      this.submit(); // Allow form to submit only if valid
    }
  });

  });
