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
          <div class="row align-items-center">
            <div class="col-md-6 col-xs-12 order-2 order-md-1">
              <span
                class="fs-7 d-block mb-0 text-md-start text-center text-white">
                Copyright © <?= date('Y'); ?> <?php echo $app_name; ?>. All rights reserved
              </span>
            </div>

            <div
              class="col-md-6 col-xs-12 mb-4 mb-md-0 order-1 order-md-2 text-center text-md-end">
              <div class="d-inline-flex flex-row gap-2">
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

    <div id="site_alert"></div>
    <script src="./js/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery.validate.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/owl.carousel.min.js"></script>
    <script type="text/javascript" src="js/jquery.datetimepicker.full.js"></script>
    <script type="text/javascript" src="js/select2.min.js"></script>
    <script src="./js/masonry.pkgd.min.js"></script>
    <script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/vi.alert.js"></script>

    </body>

    </html>

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

    <script>
      const metaData = <?php echo json_encode($meta_tags, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
      const getPageMeta = (pageName) => {
        return metaData.find(item => item.page === pageName);
      };
      const metas = getPageMeta('<?= $_SESSION['page_type'] ?>');
      console.log(metas);
      if (metas) {
        var meta = document.createElement('meta');

        meta.setAttribute('name', 'keywords');
        meta.setAttribute('content', metas.keywords);
        var meta2 = document.createElement('meta');
        meta2.setAttribute('name', 'description');
        meta2.setAttribute('content', metas.description);
        document.title = metas.title;
        document.getElementsByTagName('head')[0].appendChild(meta);
        document.getElementsByTagName('head')[0].appendChild(meta2);
      }
    </script>
<!-- Tidio Chat -->
<?php
echo '<script src="'.$tidio_chat.'"></script>';?>

    </body>

    </html>