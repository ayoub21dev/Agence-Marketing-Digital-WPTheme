# Blog permalinks moved under /blog/ (2026-07-07)

## What changed

**One file: `functions.php`** — two additions at the end of the migrations
section (search for `v5_digital_set_blog_permalink_structure_once`):

1. **One-time permalink switch** — on the next wp-admin visit by an
   administrator, the permalink structure is set to `/blog/%postname%/` and
   rewrite rules are flushed. Guarded by the `v5_blog_permalinks_v1_done`
   option so it runs exactly once per site (local AND production each run it
   on their own first admin visit after deploy). Afterwards the setting
   remains freely editable in Settings → Permalinks.
2. **301 redirects for the old URLs** — requests to old root-level article
   URLs (`/top-agencies/`) permanently redirect to the new home
   (`/blog/top-agencies/`). Pages are unaffected (they keep root URLs). This
   handler is generic: any published post requested as a bare root slug gets
   redirected, so no per-article list to maintain.

The XML sitemap needs no change — it emits `get_permalink()`, so it serves
the new `/blog/…` URLs automatically once the structure switches.

## Effects to expect

- Article URLs change: `agencemarketingdigital.com/top-agencies/` →
  `agencemarketingdigital.com/blog/top-agencies/`.
- Google re-crawls via the 301s and the updated sitemap; a brief settling
  period (days, not months) is normal for a site this young.
- **After deploying, log into the production wp-admin once** — that's what
  triggers the switch there.

## How to revert

A code revert alone is NOT enough — the permalink structure is a database
setting. Full revert:

1. Delete the two code blocks in `functions.php` (the
   `v5_digital_set_blog_permalink_structure_once` function + its
   `add_action`, and the `template_redirect` 301 closure right below it).
2. In wp-admin → Settings → Permalinks, set the structure back to
   `/%postname%/` and save (this also reflushes rewrite rules).
3. Optionally clean the flag:
   `delete_option('v5_blog_permalinks_v1_done')` (or via WP-CLI:
   `wp option delete v5_blog_permalinks_v1_done`).

Note: reverting after Google has indexed the `/blog/…` URLs would need
reverse 301s — avoid flip-flopping.
