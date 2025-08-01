<form id="frm_activities_search">

  <div class="row">

    <input type='hidden' id='page_type' value='search_page' name='search_page' />

    <!-- *** City Name *** -->

    <div class="col-md-3 col-sm-6 col-12 mb-3">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">

          <span class="d-block fs-7 text-secondary mb-1">
            City*
          </span>

          <div class="c-select2DD c-advanceSelect transparent">

            <select id='activities_city_filter' class="full-width js-roomCount js-advanceSelect" onchange="activities_names_load(this.id);">
              <option value="">City Name</option>

              <?php
              foreach ($city_data as $city) {
                echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
              }
              ?>
            </select>

          </div>

        </div>
      </div>
    </div>

    <!-- *** City Name End *** -->

    <!-- *** Activities Name *** -->

    <div class="col-md-3 col-sm-6 col-12 mb-3" style="display:none">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">


          <span class="d-block fs-7 text-secondary mb-1">
            Activity
          </span>

          <div class="c-select2DD c-advanceSelect transparent">

            <select id='activities_name_filter' class="full-width js-roomCount js-advanceSelect">

              <option value=''>Activity Name</option>

              <?php

              $query = "select entry_id, excursion_name from excursion_master_tariff where 1";

              $sq_act = mysqlQuery($query);

              while ($row_act = mysqli_fetch_assoc($sq_act)) {

              ?>

                <option value="<?php echo $row_act['entry_id'] ?>"><?php echo $row_act['excursion_name'] ?></option>

              <?php } ?>

            </select>

          </div>

        </div>
      </div>
    </div>

    <!-- *** Activities Name End *** -->



    <div class="col-md-3 col-sm-6 col-12 mb-3">

      <div class="filterItemSection mb-md-0 mb-3">

        <div class="form-group">

          <span class="d-block fs-7 text-secondary mb-1">
          Date*
                </span>
                <div class="c-calendar transparent">
                <div class="input-group date js-calendar">
                    <input
                    type="text"
                    class="form-control js-calendar-date" placeholder="mm/dd/yyyy" id="checkDate"  required /><span class="input-group-addon"><i
                        class="fa-sharp fa-solid fa-calendar-days"></i></span>
                </div>
          
                </div>

        </div>

      </div>
    </div>

    <!-- *** Date End *** -->



    <!-- *** Adult *** -->

    <div class="col-md-3 col-sm-6 col-12 mb-3">
      <div class="filterItemSection mb-md-0 mb-3">

        <div class="form-group">


          <span class="d-block fs-7 text-secondary mb-1">
            Adults*
          </span>

          <div class="selector c-advanceSelect transparent">

            <select name="adult" id='adult' class="full-width js-advanceSelect" required>

              <?php for ($i = 0; $i <= 20; $i++) { ?>

                <option value="<?= $i ?>"><?= $i ?></option>

              <?php } ?>

            </select>

          </div>

        </div>

      </div>
    </div>

    <!-- *** Adult End *** -->

    <!-- *** Child *** -->

    <div class="col-md-3 col-sm-6 col-12" style="display:none">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">

          <span class="d-block fs-7 text-secondary mb-1">
            Children(2-12 Yrs)
          </span>

          <div class="selector c-advanceSelect transparent">

            <select name="child" id='child' class="full-width js-advanceSelect">

              <?php for ($i = 0; $i <= 20; $i++) { ?>

                <option value="<?= $i ?>"><?= $i ?></option>

              <?php } ?>

            </select>

          </div>

        </div>
      </div>
    </div>

    <!-- *** Child End *** -->

    <!-- *** Infant *** -->

    <div class="col-md-3 col-sm-6 col-12" style="display:none">
      <div class="filterItemSection mb-md-0 mb-3">
        <div class="form-group">
          <span class="d-block fs-7 text-secondary mb-1">
            Infants(0-2 Yrs)
          </span>

          <div class="selector c-advanceSelect transparent">

            <select name="infant" id='infant' class="full-width js-advanceSelect">

              <?php for ($i = 0; $i <= 20; $i++) { ?>

                <option value="<?= $i ?>"><?= $i ?></option>

              <?php } ?>

            </select>

          </div>

        </div>
      </div>
    </div>

    <!-- *** Infant End *** -->

    <!-- <div class="col-lg-3 col-md-4 col-sm-6 col-12">

            <button class="c-button lg colGrn m26-top m15-top">

                <i class="icon itours-search"></i> SEARCH NOW

            </button>

        </div> -->

    <div class="col-12 mt-3">
      <div class="text-center">
        <button class="btn c-button btn-lg">Search</button>
      </div>
    </div>

  </div>

</form>