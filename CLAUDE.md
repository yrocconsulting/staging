# Yroc Consulting website: working notes

WordPress block theme (`theme/yroc`) + content (`content/`) deployed to SiteGround staging
(https://yrocstaging.yroc.host) by `.github/workflows/deploy.yml` on every push to `main`.

## Content & style rules (from the client)
- **Never use em dashes (—)** in any copy, code comments, alt text or emails. Use a comma, colon,
  period or "|" (for title separators) instead. En dashes in number ranges (50–100, Mon – Fri) are OK.
- **No employee names anywhere on the site**, except the owner, **Cory Long**.
- No client logos until the client provides an approved, current set.
- Brand: navy/ink #0B1220, Yroc green #7ED957 (dark green #3F8F1C on light backgrounds), steel #547590.
- Type: Inter (body), Inter Tight (headings), JetBrains Mono (small labels only). Emphasis = color, not italics.
- AI Authority Engine lives on its own site, https://aiauthorityengine.org (Cory's AI SEO certification &
  training program). Link there (new tab); /ai-authority-engine/* on this site 301-redirects to it.
- Tone: established firm (team, office in Sherman TX, track record), not a solo agency.

## How things work
- Pages: `content/manifest.json` + `content/pages/*.html` (block markup). The importer never
  overwrites a page edited in wp-admin unless the workflow is run with "force".
- Homepage sections are PHP patterns in `theme/yroc/patterns/` (`home-hero`, `method`, `cases`, ...).
- Business info, mega menu, departments, speaking list: `theme/yroc/inc/site.php`.
- Forms: `[yroc_form type="contact|audit"]`, submissions saved under **Leads** (`inc/forms.php`).
- Reviews: synced daily from Trustindex (`inc/reviews.php`), 5-star only, staff-name filter.
- Homepage stats: `inc/ai-stats.php`. AI numbers come from the weekly ProRankTracker PDF that lands in a
  shared Drive folder each Monday; `.github/workflows/ai-stats.yml` parses it (`tools/prt_report.py`,
  totals only, Yroc's own domain excluded, never client names) and applies it via `deploy/apply_ai_stats.php`.
- Hero video: `theme/yroc/assets/video/hero-speaking.mp4` (720p H.264, keep under ~4 MB).
- Bump `Version` in `style.css` when adding new pattern files (WordPress caches the pattern list).
