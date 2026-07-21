<?php
// Check if the user is logged in
require_once('../../config.php');

$type = getReq('searchType');

if($type == 'oneway'){
	$from = getReq('from');
	$to = getReq('to');
	$departureDate = getReq('departureDate');
	$returnDate='';
	$adult = getReq('adult', 0); // Default to 0 if 'adult' is not set
	$child = getReq('child', 0);
	$infant = getReq('infant', 0);
	$travelClass = getReq('travelClass');

    $fromData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$from'");
    $toData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$to'");

    if(mysqli_num_rows($fromData) == 0 || mysqli_num_rows($toData) == 0){
        header('Location: ../../index.php');
    }

    $fromData = mysqli_fetch_assoc($fromData);
    $toData = mysqli_fetch_assoc($toData);}
elseif($type == 'round'){
	$from = getReq('from');
	$to = getReq('to');
	$departureDate = getReq('departureDate');
	$returnDate = getReq('returnDate');
	$adult = getReq('adult', 0); // Default to 0 if 'adult' is not set
	$child = getReq('child', 0);
	$infant = getReq('infant', 0);
	$travelClass = getReq('travelClass');

    $fromData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$from'");
    $toData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$to'");

    if(mysqli_num_rows($fromData) == 0 || mysqli_num_rows($toData) == 0){
        header('Location: ../../index.php');
    }

    $fromData = mysqli_fetch_assoc($fromData);
    $toData = mysqli_fetch_assoc($toData);
}
elseif($type == 'multicity'){
    foreach(getReq('multicity') as $index => $row)
    {
        if($index==0)
        {
            $from = $row['from'];
    	    $to =  $row['to'];
    	    $departureDate = $row['departureDate'];
    	    $adult = $row['adult']; // Default to 0 if 'adult' is not set
	        $child = $row['child'];
	        $infant = $row['infant'];
	        $travelClass = $row['travelClass'];
        }
    }
    
	$returnDate = '';

    $fromData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$from'");
    $toData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$to'");

    if(mysqli_num_rows($fromData) == 0 || mysqli_num_rows($toData) == 0){
        header('Location: index.php');
    }

    $fromData = mysqli_fetch_assoc($fromData);
    $toData = mysqli_fetch_assoc($toData);
}
// $departureDate = DateTime::createFromFormat("m/d/Y", $departureDate);
$dep_dummy_date = DateTime::createFromFormat('m/d/Y', $departureDate);
$departureDate = $dep_dummy_date->format('d/m/Y'); // convert to m/d/Y to d/m/Y


// for local comment this code 
// if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
//     ob_start("ob_gzhandler");
// } else {
//     ob_start();
// }

