<!-- ***** Footer Section ***** -->

<button id="scrollToTopBtn" title="Go to top">
  ↑
</button>
<footer class="c-footer">
  <!-- Footer Top -->
  <div class="footer-top">
    <div class="container-lg">
      <div class="row">
        <div class="col-md-4 col-sm-6">
          <div class="sectionBlock mb-3 mb-md-0">
            <div class="heading">
              <p class="title fs-6 fw-medium mb-0">Need any help?</p>
            </div>
            <div class="body">
              <span class="fs-7 fw-medium d-block text-secondary">
                Call 24/7 for any help
              </span>
              <a
                href="tel:<?php echo $app_contact_no; ?>"
                class="fs-5 color-primary d-block fw-medium mb-3 text-decoration-none">
                <i class="fa-solid fa-phone me-2 fs-6"></i> <?php echo $app_contact_no; ?>
              </a>
              <span class="fs-7 fw-medium d-block text-secondary">
                Mail to our support team
              </span>
              <a
                href="mailTo:support@domain.com"
                class="fs-5 color-primary d-block fw-medium mb-2 text-decoration-none">
                <i class="fa-solid fa-envelope me-2 fs-6"></i>
                <?php echo $app_email_id_send; ?>
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-3 mb-md-0">
            <div class="heading">
              <p class="title fs-6 fw-medium mb-0">Company</p>
            </div>
            <div class="body">
              <a
                href="<?= BASE_URL_B2C ?>about.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                About Us
              </a>
              <a
                href="<?= BASE_URL_B2C ?>award.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Awards
              </a>
              <a
                href="<?= BASE_URL_B2C ?>careers.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Career
              </a>
              <a
                href="<?= BASE_URL_B2C ?>gallery.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Gallery
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-3 mb-md-0">
            <div class="heading">
              <p class="title fs-6 fw-medium mb-0">Recommended</p>
            </div>
            <div class="body">
              <a
                href="<?= BASE_URL_B2C ?>offers.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Offers
              </a>
              <a
                href="<?= BASE_URL_B2C ?>services.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Services
              </a>
              <a
                href="<?= BASE_URL_B2C ?>testimonials.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Testimonials
              </a>
              <a
                href="<?= BASE_URL_B2C ?>contact.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Contact Us
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-3 mb-md-0">
            <div class="heading">
              <p class="title fs-6 fw-medium mb-0">Services</p>
            </div>
            <div class="body">
              <a
                href="<?= BASE_URL_B2C ?>view/activities/activities-listing.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Activities
              </a>
              <a
                href="<?= BASE_URL_B2C ?>view/ferry/ferry-listing.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Cruise
              </a>
              <a
                href="<?= BASE_URL_B2C ?>view/hotel/hotel-listing.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Hotel
              </a>
              <a
                href="<?= BASE_URL_B2C ?>view/visa/visa-listing.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Visa
              </a>
            </div>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-3 mb-md-0">
            <div class="heading">
              <p class="title fs-6 fw-medium mb-0">Important Links</p>
            </div>
            <div class="body">
              <a
                href="<?= BASE_URL_B2C ?>terms-conditions.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Terms Of Use
              </a>
              <a
                href="<?= BASE_URL_B2C ?>privacy-policy.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Privacy Policy
              </a>
              <a
                href="<?= BASE_URL_B2C ?>cancellation-policy.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Cancellation Policy
              </a>
              <a
                href="<?= BASE_URL_B2C ?>refund-policy.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
                Refund Policy
              </a>
              <a
                href="<?= BASE_URL_B2C ?>blog.php"
                class="text-decoration-none d-block fs-6 mb-2 text-secondary">
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
          <span class="fs-7 d-block mb-0 text-md-start text-center">
            Copyright © 2025 All Rights Reserved
          </span>
        </div>
        <div
          class="col-md-6 col-xs-12 mb-4 mb-md-0 order-1 order-md-2 text-center text-md-end">
          <img src="./images/cards.png" alt="'cards" />
        </div>
      </div>
    </div>
  </div>
  <div id="site_alert"></div>

  <!-- Footer Bottom End -->
</footer>
<!-- ***** Footer Section Section ***** -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="<?php echo BASE_URL_B2C ?>js/jquery-3.6.3.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery.validate.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="js/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" src="js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/vi.alert.js"></script>
<style>
  .vi_alert_parent .item {
    padding: 0px 0px 78px 19px !important;
  }
</style>
<script>
  if (!sessionStorage.getItem('final_arr')) {
    var initial_array = [];
    initial_array.push({
      rooms: {
        room: 1,
        adults: 2,
        child: 0,
        childAge: []
      }
    });
    sessionStorage.setItem('final_arr', JSON.stringify(initial_array));
  }
  // scroling feature add js code by vidya 

  // बटन को दिखाने के लिए जब यूज़र नीचे स्क्रॉल करता है
  window.onscroll = function() {
    let btn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  };

  // बटन पर क्लिक करने से पेज ऊपर स्क्रॉल हो जाएगा
  document.getElementById("scrollToTopBtn").onclick = function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };
</script>

</body>

</html>