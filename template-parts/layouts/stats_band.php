<?php
/**
 * Stats Band Layout (agence-marketing-digital)
 */
$stats = v5_get_field_default('stats', array());

if (!is_array($stats)) {
    $stats = array();
}

if (empty($stats)) {
    return;
}
?>
<section class="relative z-20 -mt-11 md:-mt-16 pb-9 md:pb-12 bg-transparent">
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-3xl mx-auto">
            <?php foreach ($stats as $stat) :
                $number = isset($stat['number']) ? $stat['number'] : '';
                $label  = isset($stat['label']) ? $stat['label'] : '';
                ?>
                <div class="text-center">
                    <div class="text-[1rem] md:text-[1.15rem] font-extrabold text-slate-900 tracking-tight font-display"><?php echo esc_html($number); ?></div>
                    <div class="text-[8.5px] md:text-[9.5px] text-slate-500 font-semibold mt-1 uppercase tracking-wider font-mono leading-snug"><?php echo esc_html($label); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
