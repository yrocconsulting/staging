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
- Never show how many clients we have (it reads small). Aggregate totals only.
- AI tracking clients may be named (client approved). Display names: `theme/yroc/data/clients.json`;
  a domain not listed there is never shown.
  White-label clients (Lovewell & Water Works, Cash Out On My Home, BTAC, Droddy Roofing, Class Act
  Interpreting, Atlas Real Estate Inspections, J3KL Roof Rejuvenation, Riker Roofing) are never named or used as case
  studies; they count in aggregate totals only.
- Tone: established firm (team, office in Sherman TX, track record), not a solo agency.

## How things work
- Pages: `content/manifest.json` + `content/pages/*.html` (block markup). The importer never
  overwrites a page edited in wp-admin unless the workflow is run with "force".
- Homepage sections are PHP patterns in `theme/yroc/patterns/` (`home-hero`, `method`, `cases`, ...).
- Business info, mega menu, departments, speaking list: `theme/yroc/inc/site.php`.
- Forms: `[yroc_form type="contact|audit"]`, submissions saved under **Leads** (`inc/forms.php`).
- Reviews: only from Trustindex (synced daily, `inc/reviews.php`, 5-star only, staff-name filter), Clutch and
  AI Authority Engine's Trustpilot (`theme/yroc/data/reviews-aae.json`, curated snapshot; Trustpilot blocks bots).
  Never use testimonials from the old website.
- Homepage stats: `inc/ai-stats.php`. AI numbers come from the weekly ProRankTracker PDF that lands in a
  shared Drive folder each Monday; `.github/workflows/ai-stats.yml` parses it (`tools/prt_report.py`,
  totals with Yroc's own domain excluded, plus per-prompt showcase rows) and applies it via `deploy/apply_ai_stats.php`.
  The same job posts full per-client results (`--portal-out`) to the Yroc Portal (`/api/prt/ingest`,
  secret `PORTAL_INGEST_SECRET`); that data is internal and may include white-label clients.
- Redirects for old-site URLs: `theme/yroc/inc/redirects.php` (only fire on 404). Add an entry whenever a
  page is removed or renamed.
- Legal pages: `tools/gen_legal.py`. Client service + affiliate terms are carried verbatim; don't reword them.
- SEO: `theme/yroc/inc/seo.php` (titles, descriptions, OG/Twitter, page schema incl. FAQPage/Service/
  BreadcrumbList, core sitemap at /wp-sitemap.xml). Per-page title/description live in `content/seo.json`;
  add an entry for every new page (title under 60 chars, description 110 to 160, no em dashes).
- Homepage hero: live AI results window cycling real prompts from the weekly report (`showcase` in
  `tools/prt_report.py`, prompts at 75%+ visibility, max two per client). No conference footage in the hero.
- Bump `Version` in `style.css` when adding new pattern files (WordPress caches the pattern list).
- Blog: an automated routine publishes an **AI News & Tips** post (category `ai-news`) every Tuesday and Friday
  morning: pushed to `main` (staging deploys), and yroc-wp's "Publish new blog posts" job puts blog-only changes
  live within about 30 minutes. Playbook: `.claude/skills/ai-news-post/SKILL.md`; posts are added with
  `tools/new_post.py` (writes the post, posts.json entry and a branded featured image in `content/blog-images/`).
