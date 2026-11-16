"""One-time converter: old WPBakery case study pages -> block-markup pages for the Yroc theme.

Usage: python3 tools/convert_cases.py <dir with <slug>.json + media.json> content/pages
"""
import html
import json
import re
import sys

from bs4 import BeautifulSoup, Comment

src, out = sys.argv[1], sys.argv[2]
media = {str(m['id']): m['source_url'] for m in json.load(open(f'{src}/media.json'))}

CASES = [
    # slug, title, tag, category, summary
    ('thai-ginger-case-study', 'Thai Ginger: #1 in ChatGPT and Google AI Overviews', 'Restaurant · Bellevue, WA', 'ai-visibility',
     'An AI SEO strategy took a beloved Seattle-area restaurant to the #1 recommendation in ChatGPT, Google\'s AI Overview, the local map pack and organic search.'),
    ('fractional-cfo-reputation-management-case-study', 'Fractional CFO: Page One Reclaimed in 90 Days', 'Reputation Management', 'reputation',
     'Negative press buried a financial professional\'s brand. Ninety days later, he owned page one and controlled his online narrative.'),
    ('blacksmith-roofing-case-study', 'Blacksmith Roofing: 8× the Phone Calls', 'Roofing · Oklahoma', 'local',
     'An Oklahoma roofing company\'s phone calls octupled in a single month as its maps and organic visibility took off.'),
    ('irs-trouble-solvers-case-study', 'IRS Trouble Solvers: Outranking the IRS', 'Tax Resolution · National', 'local',
     'A tax resolution firm outranked the IRS on Google and turned that search traffic into a steady stream of high-value leads.'),
    ('the-bug-guys', 'The Bug Guys: +285% Organic Traffic', 'Pest Control · Local', 'local',
     'A locally owned pest control company nearly quadrupled its organic traffic in a single month.'),
    ('medical-weight-loss-case-study', 'Wake Up Skinny: 50–100 Leads a Month', 'Medical Weight Loss Clinic', 'local',
     'A bold SEO strategy doubled new users and produced 50–100 monthly leads without spending on ads.'),
    ('hormone-replacement-therapy-clinic-case-study', 'Hormone Clinic: $20K in 60 Days', 'Hormone Replacement Clinic', 'local',
     'SEO, paid social and local marketing drove a 70% traffic surge and $20K in new revenue in 60 days.'),
]


def no_emdash(s):
    s = re.sub(r'\s*—\s*(?=(?:<[^>]+>)*[A-Z0-9"“])', ': ', s)
    return re.sub(r'\s*—\s*', ', ', s)


