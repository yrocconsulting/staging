---
name: ai-news-post
description: Research, write and publish the three-times-weekly "AI News & Tips" blog post for yrocconsulting.com (pushed to main; staging deploys at once and production picks it up automatically). Use when a scheduled run or the user asks for a new AI news post.
---

# AI News & Tips post

Three times a week (Monday, Wednesday and Friday mornings, US Central) the site publishes one post in the
**AI News & Tips** category (`ai-news`). Posts go live automatically: there is no human review,
so every check below matters.

## 1. Pick the topic

1. Read the titles of the last 10 posts in `content/posts.json` so you never repeat a topic or angle.
2. Search the web for what happened in AI in the **last 2 to 3 days** (Monday's post covers Friday and the weekend,
   Wednesday's covers Monday and Tuesday, Friday's covers Wednesday and Thursday). Good sources: official blogs and changelogs (Google Search Central,
   Google Keyword/Ads blogs, OpenAI, Anthropic, Microsoft, Perplexity, Meta), Search Engine Land,
   Search Engine Roundtable, The Verge, TechCrunch, Ars Technica, reputable newsrooms.
3. Choose the one thing a **small or mid-size business owner or marketer** would most want to know.
   Priority: changes to AI search and assistants (Google AI Overviews / AI Mode, ChatGPT search, Gemini,
   Perplexity, Copilot), local search and reviews, new tools that save time, practical how-tos.
   Skip pure funding news, model benchmarks and hype unless there is a clear "so what" for a business.
4. If the week is quiet, write an evergreen practical post instead (a tip, a checklist, a how-to,
   a myth vs fact), still tied to something current where possible.

## 2. Verify

- Every factual claim (dates, numbers, features, availability) must come from a source you actually opened.
  Prefer the primary source. If two sources disagree or you are unsure, leave the claim out.
- Link 2 to 5 sources inline (`<a href="..." target="_blank" rel="noopener">`). Never invent quotes,
  statistics, studies or URLs. Do not copy sentences from sources; write in your own words.
- No predictions stated as fact, no legal, medical or financial advice.

## 3. Write

- **Length**: 700 to 1,100 words. **Reading level**: plain English, short paragraphs (2 to 4 sentences).
- **Structure** (plain HTML, same as other posts in `content/posts/`; no `<h1>`, no inline styles, no scripts):
  - Opening `<p>`: what happened and why it matters, in 2 to 3 sentences.
  - 3 to 5 `<h2>` sections, e.g. "What changed", "Who it affects", "What to do now". Use `<h3>`, `<ul>`/`<ol>` where helpful.
  - A practical section with concrete steps the reader can take this week.
  - Closing `<p>` that ties back to AI visibility and links to one relevant page on the site, e.g.
    `/ai-visibility-audit/`, `/ai-search-optimization/`, `/reputation-management/`, `/book-a-strategy-call/`,
    or a `/kb/` article. One soft call to action, no hard sell.
- **Voice**: Yroc Consulting as an established firm ("we", "our team", office in Sherman, TX). Confident, helpful, no hype.
- **Title**: specific and useful, under 70 characters, no clickbait. **Excerpt**: 110 to 160 characters, used as the meta description.
- **Tags**: 2 to 4, comma-separated (e.g. "Google AI Overviews, Local SEO").

### House rules (from the client; never break these)
- **Never use em dashes** (the long dash). Use a comma, colon or period. En dashes only in number ranges.
- **No employee names** anywhere, except the owner, **Cory Long** (and only when relevant).
- Never state how many clients we have. Never name clients, and never name or hint at white-label clients.
- No client logos, no testimonials from the old website, no invented case results.
- Emphasis with `<strong>`, not italics.

## 4. Publish to staging

```bash
python3 tools/new_post.py --title "..." --excerpt "..." --body /path/to/body.html --tags "A, B"
```

The script checks the rules above (it refuses em dashes, bad lengths, duplicate slugs), creates the
branded featured image in `content/blog-images/`, the post file and the `posts.json` entry, dated now.
Fix anything it reports and run it again. Then:

```bash
git add content/posts.json content/posts content/blog-images
git commit -m "Blog: <title>"
git fetch origin main && git rebase origin/main
git push origin HEAD:main
```

If the push is rejected because `main` moved, fetch, rebase and push again. Pushing to `main` deploys
staging automatically (`.github/workflows/deploy.yml`); wait for that run to succeed and check the post
loads at `https://yrocstaging.yroc.host/ai-news/<slug>/`.

## 5. Production

You do not deploy production yourself. The **Publish new blog posts** workflow in `yrocconsulting/yroc-wp`
runs every 30 minutes and puts blog-only changes from `main` live automatically. After the staging check,
poll `https://yrocconsulting.com/ai-news/<slug>/?v=<anything>` every few minutes (up to about 45 minutes)
until it returns 200 and shows the title. If it never appears, say so in the report (a non-blog change
waiting on `main` blocks auto-publishing until someone runs **Deploy to production**).
Never touch other pages, posts or theme files.

## 6. Report

Finish with: the title, the live URL, the sources used, and anything you were unsure about.
