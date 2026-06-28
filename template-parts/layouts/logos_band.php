<?php
/**
 * Logos Band Layout (agence-marketing-digital)
 * Infinite horizontal marquee. CSS keeps it looping as a fallback; JS enhances
 * it so hovering pauses the loop, dragging controls the whole strip, and
 * leaving the band resumes the automatic loop.
 */
$title = v5_get_field_default('section_title', 'ILS NOUS FONT CONFIANCE');

// Collect logos into a normalized list so we can render the row twice (loop).
$items = array();

$logo_query = new WP_Query(array(
  'post_type'      => 'partner_logo',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
));

if ($logo_query->have_posts()) :
  while ($logo_query->have_posts()) :
    $logo_query->the_post();
    $logo_id    = get_the_ID();
    $logo_media = v5_digital_get_field('logo_image_media', $logo_id);
    $logo_url   = v5_digital_get_field('logo_image_url', $logo_id);
    $items[] = array(
      'src'  => !empty($logo_media) ? $logo_media : $logo_url,
      'name' => get_the_title(),
    );
  endwhile;
  wp_reset_postdata();
endif;

if (empty($items)) {
  return;
}
?>

<style>
  .v5-logos-marquee {
    width: 100%;
    overflow: hidden;
    cursor: grab;
    user-select: none;
    touch-action: pan-y;
    /* Soft fade on both edges so logos appear/disappear smoothly. */
    -webkit-mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
            mask-image: linear-gradient(to right, transparent, #000 8%, #000 92%, transparent);
  }
  .v5-logos-marquee.is-dragging {
    cursor: grabbing;
  }
  .v5-logos-track {
    display: flex;
    width: max-content;
    align-items: center;
    animation: v5-logos-scroll 35s linear infinite;
    will-change: transform;
  }
  .v5-logos-marquee.is-js-marquee .v5-logos-track {
    animation: none;
    transform: translate3d(var(--v5-logos-offset, 0px), 0, 0);
  }
  /* Pause when the visitor hovers the band. */
  .v5-logos-marquee:hover .v5-logos-track {
    animation-play-state: paused;
  }
  .v5-logos-item {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    /* Space between logos. */
    padding: 0 2.25rem;
    height: 2.5rem;
  }
  /* Uniform sizing: same height for all, and a width cap so wide wordmarks
     (Samsung, Volvo…) can't dominate the compact icons (Google, Spotify…). */
  .v5-logos-item img {
    height: 1.75rem;
    width: auto;
    max-width: 130px;
    object-fit: contain;
    filter: grayscale(1) saturate(0);
    opacity: 0.62;
    transition: filter 180ms ease, opacity 180ms ease, transform 180ms ease;
  }
  .v5-logos-item:hover img {
    filter: grayscale(0) saturate(1);
    opacity: 1;
    transform: translateY(-1px);
  }
  @media (min-width: 768px) {
    .v5-logos-item img {
      height: 2rem;
      max-width: 150px;
    }
  }
  @keyframes v5-logos-scroll {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
  }
  @media (prefers-reduced-motion: reduce) {
    .v5-logos-track { animation: none; }
  }
</style>

<section class="py-12 bg-slate-50 border-t border-b border-slate-200 overflow-hidden">
  <?php if ($title) : ?>
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-8 text-center">
      <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block mb-10"><?php echo esc_html($title); ?></span>
    </div>
  <?php endif; ?>

  <?php
  if (!empty($items)) :
    // Repeat the set so ONE "half" comfortably exceeds the screen width. Two
    // identical halves animated by exactly -50% make the loop perfectly
    // seamless: a logo leaving the left edge is already re-entering on the
    // right, with no gap and no jump at the wrap point.
    $repeat = max(1, (int) ceil(12 / count($items)));
    $half   = array();
    for ($r = 0; $r < $repeat; $r++) {
      foreach ($items as $it) {
        $half[] = $it;
      }
    }
    // Keep the scroll SPEED constant no matter how many logos there are
    // (~3s of travel per logo), so it always feels the same.
    $duration = max(20, count($half) * 3);
  ?>
    <div class="v5-logos-marquee" data-logo-marquee data-duration="<?php echo (int) $duration; ?>">
      <div class="v5-logos-track" style="animation-duration: <?php echo (int) $duration; ?>s;">
        <?php for ($copy = 0; $copy < 2; $copy++) : ?>
          <?php foreach ($half as $item) : ?>
            <div class="v5-logos-item"<?php echo $copy === 1 ? ' aria-hidden="true"' : ''; ?>>
              <?php if (!empty($item['src'])) : ?>
                <img src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
              <?php else : ?>
                <span class="text-[15px] font-bold text-slate-500 uppercase font-display select-none whitespace-nowrap"><?php echo esc_html($item['name']); ?></span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</section>
