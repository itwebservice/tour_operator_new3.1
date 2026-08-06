</div>
<div id="div_customer_save_modal"></div>
<div id="div_city_save_modal"></div>
<div id="div_hotel_supplier_quick_modal"></div>
<div id="div_airport_quick_modal"></div>
<div id="site_alert"></div>
<div id="vi_confirm_box"></div>
<div id="app_color_scheme_content"></div>
<div id="div_content_modal"></div>
<div id="div_itinerary_modal"></div>
<div id="vehicle_add_modal"></div>

<style>
.relative.inline-flex.shrink-0.justify-center{
  display: none;
}
    .toast-container {
  width: 90%;
  max-width: 580px;
  margin: 0 auto;
}

[class*="toast-pos-"] {
  position: absolute;
  padding: 10px;
}

.toast-pos-top {
  top: 0;
}

.toast-pos-right {
  right: 0;
}

.toast-pos-bottom {
  bottom: 0;
}

.toast-pos-left {
  left: 0;
}

.toast {
  display: none;
  padding: 20px;
  margin: 20px 0;
  background: #eeeeee;
  color: #fff;
}

.close-toast {
  float: right;
  text-decoration: none;
  color: #ffffff;
  vertical-align: middle;
}


.toast-trigger {
  color: #ffffff;  
}

.toast {
 display: none;
  padding: 20px;
  margin: 20px 0;
  background: rgb(29, 145, 42);
  color: #fff;
}

.toast-trigger {
  display: inline-block;
  top: 50%;
  left: 50%;
  margin: 10px;
  padding: 20px 40px;
  background: transparent;
  color: #ffffff;
  border: 1px solid #ffffff;
  text-decoration: none;
  transition: ease .2s;
}

.toast-trigger:hover {
  background: #ffffff;
  color: #009688;
}
.viewMessage
{
  color: white;
  text-decoration: underline !important;
  font-size: 11px;
}

</style>
<div id="messageNotifi" class="toast-container toast-pos-right toast-pos-bottom"></div>

<!-- Topbar Element -->
<div id="notification_block_bg_id" class="notification_bg"  onclick="display_notification()">
</div>

<!-- Notificatio Body -->
<div id="notification_block_body_id" class="notifications_body_block">
      <?php include_once("notifications/display_notification_modal.php")  ?>
 </div>
<?php
$settings = mysqli_fetch_array(mysqlQuery("select client_id from app_settings"));
$client_id = !empty($settings['client_id']) ? $settings['client_id'] : 0;
?>
<input type="hidden" id="client_id" value="<?= $client_id ?>">
<input type="hidden" id="base_url_support" value="https://itourssupport.in/">
<script>
//**Sidebar toggle script start
$(function(){
	$('.sidebar_toggle_btn').click(function(){
		$('.sidebar_wrap, .app_content_wrap').toggleClass('toggle');
	});
	var width = $(window).width();
	if(width<992){
		$('.sidebar_wrap, .app_content_wrap').addClass('toggle');
	}else{
		$('.sidebar_wrap, .app_content_wrap').removeClass('toggle');
	}
});
$(window).resize(function(){
	var width = $(window).width();
	if(width<992){
		$('.sidebar_wrap, .app_content_wrap').addClass('toggle');
	}else{
		$('.sidebar_wrap, .app_content_wrap').removeClass('toggle');
	}
});
//**Sidebar toggle script end
    function notification_count_update() {
        
        var base_url = $('#base_url').val();
        $.post(base_url+'view/layouts/notifications/notification_count.php', {    }, function(data) {
            $('#notify_count').html(data);
        });
    }
    notification_count_update();

