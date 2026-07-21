<?php

include 'config.php';

//Include header

include 'layouts/header2.php';
$_SESSION['page_type'] = 'award';

$awards = mysqli_fetch_all(mysqlQuery("SELECT * FROM `b2c_awards`"), MYSQLI_ASSOC);


?>


<!-- ********** Component :: Page Title ********** -->

<div class="c-pageTitleSect ts-pageTitleSect">

  <div class="container">

    <div class="row">

      <div class="col-md-7 col-12">



        <!-- *** Search Head **** -->

        <div class="searchHeading">

          <span class="pageTitle mb-0">Awards</span>

        </div>

        <!-- *** Search Head End **** -->

      </div>



      <div class="col-md-5 col-12 c-breadcrumbs">

        <ul>

          <li>

            <a href="<?= BASE_URL_B2C ?>">Home</a>

          </li>

          <li class="st-active">

            <a href="javascript:void(0)">Awards</a>

          </li>

        </ul>

      </div>



    </div>

  </div>

</div>

<!-- ********** Component :: Page Title End ********** -->

<section class="ts-destinations-section">

  <div class="container-fluid">

    <div id="lightGalleryImage" class="light-gallery-list">

      <?php
      if (!empty($awards)) :
        foreach ($awards as $award) :
          // Normalize the image path
          $cleanUrl = preg_replace('/(\/+)/', '/', $award['image']); // Remove duplicate slashes
          $finalImageUrl = BASE_URL . str_replace('../', '', $cleanUrl); // Remove '../' and prepend base URL

      ?>
          <a href="<?= htmlspecialchars($finalImageUrl) ?>" class="lightGalleryImage award-item">
            <div class="award-img-wrapper">
              <img alt="<?= htmlspecialchars($award['title']) ?>" src="<?= htmlspecialchars($finalImageUrl) ?>" class="img-fluid" />
              <div class="award-hover-content">
                <h5><?= htmlspecialchars($award['title']) ?></h5>
              </div>
            </div>
          </a>
        <?php
        endforeach;
      else :
        ?>
        <p class="text-center font-weight-bold text-danger">No awards available</p>
      <?php
      endif;
      ?>


    </div>

  </div>

</section>

<?php include 'layouts/footer2.php'; ?>



<script type="text/javascript" src="js2/scripts.js"></script>

<script>
  $(document).ready(function() {



    lightGallery(document.getElementById('lightGalleryImage'), {

      plugins: [lgZoom, lgThumbnail],

      speed: 500,

      download: true,

    });

    var width = $(".light-gallery-item img").width();

    $(".light-gallery-item img").height(width);



  });
</script>