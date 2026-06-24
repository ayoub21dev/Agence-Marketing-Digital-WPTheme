<?php
/**
 * Logos Band Layout (v5-digital)
 */
$title = v5_get_field_default('section_title', 'ILS NOUS FONT CONFIANCE');
$logo_query = new WP_Query(array(
  'post_type' => 'partner_logo',
  'posts_per_page' => -1,
  'post_status' => 'publish'
));
?>
<section class="py-10 bg-white border-b border-slate-200">
  <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
    <?php if ($title): ?>
      <span
        class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-5"><?php echo esc_html($title); ?></span>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-8 md:gap-x-16">
      <?php
      if ($logo_query->have_posts()):
        while ($logo_query->have_posts()):
          $logo_query->the_post();
          $logo_id = get_the_ID();
          $logo_media = get_field('logo_image_media', $logo_id);
          $logo_url = get_field('logo_image_url', $logo_id);
          $logo_src = !empty($logo_media) ? $logo_media : $logo_url;
          ?>
          <div class="flex items-center justify-center h-10 min-w-[100px] max-w-[150px]">
            <?php if (!empty($logo_src)): ?>
              <img src="<?php echo esc_url($logo_src); ?>" alt="<?php the_title_attribute(); ?>" class="max-h-8 md:max-h-10 w-auto object-contain transition-all duration-300 opacity-60 hover:opacity-100 filter grayscale hover:grayscale-0">
            <?php else: ?>
              <div class="text-[15px] font-bold text-slate-400 hover:text-slate-700 transition-colors uppercase font-display select-none">
                <?php the_title(); ?>
              </div>
            <?php endif; ?>
          </div>
        <?php
        endwhile;
        wp_reset_postdata();
      else:
        // Fallback static items
        $fallbacks = array(
          array('name' => 'Nestle', 'slug' => 'nestle'),
          array('name' => 'Google', 'slug' => 'google'),
          array('name' => 'Hyundai', 'slug' => 'hyundai'),
          array('name' => 'L\'Oreal', 'slug' => 'loreal'),
          array('name' => 'Volvo', 'slug' => 'volvo'),
          array('name' => 'Samsung', 'slug' => 'samsung')
        );
        foreach ($fallbacks as $fallback):
          $has_simple_icon = in_array($fallback['slug'], array('google', 'hyundai', 'volvo', 'samsung'));
          ?>
          <div class="flex items-center justify-center h-10 min-w-[100px] max-w-[150px]">
            <?php if ($has_simple_icon): ?>
              <img src="https://cdn.simpleicons.org/<?php echo $fallback['slug']; ?>/66717f" alt="<?php echo esc_attr($fallback['name']); ?>" class="max-h-8 md:max-h-10 w-auto object-contain transition-all duration-300 opacity-60 hover:opacity-100 filter grayscale hover:grayscale-0">
            <?php else: ?>
              <div class="text-[15px] font-bold text-slate-400 hover:text-slate-700 transition-colors uppercase font-display select-none">
                <?php echo esc_html($fallback['name']); ?>
              </div>
            <?php endif; ?>
          </div>
          <?php
        endforeach;
      endif;
      ?>
    </div>
  </div>
</section>