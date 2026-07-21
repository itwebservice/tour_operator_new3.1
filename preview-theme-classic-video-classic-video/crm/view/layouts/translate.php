<?php
include_once('../../model/model.php');

global $theme_color, $theme_color_dark, $theme_color_2, $topbar_color, $sidebar_color;

?>

<div class=" google_translation_section" style="text-align:left;display: inline-block; vertical-align: middle; margin: 0px 0;">
    <div id="google_translate_element" style="margin-top: 2px !important; display: inline-block; vertical-align: middle;"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'af,sq,am,ar,hy,az,eu,be,bn,bho,bs,bg,ca,ceb,zh-CN,zh-TW,co,hr,cs,da,nl,en,eo,et,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,he,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,rw,ko,ku,ky,lo,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,ny,or,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,tt,te,th,tr,tk,uk,ur,ug,uz,vi,cy,xh,yi,yo,zu'

            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <style>
        :root {
            --theme-color: <?= $theme_color ?>;
            --theme-color-dark: <?= $theme_color_dark ?>;
        }

        /* ---------- HEADER + TOPBAR ---------- */
        .main-header {
            padding-top: 0;
            background-image: linear-gradient(20deg, #009898, white);
        }

        #topbar {
            background: #0042b3;
            padding: 5px;
        }

        /* ---------- LANGUAGE DROPDOWN ---------- */
        .goog-te-gadget {
            font-size: 1px;
            line-height: 0;
            color: rgb(0, 59, 84);
        }

        .silder_hold .skiptranslate.goog-te-gadget {
            font-size: 0;
        }

        .goog-te-gadget .goog-te-combo {
            margin-top: -24px;
            display: inline-block !important;
            padding: 11px 10px;
            line-height: 11px;
            border: 1px solid var(--theme-color);
            border-radius: 5px;
            background: #fff url('https://w7.pngwing.com/pngs/323/873/png-transparent-arrow-computer-icons-down-arrow-angle-hand-logo-thumbnail.png') no-repeat center right / 15px;
            background-origin: content-box;
            padding-right: 20px;
            color: var(--theme-color);
            -moz-appearance: none;
            appearance: none;
            -webkit-appearance: none;
        }

        .goog-te-gadget .goog-te-combo:hover {
            background-color: var(--theme-color);
            color: #fff;
            border: 1px solid var(--theme-color-dark);
        }

        .goog-te-gadget .goog-te-combo:focus {
            outline: none !important;
        }

        .goog-te-combo option {
            background-color: white !important;
            color: var(--theme-color) !important;
        }

        .goog-te-combo {
            background-color: white !important;
            color: var(--theme-color) !important;
            border: 1px solid var(--theme-color) !important;
        }

        /* ---------- HIDE GOOGLE BRANDING ---------- */
        .goog-te-banner-frame,
        .goog-logo-link {
            display: none !important;
        }

        .VIpgJd-ZVi9od-l4eHX-hSRGPd:link,
        .VIpgJd-ZVi9od-l4eHX-hSRGPd:visited,
        .VIpgJd-ZVi9od-l4eHX-hSRGPd:hover,
        .VIpgJd-ZVi9od-l4eHX-hSRGPd:active {
            display: none;
            font-size: 12px;
            font-weight: bold;
            color: #444;
            text-decoration: none;
        }

        .VIpgJd-ZVi9od-ORHb-OEVmcd {
            z-index: -1 !important;
        }

        /* ---------- CHAT ICON ---------- */
        a.chat-cus i {
            font-size: 45px;
        }

        a.chat-cus {
            color: #fff;
        }

        a.chat-cus:hover {
            color: #009898;
        }

        /* ---------- NAV BAR ---------- */
        .nav-bar ul li.active a {
            color: #0042b3;
            border-bottom: 2px solid #0042b3;
        }

        /* ---------- FOOTER & BUTTONS ---------- */
        .main-footer {
            background-color: #cf0a21;
        }

        .banner .owl-carousel .owl-nav button.owl-prev,
        .banner .owl-carousel .owl-nav button.owl-next {
            background-color: #cf0a21;
        }

        .btn-theme {
            background-color: #cf0a21;
            border: 1px solid #fff;
        }

        /* ---------- MISC ---------- */
        .icons {
            margin: 0 auto;
        }

        .cov-a {
            text-align: center;
        }

        @media only screen and (max-width: 600px) {
            .main-header .goog-te-gadget .goog-te-combo {
                width: 110px;
            }
        }

        @media (min-width: 1200px) {
            .col-lg-4 {
                width: unset !important;
                float: unset !important;
            }
        }
    </style>
</div>