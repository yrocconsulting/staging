"""Build the AI Search Learning Center (/kb/) from the public framework repo.

Usage: python3 tools/gen_learning_center.py <path to ai-search-optimization-framework checkout>

Guides come from the framework's markdown files; the hub, glossary and FAQ are defined here.
Writes content/pages/kb*.html and updates content/manifest.json.
"""
import html
import json
import re
import sys

sys.path.insert(0, 'tools')
from blocks import p, h, ul, buttons, group, raw, details, pattern, head  # noqa: E402

FW = sys.argv[1]
OUT = 'content/pages/'
REPO = 'https://github.com/yrocconsulting/ai-search-optimization-framework'

GUIDES = [
    # slug, source file, short title, summary, eyebrow
    ('what-is-ai-search-optimization', 'overview/what-is-ai-search-optimization.md', 'What is AI search optimization?',
     'The plain-English definition, and why AI recommends some businesses and ignores others.', 'Start here'),
    ('how-ai-understands-entities', 'overview/how-ai-understands-entities.md', 'How AI understands entities',
     'AI doesn\'t search for pages. It searches for entities it can identify and trust.', 'Foundations'),
    ('how-ai-decides-what-to-recommend', 'overview/ai-entity-evaluation-diagram.md', 'How AI decides what to recommend',
     'The step-by-step evaluation an AI system runs before it names a business.', 'Foundations'),
    ('ai-search-vs-traditional-seo', 'overview/ai-vs-traditional-seo.md', 'AI search vs. traditional SEO',
     'What changes when search returns an answer instead of a list of links.', 'Foundations'),
    ('authority-and-trust-signals', 'signals/authority-and-trust-signals.md', 'Authority & trust signals',
     'The signals that make AI confident enough to recommend you.', 'Signals'),
    ('structured-data-and-ai', 'signals/structured-data-role.md', 'The role of structured data',
     'How schema markup reduces ambiguity and confirms who you are.', 'Signals'),
    ('reviews-and-reputation', 'signals/reviews-and-reputation.md', 'Reviews & reputation in AI search',
     'How reviews and public sentiment shape the way AI describes you.', 'Signals'),
]

GLOSSARY = [
    ('AI Search Optimization (AISO)', 'Structuring a business\'s digital presence so AI search and answer systems can understand, trust and recommend it.'),
    ('Answer Engine Optimization (AEO)', 'Creating content that AI assistants can extract, quote and cite directly in their answers.'),
    ('Generative Engine Optimization (GEO)', 'Optimizing for generative AI results such as ChatGPT, Gemini and Google AI Overviews, which write an answer instead of listing links.'),
    ('AI Overviews', 'The AI-written summaries Google shows at the top of many search results, often citing a handful of sources.'),
    ('AI Mode', 'Google\'s conversational search experience that answers questions in depth and recommends businesses inside the answer.'),
    ('Large Language Model (LLM)', 'The AI model behind tools like ChatGPT, Gemini, Claude and Copilot. It generates answers from patterns learned in its training data and from sources it retrieves.'),
    ('Entity', 'A uniquely identifiable real-world thing: a business, person, product or place. AI systems reason about entities, not keywords.'),
    ('Entity disambiguation', 'How AI tells similar entities apart, for example two businesses with similar names in different cities.'),
    ('Knowledge Graph', 'Google\'s database of entities and the relationships between them. It powers Knowledge Panels and informs AI answers.'),
    ('Knowledge Panel', 'The information box Google shows for a known business or person, drawn from its Knowledge Graph.'),
    ('Schema markup', 'Structured data added to a website (usually JSON-LD) that tells machines exactly what a page and business are.'),
    ('NAP consistency', 'Keeping your business Name, Address and Phone identical everywhere online so AI can confidently connect every mention to you.'),
    ('Citation', 'A mention of your business on another website. In AI answers, a citation is a source the answer links to.'),
    ('Mention', 'When an AI answer names your business, with or without a link.'),
    ('AI visibility', 'How often, and how favorably, your business appears in AI-generated answers for the questions your customers ask.'),
    ('Prompt', 'The question or request a person types into an AI tool. AI visibility is measured across the prompts your buyers use.'),
    ('Zero-click search', 'A search where the user gets an answer without visiting any website, increasingly common with AI answers.'),
    ('E-E-A-T', 'Experience, Expertise, Authoritativeness and Trustworthiness: the qualities Google uses to judge content and sources.'),
    ('Topical authority', 'Being recognized as a deep, reliable source on a subject because you cover it thoroughly and consistently.'),
    ('Retrieval (RAG)', 'When an AI system looks up fresh sources from the web or a database before writing its answer, instead of relying only on training data.'),
]

