<form id="frm_transfer_search">

  <div class="row">

    <div class="col-12 mb-3">

      <div class="btn-group c-btnGroup" role="group" aria-label="Basic radio toggle button group">
        <input type="radio" class="btn-check" name="transfer_type" value="oneway" id="oneway" autocomplete="off" onclick="fields_enable_disable()" checked>
        <label class="btn btn-outline-secondary" for="oneway">One Way</label>

        <input type="radio" class="btn-check" value="roundtrip" id="roundtrip" name="transfer_type" autocomplete="off" onclick="fields_enable_disable()">
        <label class="btn btn-outline-secondary" for="roundtrip">Round Trip</label>

      </div>

    </div>

    <div class="col-md-4 col-sm-6 col-12">

      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">
          <span class="d-block fs-7 text-secondary mb-1">
            Pickup Location*
          </span>

          <div class="c-select2DD c-advanceSelect transparent">

            <select id='pickup_location' class="full-width js-roomCount js-advanceSelect">

              <option value="">Select Pickup Location</option>

              <optgroup value='city' label="City Name">

                <?php get_cities_dropdown('1'); ?>

              </optgroup>

              <optgroup value='airport' label="Airport Name">

                <?php get_airport_dropdown(); ?>

              </optgroup>

              <optgroup value='hotel' label="Hotel Name">

                <?php get_hotel_dropdown(); ?>

              </optgroup>

            </select>

          </div>

        </div>

      </div>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12 mb-md-3">
      <div class="filterItemSection mb-md-0 mb-3">
        <span class="d-block fs-7 text-secondary mb-1">
          Pickup Date&Time*
        </span>
        <div class="c-calendar transparent">
          <div class="input-group date">
            <input type="text" class="form-control js-calendar-dateTime" placeholder="mm/dd/yy H:i" id="pickup_date" name="pickup_date" /><span class="input-group-addon"><i
                class="fa-sharp fa-solid fa-calendar-days"></i></span>
          </div>
        </div>
      </div>
    </div>




    <div class="col-md-4 col-sm-6 col-12">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">
          <span class="d-block fs-7 text-secondary mb-1">
            Total Passengers*
          </span>
          <input type="number" max="35" name="passengers" class="input-text full-width c-input transparent" placeholder="Total Passengers" id="passengers" style="width: -webkit-fill-available;" />
        </div>
      </div>
    </div>



    <div class="col-md-4 col-sm-6 col-12">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">



          <span class="d-block fs-7 text-secondary mb-1">
            Dropoff Location*
          </span>

          <div class="c-select2DD c-advanceSelect transparent">

            <select id='dropoff_location' class="full-width js-roomCount js-advanceSelect">

              <option value="">Select Drop-Off Location</option>

              <optgroup value='city' label="City Name">

                <?php get_cities_dropdown('1'); ?>

              </optgroup>

              <optgroup value='airport' label="Airport Name">

                <?php get_airport_dropdown(); ?>

              </optgroup>

              <optgroup value='hotel' label="Hotel Name">

                <?php get_hotel_dropdown(); ?>

              </optgroup>

            </select>

          </div>

        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">
          <span class="d-block fs-7 text-secondary mb-1">
            Return Date&Time*
          </span>
          <div class="c-calendar transparent">
            <div class="input-group date">
              <input type="text" class="form-control js-calendar-dateTime" placeholder="mm/dd/yy H:i" id="return_date" name="return_date" onchange="check_valid_date_trs()" /><span class="input-group-addon"><i
                  class="fa-sharp fa-solid fa-calendar-days"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="col-12 mt-3">
      <div class="text-center">
        <button class="btn c-button btn-lg">Search</button>
      </div>
    </div>

  </div>



</form>