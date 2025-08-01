<form id="frm_hotel_search">

    <div class="row">
        <input type='hidden' id='page_type' value='search_page' name='search_page' />
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="filterItemSection mb-md-0 mb-3">
                <span class="d-block fs-7 text-secondary mb-1">
                    City*
                </span>
                <div class="c-advanceSelect transparent">
                    <select id='hotel_city_filter' class="js-advanceSelect" name="state">
                        <option value="">City Name</option>
                        <?php
                        foreach ($city_data as $city) {
                            echo '<option value="' . $city['city_id'] . '">' . $city['city_name'] . '</option>';
                        }
                        ?>
                    </select>
                    <!--  as hotel name field is hidden onchange="hotel_names_load(this.id); is removed from select -->
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3" style="display:none">
            <div class="filterItemSection mb-md-0 mb-3">
                <span class="d-block fs-7 text-secondary mb-1">
                    Hotel
                </span>
                <div class="c-advanceSelect transparent">
                    <select id='hotel_name_filter' class="js-advanceSelect" name="state" onchange="hotel_names_load(this.id);">
                        <option value="">Hotel Name</option>

                        <?php

                        $query = "select hotel_id, hotel_name from hotel_master where 1";

                        $sq_hotel = mysqlQuery($query);

                        while ($row_hotel = mysqli_fetch_assoc($sq_hotel)) { ?>

                            <option value="<?php echo $row_hotel['hotel_id'] ?>"><?php echo $row_hotel['hotel_name'] ?></option>

                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12 mb-3">
            <div class="filterItemSection mb-md-0 mb-3">
                <div class="row">
                    <div class="col-md-6 col-sm-12 mb-md-0 mb-3">
                        <span class="d-block fs-7 text-secondary mb-1">
                            Check In*
                        </span>
                        <div class="c-calendar transparent">
                            <div class="input-group date">
                                <input
                                    type="text"
                                    class="form-control js-calendar-date" placeholder="mm/dd/yy" id="checkInDate" onchange='get_to_date("checkInDate","checkOutDate")' required /><span class="input-group-addon"><i
                                        class="fa-sharp fa-solid fa-calendar-days"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 mb-md-0 mb-3">

                        <span class="d-block fs-7 text-secondary mb-1">
                            Check Out* <span class="nytCount" id='total_stay' style='display:none;'></span>
                        </span>

                        <div class="c-calendar transparent">
                            <div class="input-group date">
                                <input
                                    type="text"
                                    class="form-control js-calendar-date" placeholder="mm/dd/yy" id="checkOutDate" onchange='total_nights_reflect();' required /><span class="input-group-addon"><i
                                        class="fa-sharp fa-solid fa-calendar-days"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='block' id='display_addRooms_modal'></div>
        <!-- *** Add Rooms *** -->
        <div class="col-md-4 col-sm-6 col-xs-12" style="display:none">
            <div class="filterItemSection mb-md-0 mb-3">
                <span class="d-block fs-7 text-secondary mb-1">
                    Total Rooms
                </span>

                <div class="roomFilter mb-1" role="button" onclick="display_addRooms_modal()">
                    <span class="fs-6">
                        <span data-x-val="oneway-pax-txt">
                            <span id='total_pax'>2</span>Person in<span id='room_count'>1 Room</span></span>
                        <i class="fa-solid fa-users"></i>
                    </span>
                </div>

            </div>
        </div>
        <div class="col-md-4 col-sm-6 col-xs-12" style="display:none">

            <div class="filterItemSection mb-md-0 mb-3">

                <span class="d-block fs-7 text-secondary mb-1">
                    Hotel Star Catagory
                </span>

                <div class="startRatingGroup">
                    <div class="form-check form-check-inline c-checkGroup">

                        <input class="form-check-input" type="checkbox" id="c1" value="1" name='star_category'>

                        <label class="form-check-label" role="button" for="c1">

                            1 <i class="fa-solid fa-star"></i>

                        </label>

                    </div>

                    <div class="form-check form-check-inline c-checkGroup">

                        <input class="form-check-input" type="checkbox" id="c2" value="2" name='star_category'>

                        <label class="form-check-label" role="button" for="c2">

                            2 <i class="fa-solid fa-star"></i>

                        </label>

                    </div>

                    <div class="form-check form-check-inline c-checkGroup">

                        <input class="form-check-input" type="checkbox" id="c3" value="3" name='star_category'>

                        <label class="form-check-label" role="button" for="c3">

                            3 <i class="fa-solid fa-star"></i>

                        </label>

                    </div>

                    <div class="form-check form-check-inline c-checkGroup">

                        <input class="form-check-input" type="checkbox" id="c4" value="4" name='star_category'>

                        <label class="form-check-label" role="button" for="c4">

                            4 <i class="fa-solid fa-star"></i>

                        </label>

                    </div>

                    <div class="form-check form-check-inline c-checkGroup">

                        <input class="form-check-input" type="checkbox" id="c5" value="5" name='star_category'>

                        <label class="form-check-label" role="button" for="c5">

                            5 <i class="fa-solid fa-star"></i>

                        </label>

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
    <input type='hidden' id='adult_count' name='adult_count' />
    <input type='hidden' id='child_count' name='child_count' />
    <input type='hidden' value='1' id='dynamic_room_count' name='dynamic_room_count' />

</form>