FAQ = [
    ('Is AI search replacing Google?', 'Not replacing, but changing it. Google itself now answers many searches with AI Overviews and AI Mode, and more people ask ChatGPT, Gemini and Perplexity directly. Businesses need to be visible in both classic results and AI answers.'),
    ('How do I know if AI recommends my business?', 'Ask the AI tools the questions your customers ask and see who they name. For a thorough answer across every major platform, request an <a href="/ai-visibility-audit/">AI Visibility Audit</a>.'),
    ('Does traditional SEO still matter?', 'Yes. AI systems often draw on the same sources that rank well in search. AI search optimization builds on SEO by strengthening entity clarity, authority and trust.'),
    ('What is the fastest way to improve AI visibility?', 'Fix the foundation first: consistent business information everywhere, clear service pages, schema markup and a steady flow of genuine reviews. Those signals reduce AI\'s uncertainty about you.'),
    ('Can I learn to do this for clients?', 'Yes. Agencies and consultants can learn the full system through the <a href="https://aiauthorityengine.org/" target="_blank" rel="noopener">AI Authority Engine certification ↗</a>.'),
]


def no_emdash(s):
    s = re.sub(r'\s*—\s*(?=[A-Z0-9"“])', ': ', s)
    return re.sub(r'\s*—\s*', ', ', s)


def inline(t):
    t = html.escape(t, quote=False)
    t = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', t)
    t = re.sub(r'(?<!\*)\*(?!\s)(.+?)(?<!\s)\*(?!\*)', r'<em>\1</em>', t)
    return no_emdash(t)


def md_to_blocks(md):
    lines = md.splitlines()
    title = re.sub(r'^#\s*', '', lines[0]).strip()
    out, para, items = [], [], []

    def flush():
        nonlocal para, items
        if para:
            out.append(p(inline(' '.join(para))))
            para = []
        if items:
            out.append(ul([inline(i) for i in items], 'yr-checks'))
            items = []

    for line in lines[1:]:
        s = line.strip()
        if not s or s == '---':
            flush()
            continue
        if s.startswith('### '):
            flush(); out.append(h(inline(s[4:]), 3))
        elif s.startswith('## '):
            flush(); out.append(h(inline(s[3:])))
        elif s.startswith('- '):
            if para:
                out.append(p(inline(' '.join(para)))); para = []
            items.append(s[2:])
        elif s.startswith('>'):
            flush()
            out.append('<!-- wp:quote {"className":"yr-kbquote"} -->\n<blockquote class="wp-block-quote yr-kbquote">' + p(inline(s.lstrip('> ').strip())) + '</blockquote>\n<!-- /wp:quote -->')
        else:
            para.append(s)
    flush()
    return title, out


FLOW = [('Discovered', 'AI finds a business mentioned somewhere on the web.'),
        ('Identified', 'Name, location and category are recognized.'),
        ('Disambiguated', 'Separated from similar businesses and tied to the right place and services.'),
        ('Context built', 'Services, people, locations and industry relationships are mapped.'),
        ('Authority evaluated', 'Expertise, service clarity and industry alignment are weighed.'),
        ('Trust evaluated', 'Reputation, consistency across platforms and stability are checked.'),
        ('Structured data validated', 'Schema confirms identity and relationships, reducing ambiguity.'),
        ('Confidence modeled', 'Signals are corroborated and risk is assessed.'),
        ('Recommended', 'Eligible to be named in AI answers and suggestions.')]


def flow_html():
    li = ''.join(f'<li><b>{t}</b><span>{d}</span></li>' for t, d in FLOW)
    return raw(f'<ol class="yr-flow">{li}</ol>')


