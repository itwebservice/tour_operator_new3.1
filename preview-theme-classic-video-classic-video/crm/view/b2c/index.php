<?php
include "../../model/model.php";
/*======******Header******=======*/
require_once('../layouts/admin_header.php');
?>
<?= begin_panel('B2C Settings', '') ?>
<?php
if ($b2c_flag == '1') { ?>

<div class="div_left type-02">
    <ul class="nav nav-pills">
        <li role="presentation" class="dropdown active">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('24')">
                <i class="fa fa-image" aria-hidden="true"></i>Header Menu
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('1')">
                <i class="fa fa-image" aria-hidden="true"></i>Banner Images
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('2')">
                <i class="fa fa-map" aria-hidden="true"></i>Popular Package Tours
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('23')">
                <i class="fa fa-users" aria-hidden="true"></i>Popular Group Tours
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('3')">
                <i class="fa fa-building" aria-hidden="true"></i>Popular Hotels
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('4')">
                <i class="fa fa-life-ring" aria-hidden="true"></i>Popular Activities
            </a>
        </li>
        <!-- <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" onclick="reflect_data('5')">
                <i class="fa fa-database" aria-hidden="true"></i>Package Tours 
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" onclick="reflect_data('12')">
                <i class="fa fa-database" aria-hidden="true"></i>Group Tours 
            </a>
        </li> -->
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('10')">
                <i class="fa fa-envelope" aria-hidden="true"></i>Social Media
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('7')">
                <i class="fa fa-money" aria-hidden="true"></i>Payment Gateway
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('8')">
                <i class="fa fa-male" aria-hidden="true"></i>Customer Testimonials
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('9')">
                <i class="fa fa-book" aria-hidden="true"></i>Policies
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('13')">
                <i class="fa fa-map-pin" aria-hidden="true"></i>Blogs
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('18')">
                <i class="fa fa-sitemap" aria-hidden="true"></i>Meta Tags
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('14')">
                <i class="fa fa-window-restore" aria-hidden="true"></i>Gallery
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('17')">
                <i class="fa fa-file-text" aria-hidden="true"></i>Coupon
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('16')">
                <i class="fa fa-google" aria-hidden="true"></i>Google Map
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('21')">
                <i class="fa fa-google" aria-hidden="true"></i>Google Analytics
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('19')">
                <i class="fa fa-random" aria-hidden="true"></i>Color Scheme
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('20')">
                <i class="fa fa-handshake-o" aria-hidden="true"></i>Association Partners
            </a>
        </li>
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('22')">
                <i class="fa fa-comment-o" aria-hidden="true"></i>Tidio Chat
            </a>
        </li>
        
        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('25')">
                <i class="fa fa-briefcase" aria-hidden="true" style="color:rgb(51, 194, 234);"></i>Career
            </a>
        </li>

        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('26')">
                <i class="fa fa-cogs" aria-hidden="true" style="color:rgb(64, 15, 209);"></i>Services
            </a>
        </li>

        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('27')">
                <i class="fa fa-user-circle" aria-hidden="true" style="color:rgb(194, 85, 87);"></i>Our Team
            </a>
        </li>

        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('28')">
                <i class="fa fa-trophy" aria-hidden="true" style="color:rgb(239, 193, 43);"></i>Awards
            </a>
        </li>

        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('29')">
                <i class="fa fa-id-card" aria-hidden="true" style="color:rgb(89, 79, 148);"></i>About Us
            </a>
        </li>

        <li role="presentation" class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                aria-expanded="false" onclick="reflect_data('30')">
                <i class="fa fa-list-alt" aria-hidden="true" style="color:rgb(194, 85, 87);"></i>Booking
            </a>
        </li>
    </ul>
</div>
<div class="div_right type-02">
    <div id="section_data_form"></div>
</div>

<script src="<?php echo BASE_URL ?>js/app/footer_scripts.js"></script>
<script src="<?php echo BASE_URL ?>js/app/field_validation.js"></script>
<script type="text/javascript">
function reflect_data(section, dest_id1 = '') {

    if (section === '1') {
        $.post('banners/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '2') {
        $.post('package_tours/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '3') {
        $.post('hotels/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '4') {
        $.post('activities/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '5') {
        $.post('package_tours_datewise/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '12') {
        $.post('group_tours_datewise/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '6') {
        $.post('footer_package_tours/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '7') {
        $.post('enquiry_or_book/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '8') {
        $.post('testimonials/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '9') {
        $.post('policies/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '10') {
        $.post('social_media.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '13') {
        $.post('blogs/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '14') {
        $.post('gallery/index.php', {
            dest_id1: dest_id1
        }, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '15') {
        $.post('b2c_training.php', {
            dest_id1: dest_id1
        }, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '16') {
        $.post('google_map_script.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '17') {
        $.post('coupon_codes.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '18') {
        $.post('meta_tags.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '19') {
        $.post('color_scheme.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '20') {
        $.post('association_logos.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '21') {
        $.post('google_analytics.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '22') {
        $.post('chat_code.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '23') {
        $.post('group_tours/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }
    if (section === '24') {
        $.post('menus/index.php', {}, function(data) {
            $('#section_data_form').html(data);
        });
    }


    if(section === '25'){

$.post('career/index.php' , {} ,function(data){

    $('#section_data_form').html(data);
})
}


if(section === '26'){

$.post('services/index.php' , {} ,function(data){

$('#section_data_form').html(data);
})
}

if(section === '27'){

$.post('team_details/index.php' , {} ,function(data){

$('#section_data_form').html(data);
})
}


if(section === '28'){

$.post('awards/index.php' , {} ,function(data){

$('#section_data_form').html(data);
})
}

if(section === '29'){

$.post('about_us/index.php' , {} ,function(data){

$('#section_data_form').html(data);
})
}

if(section === '30'){

$.post('booking/index.php' , {} ,function(data){

$('#section_data_form').html(data);
})
}

    $('.type-02 .dropdown .dropdown-toggle').on('click', function() {
        $(this).parent('.dropdown').addClass('active').siblings().removeClass('active');
    })
}
reflect_data('24');
</script>
<?= end_panel() ?>
<?php
    /*======******Footer******=======*/
    require_once('../layouts/admin_footer.php');
    ?>
<?php } else { ?>
<div class="alert alert-danger" role="alert">
    Please upgrade the subscription to use this feature.
</div>
<?php } ?>