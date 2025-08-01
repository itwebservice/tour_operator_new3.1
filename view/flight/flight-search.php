<?php ob_start(); ?>
<?php foreach ($airportsResultdata as $row) { ?>
    <option value="<?= $row['code'] ?>" data-title="<?= $row['city'] ?>, <?= $row['country'] ?>">
        <?= $row['city'] ?> (<?= $row['citycode'] ?>) / <?= $row['name'] ?>
    </option>
<?php } ?>
<?php $airportOptions = ob_get_clean(); ?>
<script>
    var airportOptions = `<?= $airportOptions ?>`;
</script>
<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button
            class="nav-link filterButton fs-7 active"
            id="flight-oneway-tab"
            data-bs-toggle="tab"
            data-bs-target="#flight-oneway"
            type="button"
            role="tab"
            aria-controls="flight-oneway"
            aria-selected="true">
            One Way
        </button>
        <button
            class="nav-link filterButton fs-7"
            id="flight-roundTrip-tab"
            data-bs-toggle="tab"
            data-bs-target="#flight-roundTrip"
            type="button"
            role="tab"
            aria-controls="flight-roundTrip"
            aria-selected="false">
            Round Trip
        </button>
        <button
            class="nav-link filterButton fs-7"
            id="flight-multicity-tab"
            data-bs-toggle="tab"
            data-bs-target="#flight-multicity"
            type="button"
            role="tab"
            aria-controls="flight-multicity"
            aria-selected="false">
            Multi City
        </button>
    </div>