def guide_page(i, slug, src, short, summary, eyebrow):
    md = open(f'{FW}/{src}').read()
    title, body = md_to_blocks(md)
    if slug == 'how-ai-decides-what-to-recommend':
        # replace the ASCII diagram with a designed flow
        cut = next(k for k, b in enumerate(body) if 'How to Read This Diagram' in b)
        body = [p('The flow below shows how AI-driven search and answer systems typically evaluate whether a business can be confidently recommended. It\'s a conceptual model, not a description of any single proprietary system.', 'yr-lead'), flow_html()] + body[cut:]
    words = len(re.sub(r'<[^>]+>', ' ', ' '.join(body)).split())
    prev_g = GUIDES[i - 1] if i > 0 else None
    next_g = GUIDES[i + 1] if i + 1 < len(GUIDES) else None
    nav = '<nav class="yr-kbnav" aria-label="Guides">'
    nav += f'<a href="/kb/{prev_g[0]}/"><small>← Previous guide</small>{prev_g[2]}</a>' if prev_g else '<span></span>'
    nav += f'<a class="next" href="/kb/{next_g[0]}/"><small>Next guide →</small>{next_g[2]}</a>' if next_g else '<a class="next" href="/kb/"><small>Back to →</small>AI Search Learning Center</a>'
    nav += '</nav>'
    hero = group([p(f'<a href="/kb/">Learning Center</a> · {eyebrow}', 'yr-eyebrow'), h(inline(short), 1), p(inline(summary), 'yr-lead'),
                  p(f'{max(1, round(words / 220))} min read · From Yroc\'s <a href="{REPO}" target="_blank" rel="noopener">AI Search Optimization Framework ↗</a>', 'yr-meta')],
                 'yr-pagehero yr-casehero', 900)
    body_grp = ('<!-- wp:group {"className":"yr-casebody yr-kbbody","layout":{"type":"constrained","contentSize":"760px"}} -->\n<div class="wp-block-group yr-casebody yr-kbbody">\n'
                + '\n\n'.join(body) + '\n\n' + raw(nav) + '\n</div>\n<!-- /wp:group -->')
    content = hero + '\n\n' + body_grp + '\n\n' + pattern('yroc/cta-band') + '\n'
    assert '—' not in content, slug
    open(f'{OUT}kb-{slug}.html', 'w').write(content)
    return {'slug': slug, 'title': short, 'parent': 'kb', 'file': f'pages/kb-{slug}.html', 'template': 'page-landing', 'order': i, 'excerpt': summary}


entries = [guide_page(i, *g) for i, g in enumerate(GUIDES)]

# ---------- Glossary ----------
gl = sorted(GLOSSARY, key=lambda x: x[0].lower())
letters = sorted({t[0][0].upper() for t in gl})
gl_html = '<div class="yr-gloss">' + ''.join(
    f'<div class="yr-gloss__item" id="{re.sub(r"[^a-z0-9]+", "-", t.lower()).strip("-")}" data-term="{html.escape(t.lower())}"><dt>{html.escape(t)}</dt><dd>{no_emdash(d)}</dd></div>' for t, d in gl) + '</div>'
glossary = '\n\n'.join([
    group([p('<a href="/kb/">Learning Center</a> · Reference', 'yr-eyebrow'), h('AI search glossary', 1),
           p('The terms you\'ll hear in AI search, explained in plain English.', 'yr-lead'),
           raw('<label class="yr-kbsearch"><span class="yr-sr">Filter terms</span><input type="search" placeholder="Filter terms…" data-yr-gloss-filter></label>')],
          'yr-pagehero yr-casehero', 900),
    '<!-- wp:group {"className":"yr-casebody","layout":{"type":"constrained","contentSize":"860px"}} -->\n<div class="wp-block-group yr-casebody">\n' + raw(gl_html) + '\n</div>\n<!-- /wp:group -->',
    pattern('yroc/cta-band'),
]) + '\n'
assert '—' not in glossary
open(f'{OUT}kb-ai-search-glossary.html', 'w').write(glossary)
entries.append({'slug': 'ai-search-glossary', 'title': 'AI Search Glossary', 'parent': 'kb', 'file': 'pages/kb-ai-search-glossary.html', 'template': 'page-landing', 'order': 50,
                'excerpt': 'AI search terms explained in plain English.'})

