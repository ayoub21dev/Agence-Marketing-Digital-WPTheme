<?php
/**
 * Layout: Formulaire — renders a form built in the "AMD Contact Forms" plugin.
 *
 * Added to the page builder so a plugin form can be dropped in as a movable
 * section (choose which form from the dropdown, drag it anywhere like Hero/Stats).
 * ACF layout `form_section` → this file (template dispatch strips `_section`).
 */

$amd_form_id = v5_digital_get_sub_field('amd_form_id');
if (!$amd_form_id) {
    return; // no form chosen yet
}

$section_title = v5_digital_get_sub_field('section_title');
$section_desc  = v5_digital_get_sub_field('section_desc');
?>

<section class="py-12 md:py-16 bg-slate-50/30">
    <div class="max-w-3xl mx-auto px-5 sm:px-6 lg:px-8">
        <?php
        if (class_exists('AMD_CF_Form')) {
            // Direct render (avoids shortcode-attribute quoting issues with the title/desc).
            echo AMD_CF_Form::render(array(
                'id'          => $amd_form_id,
                'title'       => (string) $section_title,
                'description' => (string) $section_desc,
            ));
        } else {
            // Plugin inactive: fall back to the shortcode (renders nothing if absent).
            echo do_shortcode('[amd_contact_form id="' . esc_attr($amd_form_id) . '"]');
        }
        ?>
    </div>
</section>
