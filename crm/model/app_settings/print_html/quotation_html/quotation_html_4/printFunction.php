
<!DOCTYPE html>
<html>
<head>
	<title>.</title>
  <meta charset="utf-8">
	<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,500" rel="stylesheet">

  <link rel="stylesheet" href="<?php echo BASE_URL ?>css/font-awesome-4.7.0/css/font-awesome.min.css">
  
  <link rel="stylesheet" type="text/css" href="<?php echo BASE_URL ?>css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>css/app/admin.php">
  <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>css/app/app.php">
  <link rel="stylesheet" media="all" href="<?= BASE_URL ?>css/print/quotationGeneric.php"/>
  <link rel="stylesheet" media="all" href="<?= BASE_URL ?>css/print/printQuotationfour/quotationPrint.php"/>
	<link rel="stylesheet" media="all" href="<?= BASE_URL ?>css/print/printQuotationfour/quotationPdf.css"/>

<script src="<?= BASE_URL ?>js/jquery-3.1.0.min.js"></script>
<script src="<?= BASE_URL ?>js/jquery-ui.min.js"></script>
<script src="<?= BASE_URL ?>js/bootstrap.min.js"></script>

<script type="text/javascript">
  (function() {
    var printed = false;
    function doPrint() {
      if (printed) return;
      printed = true;
      try { window.focus(); } catch (e) {}
      window.print();
    }
    function waitForImages() {
      var imgs = Array.prototype.slice.call(document.images || []);
      var pending = imgs.filter(function(img) { return !img.complete; });
      if (pending.length === 0) return Promise.resolve();
      return Promise.all(pending.map(function(img) {
        return new Promise(function(resolve) {
          img.addEventListener('load', resolve, { once: true });
          img.addEventListener('error', resolve, { once: true });
        });
      }));
    }
    function waitForFonts() {
      if (document.fonts && document.fonts.ready) {
        return document.fonts.ready.catch(function() {});
      }
      return Promise.resolve();
    }
    function ready() {
      var safety = new Promise(function(resolve) { setTimeout(resolve, 6000); });
      Promise.race([
        Promise.all([waitForImages(), waitForFonts()]),
        safety
      ]).then(function() {
        setTimeout(doPrint, 200);
      });
    }
    if (document.readyState === 'complete') {
      ready();
    } else {
      window.addEventListener('load', ready);
    }
  })();
</script>

</head>
<body>
