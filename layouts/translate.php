<?php

$theme_color = '#007BFF'; // Bright blue
$theme_color_dark = '#0056b3'; // Dark blue
$theme_color_2 = '#17A2B8'; // Cyan accent
$topbar_color = '#343A40'; // Dark gray
$sidebar_color = '#212529'; // Almost black (for clean UI)

?>
<style>
    .goog-te-banner-frame {
        display: none !important;
    }

    .VIpgJd-ZVi9od-ORHb {
        display: none !important;
    }

    .VIpgJd-ZVi9od-ORHb-OEVmcd .skiptranslate {
        display: none !important;
    }

    .skiptranslate iframe {
        display: none !important;
    }

    body {
        top: 0px !important;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Google Translate Element (hidden) -->
<div id="google_translate_element" style="display:none;"></div>
<select id="lang-select" name="state" class="js-advanceSelect notranslate" translate="no">
    <!-- Top common ones -->
    <option value="en">English</option>
    <option value="es">Spanish</option>
    <option value="fr">French</option>
    <option value="de">German</option>
    <option value="zh-CN">Chinese (Simplified)</option>
    <option value="ja">Japanese</option>
    <option value="ru">Russian</option>
    <option value="ar">Arabic</option>
    <!-- Add all supported Google Translate languages -->
    <option value="af">Afrikaans</option>
    <option value="am">Amharic</option>
    <option value="az">Azerbaijani</option>
    <option value="be">Belarusian</option>
    <option value="bg">Bulgarian</option>
    <option value="bn">Bengali</option>
    <option value="bs">Bosnian</option>
    <option value="ca">Catalan</option>
    <option value="ceb">Cebuano</option>
    <option value="cs">Czech</option>
    <option value="cy">Welsh</option>
    <option value="da">Danish</option>
    <option value="el">Greek</option>
    <option value="et">Estonian</option>
    <option value="fa">Persian</option>
    <option value="fi">Finnish</option>
    <option value="gl">Galician</option>
    <option value="gu">Gujarati</option>
    <option value="ha">Hausa</option>
    <option value="haw">Hawaiian</option>
    <option value="hi">Hindi</option>
    <option value="hmn">Hmong</option>
    <option value="hr">Croatian</option>
    <option value="ht">Haitian Creole</option>
    <option value="hu">Hungarian</option>
    <option value="hy">Armenian</option>
    <option value="id">Indonesian</option>
    <option value="ig">Igbo</option>
    <option value="is">Icelandic</option>
    <option value="it">Italian</option>
    <option value="iw">Hebrew</option>
    <option value="jv">Javanese</option>
    <option value="ka">Georgian</option>
    <option value="kk">Kazakh</option>
    <option value="km">Khmer</option>
    <option value="kn">Kannada</option>
    <option value="ko">Korean</option>
    <option value="ku">Kurdish</option>
    <option value="ky">Kyrgyz</option>
    <option value="la">Latin</option>
    <option value="lo">Lao</option>
    <option value="lt">Lithuanian</option>
    <option value="lv">Latvian</option>
    <option value="mg">Malagasy</option>
    <option value="mi">Maori</option>
    <option value="mk">Macedonian</option>
    <option value="ml">Malayalam</option>
    <option value="mn">Mongolian</option>
    <option value="mr">Marathi</option>
    <option value="ms">Malay</option>
    <option value="mt">Maltese</option>
    <option value="my">Myanmar (Burmese)</option>
    <option value="ne">Nepali</option>
    <option value="nl">Dutch</option>
    <option value="no">Norwegian</option>
    <option value="ny">Chichewa</option>
    <option value="pa">Punjabi</option>
    <option value="pl">Polish</option>
    <option value="ps">Pashto</option>
    <option value="ro">Romanian</option>
    <option value="si">Sinhala</option>
    <option value="sk">Slovak</option>
    <option value="sl">Slovenian</option>
    <option value="so">Somali</option>
    <option value="sq">Albanian</option>
    <option value="sr">Serbian</option>
    <option value="su">Sundanese</option>
    <option value="sv">Swedish</option>
    <option value="sw">Swahili</option>
    <option value="ta">Tamil</option>
    <option value="te">Telugu</option>
    <option value="th">Thai</option>
    <option value="tl">Filipino</option>
    <option value="tr">Turkish</option>
    <option value="uk">Ukrainian</option>
    <option value="ur">Urdu</option>
    <option value="uz">Uzbek</option>
    <option value="vi">Vietnamese</option>
    <option value="xh">Xhosa</option>
    <option value="yi">Yiddish</option>
    <option value="yo">Yoruba</option>
    <option value="zu">Zulu</option>
</select>


<script>
    $(document).ready(function() {
        $('#lang-select2').html($('#lang-select').html());
        $('#lang-select, #lang-select2').select2();

        $('.select2-container').attr('translate', 'no').addClass('notranslate');

        $('#lang-select, #lang-select2').on('select2:open', function() {
            $('.select2-dropdown').attr('translate', 'no').addClass('notranslate');
        });

        // Initialize Google Translate widget
        window.googleTranslateElementInit = function() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                autoDisplay: false
            }, 'google_translate_element');
        };

        (function() {
            var gtScript = document.createElement('script');
            gtScript.type = 'text/javascript';
            gtScript.async = true;
            gtScript.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            document.head.appendChild(gtScript);
        })();

        // Cookie-based language switch
        function triggerTranslate(lang) {
            if (lang === 'en' || lang === '') {
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=" + window.location.hostname + ";";
                location.reload();
                return;
            }

            document.cookie = `googtrans=/en/${lang}; path=/;`;
            document.cookie = `googtrans=/en/${lang}; path=/; domain=${window.location.hostname};`;
            location.reload();
        }

        $('#lang-select, #lang-select2').on('change', function() {
            var lang = $(this).val();
            triggerTranslate(lang);
        });

        // Pre-select current language if cookie exists
        function getCookie(name) {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? decodeURIComponent(match[2]) : null;
        }

        const currentLang = getCookie('googtrans');
        if (currentLang) {
            const langCode = currentLang.split('/')[2];
            $('#lang-select, #lang-select2').val(langCode).trigger('change.select2');
        }
    });
</script>