</script>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script>
var totmsg=0;
function getNotifications() {
	var base_url = $('#base_url_support').val();
	var client_id = $('#client_id').val();
	$.post(base_url + 'view/ask_gayatri/chat/get_notification_client.php', { is_client: true,client_id: client_id }, function (data) {
		var resp = JSON.parse(data);
		var html = "";
		if(resp)
		{
    		resp.forEach(function (res) 
    		{
    		    if(totmsg!=res.totalmessage)
    		    {
    		    $(".newmessagetost").remove();
    		    var base_url = $('#base_url').val();
                      html +='<div class="toast newmessagetost" id="toast-name-1">';
                      html +='<a href="#" class="close-toast">&#10006;</a>';
                      html +='<b>'+res.totalmessage+' New Messege!</b> '+ res.message +'.';
                      html +='<br><a class="viewMessage" href="'+base_url+'view/ask_gayatri/index.php?notifi=newmessage"  class="viewMessage">View Message</a>';
                      html +='</div>';
                     $('#messageNotifi').html(html);
                     $(".newmessagetost").fadeIn(400);
                     totmsg=res.totalmessage;
    		    }
    	});
		}
		else
		{
		    totmsg=0;
		    $('.close-toast').parent().remove();
		}
		
		

	});
}
$(document).on("click",".close-toast",function(e) {
  e.preventDefault();
  $(this).parent().remove();
});
// $(document).on("click",".viewMessage",function() {
//   var base_url = $('#base_url').val();
//   totmsg=0;
//   $('.close-toast').parent().remove();
//   window.location.href=base_url+"view/ask_gayatri/index.php?notifi=newmessage";
// });

</script>
<?php
if(!$_GET['notifi'])
{
?>
<script>
$(document).ready(function () {
    getNotifications();
    
	setInterval(function () {
		getNotifications();
	}, 2000);
});

</script>
<?php
}
?>
<style>
/* Prevent 1s flash of logo / Need help? while widget boots */
elevenlabs-convai:not(.el-header-ready) {
  visibility: hidden !important;
}
</style>
<script src="https://unpkg.com/@elevenlabs/convai-widget-embed" async type="text/javascript"></script>

<elevenlabs-convai agent-id="agent_1901krgdr6jyedwt06z6grvqgzx0"></elevenlabs-convai>

<script>
(function () {
  var HEADER_SELECTOR = ".flex.items-center.gap-2.min-w-60";
  var STYLE_ID = "el-hide-need-help-header";

  function injectShadowCss(shadow) {
    if (!shadow || shadow.getElementById(STYLE_ID)) return;
    var style = document.createElement("style");
    style.id = STYLE_ID;
    // Hide logo + Need help? header only; keep call/chat/tools
    style.textContent =
      HEADER_SELECTOR + '{display:none!important;}' +
      HEADER_SELECTOR + ' *{display:none!important;}';
    shadow.appendChild(style);
  }

  function revealWidget(widget) {
    if (widget && !widget.classList.contains("el-header-ready")) {
      widget.classList.add("el-header-ready");  
    }
  }

  function hideNeedHelpHeader() {
    var widget = document.querySelector("elevenlabs-convai");
    if (!widget) return false;
    if (!widget.shadowRoot) return false;

    injectShadowCss(widget.shadowRoot);

    var headers = widget.shadowRoot.querySelectorAll(HEADER_SELECTOR);
    headers.forEach(function (el) {
      var text = (el.textContent || "").trim();
      if (!text || text.indexOf("Need help?") !== -1) {
        el.style.setProperty("display", "none", "important");
      }
    });

    // Reveal once shadow UI exists (CSS already hides header)
    if (widget.shadowRoot.childNodes.length) {
      revealWidget(widget);
      return true;
    }
    return false;
  }

  var tries = 0;
  var timer = setInterval(function () {
    tries++;
    var done = hideNeedHelpHeader();
    if (done || tries > 50) {
      clearInterval(timer);
      // Fail-safe: never leave widget permanently invisible
      revealWidget(document.querySelector("elevenlabs-convai"));
      var widget = document.querySelector("elevenlabs-convai");
      if (widget && widget.shadowRoot) {
        new MutationObserver(function () {
          injectShadowCss(widget.shadowRoot);
          hideNeedHelpHeader();
        }).observe(widget.shadowRoot, { childList: true, subtree: true });
      }
    }
  }, 50);
})();
</script>
</body>
</html>