def clean(fragment):
    s = BeautifulSoup(fragment, 'html.parser')
    for x in s.find_all(string=lambda t: isinstance(t, Comment)):
        x.extract()
    for t in s.find_all(['script', 'style', 'noscript']):
        t.decompose()
    for t in s.find_all(True):
        if t.name in ('div', 'span', 'section', 'font'):
            t.unwrap()
            continue
        keep = {'a': ['href', 'target', 'rel'], 'img': ['src', 'alt']}.get(t.name, [])
        for a in list(t.attrs):
            if a not in keep:
                del t[a]
    for p in s.find_all('p'):
        if p.find(['p', 'h2', 'h3', 'ul', 'ol']):
            p.unwrap()
    for p in s.find_all(['p', 'h2', 'h3', 'h4', 'strong', 'em', 'li']):
        if not p.get_text(strip=True) and not p.find(['img', 'a', 'br']):
            p.decompose()
    # Group loose top-level text and inline elements into <p> so nothing escapes the page's reading width.
    BLOCK = {'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'figure', 'img', 'table', 'blockquote', 'hr', 'iframe'}
    run = []
    def close_run(before):
        if run and ''.join(str(x) for x in run).strip():
            p_tag = s.new_tag('p')
            before.insert_before(p_tag) if before is not None else s.append(p_tag)
            for x in run:
                p_tag.append(x.extract())
        run.clear()
    for node in list(s.contents):
        if getattr(node, 'name', None) in BLOCK:
            close_run(node)
        elif getattr(node, 'name', None) == 'br':
            close_run(node); node.extract()
        else:
            run.append(node)
    close_run(None)
    for img in s.find_all('img'):
        if img.parent is not None and img.parent.name == 'p' and len(img.parent.get_text(strip=True)) == 0:
            img.parent.name = 'figure'
    o = str(s)
    o = re.sub(r'\[/?(gravityform|contact-form-7|vc_[a-z_]+)[^\]]*\]', '', o)
    o = re.sub(r'href="https?://(?:www\.)?yrocconsulting\.com/(?!wp-content/)', 'href="/', o)
    return no_emdash(re.sub(r'\n{2,}', '\n', o).strip())


def attr(sc, name):
    m = re.search(name + r'=[”“″"\'](.*?)[”“″"\'](?:\s|$)', sc + ' ')
    return html.unescape(m.group(1)) if m else ''


def p(text, cls=None):
    a = f' {{"className":"{cls}"}}' if cls else ''
    c = f' class="{cls}"' if cls else ''
    return f'<!-- wp:paragraph{a} -->\n<p{c}>{text}</p>\n<!-- /wp:paragraph -->'


def h(text, level=2, cls=None):
    a = {}
    if level != 2:
        a['level'] = level
    if cls:
        a['className'] = cls
    attrs = (' ' + json.dumps(a, separators=(',', ':'))) if a else ''
    c = 'wp-block-heading' + (f' {cls}' if cls else '')
    return f'<!-- wp:heading{attrs} -->\n<h{level} class="{c}">{text}</h{level}>\n<!-- /wp:heading -->'


def freeform(htmltext):
    return f'<!-- wp:freeform -->\n{htmltext}\n<!-- /wp:freeform -->'


STAT_OVERRIDES = {
    'thai-ginger-case-study': [['98.7%', 'AI visibility'], ['#1', 'in ChatGPT, Google AI Overviews & organic']],
    'fractional-cfo-reputation-management-case-study': [['80%', 'Negative results suppressed'], ['60 days', 'To take over page one of Google']],
}

manifest = []
for order, (slug, title, tag, cat, summary) in enumerate(CASES):
    d = json.load(open(f'{src}/{slug}.json'))[0]
    raw = html.unescape(d['content']['rendered'])
    tokens = re.split(r'(\[/?vc_[a-z_]+[^\]]*\])', raw)
    stats, blocks, pending_heading = [], [], None
    started = False  # skip intro headings until the first story section
    for tok in tokens:
        if not tok.strip():
            continue
        if re.match(r'\[/vc_', tok):
            continue
        m = re.match(r'\[(vc_[a-z_]+)([^\]]*)\]', tok)
        if m:
            name, a = m.group(1), m.group(2)
            if name == 'vc_custom_heading':
                text = no_emdash(attr(a, 'text').strip())
                if not text:
                    continue
                if re.match(r'^[#$+]?\d[\d.,]*\s?[%×xK+]*$', text):
                    stats.append([text])
                    continue
                if stats and len(stats[-1]) == 1:
                    stats[-1].append(text.replace('Lisings', 'Listings'))
                    continue
                if not started and text.lower() not in ('background', 'the client', 'about'):
                    continue
                started = True
                blocks.append(h(text))
            elif name == 'vc_single_image':
                url = media.get(attr(a, 'image'))
                if url:
                    blocks.append(f'<!-- wp:image {{"sizeSlug":"large","className":"yr-case-img"}} -->\n<figure class="wp-block-image size-large yr-case-img"><img src="{url}" alt=""/></figure>\n<!-- /wp:image -->')
            continue
        body = re.sub(r'(/wp-content/uploads/[^"]+?)-\d+x\d+(\.\w+)"', r'\1\2"', clean(tok))
        if not started:
            body = re.sub(r'<img[^>]*>', '', body)  # intro blocks carry client logos; no logos on the site for now
        if not BeautifulSoup(body, 'html.parser').get_text(strip=True):
            continue
        # vc_message content shows up as plain text between its tags; treat short blocks after "Did You Know" as callouts
        blocks.append(freeform(body))

    stats = STAT_OVERRIDES.get(slug, stats)
    stat_html = ''.join(
        f'<div class="yr-casestat"><b>{html.escape(s[0])}</b><span>{html.escape(s[1] if len(s) > 1 else "")}</span></div>' for s in stats if s
    )
    hero = (
        '<!-- wp:group {"align":"full","className":"yr-pagehero yr-casehero","layout":{"type":"constrained","contentSize":"1100px"}} -->\n'
        '<div class="wp-block-group alignfull yr-pagehero yr-casehero">\n'
        + p(f'<a href="/case-studies/">Case studies</a> · {html.escape(tag)}', 'yr-eyebrow') + '\n'
        + h(html.escape(title), 1) + '\n'
        + p(html.escape(summary), 'yr-lead') + '\n'
        + (f'<!-- wp:html -->\n<div class="yr-casestats">{stat_html}</div>\n<!-- /wp:html -->\n' if stat_html else '')
        + '</div>\n<!-- /wp:group -->'
    )
    body = (
        '<!-- wp:group {"className":"yr-casebody","layout":{"type":"constrained","contentSize":"760px"}} -->\n'
        '<div class="wp-block-group yr-casebody">\n' + '\n\n'.join(blocks) + '\n</div>\n<!-- /wp:group -->'
    )
    content = hero + '\n\n' + body + '\n\n<!-- wp:pattern {"slug":"yroc/cta-band"} /-->\n'
    fname = f'pages/case-{slug}.html'
    open(f'{out}/case-{slug}.html', 'w').write(content)
    manifest.append({'slug': slug, 'title': title, 'parent': 'case-studies', 'file': fname, 'template': 'page-landing',
                     'order': order, 'excerpt': summary, 'meta': {'_yroc_case_tag': tag, '_yroc_case_cat': cat,
                     '_yroc_case_metric': stats[0][0] if stats else '', '_yroc_case_metric_l': (stats[0][1] if stats and len(stats[0]) > 1 else '')},
                     'featured': media.get(str(d['featured_media']), '')})
    print(slug, 'stats:', stats, 'blocks:', len(blocks))

json.dump(manifest, open(f'{src}/cases_manifest.json', 'w'), indent=1, ensure_ascii=False)