ob_start();
include '../../layouts/header2.php';
?>
			</div>
			<!-- ********** Component :: Header End ********** -->

			<!-- ********** Component :: Page Title ********** -->
			<div class="c-pageTitleSect">
				<div class="container">
					<div class="row">
						<div class="col-md-7 col-12">
							<!-- *** Search Head **** -->
							<div class="searchHeading">
							<span class="pageTitle">
                                Flight - 
                                <?php 
                                    echo isset($fromData['city']) ? $fromData['city'] : 'City not available'; 
                                    echo ' ('; 
                                    echo isset($fromData['code']) ? $fromData['code'] : 'Code not available'; 
                                    echo ') To ';
                                    echo isset($toData['city']) ? $toData['city'] : 'City not available'; 
                                    echo ' (';
                                    echo isset($toData['code']) ? $toData['code'] : 'Code not available';
                                    echo ')';
                                ?>
                            </span>


								<div class="clearfix">
									<div class="sortSection">
										<span class="sortTitle st-search">
											<i class="icon it itours-timetable"></i>
											Departure from <?php echo isset($fromData['code']) ? $fromData['code'] : 'Code not available';  ?>:
											<strong><?php echo isset($departureDate) ? $departureDate : 'Date not available'; ?></strong>
										</span>
									</div>

									<div class="sortSection">
										<span class="sortTitle st-search">
											<i class="icon it itours-timetable"></i>
											Class: <strong><?php echo isset($travelClass) ? $travelClass : 'Date not available'; ?></strong>
											
										</span>
									</div>

									<div class="sortSection">
										<span class="sortTitle st-search">
											<i class="icon it itours-person"></i>
											<?php echo isset($adult) ? $adult : '0'; ?> Adults, <?php echo isset($child) ? $child : '0'; ?> Child, <?php echo isset($infant) ? $infant : '0'; ?> Infant
											
											
										</span>
									</div>
								</div>

								<div class="clearfix">

									<div class="sortSection">
										<span class="sortTitle st-search">
											<i class="icon it itours-search"></i>
											Showing <span id="flightsCount"></span> flights
										</span>
									</div>
								</div>
							</div>
							<!-- *** Search Head End **** -->
						</div>

						<div class="col-md-5 col-12 c-breadcrumbs">
							<ul>
								<li>
									<a href="#">Home</a>
								</li>
								<li class="st-active">
									<a href="#">Flight Search Result</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- ********** Component :: Page Title End ********** -->

			<!-- ********** Component :: Hotel Listing  ********** -->
			<div class="c-containerDark">
				<div class="container">

					<div class="row">
						<!-- ***** Hotel Listing Filter ***** -->
						<div class="col-md-3 col-sm-12">

							<!-- ***** Price Filter ***** -->
							<div class="accordion c-accordion" id="filterPrice">
								<div class="card">
									<div class="card-header">
										<button
											class="btn btn-link"
											type="button"
											data-toggle="collapse"
											data-target="#jsFilterPrice"
											aria-expanded="false"
											aria-controls="jsFilterPrice"
										>
											Price (₹)
										</button>
									</div>

									<div
										id="jsFilterPrice"
										class="collapse"
										aria-labelledby="jsFilterPrice"
										data-parent="#filterPrice"
									>
										<div class="card-body">
										<div class="range-slider">
                                                <input type="text" class="js-range-slider" value="" />
                                            </div>
                                            <div class="extra-controls">
                                                <input type="text" class="js-input-from" readonly value="0" />
                                                <input type="text" class="js-input-to" readonly value="0" />
                                            </div>
										</div>
									</div>
								</div>
							</div>
							<!-- ***** Price Filter End ***** -->

							<!-- ***** Stops  Filter ***** -->
							<div class="accordion c-accordion" id="filterStops">
								<div class="card">
									<div class="card-header">
										<button
											class="btn btn-link"
											type="button"
											data-toggle="collapse"
											data-target="#jsFilterStops"
											aria-expanded="false"
											aria-controls="jsFilterStops"
										>
											Stops
										</button>
									</div>

									<div
										id="jsFilterStops"
										class="collapse"
										aria-labelledby="jsFilterStops"
										data-parent="#filterStops"
									>
										<div class="card-body filters-container">
											<ul class="c-checkSquare">
												<li>
													<button type="button" data-field="stops" data-value="0" class="filterCheckbox">Non Stop (<span class="stops-0" data-count="stops-0">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="stops" data-value="1" class="filterCheckbox">1 Stop (<span class="stops-1" data-count="stops-1">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="stops" data-value="2" class="filterCheckbox">2 Stop (<span class="stops-2" data-count="stops-2">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="stops" data-value="3" class="filterCheckbox">3+ Stops (<span class="stops-3" data-count="stops-3">0</span>)</button>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- ***** Stops  Filter End ***** -->

							<!-- ***** Arrival  Filter ***** -->
							<div class="accordion c-accordion" id="filterDeparture">
								<div class="card">
									<div class="card-header">
										<button
											class="btn btn-link"
											type="button"
											data-toggle="collapse"
											data-target="#jsFilterDeparture"
											aria-expanded="false"
											aria-controls="jsFilterDeparture"
										>
											Departure Time
										</button>
									</div>

									<div
										id="jsFilterDeparture"
										class="collapse"
										aria-labelledby="jsFilterDeparture"
										data-parent="#filterDeparture"
									>
										<div class="card-body filters-container">
											<ul class="c-checkSquare">
												<li>
													<button type="button" data-field="departureTime" data-value="0-6"  class="filterCheckbox">Before 6AM (<span class="departureTime-0-6" data-count="departureTime-0-6">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="departureTime" data-value="6-12"  class="filterCheckbox">6AM to 12PM (<span  class="departureTime-6-12" data-count="departureTime-6-12">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="departureTime" data-value="12-18" class="filterCheckbox" >12PM to 6PM (<span class="departureTime-12-18" data-count="departureTime-12-18">0</span>)</button>
												</li>
												<li>
													<button type="button" data-field="departureTime" data-value="18-24" class="filterCheckbox">After 6PM (<span class="departureTime-18-24" data-count="departureTime-18-24">0</span>)</button>
												</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- ***** Arrival  Filter End ***** -->

							<!-- ***** Departure Filter ***** -->
							<div class="accordion c-accordion" id="filterArrival">
								<div class="card">
									<div class="card-header">
										<button
											class="btn btn-link"
											type="button"
											data-toggle="collapse"
											data-target="#jsFilterArrival"
											aria-expanded="false"
											aria-controls="jsFilterArrival"
										>
											Arrival Time
										</button>
									</div>

									<div
										id="jsFilterArrival"
										class="collapse"
										aria-labelledby="jsFilterArrival"
										data-parent="#filterArrival"
									>
										<div class="card-body filters-container">
											<ul class="c-checkSquare">
											<li>
                                                <button type="button" data-field="arrivalTime" data-value="0-6" class="filterCheckbox">Before 6AM (<span class="arrivalTime-0-6" data-count="arrivalTime-0-6">0</span>)</button>
                                            </li>
                                            <li>
                                                <button type="button" data-field="arrivalTime" data-value="6-12" class="filterCheckbox">6AM to 12PM (<span class="arrivalTime-6-12" data-count="arrivalTime-6-12">0</span>)</button>
                                            </li>
                                            <li>
                                                <button type="button" data-field="arrivalTime" data-value="12-18" class="filterCheckbox">12PM to 6PM (<span class="arrivalTime-12-18" data-count="arrivalTime-12-18">0</span>)</button>
                                            </li>
                                            <li>
                                                <button type="button" data-field="arrivalTime" data-value="18-24" class="filterCheckbox">After 6PM (<span class="arrivalTime-18-24" data-count="arrivalTime-18-24">0</span>)</button>
                                            </li>

											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- ***** Departure Filter End ***** -->

							<!-- ***** Airlines Filter ***** -->
							<div class="accordion c-accordion" id="filterAirlines">
								<div class="card">
									<div class="card-header">
										<button
											class="btn btn-link"
											type="button"
											data-toggle="collapse"
											data-target="#jsFilterAirlines"
											aria-expanded="false"
											aria-controls="jsFilterAirlines"
										>
											Airlines
										</button>
									</div>

									<div
										id="jsFilterAirlines"
										class="collapse"
										aria-labelledby="jsFilterAirlines"
										data-parent="#filterAirlines"
									>
										<div class="card-body filters-container">
											<ul class="c-checkSquare">
											

											</ul>
										</div>
									</div>
								</div>
							</div>
							<!-- ***** Airlines Filter End ***** -->
						</div>
						<!-- ***** Hotel Listing Filter End ***** -->

						<!-- ***** Hotel Listing ***** -->
						<div class="col-md-9 col-sm-12">
							<!-- ***** No Records ***** -->
							
							<!-- ***** No Records End ***** -->

							<!-- ***** Hotel Card ***** -->
							<div class="flightResults" id="flightResults">
							    <?php
							    if($type == 'oneway'){
                                	?>
                                	 <!-- Tab buttons -->
                                      <div class="tab">
                                        <button class="tab-button active" onclick="openTab(event, 'flightResults_ONWARD')"><?=$fromData['city']?> 
                                            <i class="fa-sharp fa-solid fa-plane me-2" aria-hidden="true"></i> 
                                            <?=$toData['city']?>
                                            <?php $departureDateFormate = DateTime::createFromFormat("d/m/Y", $departureDate); ?>
                                            <p class="flightResultsDate"><?=$departureDateFormate->format("D, M jS Y");?></p>
                                        </button>
                                        
                                      </div>
                                	  <div id="flightResults_ONWARD" class="tab-content flightResultstabcontent flightResults_ONWARD active">
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                           
                                      </div>
                                	<?php
                                }elseif($type == 'round'){
                                	?>
                                	 <!-- Tab buttons -->
                                      <div class="tab">
                                        <button class="tab-button active" onclick="openTab(event, 'flightResults_ONWARD')"><?=$fromData['city']?> 
                                            <i class="fa-sharp fa-solid fa-plane me-2" aria-hidden="true"></i> 
                                            <?=$toData['city']?>
                                            <?php $departureDateFormate = DateTime::createFromFormat("d/m/Y", $departureDate); ?>
                                            <p class="flightResultsDate"><?=$departureDateFormate->format("D, M jS Y");?></p>
                                        </button>
                                        <button class="tab-button" onclick="openTab(event, 'flightResults_RETURN')"><?=$toData['city']?> 
                                            <i class="fa-sharp fa-solid fa-plane me-2" aria-hidden="true"></i> 
                                            <?=$fromData['city']?>
                                            <?php $returnDateFormate = DateTime::createFromFormat("d/m/Y", $returnDate); ?>
                                            <p class="flightResultsDate"><?=$returnDateFormate->format("D, M jS Y");?></p>
                                        </button>
                                      </div>
                                      
                                      
                                       <!-- Tab content -->
                                      <div id="flightResults_ONWARD" class="tab-content flightResultstabcontent flightResults_ONWARD active">
                                        <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            
                                      </div>
                                    
                                      <div id="flightResults_RETURN" class="tab-content flightResultstabcontent flightResults_RETURN">
                                        <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            
                                      </div>
                                    
                                	<?php
                                }elseif($type == 'multicity'){
                                	?>
                                	<div class="tab">
                                	    <?php
                                    	foreach(getReq('multicity') as $index => $row)
                                        {
                                            $Tabfrom=$row['from'];
                                            $Tabto=$row['to'];
                                            $TabfromData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$Tabfrom'");
                                            $TabtoData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$Tabto'");
                                        
                                            if(mysqli_num_rows($TabfromData) == 0 || mysqli_num_rows($TabtoData) == 0){
                                                header('Location: index.php');
                                            }
                                        
                                            $TabfromData = mysqli_fetch_assoc($TabfromData);
                                            $TabtoData = mysqli_fetch_assoc($TabtoData);
                                            
                                            ?>
                                                <button class="tab-button <?php if($index==0){ echo "active";}?>" onclick="openTab(event, 'flightResults_<?=$index?><?=$row['from']?><?=$row['to']?>')"><?=$TabfromData['city']?> 
                                                    <i class="fa-sharp fa-solid fa-plane me-2" aria-hidden="true"></i> 
                                                    <?=$TabtoData['city']?>
                                                    <?php $departureDateFormate = DateTime::createFromFormat("d/m/Y", $row['departureDate']); ?>
                                                    <p class="flightResultsDate"><?=$departureDateFormate->format("D, M jS Y");?></p>
                                                </button>
                                                
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                    	foreach(getReq('multicity') as $index => $row)
                                        {
                                            $Tabfrom=$row['from'];
                                            $Tabto=$row['to'];
                                            $TabfromData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$Tabfrom'");
                                            $TabtoData = mysqli_query($connection, "SELECT * FROM airports WHERE code = '$Tabto'");
                                        
                                            if(mysqli_num_rows($TabfromData) == 0 || mysqli_num_rows($TabtoData) == 0){
                                                header('Location: index.php');
                                            }
                                        
                                            $TabfromData = mysqli_fetch_assoc($TabfromData);
                                            $TabtoData = mysqli_fetch_assoc($TabtoData);
                                            
                                            ?>
                                                 <div id="flightResults_<?=$index?><?=$row['from']?><?=$row['to']?>" class="tab-content flightResultstabcontent flightResults_<?=$index?><?=$row['from']?><?=$row['to']?> <?php if($index==0){ echo "active";}?>">
                                        <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            
                                                </div>
                                                
                                            <?php
                                        }
                                        ?>
                                   
                                    <?php

                                }
							    
							    ?>
							</div>
							<!-- ***** Hotel Card End ***** -->

							
						</div>
						<!-- ***** Hotel Listing End ***** -->
					</div>
				</div>
			</div>
			<!-- ********** Component :: Hotel Listing End ********** -->

			<!-- ********** Component :: Footer ********** -->
			<?php include '../../layouts/footer2.php'; ?>
			<!-- ********** Component :: Footer End ********** -->
		</div>
<div class="container">
  <div class="bottom-fixed" >
    
  </div>
</div>


		<!-- Javascript -->
		<!-- Javascript -->
		<?php
		if($type == 'multicity')
        {
            $from=array();
            $to=array();
            $departureDate=array();
            foreach(getReq('multicity') as $index => $row)
            {
                    $from[] = $row['from'];
            	    $to[] =  $row['to'];
            	    $departureDate[] = $row['departureDate'];
            }
            $from=implode(",",$from);
            $to=implode(",",$to);
            $departureDate=implode(",",$departureDate);
        }
		
		?>
        
		<script type="text/javascript" src="../../js2/jquery-ui.1.10.4.min.js"></script>
		<script type="text/javascript" src="../../js2/popper.min.js"></script>
		<script type="text/javascript" src="../../js2/bootstrap-4.min.js"></script>
		<script type="text/javascript" src="../../js2/owl.carousel.min.js"></script>
		<script type="text/javascript" src="../../js2/theme-scripts.js"></script>
		<script src="../../js2/select2.min.js"></script>
		
		<!--<script type="text/javascript" src="../../js2/scripts.js"></script>-->

        <script type="text/javascript">
        function buttonbookLoader()
        {
            let htmlloader=`<div class="content-loader">
                <div class="item">
                  <div class="placeholder image"></div>
                  <div class="text">
                    <div class="placeholder line"></div>
                    <div class="placeholder line"></div>
                  </div>
                </div>
                <div class="item">
                  <div class="placeholder image"></div>
                  <div class="text">
                    <div class="placeholder line"></div>
                    <div class="placeholder line"></div>
                  </div>
                </div>
                
              </div>
              <div class="total-and-button content-loader">
                  <span class="total-price item" style="display: flex;flex-direction: column; gap: 3px;align-items: flex-start;">
                    <div class="placeholder line" style="width: 120px;height: 11px;"></div>
                    <div class="placeholder line" style="width: 115px;height: 11px;"></div>
                  </span>
                  <span class="item">
                  <div class="placeholder image" style="width: 125px;"></div>
                  </span>
                </div>`;
              $(".bottom-fixed").html(htmlloader);
        }
        buttonbookLoader();
         // Ensure the form data is present in the page when loaded
         var data = {
                searchType: '<?php echo $type; ?>',
                from: '<?php echo $from; ?>',
                to: '<?php echo $to; ?>',
                departureDate: '<?php echo $departureDate; ?>',
                returnDate: '<?php echo $returnDate; ?>',
                adult: '<?php echo $adult; ?>',
                child: '<?php echo $child; ?>',
                infant: '<?php echo $infant; ?>',
                travelClass: '<?php echo $travelClass; ?>',
        		pricefrom:$(".js-input-from").val(),
        		priceto:$(".js-input-to").val(),
            };
            

            $(document).ready(function() {
                // Call submitForm() automatically after loading the page
                submitForm(data);
            });

            function setFilter(field, value, checked = true)
            {
                
				if (!data[field]) {
                    data[field] = [];
                }
	            switch(field) {
                case 'travelClass':
                    data[field] = value;
                break;

                    default:
                        if (checked) {
                            // If the value is not already in the array, push it
                            if (data[field].indexOf(value) === -1) {
                                data[field].push(value);
                            }
                        } else {
                            // If the value is in the array, remove it
                            const index = data[field].indexOf(value);
                            if (index > -1) {
                                data[field].splice(index, 1);
                            }
                        }
                }
                data.pricefrom=$(".js-input-from").val();
        		data.priceto=$(".js-input-to").val();
                
                console.log(data);
				submitForm(data);
			}
			
			
			function renderStopsFilter(data)
			{
			    $("#jsFilterStops li").hide();
			    $.each(data, function (index, value) {
        			    $(".stops-"+index).text(value); 
        			    $(".stops-" +index).closest("li").show();
    			});
			}
			
			function renderdepartureTimeFilter(data)
			{
			    $("#jsFilterDeparture li").hide();
		    	$.each(data, function (index, value) {
    			    $(".departureTime-"+index).text(value); 
    			    $(".departureTime-" +index).closest("li").show();
			    });
			}
			function renderarrivalTimeFilter(data)
			{
			    $("#jsFilterArrival li").hide();
    			$.each(data, function (index, value) {
    			    $(".arrivalTime-"+index).text(value);  
    			    $(".arrivalTime-" +index).closest("li").show();
			    });
			    
			}
			
			function renderAirlineFilter(data)
			{
			    if(callBy!='airlines')
			    {
			        $("#jsFilterAirlines .c-checkSquare").html(""); 
        			    $.each(data, function (index, value) {
        			    let filterCardList=`<li>
                                            <button type="button" data-field="airlines" data-value="${index}" class="filterCheckbox">${index} (${value})</button>
                                        </li>`;
                        $("#jsFilterAirlines .c-checkSquare").append(filterCardList);   
    			    });
			    }
			}
			
			// Function to calculate layover duration in hours and minutes
            function calculateLayover(arrival, departure) {
                const diffMs = departure - arrival; // Difference in milliseconds
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                return `${diffHours}hr ${diffMinutes}min`;
            }
    
            function renderFlightCards(response,type) 
            {
                let container = $('#flightResults_'+type);
                container.empty(); // Clear previous results
                if (response.length < 1) {
                    container.html(`<div class="c-emptyList">
        								<div class="imgDiv">
        									<img src="${BASE_URL}./images/search_illustration.svg" alt="" />
        								</div>
        								<span class="infoDiv"> No records Found </span>
        							</div>`);
                    return;
                }
            
                let currentDisplayCount = 10; // Number of items to show initially
            
                function renderSubset(start, end) {
                    // Render a subset of data
                    response.slice(start, end).forEach(flight => {
                        let flight_number = "";
                        let departure = "";
                        let class_Index ="";
                        let refundornot_Index ="";
                        let arrival = "";
                        let flight_logo = "";
                        let segment_id ="";
                        let airline_name ="";
                        let total_time =0;
                        let departure_code="";
                        let departure_time="";
                        let departure_date="";
                        let departure_date_time="";
                        let departure_city="";
                        let arrival_code=""
                        let arrival_time="";
                        let arrival_date="";
                        let arrival_date_time="";
                        let arrival_city="";
                        let date_options = {
                            weekday: 'short',  // "Wed"
                            day: '2-digit',    // "02"
                            month: 'short'     // "Mar"
                        };
                        const totalSegments = flight.segments.length;
                            flight.segments.forEach((segment, index) => {
                                // Check if it's the first segment
                                if (index === 0) 
                                {
                                    departure_code =`${segment.departure.code}`;
                                    departure_time =`${segment.departure.at.time}`;
                                    departure_date =`${segment.departure.at.date}`;
                                    departure_date_time=`${segment.departure.at.date} ${segment.departure.at.time}`;
                                    departure_city =`${segment.departure.city}`;
                                    // Create a new Date object
                                    departure_date = new Date(departure_date);
                                    departure_date = new Intl.DateTimeFormat('en-GB', date_options).format(departure_date);
                                    if(segment.departure.terminal)
                                    {
                                        departure=segment.departure;
                                    }
                                }
                                
                                // Check if it's the last segment
                                if (index === totalSegments - 1) 
                                {
                                    arrival_code = `${segment.arrival.code}`;
                                    arrival_time =`${segment.arrival.at.time}`;
                                    arrival_date =`${segment.arrival.at.date}`;
                                    arrival_date_time=`${segment.arrival.at.date} ${segment.arrival.at.time}`;
                                    arrival_date = new Date(arrival_date);
                                    arrival_date = new Intl.DateTimeFormat('en-GB', date_options).format(arrival_date);
                                    arrival_city =`${segment.arrival.city}`;
                                    if(segment.arrival.terminal)
                                    {
                                        arrival=segment.arrival;
                                    }
                                }
                                
                                
                                
                                //console.log(total_time);
                                
                                
                                // Construct the airline logo URL
                                flight_logo = `https://static.tripjack.com/img/airlineLogo/v1/${segment.airline_code}.png`;
                                
                                
                                
                                // Get the airline name from the first segment (assuming all segments have the same airline name)
                                airline_name = segment.airline_name;
                        
                                // Construct the flight number by appending airline_code and segment_id
                                flight_number += `${segment.airline_code}-${segment.flight_number} | `;
                                segment_id+=`${segment.segment_id}`;
                            });
                            
                            // Convert strings to Date objects
                            let departureDate = new Date(departure_date_time.replace(" ", "T"));
                            let arrivalDate = new Date(arrival_date_time.replace(" ", "T"));
                            
                            // Calculate the difference in milliseconds
                            let differenceInMilliseconds = arrivalDate - departureDate;
                            
                            // Convert milliseconds to hours and minutes
                            let totalHours = Math.floor(differenceInMilliseconds / (1000 * 60 * 60));  // Get total hours
                            let totalMinutes = Math.floor((differenceInMilliseconds % (1000 * 60 * 60)) / (1000 * 60));  // Get remaining minutes
                            // Calculate remaining minutes
                                
                                // Format the result as "1hr 15min"
                            total_time = `${totalHours}hr ${totalMinutes}min`;
                            // Remove the last comma and space using slice
                            flight_number = flight_number.slice(0, -2);
                            
                            flight.prices.forEach((price, price_index) => {
                                if(price_index==0)
                                {
                                    flight_amount=price.total_amount;
                                    fare_id=price.id;
                                    let explodedArray = fare_id.split("~");
        
                                    // Display each value in the array
                                    explodedArray.forEach((value, index1) => {
                                        if(index1==0)
                                        {
                                            fare_name="flight-"+value;
                                        }
                                    });
                                    
                                    
                                    fareIdentifier=price.fareIdentifier;
                                }
                            });
                            
        
                        let cardHTML = `<div class="c-cardList">
        								<div class="c-cardListTable">
        									<!-- *** Hotel Card image *** -->
        									<div class="cardList-image type-flight">
        										<div class="flightDetails">
        											<div class="flightInfo">
        												<div class="flight_icon">
        													<img src="${flight_logo}" loading="lazy" alt="${airline_name}" />
        												</div>
        												<div class="flight_name">${airline_name}</div>
        												<div class="flight_number">${flight_number}</div>
        											</div>
        										</div>
        									</div>
        									<!-- *** Hotel Card image End *** -->
        
        									<!-- *** Hotel Card Info *** -->
        									<div class="cardList-info" role="button">
        										<button class="expandSect forFlight" 
        										        data-toggle="collapse"
        												role="button"
        												href="#collapseExample_${fare_name}"
        												aria-expanded="false"
        												aria-controls="collapseExample">View Details</button>
        										<div class="dividerSection type-1 noborder">
        											<div
        												class="divider s1"
        												data-toggle="collapse"
        												role="button"
        												href="#collapseExample_${fare_name}"
        												aria-expanded="false"
        												aria-controls="collapseExample"
        											>
        												<div class="flightSection">
        													<div class="fs_start">
        														<div class="airport_details">
        															<span class="flight_detail_lbl uppercase"
        																>${departure_code}</span
        															>`;
        															if(departure.terminal)
        															{
        															cardHTML +=`<span class="flight_detail_lbl font-12 color-light"
        																>${departure.terminal}</span
        															>`;
        															}
        															cardHTML +=`<span class="flight_detail_lbl bold font-18"
        																>${departure_time}</span
        															>
        															<span
        																class="flight_detail_lbl font-12 color-light"
        																>${departure_date}</span
        															>
        															<span
        																class="flight_detail_lbl font-12 color-light"
        																>${departure_city}</span
        															>
        														</div>
        													</div>
        
        													<div class="fs_to t2">
        														<div class="airport_details">
        															<span
        																class="flight_detail_lbl color-light bold sm"
        																>${total_time}</span
        															>
        															<div class="flight_arrow"></div>`;
        															if(totalSegments-1==0)
        															{
        															    cardHTML +=`<span class="flight_detail_lbl color-light sm bold"	>Non Stop</span>`;
        															}
        															else
        															{
        															    cardHTML +=`<span class="flight_detail_lbl color-light sm bold"	>${totalSegments-1} stop</span>`;
        															}

        														cardHTML +=`</div>
        													</div>
        
        													<div class="fs_end">
        														<div class="airport_details">
        															<span class="flight_detail_lbl uppercase"
        																>${arrival_code}</span
        															>`;
        															if(arrival.terminal)
        															{
        															cardHTML +=`<span class="flight_detail_lbl font-12 color-light"
        																>${arrival.terminal}</span
        															>`;
        															}
        															cardHTML +=`<span class="flight_detail_lbl bold font-18"
        																>${arrival_time}</span
        															>
        															<span
        																class="flight_detail_lbl font-12 color-light"
        																>${arrival_date}</span
        															>
        															<span
        																class="flight_detail_lbl font-12 color-light"
        																>${arrival_city}</span
        															>
        														</div>
        													</div>
        												</div>
        											</div>
        
        											<div class="divider s2">`;
        											flight.prices.forEach((price, price_index) => {
                                                            let refundornot="";
                                                            
                                                            flight_amount=price.total_amount;
                                                            fare_id=price.id;
                                                            if(price.variants.adult.refundable==1)
                                                            {
                                                                refundornot="Refundable";
                                                            }
                                                            else if(price.variants.adult.refundable==2)
                                                            {
                                                                
                                                              refundornot="Partial Refundable"; 
                                                            }
                                                            else
                                                            {
                                                                 refundornot="Not Refundable";
                                                            }
                                                            if(price_index==0)
                                                            {
                                                                // Display each value in the array
                                                                let explodedArray = fare_id.split("~");
                                                                explodedArray.forEach((value, index1) => {
                                                                    if(index1==0)
                                                                    {
                                                                        fare_name="flight-"+value;
                                                                    }
                                                                });
                                                                
                                                                refundornot_Index=refundornot;
                                                                class_Index=price.variants.adult.class;
                                                            }
                                                            fareIdentifier=price.fareIdentifier;
                                                        
                                                        cardHTML +=`<div class="priceTag ${price.msri.join(' ')}">
        													<div class="p-old">
        														
        														<span class="price_main">
        															<input class="sort-field" 
                                                                           type="radio" 
                                                                           id="${fare_id}" 
                                                                           data-type="false"
                                                                           data-fareIdentifier="${fareIdentifier}"
                                                                           name="${type}" 
                                                                           data-refundornot="${refundornot}"
                                                                           data-class_Index="${price.variants.adult.class}"
                                                                           data-msri="${price.msri.join(',')}" 
                                                                           data-sri="${price.sri}" 
                                                                           value="${fare_id}" />
        															<span class="p_currency">₹</span>
        															<span class="p_cost">${flight_amount}</span>
        															<span class="o_lbl ${price.msri.join(' ')}">${fareIdentifier}</span>
        														</span>
        														<span class="price_type_details">
        														<span>${price.variants.adult.class},</span>
        														<span>${refundornot}</span>`;
        														if(price.free_meal)
        														{
        														    cardHTML +=`, <span>Free Meal</span>`;
        														}
        														
        														cardHTML +=`</span>
        													</div>
        												</div>`;
                                                    });
        												
        												
        											cardHTML +=`</div>
        										</div>
        									</div>
        									<!-- *** Hotel Card Info End *** -->
        								</div>
        
        								<!-- *** flight Details Accordian *** -->
        								<div class="collapse" id="collapseExample_${fare_name}">
        									<div class="cardList-accordian">
        										<!-- ***** Hotel Info Tabs ***** -->
        										<div class="c-compTabs">
        											<ul class="nav nav-tabs" id="myTab" role="tablist">
        												<li class="nav-item">
        													<a
        														class="nav-link active"
        														id="FlightDetails_${segment_id}-tab"
        														data-toggle="tab"
        														href="#FlightDetails_${segment_id}"
        														role="tab"
        														aria-controls="FlightDetails_${segment_id}"
        														aria-selected="true"
        														>Flight Details</a
        													>
        												</li>
        
        												<li class="nav-item">
        													<a
        														class="nav-link"
        														id="Baggage_${segment_id}-tab"
        														data-toggle="tab"
        														href="#Baggage_${segment_id}"
        														role="tab"
        														aria-controls="Baggage_${segment_id}"
        														aria-selected="true"
        														>Baggage</a
        													>
        												</li>
        											</ul>
        
        											<div class="tab-content" id="myTabContent">
        												<!-- **** Flight Details **** -->
        												<div
        													class="tab-pane fade show active"
        													id="FlightDetails_${segment_id}"
        													role="tabpanel"
        													aria-labelledby="FlightDetails_${segment_id}-tab"
        												>
        													<div class="c-cardListHolder">
        														<div class="c-pointList">
        															<span>${airline_name}</span>
        															<span>${flight_number}</span>
        															
        															<span class="class_Index">${class_Index}</span>
        															<span class="refundornot_Index">${refundornot_Index}</span>
        														</div>
        														
        														<!-- ***** Flight Details Holder :: With Holt ***** -->`;
        													cardHTML +=`<div class="flight-details-holder">`;	
        													 flight.segments.forEach((segment, index) => {
        													     // Check if it's the first segment
                                                                        
                                                                            departure_code =`${segment.departure.code}`;
                                                                            departure_time =`${segment.departure.at.time}`;
                                                                            departure_date =`${segment.departure.at.date}`;
                                                                            departure_date_time=`${segment.departure.at.date} ${segment.departure.at.time}`;
                                                                            departure_city =`${segment.departure.city}`;
                                                                            departure_country =`${segment.departure.country}`;
                                                                            
                                                                            // Create a new Date object
                                                                            departure_date = new Date(departure_date);
                                                                            departure_date = new Intl.DateTimeFormat('en-GB', date_options).format(departure_date);
                                                                        
                                                                        
                                                                        // Check if it's the last segment
                                                                        
                                                                            arrival_code = `${segment.arrival.code}`;
                                                                            arrival_time =`${segment.arrival.at.time}`;
                                                                            arrival_date =`${segment.arrival.at.date}`;
                                                                            arrival_date_time=`${segment.arrival.at.date} ${segment.arrival.at.time}`;
                                                                            arrival_date = new Date(arrival_date);
                                                                            arrival_date = new Intl.DateTimeFormat('en-GB', date_options).format(arrival_date);
                                                                            arrival_city =`${segment.arrival.city}`;
                                                                            arrival_country =`${segment.arrival.country}`;
                                                                        
                                                                        
                                                                        //console.log(total_time);
                                                                        
                                                                        
                                                                        // Construct the airline logo URL
                                                                        flight_logo = `https://static.tripjack.com/img/airlineLogo/v1/${segment.airline_code}.png`;
                                                                        
                                                                        
                                                                        
                                                                        // Get the airline name from the first segment (assuming all segments have the same airline name)
                                                                        airline_name = segment.airline_name;
                                                                
                                                                        // Construct the flight number by appending airline_code and segment_id
                                                                        flight_number = `${segment.airline_code}-${segment.flight_number}`;
                                                                        // Convert strings to Date objects
                                                                        let departureDate = new Date(departure_date_time.replace(" ", "T"));
                                                                        let arrivalDate = new Date(arrival_date_time.replace(" ", "T"));
                                                                        
                                                                        // Calculate the difference in milliseconds
                                                                        let differenceInMilliseconds = arrivalDate - departureDate;
                                                                        
                                                                        // Convert milliseconds to hours and minutes
                                                                        let totalHours = Math.floor(differenceInMilliseconds / (1000 * 60 * 60));  // Get total hours
                                                                        let totalMinutes = Math.floor((differenceInMilliseconds % (1000 * 60 * 60)) / (1000 * 60));  // Get remaining minutes
                                                                        // Calculate remaining minutes
                                                                            
                                                                        // Format the result as "1hr 15min"
                                                                        total_time = `${totalHours}hr ${totalMinutes}min`;
                												cardHTML +=`<div class="detail-wrapper">
        																<div class="flight-logo">
        																	<div class="flight_icon">
        																		<img
        																			src="${flight_logo}"
        																			loading="lazy"
        																			alt="${airline_name}"
        																		/>
        																	</div>
        																	<div class="flight_name">${airline_name}</div>
        																	<div class="flight_number">${flight_number}</div>
        																</div>
        																<div class="flight-timings">
        																	<div class="flightSection">
        																		<div class="fs_start">
        																			<div class="airport_details">
        																				<span class="flight_detail_lbl uppercase bold" >
        																					${departure_code} &nbsp; ${departure_time}
        																				</span>
        																				<span class="flight_detail_lbl font-12 color-light" >${departure_date}</span>
        																				<span class="flight_detail_lbl font-12 color-light" > ${departure_city}, ${departure_country}</span>
        																			</div>
        																		</div>
        
        																		<div class="fs_to t2">
        																			<div class="airport_details">
        																				<span
        																					class="flight_detail_lbl color-light bold sm"
        																					>${total_time}</span
        																				>
        																				<div class="flight_arrow"></div>
        																				<span
        																					class="flight_detail_lbl color-light bold sm"
        																					>Flight Duration</span
        																				>
        																			</div>
        																		</div>
        
        																		<div class="fs_end">
        																			<div class="airport_details">
        																				<span
        																					class="flight_detail_lbl uppercase bold"
        																				>
        																					${arrival_code} &nbsp; ${arrival_time}
        																				</span>
        																				<span
        																					class="flight_detail_lbl font-12 color-light"
        																					>${arrival_date}</span
        																				>
        																				<span
        																					class="flight_detail_lbl font-12 color-light"
        																				>
        																					${arrival_city}, ${arrival_country}</span
        																				>
        																			</div>
        																		</div>
        																	</div>
        																</div>
        															</div>`;

        															if (index != totalSegments - 1) 
        															{
        															    const nextSegment = flight.segments[index + 1];
                                                                        // Extract required details
                                                                        const stopArrivalDateTime = new Date(`${segment.arrival.at.date}T${segment.arrival.at.time}`);
                                                                        const stopDepartureDateTime = new Date(`${nextSegment.departure.at.date}T${nextSegment.departure.at.time}`);
                                                                        const layoverDuration = calculateLayover(stopArrivalDateTime, stopDepartureDateTime);
                                                                
                                                                        const arrivalCity = segment.arrival.city; // Ensure this property exists
                                                                        const arrivalCountry = segment.arrival.country; // Ensure this property exists
                                                                
                                                                // Append layover information to cardHTML
                                                                cardHTML += `
                                                                    <div class="detail-wrapper-stop">
                                                                        <span>
                                                                            Flight change at ${arrivalCity}, ${arrivalCountry}, layover of ${layoverDuration} 
                                                                            (${stopDepartureDateTime.toLocaleString()})
                                                                        </span>
                                                                    </div>`;
        													        }
        													   });
        													cardHTML +=`</div>
        												</div>
        												</div>
        												<!-- **** Flight Details End **** -->
        												<!-- **** Tab Baggage **** -->
        												<div
        													class="tab-pane fade"
        													id="Baggage_${segment_id}"
        													role="tabpanel"
        													aria-labelledby="Baggage_${segment_id}-tab"
        												>
        													<!-- **** Policies List **** -->`;
        												flight.segments.forEach((segment, index) => {
        													     // Check if it's the first segment
                                                                        let price=flight.prices[index];
                                                                        
                                                                            departure_code =`${segment.departure.code}`;
                                                                            departure_time =`${segment.departure.at.time}`;
                                                                            departure_date =`${segment.departure.at.date}`;
                                                                            departure_date_time=`${segment.departure.at.date} ${segment.departure.at.time}`;
                                                                            departure_city =`${segment.departure.city}`;
                                                                            departure_country =`${segment.departure.country}`;
                                                                            
                                                                            // Create a new Date object
                                                                            departure_date = new Date(departure_date);
                                                                            departure_date = new Intl.DateTimeFormat('en-GB', date_options).format(departure_date);
                                                                        
                                                                        
                                                                        // Check if it's the last segment
                                                                        
                                                                            arrival_code = `${segment.arrival.code}`;
                                                                            arrival_time =`${segment.arrival.at.time}`;
                                                                            arrival_date =`${segment.arrival.at.date}`;
                                                                            arrival_date_time=`${segment.arrival.at.date} ${segment.arrival.at.time}`;
                                                                            arrival_date = new Date(arrival_date);
                                                                            arrival_date = new Intl.DateTimeFormat('en-GB', date_options).format(arrival_date);
                                                                            arrival_city =`${segment.arrival.city}`;
                                                                            arrival_country =`${segment.arrival.country}`;
                                                                        
                                                                        
                                                                        //console.log(total_time);
                                                                        
                                                                        
                                                                        // Construct the airline logo URL
                                                                        flight_logo = `https://static.tripjack.com/img/airlineLogo/v1/${segment.airline_code}.png`;
                                                                        
                                                                        
                                                                        
                                                                        // Get the airline name from the first segment (assuming all segments have the same airline name)
                                                                        airline_name = segment.airline_name;
                                                                
                                                                        // Construct the flight number by appending airline_code and segment_id
                                                                        flight_number = `${segment.airline_code}-${segment.flight_number}`;
                                                                     	
        													cardHTML +=` <div class="c-infoDivider">
        														<div class="infoTitle">
        															<h4 class="policyHeading">
        																${departure_code} - ${arrival_code} <br>(${airline_name} ${flight_number})
        															</h4>
        														</div>
        														<div class="infoDescription">
        															<ul class="policyListing">`;
        															if(price)
        															{
        															   cardHTML +=` <li>Cabin Baggage: <strong>${price.variants.adult.baggage_cabin}</strong></li>
        																            <li>Check-in Baggage: <strong>${price.variants.adult.baggage_checking}</strong></li>`;
        															}
        																
        																cardHTML +=`</ul>
        														</div>
        													</div>`;
        												});	
        												cardHTML +=`	<!-- **** Policies List End **** -->
        												</div>
        												<!-- **** Tab Baggage End **** -->
        											</div>
        										</div>
        										<!-- ***** Hotel Info Tabs End***** -->
        									</div>
        								</div>
        								<!-- *** Hotel Details Accordian End *** -->
        							</div>`;
                        
                        
            
                        container.append(cardHTML);
                    });
                }
                


            
                // Render the initial set of data
                renderSubset(0, currentDisplayCount);
                
                // Add "Load More" button
                if (response.length > currentDisplayCount) {
                        const loadMoreHTML = `
                            <div class="loadMoreContainer loadMoreContainer_${type}">
                                <button class="loadBtn">
                                    <i class="icon it itours-load"></i>
                                    Load More Flights
                                </button>
                            </div>`;
                            
                        // Remove any existing Load More button to avoid duplicates
                        $('.loadMoreContainer_'+type).remove();
                        
                        // Append the "Load More" button at the end
                        container.append(loadMoreHTML);
                    
                        // Attach event listener to the "Load More" button
                        $('.loadBtn').on('click', function () {
                            const previousCount = currentDisplayCount;
                            currentDisplayCount += 10; // Increment the display count by 10
                            renderSubset(previousCount, currentDisplayCount);
                    
                            // Move "Load More" button to the end of the container
                            $('.loadMoreContainer_'+type).appendTo(container);
                    
                            // Remove the "Load More" button if all records are displayed
                            if (currentDisplayCount >= response.length) {
                                $('.loadMoreContainer_'+type).remove();
                            }
                        });
                    }
                    
                }
                
            
            $(document).ready(function () {
                // Attach the change event to radio buttons inside #flightResults
                $(document).on('change', '#flightResults input[type="radio"]', function () {
                    // Retrieve the msri and sri values
                    var msri = $(this).data("msri"); // Get the msri attribute
                    var sri = $(this).data("sri");   // Get the sri attribute
                
                    // Call the function after retrieving the values
                    getBookingFlightButton(msri,sri);
                });

            });
            
            function BookingBottomButton(response)
            {
                let htmlView=`<div class="flight-details">`;
                response.flightData.forEach((data, index) => {
                    htmlView+=`<div class="flight">
                                <span class="flight-col-1 flight-col flight_icon">
                        			<img src="https://static.tripjack.com/img/airlineLogo/v1/${data.flight_code}.png" loading="lazy" alt="${data.flight_name}">
                        	    </span>
                                <span class="flight-col-1 flight-col">
                                    <span class="airline">${data.flight_name}</span>
                                    <span class="flight-code">${data.flight_code}-${data.flight_no}</span>
                                </span> 
                                <span class="flight-col-2 flight-col">
                                    <span class="timing">${data.dttime} → ${data.attime}</span>
                                    <span class="route">${data.da_code} → ${data.aa_code}</span>
                                </span> 
                                <span class="flight-col-3 flight-col">
                                    <span class="price">₹${data.flight_amount}</span>
                                </span> 
                             </div>`;
                });        
                        htmlView+=`</div>
                            <div class="total-and-button">
                              <span class="total-price">Total: ₹${response.total_amount}</span>
                              <button class="book-btn book-now">BOOK</button>
                            </div>`;
                    $(".bottom-fixed").html(htmlView); 
                    
            }
            
            function getBookingFlightButton(msri,sri) 
            {
                console.log(msri);
                console.log(sri);
                $("#flightResults .priceTag").removeClass("priceTag_SPECIAL_RETURN");
                if (sri) 
                {
                    // Add a special class to the matching priceTag
                    $("#flightResults .priceTag." + sri).addClass("priceTag_SPECIAL_RETURN");
                
                    // Find the radio button with the matching msri data and check it
                    $('#flightResults input[type="radio"].sort-field').each(function () {
                        var msri = $(this).data('msri'); // Get the msri attribute value using data()
                
                        if (msri) {
                            // Split the msri string into an array
                            var msriArray = msri.split(',');
                
                            // Check if sri matches any item in the msri array
                            if (msriArray.includes(sri)) {
                                $(this).prop('checked', true); // Check the radio button
                                console.log("Matched MSRI:", msri);
                                console.log("Checked Radio ID:", $(this).attr('id'));
                            }
                        }
                    });
                }

                
                    let selectedValues = {};
                
                    // Get the value of the checked radio button in each group
                    $('#flightResults input[type="radio"]:checked').each(function () {
                        let groupName = $(this).attr('name'); // Get the name attribute of the checked radio button
                        let value = $(this).val(); // Get the value attribute of the checked radio button
                        let refundornot=$(this).data("refundornot");
                        let class_index=$(this).data("class_index");

                        $(this).parents(".c-cardList").find(".class_Index").html(class_index);
                        $(this).parents(".c-cardList").find(".refundornot_Index").html(refundornot);
                        
                        // Store the group name and value in the object
                        selectedValues[groupName] = value;
                    });
                    
                    $(".bottom-fixed").show();
                    buttonbookLoader();
                    // Check if selectedValues is not empty
                    if (Object.keys(selectedValues).length > 0) {
                        
                        // Call AJAX if selectedValues is set
                        const queryParams = new URLSearchParams(data).toString();
                        $.ajax({
                            url: `<?php echo BASE_URL_B2C; ?>view/flight/api/getBookingFlightButton.php?${queryParams}`,
                            type: 'POST',
                            dataType: "json",
                            data: { selectedValues: selectedValues },
                            // Send the selected values
                            success: function (response) {
                                // Handle success
                                if (response.status.success) {
                                    // Process the response if successful (for example, display a success message)
                                    BookingBottomButton(response);
                                } else {
                                    // If the response is not successful, handle the error
                                    alert(response.errors[0].message); // Show error message in alert (you can customize the display)
                                    console.error(response.errors[0].message); // Log the error in the console for debugging
                                    $(".bottom-fixed").hide();
                                }
                            },
                            error: function (xhr, status, error) {
                                // Handle any AJAX error (network or server-side)
                                console.error('Error:', error);
                                alert('An error occurred while processing the request.');
                                $(".bottom-fixed").hide();
                            }
                        });
                    }
                }
                
                

            
            function enderFlightCardsNotfound(value)
            {
                var NotfoundCard=`<div class="c-emptyList">
        								<div class="imgDiv">
        									<img src="${BASE_URL}./images/search_illustration.svg" alt="" />
        								</div>
        								<span class="infoDiv"> No records Found </span>
        							</div>`;
			        $(".flightResults_"+value).html(NotfoundCard);
            }


			function submitForm(data) {
			    let searchType="<?=$type?>";
			    data.callBy = callBy;
			    if(callBy!='')
			    {
			        var preloadCard=`<div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>
                                            <div class="preloader-card">
                                                <div class="preloader-logo"></div>
                                                <div class="preloader-details">
                                                  <div class="preloader-line short"></div>
                                                  <div class="preloader-line long"></div>
                                                  <div class="preloader-line medium"></div>
                                                </div>
                                                <div class="preloader-price-btn">
                                                  <div class="preloader-price"></div>
                                                  <div class="preloader-btn"></div>
                                                </div>
                                            </div>`;
			        $(".flightResultstabcontent").html(preloadCard);    
			    }
			    
				$.ajax({
					url: '<?php echo BASE_URL_B2C; ?>view/flight/api/search.php',
					type: 'POST',
					data: data,
					dataType: "json",
					success: function(response)
					{
					    // Get all <div> elements on the page
                        var allDivs = document.querySelectorAll(".flightResultstabcontent");
                        
                        // Create a Set to store unique class names
                        var allClasses = new Set();
                        
                        // Loop through each <div> and collect its classes
                        allDivs.forEach(function (div) {
                          div.classList.forEach(function (className) {
                            allClasses.add(className);
                          });
                        });
                        
                        // Convert the Set to an Array (if needed) and log the result
                        var classArray=Array.from(allClasses);
                        // Filter classes that start with "flightResults_"
                        var filteredClasses = classArray.filter(function (className) {
                            return className.startsWith("flightResults_");
                        });
                        
                        // Remove the prefix "flightResults_"
                        var updatedclassArray = filteredClasses.map(function (className) {
                            return className.replace("flightResults_", "");
                        });

					    let type='';
					    if(response.ONWARD)
					    {
					        type="ONWARD";
					        renderStopsFilter(response.stops);
					        renderAirlineFilter(response.airline);
					        renderarrivalTimeFilter(response.arrivalTime);
					        renderdepartureTimeFilter(response.departureTime);
					        renderFlightCards(response.ONWARD,type);
					        
					         renderFlightCards(response.ONWARD,type);
					        
					        
					    }
					    else
					    {
					        type="ONWARD";
					        enderFlightCardsNotfound(type);
					    }
					    if(response.RETURN)
					    {
					        type="RETURN";
					        renderStopsFilter(response.stops);
					        renderAirlineFilter(response.airline);
					        renderarrivalTimeFilter(response.arrivalTime);
					        renderdepartureTimeFilter(response.departureTime);
					        if(response.RETURN)
					        {
					            renderFlightCards(response.RETURN,type);
					        }
					        else
					        {
					            enderFlightCardsNotfound(type);
					        }
					    }
					    else
					    {
					        type="RETURN";
					        enderFlightCardsNotfound(type);
					    }
					    if(searchType=='multicity')
					    {
					        renderStopsFilter(response.stops);
					        renderAirlineFilter(response.airline);
					        renderarrivalTimeFilter(response.arrivalTime);
					        renderdepartureTimeFilter(response.departureTime);
					        
                                    
					        $.each(response, function (index, value) {
                                if (index !== 'airline' && index !== 'arrivalTime' && index !== 'departureTime' && index !== 'stops') {
                                    // Check if the index is in updatedclassArray
                                    var arrayIndex = $.inArray(index, updatedclassArray);
                                    if (arrayIndex !== -1) {
                                        // Remove the matched index from updatedclassArray
                                        updatedclassArray.splice(arrayIndex, 1);
                                        // Call the render function
                                        renderFlightCards(value, index);
                                    }
                                }
                            });
                            
                            $.each(updatedclassArray, function (index, value) {
                                // Call the render function
                                enderFlightCardsNotfound(value);
                            });
                            

					        
					    }
					    if(response.errors)
					    {
					        alert(response.errors[0].message);
					        $(".bottom-fixed").hide();
					    }
					    else
					    {
					        $(document).ready(function () {
                                // Select all unique groups of radio buttons by name and check the first one
                                let processedGroups = new Set(); // To avoid duplicate processing of groups
                                let msri = "";
                                let sri = "";
                                $('#flightResults input[type="radio"]').each(function () {
                                    let groupName = $(this).attr('name'); // Get the name attribute of the radio button
                            
                                    if (!processedGroups.has(groupName)) {
                                        // Select the first radio button in the group
                                        let firstRadio = $(`input[name="${groupName}"]`).first();
                                        firstRadio.prop('checked', true); // Check the first radio button
                            
                                        // Get the msri and sri values from the first radio button
                                        
                                        if("ONWARD"==groupName)
                                        {
                                            msri = firstRadio.data("msri");
                                            sri = firstRadio.data("sri");
                                        }
                                        
                            
                                        processedGroups.add(groupName); // Mark this group as processed
                                    }
                                });
                                
                            
                            
                                // Call the function after processing all radio buttons
                                // Ensure this is called only once the loop has completed
                                
                                setTimeout(function() {
                                    getBookingFlightButton(msri,sri);
                                }, 0); // Using setTimeout with 0 delay ensures it runs after the loop finishes
                            });
					    }
						
					},
					error: function(response){
						alert("Something Wrong! Please Try Again.");
						$(".bottom-fixed").hide();
					}
				});
			}

			<?php if($type == 'oneway'){ ?>
				function render(){
					$('#div_content').html('<div class="loader"></div>');
				}
			<?php } ?>

var $range = $(".js-range-slider"),
    $inputFrom = $(".js-input-from"),
    $inputTo = $(".js-input-to"),
    instance,
    min = 0,
    max = 700000,
    from = 0,
    to = 700000;

$range.ionRangeSlider({
    skin: "round",
    type: "double",
    min: min,
    max: max,
    from: from,
    to: to,
    onStart: updateInputs,   // Update inputs when slider initializes
    onChange: updateInputs,  // Update inputs during slider movement
    onFinish: onSliderFinish // Trigger action after slider stops
});

instance = $range.data("ionRangeSlider");

// Function to update input fields dynamically
function updateInputs(data) {
    from = data.from;
    to = data.to;

    $inputFrom.val(from); // Update "from" input
    $inputTo.val(to);     // Update "to" input
}

// Function to handle slider finish event and submit data
function onSliderFinish(data) {
    from = data.from;
    to = data.to;

    var prices = from + '-' + to;
    var data1 = {
        searchType: '<?php echo $type; ?>',
        from: '<?php echo $from; ?>',
        to: '<?php echo $to; ?>',
        departureDate: '<?php echo $departureDate; ?>',
        returnDate: '<?php echo $returnDate; ?>',
        adult: '<?php echo $adult; ?>',
        child: '<?php echo $child; ?>',
        infant: '<?php echo $infant; ?>',
        travelClass: '<?php echo $travelClass; ?>',
        pricefrom: from,
        priceto: to,
    };
    callBy="price";
    submitForm(data1); // Submit form with updated data
    console.log("Slider stopped at range: ", data.from, "-", data.to);
}

// Handle "from" input changes
$inputFrom.on("input", function () {
    var val = $(this).val();

    // Validate value
    if (val < min) {
        val = min;
    } else if (val > to) {
        val = to;
    }

    instance.update({
        from: val // Update slider's "from" value
    });
});

// Handle "to" input changes
$inputTo.on("input", function () {
    var val = $(this).val();

    // Validate value
    if (val < from) {
        val = from;
    } else if (val > max) {
        val = max;
    }

    instance.update({
        to: val // Update slider's "to" value
    });
});

    function openTab(event, tabId) {
      // Hide all tab content
      const contents = document.querySelectorAll('.tab-content');
      contents.forEach(content => content.classList.remove('active'));

      // Remove active class from all buttons
      const buttons = document.querySelectorAll('.tab-button');
      buttons.forEach(button => button.classList.remove('active'));

      // Show the selected tab content
      document.getElementById(tabId).classList.add('active');

      // Highlight the active tab button
      event.currentTarget.classList.add('active');
    }
		
        </script>
        <script>
            $(document).on('click', '.book-now', function () {
    const button = $(this);
    const dataId = button.data('id'); // Get the data-id from the button
    const targetUrl = '<?php echo BASE_URL_B2C; ?>view/flight/guest-details.php';

    // Add loading state to the button
    button.addClass('loading');
    button.prop('disabled', true); // Disable button to prevent multiple clicks
    button.find('.loader').show(); // Show loader inside the button

    let selectedValues = {};

    // Get the value of the checked radio button in each group
    $('#flightResults input[type="radio"]:checked').each(function () {
    let groupName = $(this).attr('name'); // Get the name attribute of the checked radio button
    let value = $(this).val(); // Get the value attribute of the checked radio button
    
    // If the 'flightid' key doesn't exist, initialize it as an empty array
    if (!selectedValues['flightid']) {
        selectedValues['flightid'] = [];
    }
    
    // Push the value to the 'flightid' array
    selectedValues['flightid'].push(value); 
});

    // Combine global data object and selectedValues into query parameters
    const combinedData = { ...data, ...selectedValues }; // Merge global data and selectedValues
    const queryParams = Object.entries(combinedData)
        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
        .join('&');

    // Construct the redirect URL
    const redirectUrl = `${targetUrl}?${queryParams}`;

    // Redirect to the target URL
    window.location.href = redirectUrl;
});



        </script>
	</body>
</html>
<?php ob_end_flush(); ?>