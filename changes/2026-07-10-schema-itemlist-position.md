# ItemList: `position` was the RANK badge, not the list position (2026-07-10)

<!-- changelog: Fixed -->

Found by extracting the real JSON-LD from rendered pages while checking whether
`STRUCTURED-DATA.md` still matched the code.

## The bug

`single-blog.php` built each `ListItem` as:

```php
'position' => intval($schema_rev['rank']) ?: count($schema_items) + 1,
```

`rank` is the agency's **RANK badge** (`agency_rank`, 1–6 across the six seeded
agencies) — a property of the *agency*, not of its place in *this* list.

`schema.org/ListItem.position` is the item's position **within the list**, and
Google requires it to be 1-based and consecutive. Using the badge breaks that
whenever a listicle does not happen to start at rank 1 and step by one.

Measured on the real site before the fix:

| URL | emitted |
| --- | --- |
| `/blog/seo-casablanca/` | `numberOfItems: 1`, `itemListElement[0].position: 4` ❌ |
| `/blog/top-agencies/` | `numberOfItems: 2`, positions `[1, 2]` ✅ *by coincidence* |

`/blog/top-agencies/` ranks RMD (#1) and Pixagram (#2), so it produced a valid
list purely by luck. Any article ranking, say, agencies #3 and #5 would have
emitted positions `[3, 5]` with `numberOfItems: 2`.

## The fix

The rank still **orders** the list (the existing `usort` is untouched); it is no
longer used as the position. After sorting, items are renumbered 1..n:

```php
$schema_position = 0;
foreach ($schema_items as &$schema_item) {
    $schema_item['position'] = ++$schema_position;
}
unset($schema_item); // break the reference from foreach-by-ref
```

One file, `single-blog.php`. Nothing else changed.

## Verification

JSON-LD extracted from the rendered pages and parsed, before and after:

| Check | Result |
| --- | --- |
| `/blog/seo-casablanca/` positions | `[4]` → **`[1]`** |
| `/blog/top-agencies/` positions | `[1,2]` → `[1,2]` (unchanged) |
| positions 1-based and consecutive | both pages **yes** |
| `numberOfItems` equals item count | both pages yes |
| list order still follows the RANK badge | yes — `RMD`, `Pixagram` |
| `url` values | still the agencies' external websites |
| `/blog/hello-world/` (no ranking block) | still emits **no** `ItemList` |
| Organization schema on 12 sampled pages | 1 block each, valid JSON |
| `php -l single-blog.php` | clean |

## While here: two gaps in `STRUCTURED-DATA.md`

- It never mentioned **`sameAs`**, which `v5_digital_organization_schema()` emits
  from the Site Settings social links. Added.
- It said `logo` is omitted when absent, but not that **`description`** (the site
  tagline) and `sameAs` are omitted the same way. On the current install only
  `@id`, `name`, `url` and `email` appear — that is by design, not a bug.

It also now records that `ListItem.url` is the agency's **external website**, never
its WordPress permalink — which matters because since 2026-07-10 the `agency` CPT
is no longer publicly queryable and those permalinks 404. The JSON-LD contains no
dead links.

## Not done

`AggregateRating` on agencies is still listed as a future idea. Note that agencies
no longer have public pages, so a rating would have to live on the article, and
Google requires the reviewed item and its rating to be visible on that page.