</nav>
<div class="tab-content" id="nav-tabContent">
    <!-- ***** Flight ***** -->
    <div
        class="tab-pane fade show active"
        id="flight-tab-pane"
        role="tabpanel"
        aria-labelledby="flight-tab"
        tabindex="0">

        <div class="tab-content" id="nav-tabContent">
            <!-- ***** Flight Filter Tabs - Oneway ***** -->
            <div
                class="tab-pane fade show active"
                id="flight-oneway"
                role="tabpanel"
                aria-labelledby="flight-oneway-tab"
                tabindex="0">
                <form method="get" id="oneway-container" action="view/flight/list.php" class="row g-2">
                    <input type="hidden" name="searchType" value="oneway" />

                    <input type="hidden" name="adult" data-x-val="oneway-adult" value="1" />
                    <input type="hidden" name="child" data-x-val="oneway-child" value="0" />
                    <input type="hidden" name="infant" data-x-val="oneway-infant" value="0" />
                    <input type="hidden" name="travelClass" data-x-val="oneway-travelClass" value="Economy" />

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                From*
                            </span>
                            <div class="c-advanceSelect transparent mb-1">
                                <select class="js-advanceSelect" name="from" data-x-input="oneway-from" id="oneway_from">
                                    <option value="" data-title="Please Select From">Select</option>
                                    <?php foreach ($airportsResultdata as $row) { ?>
                                        <option value="<?php echo $row['code']; ?>" data-title="<?php echo $row['city']; ?>, <?php echo $row['country']; ?>"><?php echo $row['city']; ?> (<?php echo $row['citycode']; ?>) / <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span class="fs-8 fw-medium text-secondary" data-x-val="oneway-from">Please Select From</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                To*
                            </span>
                            <div class="c-advanceSelect transparent mb-1">
                                <select class="js-advanceSelect" name="to" data-x-input="oneway-to" id="oneway_to">
                                    <option value="" data-title="Please Select To">Select</option>
                                    <?php foreach ($airportsResultdata as $row) { ?>
                                        <option value="<?php echo $row['code']; ?>" data-title="<?php echo $row['city']; ?>, <?php echo $row['country']; ?>"><?php echo $row['city']; ?> (<?php echo $row['citycode']; ?>) / <?php echo $row['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span class="fs-8 fw-medium text-secondary" data-x-val="oneway-to">Please Select To</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                Departure*
                            </span>
                            <div class="c-calendar transparent mb-1">
                                <div class="input-group date js-calendar">
                                    <input
                                        type="text"
                                        name="departureDate"
                                        class="form-control js-calendar-date" placeholder="mm/dd/yy" id="onedepartureDate"
                                        data-x-input="oneway-departureDate" /><span class="input-group-addon"><i
                                            class="fa-sharp fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div>
                            <span class="fs-8 fw-medium text-secondary" data-x-val="oneway-departureDate">
                                <?php echo date('l'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                Traveller & Class*
                            </span>
                            <div
                                class="roomFilter mb-1"
                                data-bs-toggle="modal"
                                data-bs-target="#attendantModal"
                                role="button"
                                onclick="attendantModalLoader('oneway')">
                                <span class="fs-6">
                                    <span data-x-val="oneway-pax-txt">1 Adult, 0 child</span>
                                    <i class="fa-solid fa-users"></i>
                                </span>
                            </div>
                            <span class="fs-8 fw-medium text-secondary">
                                <span data-x-val="oneway-travelClass-txt">Economy</span> Class
                            </span>
                            <!-- Modal -->
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="text-center">
                            <button class="btn c-button btn-lg" type="submit">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- ***** Flight Filter Tabs - Oneway End ***** -->

            <!-- ***** Flight Filter Tabs - Round trip ***** -->
            <div
                class="tab-pane fade"
                id="flight-roundTrip"
                role="tabpanel"
                aria-labelledby="flight-roundTrip-tab"
                tabindex="0">
                <form method="get" id="roundtrip-container" action="view/flight/list.php" class="row g-2">
                    <input type="hidden" name="searchType" value="round" />
                    <input type="hidden" name="adult" data-x-val="roundTrip-adult" value="1" />
                    <input type="hidden" name="child" data-x-val="roundTrip-child" value="0" />
                    <input type="hidden" name="infant" data-x-val="roundTrip-infant" value="0" />
                    <input type="hidden" name="travelClass" data-x-val="roundTrip-travelClass" value="Economy" />

                    <div class="col-md-5 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-0">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-md-0 mb-3">
                                    <span
                                        class="d-block fs-7 text-secondary mb-1">
                                        From*
                                    </span>
                                    <div class="c-advanceSelect transparent mb-1">
                                        <select class="js-advanceSelect" name="from" id="round_from" data-x-input="roundTrip-from">
                                            <option value="" data-title="Please Select From">Select</option>
                                            <?php foreach ($airportsResultdata as $row) { ?>
                                                <option value="<?php echo $row['code']; ?>" data-title="<?php echo $row['city']; ?>, <?php echo $row['country']; ?>"><?php echo $row['city']; ?> (<?php echo $row['citycode']; ?>) / <?php echo $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <span class="fs-8 fw-medium text-secondary" data-x-val="roundTrip-from">Please Select From</span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <span
                                        class="d-block fs-7 text-secondary mb-1">
                                        To*
                                    </span>
                                    <div class="c-advanceSelect transparent mb-1">
                                        <select class="js-advanceSelect" name="to" id="round_to" data-x-input="roundTrip-to">
                                            <option value="" data-title="Please Select To">Select</option>
                                            <?php foreach ($airportsResultdata as $row) { ?>
                                                <option value="<?php echo $row['code']; ?>" data-title="<?php echo $row['city']; ?>, <?php echo $row['country']; ?>"><?php echo $row['city']; ?> (<?php echo $row['citycode']; ?>) / <?php echo $row['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <span class="fs-8 fw-medium text-secondary" data-x-val="roundTrip-to">Please Select To</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                Departure*
                            </span>
                            <div class="c-calendar transparent mb-1">
                                <div class="input-group date js-calendar">
                                    <input
                                        type="text"
                                        class="form-control js-calendar-date"
                                        name="departureDate" id="departureDate" placeholder="mm/dd/yy"
                                        data-x-input="roundTrip-departureDate" />
                                    <span class="input-group-addon">
                                        <i class="fa-sharp fa-solid fa-calendar-days"></i>
                                    </span>
                                </div>
                            </div>
                            <span class="fs-8 fw-medium text-secondary" data-x-val="roundTrip-departureDate">
                                <?php echo date('l'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                Return*
                            </span>
                            <div class="c-calendar transparent mb-1">
                                <div class="input-group date js-calendar">
                                    <input
                                        type="text"
                                        class="form-control js-calendar-date"
                                        name="returnDate" id="returnDate" placeholder="mm/dd/yy"
                                        data-x-input="roundTrip-returnDate" /><span class="input-group-addon"><i
                                            class="fa-sharp fa-solid fa-calendar-days"></i></span>
                                </div>
                            </div>
                            <span class="fs-8 fw-medium text-secondary" data-x-val="roundTrip-returnDate">
                                <?php echo date('l'); ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm6 col-xs-12">
                        <div class="filterItemSection mb-md-0">
                            <span class="d-block fs-7 text-secondary mb-1">
                                Traveller & Class*
                            </span>
                            <div
                                class="roomFilter mb-1"
                                data-bs-toggle="modal"
                                data-bs-target="#attendantModal"
                                role="button"
                                onclick="attendantModalLoader('roundTrip')">
                                <span class="fs-6"><span data-x-val="roundTrip-pax-txt">1 Adult, 0 child</span>
                                    <i class="fa-solid fa-users"></i>
                                </span>
                            </div>
                            <span class="fs-8 fw-medium text-secondary">
                                <span data-x-val="roundTrip-travelClass-txt">Economy</span> Class
                            </span>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="text-center">
                            <button class="btn c-button btn-lg">
                                Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- ***** Flight Filter Tabs - Round trip End ***** -->

            <!-- ***** Flight Filter Tabs - Multi city trip ***** -->
            <div
                class="tab-pane fade"
                id="flight-multicity"
                role="tabpanel"
                aria-labelledby="flight-multicity-tab"
                tabindex="0">
                <form id="multicity-container" method="get" action="view/flight/list.php" class="row g-2">
                    <input type="hidden" name="searchType" value="multicity" />

                    <div id="multicity-container-row">

                    </div>
                    <div class="text-center mt-3">
                        <button class="btn c-button btn-lg">Search</button>
                    </div>
                </form>


            </div>
            <!-- ***** Flight Filter Tabs - Multi city trip End ***** -->
        </div>
    </div>
    <!-- ***** Flight End ***** -->
</div>