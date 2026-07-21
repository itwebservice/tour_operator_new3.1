<style>
  .buy-now-float {
    position: fixed;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    background-color: #ff0000;
    color: #fff;
    padding: 12px 20px;
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    font-weight: bold;
    font-size: 16px;
    z-index: 9999;
    text-decoration: none;
    transition: background 0.3s ease, transform 0.3s ease;
  }

  .buy-now-float:hover {
    background-color: #f41010;
    transform: translateY(-50%) scale(1.05);
  }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    .buy-now-float {
      font-size: 14px;
      padding: 10px 16px;
    }
  }
</style>
<?php
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip === '127.0.0.1' || $ip === '::1') {
  $ip = '49.205.150.123'; // Example Indian IP
}
$locationData = json_decode(file_get_contents("http://ip-api.com/json/$ip"));
$isIndia = ($locationData && $locationData->countryCode === 'IN');
$currency = $isIndia ? "INR" : "$";
?>
<!-- Theme selection form -->
<form id="hiddenForm" method="POST" action="https://tourthemestore.com/payments/razor-pay/index.php" style="display:none;">
  <input type="hidden" name="theme" id="theme" value="Classic">
  <input type="hidden" name="banner" id="banner" value="04. Video Banner">
  <input type="hidden" name="currency" id="currency" value="<?php echo $currency; ?>">
  <input type="hidden" name="amount" id="amount" value="22000">
</form>
<a href="#hiddenForm" class="buy-now-float">Buy Now</a>

<script>
  $(document).on('click', '.buy-now-float', function(e) {
    e.preventDefault();
    e.stopPropagation();

    const theme = $('#hiddenForm #theme').val();
    const banner = $('#hiddenForm #banner').val();
    const currency = $('#hiddenForm #currency').val();
    const amount = $('#hiddenForm #amount').val();

    // Step 4: Submit or alert
    if (theme && banner && amount && currency) {
      document.getElementById('hiddenForm').submit();
    } else {
      alert("Please contact us, cannot proceed with this theme.");
    }
  });
</script>