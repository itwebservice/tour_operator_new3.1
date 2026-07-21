<button id="scrollToTopBtn" title="Go to top">
      ↑
    </button>

    <style>
      /* scrolling bar css  */

      #scrollToTopBtn {
        position: fixed;
        bottom: 120px;
        right: 40px;
        z-index: 99;
        background-color: #555;
        color: white;
        border: none;
        padding: 7px 15px;
        border-radius: 100px;
        cursor: pointer;
        font-size: 18px;
        display: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
      }

      #scrollToTopBtn:hover {
        background-color: #333;
      }
    </style>
    <!-- ***** Footer Section ***** -->
    <footer class="c-footer">
      <!-- Footer Top -->
      <div class="footer-top">
        <div class="container-lg">
          <div class="row">
            <div class="col-md-4 col-sm-6">
              <div class="sectionBlock mb-3 mb-md-0">
                <div class="heading">
                  <p class="fs-6 fw-medium mb-4 text-white">Need any help?</p>
                </div>
                <div class="body">
                  <span class="fs-8 fw-medium d-block text-white">
                    Call 24/7 for any help
                  </span>
                  <a href="tel:<?php echo $app_contact_no; ?>"
                    class="fs-5 color-secondary d-block fw-medium mb-3 text-decoration-none">
                    <i class="fa-solid fa-phone me-2 fs-6"></i> <?php echo $app_contact_no; ?>
                  </a>
                  <span class="fs-8 fw-medium d-block text-white">
                    Mail to our support team
                  </span>
                  <a
                    href="mailTo:<?= $app_email_id_send ?>"
                    class="fs-5 color-secondary d-block fw-medium mb-2 text-decoration-none">
                    <i class="fa-solid fa-envelope me-2 fs-6"></i>
                    <?php echo $app_email_id_send; ?>
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="sectionBlock mb-3 mb-md-0">
                <div class="heading">
                  <p class="fs-6 fw-medium mb-4 text-white">Company</p>
                </div>
                <div class="body">
                  <a
                    href="<?= BASE_URL_B2C ?>about.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    About Us
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>award.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Awards
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>careers.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Career
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>gallery.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Gallery
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="sectionBlock mb-3 mb-md-0">
                <div class="heading">
                  <p class="fs-6 fw-medium mb-4 text-white">Support</p>
                </div>
                <div class="body">
                  <a
                    href="<?= BASE_URL_B2C ?>offers.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Offers
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>services.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Services
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>testimonials.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Testimonials
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>contact.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Contact Us
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="sectionBlock mb-3 mb-md-0">
                <div class="heading">
                  <p class="fs-6 fw-medium mb-4 text-white">Other Services</p>
                </div>
                <div class="body">
                  <a
                    href="<?= BASE_URL_B2C ?>view/activities/activities-listing.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Activities
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>view/ferry/ferry-listing.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Cruise
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>view/hotel/hotel-listing.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Hotel
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>view/visa/visa-listing.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Visa
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-2 col-sm-6">
              <div class="sectionBlock mb-3 mb-md-0">
                <div class="heading">
                  <p class="fs-6 fw-medium mb-4 text-white">Important Links</p>
                </div>
                <div class="body">
                  <a
                    href="<?= BASE_URL_B2C ?>terms-conditions.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Terms Of Use
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>privacy-policy.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Privacy Policy
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>cancellation-policy.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Cancellation Policy
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>refund-policy.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Refund Policy
                  </a>
                  <a
                    href="<?= BASE_URL_B2C ?>blog.php"
                    class="text-decoration-none d-block fs-7 mb-2 text-white">
                    Travel Blog
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer Top End -->

      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="container-lg">
          <div class="row align-items-left">
            <div class="col-md-6 col-xs-12 order-2 order-md-1">
              <span
                class="fs-7 d-block mb-0 text-md-start text-left text-white">
                Copyright © <?= date('Y'); ?> <?php echo $app_name; ?>. All rights reserved
              </span>
            </div>

            <div
              class="col-md-6 col-xs-12 mb-4 mb-md-0 order-1 order-md-2 text-right text-md-end">
              <div class="d-inline-flex flex-row gap-2">
               <?php
    $socialIcons = $themeData->getSocialIcons();
    ?>
                <?php foreach ($socialIcons as $icon): ?>
                  <?php if ($icon['fb']) { ?>
                    <a href="<?php echo $icon['fb']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-facebook-f"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['tw']) { ?>
                    <a href="<?php echo $icon['tw']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-twitter"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['inst']) { ?>
                    <a href="<?php echo $icon['inst']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-instagram"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['li']) { ?>
                    <a href="<?php echo $icon['li']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-linkedin"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['wa']) { ?>
                    <a href="<?php echo $icon['wa']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  <?php } ?>
                  <?php if ($icon['yu']) { ?>
                    <a href="<?php echo $icon['yu']; ?>" class="settingButton transparent" target="_blank">
                      <i class="fa-brands fa-youtube"></i>
                    </a>
                  <?php } ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer Bottom End -->
    </footer>
    <!-- ***** Footer Section Section ***** -->
   
    <!-- Footer End -->

    </div>

    <div id="site_alert"></div>

    <div id='hotel-result'></div>
      <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery-ui.1.10.4.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/popper.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/bootstrap-4.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/owl.carousel.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/select2.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/theme-scripts.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/vi.alert.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/jquery.validate.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery-confirm.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/pagination.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL ?>js/jquery.datetimepicker.full.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lightgallery.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-thumbnail.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/lg-zoom.min.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/scripts.js"></script>

    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/custom.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>layouts/custom_js.js"></script>
  <script>
      window.onscroll = function() {
        let btn = document.getElementById("scrollToTopBtn");
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
          btn.style.display = "block";
        } else {
          btn.style.display = "none";
        }
      };
      document.getElementById("scrollToTopBtn").onclick = function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      };
    </script>
  <link rel="stylesheet" href="<?php echo BASE_URL_B2C ?>layouts/custom-styles.css?v=<?php echo time(); ?>">

  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/ion-rangeslider@2.3.0/js/ion.rangeSlider.min.js"></script>

<!-- Tidio Chat -->
<?php
echo '<script src="'.$tidio_chat.'"></script>';?>