# ---------- Hub ----------
cards = '<div class="yr-kbgrid">' + ''.join(
    f'<a class="yr-kbcard yr-reveal" href="/kb/{s}/" data-kb="{html.escape((t + " " + sm).lower())}"><span class="yr-kbcard__n">{i + 1:02d}</span><span class="yr-kbcard__e">{e}</span><h3>{html.escape(t)}</h3><p>{html.escape(sm)}</p><span class="yr-kbcard__go">Read the guide →</span></a>'
    for i, (s, _, t, sm, e) in enumerate(GUIDES)) + '</div>'
gl_preview = '<div class="yr-gloss yr-gloss--preview">' + ''.join(
    f'<div class="yr-gloss__item"><dt>{html.escape(t)}</dt><dd>{no_emdash(d)}</dd></div>' for t, d in gl[:6]) + '</div>'
posts_query = '''<!-- wp:query {"queryId":7,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false,"taxQuery":null},"className":"yr-postgrid"} -->
<div class="wp-block-query yr-postgrid">
<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"className":"yr-card yr-postcard","layout":{"type":"default"}} -->
<div class="wp-block-group yr-card yr-postcard">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
<!-- wp:post-terms {"term":"category","className":"yr-eyebrow"} /-->
<!-- wp:post-title {"level":3,"isLink":true} /-->
<!-- wp:post-excerpt {"excerptLength":20} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
</div>
<!-- /wp:query -->'''
tools = '<div class="yr-kbtools">' + ''.join(
    f'<a class="yr-kbtool" href="{u}"{t}><span>{lab}</span><b>{tt}</b><p>{d}</p></a>' for lab, tt, d, u, t in [
        ('Free audit', 'AI Visibility Audit', 'See what ChatGPT, Gemini and Google AI say about your business.', '/ai-visibility-audit/', ''),
        ('Free tool', 'AI Visibility Score ↗', 'A quick score for how ready your business is for AI search.', 'https://aiauthorityengine.org/ai-visibility-calculator/', ' target="_blank" rel="noopener"'),
        ('Open source', 'The full framework ↗', 'Our AI Search Optimization Framework, published on GitHub.', REPO, ' target="_blank" rel="noopener"'),
    ]) + '</div>'

hub = '\n\n'.join([
    group([p('AI Search Learning Center', 'yr-eyebrow'), h('Learn how AI decides <em>who to recommend.</em>', 1),
           p('Plain-English guides, a glossary and answers to the questions business owners ask most about ChatGPT, Gemini, Google AI Overviews and AI search. Free, from the team that does this work every day.', 'yr-lead'),
           raw('<label class="yr-kbsearch"><span class="yr-sr">Search the guides</span><input type="search" placeholder="Search guides, e.g. schema, reviews, entities…" data-yr-kb-filter></label>')],
          'yr-pagehero yr-casehero', 1200),
    group(head('Guides', 'Start with the fundamentals.', 'Seven short guides, in the order we recommend reading them.') + [raw(cards)], 'yr-section yr-section--tight'),
    group(head('Glossary', 'The language of AI search.') + [raw(gl_preview), buttons(('See the full glossary', '/kb/ai-search-glossary/'))], 'yr-section yr-bg', 1100),
    group(head('Latest insights', 'From the Yroc blog.') + [posts_query, buttons(('Read all articles', '/blog/', True))], 'yr-section', 1200),
    group(head('Free tools', 'See where you stand today.') + [raw(tools)], 'yr-section yr-section--tight'),
    group(head('FAQ', 'Common questions.') + [details(q, a) for q, a in FAQ], 'yr-section yr-light yr-faq', 860),
    pattern('yroc/cta-band'),
]) + '\n'
hub = hub.replace('<!-- wp:group {"align":"full","className":"yr-section yr-section--tight"', '<!-- wp:group {"align":"full","className":"yr-section yr-section--tight"', 1)
assert '—' not in hub
open(f'{OUT}kb.html', 'w').write(hub)

m = json.load(open('content/manifest.json'))
m['pages'] = [x for x in m['pages'] if not (x.get('parent') == 'kb' or x['slug'] == 'kb')]
m['pages'].append({'slug': 'kb', 'title': 'AI Search Learning Center', 'file': 'pages/kb.html', 'template': 'page-landing'})
m['pages'].extend(entries)
json.dump(m, open('content/manifest.json', 'w'), indent=2, ensure_ascii=False)
print('guides:', [e['slug'] for e in entries])
