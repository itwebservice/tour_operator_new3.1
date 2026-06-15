<?php
include "../../../model/model.php";
include_once "../../../model/app_settings/print_html/quotation_html/generic_builder_config.php";

$config   = gqb_get_config();
$active   = !empty($config['active']);
$sections = isset($config['sections']) ? $config['sections'] : array();
$testims  = isset($config['testimonials']) ? $config['testimonials'] : array();
$active_template = isset($config['active_template']) ? $config['active_template'] : '';

// Human readable labels for each section toggle.
$section_labels = array(
    'hero'                => 'Hero Page',
    'tour_overview'       => 'Tour Overview',
    'hotels'              => 'Hotels',
    'flights'             => 'Flight Details',
    'trains'              => 'Train Details',
    'cruises'             => 'Cruise Details',
    'activities'          => 'Activity Details',
    'vehicles'            => 'Vehicles / Transportation',
    'itinerary'           => 'Itinerary',
    'inclusion_exclusion' => 'Inclusions / Exclusions',
    'costing'             => 'Costing Details',
    'bank_details'        => 'Bank Details',
    'terms_conditions'    => 'Terms & Conditions',
    'testimonials'        => 'Customer Testimonials',
    'thank_you'           => 'Thank You',
);

$api_url = BASE_URL . 'model/app_settings/print_html/quotation_html/quotation_data_api.php';
?>

<form id="quotation_builder_info" class="mg_tp_30">
    <div class="row mg_tp_30">
        <div class="col-md-12">
            <div class="panel panel-default panel-body app_panel_style feildset-panel">
                <legend>Generic Quotation Builder</legend>

                <div class="row mg_bt_20">
                    <div class="col-md-6">
                        <label class="app_dual_button <?= $active ? 'active' : '' ?>" for="qb_active">
                            <input type="checkbox" id="qb_active" name="qb_active" value="1" <?= $active ? 'checked' : '' ?>>
                            &nbsp;&nbsp;Activate Generic Quotation Builder
                        </label>
                        <p class="mg_tp_10">
                            <small>When active, developers can fetch a complete quotation data-set (JSON) from the API endpoint below and build any custom template design.</small>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label>Data API Endpoint</label>
                        <input type="text" class="form-control" readonly value="<?= htmlspecialchars($api_url) ?>?quotation_id=YOUR_ID" onclick="this.select();">
                        <small>Returns all sections (hero, overview, hotels, flights, trains, cruises, activities, vehicles, itinerary, inclusions/exclusions, costing, bank, testimonials, terms, thank you) in one response.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section visibility -->
    <div class="row mg_tp_20">
        <div class="col-md-12">
            <div class="panel panel-default panel-body app_panel_style feildset-panel">
                <legend>Sections To Expose</legend>
                <div class="row">
                    <?php foreach ($section_labels as $key => $label) {
                        $checked = (!isset($sections[$key]) || $sections[$key]) ? 'checked' : '';
                    ?>
                        <div class="col-md-3 col-sm-4 col-xs-6 mg_bt_10">
                            <label>
                                <input type="checkbox" class="qb_section" data-key="<?= $key ?>" value="1" <?= $checked ?>>
                                &nbsp;<?= $label ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="row mg_tp_20">
        <div class="col-md-12">
            <div class="panel panel-default panel-body app_panel_style feildset-panel">
                <legend>Customer Testimonials</legend>
                <div id="qb_testimonials_wrap">
                    <?php
                    if (!empty($testims)) {
                        foreach ($testims as $t) {
                            $name = isset($t['name']) ? htmlspecialchars($t['name']) : '';
                            $desig = isset($t['designation']) ? htmlspecialchars($t['designation']) : '';
                            $review = isset($t['review']) ? htmlspecialchars($t['review']) : '';
                            $photo = isset($t['photo']) ? htmlspecialchars($t['photo']) : '';
                    ?>
                            <div class="row qb_testm_row mg_bt_10">
                                <div class="col-md-3"><input type="text" class="form-control qb_t_name" placeholder="Traveller Name" value="<?= $name ?>"></div>
                                <div class="col-md-2"><input type="text" class="form-control qb_t_desig" placeholder="Designation" value="<?= $desig ?>"></div>
                                <div class="col-md-3"><input type="text" class="form-control qb_t_review" placeholder="Review" value="<?= $review ?>"></div>
                                <div class="col-md-3"><input type="text" class="form-control qb_t_photo" placeholder="Photo URL" value="<?= $photo ?>"></div>
                                <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm qb_remove_testm"><i class="fa fa-trash"></i></button></div>
                            </div>
                    <?php }
                    } ?>
                </div>
                <button type="button" class="btn btn-info btn-sm mg_tp_10" id="qb_add_testm"><i class="fa fa-plus"></i>&nbsp;Add Testimonial</button>
            </div>
        </div>
    </div>

    <div class="row mg_tp_20">
        <div class="col-md-12 text-right">
            <button class="btn btn-sm btn-success" id="qb_save"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;Save</button>
        </div>
    </div>

    <input type="hidden" name="active_template" id="qb_active_template" value="<?= htmlspecialchars($active_template) ?>">
</form>

<script>
    function qb_testm_template() {
        return '<div class="row qb_testm_row mg_bt_10">' +
            '<div class="col-md-3"><input type="text" class="form-control qb_t_name" placeholder="Traveller Name"></div>' +
            '<div class="col-md-2"><input type="text" class="form-control qb_t_desig" placeholder="Designation"></div>' +
            '<div class="col-md-3"><input type="text" class="form-control qb_t_review" placeholder="Review"></div>' +
            '<div class="col-md-3"><input type="text" class="form-control qb_t_photo" placeholder="Photo URL"></div>' +
            '<div class="col-md-1"><button type="button" class="btn btn-danger btn-sm qb_remove_testm"><i class="fa fa-trash"></i></button></div>' +
            '</div>';
    }

    $('#qb_add_testm').on('click', function() {
        $('#qb_testimonials_wrap').append(qb_testm_template());
    });

    $(document).on('click', '.qb_remove_testm', function() {
        $(this).closest('.qb_testm_row').remove();
    });

    $('#qb_save').on('click', function(e) {
        e.preventDefault();
        var base_url = $('#base_url').val();

        var sections = {};
        $('.qb_section').each(function() {
            sections[$(this).data('key')] = $(this).is(':checked');
        });

        var testimonials = [];
        $('.qb_testm_row').each(function() {
            var name = $(this).find('.qb_t_name').val();
            var review = $(this).find('.qb_t_review').val();
            if ((name && name.trim() !== '') || (review && review.trim() !== '')) {
                testimonials.push({
                    name: name,
                    designation: $(this).find('.qb_t_desig').val(),
                    review: review,
                    photo: $(this).find('.qb_t_photo').val()
                });
            }
        });

        var payload = {
            active: $('#qb_active').is(':checked') ? 1 : 0,
            sections: JSON.stringify(sections),
            testimonials: JSON.stringify(testimonials),
            active_template: $('#qb_active_template').val()
        };

        $('#qb_save').button('loading');
        $.ajax({
            type: 'post',
            url: base_url + 'controller/app_settings/setting/quotation_builder_save.php',
            data: payload,
            success: function(result) {
                success_msg_alert(result);
                $('#qb_save').button('reset');
            },
            error: function() {
                error_msg_alert('Sorry, settings could not be saved.');
                $('#qb_save').button('reset');
            }
        });
    });
</script>
