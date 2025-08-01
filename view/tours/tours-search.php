 <form id="frm_tours_search">

   <div class="row">

     <input type='hidden' id='page_type' value='search_page' name='search_page' />

     <!-- *** Destination Name *** -->

     <div class="col-md-3 col-sm-6 col-12 mb-3">
       <div class="filterItemSection mb-md-0 mb-3">
         <div class="form-group">


           <span class="d-block fs-7 text-secondary mb-1">
             Destination*
           </span>

           <div class="c-select2DD c-advanceSelect transparent">

             <select id='tours_dest_filter' class="full-width js-roomCount js-advanceSelect" onchange="package_dynamic_reflect(this.id);" style="width:220px !important;">

               <option value="">Destination</option>

               <?php

                $sq_query = mysqlQuery("select * from destination_master where status != 'Inactive'");

                while ($row_dest = mysqli_fetch_assoc($sq_query)) { ?>

                 <option value="<?php echo $row_dest['dest_id']; ?>"><?php echo $row_dest['dest_name']; ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** Destination Name End *** -->

     <!-- *** tours Name *** -->

     <div class="col-md-3 col-sm-6 col-12 mb-3">
       <div class="filterItemSection mb-md-0 mb-3">
         <div class="form-group">


           <span class="d-block fs-7 text-secondary mb-1">
             Tour
           </span>


           <div class="c-select2DD c-advanceSelect transparent">

             <select id='tours_name_filter' class="full-width js-roomCount js-advanceSelect">

               <option value=''>Tour Name</option>

               <?php

                $query = "select package_id, package_name,total_nights,total_days from custom_package_master where 1 and status!='Inactive'";

                $sq_tours = mysqlQuery($query);

                while ($row_tours = mysqli_fetch_assoc($sq_tours)) {

                ?>

                 <option value="<?php echo $row_tours['package_id'] ?>"><?php echo $row_tours['package_name'] . " (" . $row_tours['total_nights'] . "N /" . $row_tours['total_days'] . "D)" ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** tours Name End *** -->



     <!-- *** Date *** -->

     <div class="col-md-3 col-sm-6 col-12 mb-3">

       <div class="filterItemSection mb-md-0 mb-3">

         <div class="form-group">

           <span class="d-block fs-7 text-secondary mb-1">
             Travel Date*
           </span>
           <div class="c-calendar transparent">
             <div class="input-group date js-calendar">
               <input
                 type="text"
                 class="form-control js-calendar-date" placeholder="mm/dd/yy" name="travelDate" id="travelDate" required /><span class="input-group-addon"><i
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

             <select name="tadult" id='tadult' class="full-width js-advanceSelect" required>

               <?php for ($i = 0; $i <= 10; $i++) { ?>

                 <option value="<?= $i ?>"><?= $i ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** Adult End *** -->

     <!-- *** Child W/o Bed *** -->

     <div class="col-md-3 col-sm-6 col-12" style="display:none">
       <div class="filterItemSection mb-md-0 mb-3">
         <div class="form-group">

           <span class="d-block fs-7 text-secondary mb-1">
             Child Without Bed(2-5 Yrs)
           </span>


           <div class="selector c-advanceSelect transparent">

             <select name="child_wobed" id='child_wobed' class="full-width js-advanceSelect">

               <?php for ($i = 0; $i <= 10; $i++) { ?>

                 <option value="<?= $i ?>"><?= $i ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** Child W/o Bed End *** -->

     <!-- *** Child With Bed *** -->

     <div class="col-md-3 col-sm-6 col-12" style="display:none">
       <div class="filterItemSection mb-md-0 mb-3">

         <div class="form-group">
           <span class="d-block fs-7 text-secondary mb-1">
             Child With Bed(5-12 Yrs)
           </span>
           <div class="selector c-advanceSelect transparent">

             <select name="child_wibed" id='child_wibed' class="full-width js-advanceSelect">

               <?php for ($i = 0; $i <= 10; $i++) { ?>

                 <option value="<?= $i ?>"><?= $i ?></option>

               <?php } ?>

             </select>

           </div>

         </div>

       </div>
     </div>

     <!-- *** Child With Bed End *** -->

     <!-- *** Extra Bed *** -->

     <div class="col-md-3 col-sm-6 col-12" style="display:none">
       <div class="filterItemSection mb-md-0 mb-3">
         <div class="form-group">


           <span class="d-block fs-7 text-secondary mb-1">
             Extra Bed
           </span>

           <div class="selector c-advanceSelect transparent">

             <select name="extra_bed" id='extra_bed' class="full-width js-advanceSelect">

               <?php for ($i = 0; $i <= 10; $i++) { ?>

                 <option value="<?= $i ?>"><?= $i ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** Extra Bed End *** -->

     <!-- *** Infant *** -->

     <div class="col-md-3 col-sm-6 col-12" style="display:none">
       <div class="filterItemSection mb-md-0 mb-3">
         <div class="form-group">
           <span class="d-block fs-7 text-secondary mb-1">
             Infants(0-2 Yrs)
           </span>

           <div class="selector c-advanceSelect transparent">

             <select name="tinfant" id='tinfant' class="full-width js-advanceSelect">

               <?php for ($i = 0; $i <= 10; $i++) { ?>

                 <option value="<?= $i ?>"><?= $i ?></option>

               <?php } ?>

             </select>

           </div>

         </div>
       </div>
     </div>

     <!-- *** Infant End *** -->

     <div class="col-12 mt-3">
       <div class="text-center">
         <button class="btn c-button btn-lg">Search</button>
       </div>
     </div>

   </div>

 </form>