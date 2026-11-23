"""Generate the four AI Optimization service pages (run from the repo root)."""
import json
import sys

sys.path.insert(0, 'tools')
from blocks import p, h, ul, buttons, group, columns, column, raw, cards, faq, head, hero  # noqa: E402

OUT = 'content/pages/'


def steps(items):
    inner = ['<!-- wp:group {"className":"yr-step","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-step">\n' + h(t, 3) + '\n' + p(d) + '\n</div>\n<!-- /wp:group -->' for t, d in items]
    return '<!-- wp:group {"className":"yr-steps","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-steps">\n' + '\n\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'


def glass(inner):
    return '<!-- wp:group {"className":"yr-glass","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-glass">\n' + '\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'


RELATED = {
    'entity-optimization': ('ID', 'Entity Optimization', 'Make AI certain who you are.', ('Learn more', '/entity-optimization/')),
    'schema-structured-data': ('{ }', 'Schema & Structured Data', 'Machine-readable facts AI reads first.', ('Learn more', '/schema-structured-data/')),
    'ai-content-optimization': ('Q&A', 'AI Content Optimization', 'Content AI wants to quote and cite.', ('Learn more', '/ai-content-optimization/')),
    'ai-search-reporting': ('↗', 'AI Search Reporting', 'Track mentions across every AI platform.', ('Learn more', '/ai-search-reporting/')),
    'ai-visibility-audit': ('AUD', 'AI Visibility Audit', 'See where you stand today.', ('Get an audit', '/ai-visibility-audit/')),
}


def page(slug, d):
    rel = [RELATED[k] for k in d['related']]
    parts = [
        hero('AI Optimization', d['h1'], d['lead'],
             [('Get a free AI Visibility Audit', '/ai-visibility-audit/'), ('Book a strategy call', '/book-a-strategy-call/', True)],
             [glass([p(d['glass_eyebrow'], 'yr-eyebrow'), h(d['glass_h'], 3), ul(d['glass_list'], 'yr-checks')])]),
        group(head('What it is', d['what_h'], None) + [p(x, 'yr-lead' if i == 0 else None) for i, x in enumerate(d['what'])]
              + ([p(f'<a href="{d["guide"][1]}">Read the guide: {d["guide"][0]} →</a>', 'yr-card__link')] if d.get('guide') else []),
              'yr-section yr-intro', 880),
        group(head('Why it matters', d['why_h'], d.get('why_lead')) + [cards(d['why'], 'yr-cards yr-trio')], 'yr-section yr-section--tight'),
        group(head("What's included", d['inc_h'], d.get('inc_lead')) + [cards(d['inc'][:3], 'yr-cards', 'yr-card yr-card--glow'), cards(d['inc'][3:], 'yr-cards', 'yr-card yr-card--glow')],
              'yr-section yr-dark yr-services'),
        group(head('Our process', d['proc_h']) + [steps(d['proc'])], 'yr-section yr-bg', 900),
        group(head('Connected services', 'Works best together.', 'AI visibility comes from every signal pointing the same direction.') + [cards(rel, 'yr-cards yr-link-cards', 'yr-card yr-card--glow')], 'yr-section'),
        faq(d['faq'], d['faq_title']),
        group([p(d['cta_eyebrow'], 'yr-eyebrow'), h(d['cta_h']), p(d['cta_p'], 'yr-lead'),
               buttons(('Get my free AI Visibility Audit', '/ai-visibility-audit/'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
    ]
    s = '\n\n'.join(parts) + '\n'
    assert '—' not in s, slug
    open(f'{OUT}{slug}.html', 'w').write(s)


PAGES = {
    'entity-optimization': dict(
        title='Entity Optimization',
        h1='Make AI <em>certain</em> who you are.',
        lead='AI recommends businesses it can clearly identify. Entity optimization makes your name, services, locations and leadership unmistakable everywhere AI looks, so it stops guessing and starts recommending.',
        glass_eyebrow='Signs you need it', glass_h='AI may be confused about you if…',
        glass_list=['AI describes your services or location incorrectly', 'You share a name with another business', 'Your details differ across directories and profiles',
                    'You\'ve moved, rebranded or changed ownership', 'You rank in Google but never appear in AI answers'],
        what_h='What is entity optimization?',
        what=['An entity is a uniquely identifiable real-world thing: your business, your founder, your locations, your services. AI systems don\'t evaluate keywords. They evaluate entities, and they only recommend the ones they can confidently identify.',
              'Entity optimization aligns every signal about your business across your website, Google Business Profile, directories, social profiles, structured data and knowledge sources, so AI builds one clear, trustworthy picture of who you are.'],
        guide=('How AI understands entities', '/kb/how-ai-understands-entities/'),
        why_h='Uncertainty costs you recommendations.',
        why=[('01', 'Inconsistency creates doubt', 'Different names, addresses or service lists across the web lower AI\'s confidence that it\'s talking about the right business.', None),
             ('02', 'Similar names get confused', 'Without clear disambiguation, AI can merge you with a competitor or skip you to avoid being wrong.', None),
             ('03', 'Clear entities get chosen', 'When every signal agrees, AI can confidently connect you to the questions your customers ask.', None)],
        inc_h='A complete entity foundation.',
        inc=[('NAP', 'Business data alignment', 'Name, address, phone, hours and categories made consistent across every major platform.', None),
             ('GBP', 'Google Business Profile', 'Categories, services, descriptions and attributes tuned for AI and map visibility.', None),
             ('KG', 'Knowledge sources', 'Wikidata, industry databases and authoritative profiles that feed AI knowledge graphs.', None),
             ('REL', 'Relationship mapping', 'Clear connections between your business, people, services and locations.', None),
             ('{ }', 'Entity schema', 'Organization, LocalBusiness and Person markup with sameAs links tying your profiles together.', None),
             ('FIX', 'Conflict cleanup', 'Duplicate listings, outdated addresses and wrong associations found and fixed at the source.', None)],
        proc_h='How we build your entity.',
        proc=[('Entity audit', 'We map how AI and search engines currently identify you, and every conflicting or missing signal.'),
              ('Source of truth', 'We define your official entity: name, services, locations, people, categories and relationships.'),
              ('Alignment', 'We update your website, profiles, directories and knowledge sources to match it everywhere.'),
              ('Reinforcement', 'Schema, citations and authoritative mentions confirm your identity from independent sources.'),
              ('Monitoring', 'We watch for new inconsistencies and track how AI describes you over time.')],
        related=['schema-structured-data', 'ai-content-optimization', 'ai-search-reporting'],
        faq_title='Entity optimization FAQs',
        faq=[('What is an entity in AI search?', 'A uniquely identifiable real-world thing such as a business, person, place or product. AI systems build internal representations of entities and recommend the ones they understand and trust.'),
             ('Is this the same as local SEO citations?', 'It overlaps, but goes further. Citations are one input. Entity optimization also covers structured data, knowledge sources, relationships between people and services, and how AI disambiguates you from similar businesses.'),
             ('How long does it take?', 'Core alignment usually happens in the first weeks. AI systems update at different speeds, so how they describe you improves over the following months.'),
             ('Do personal brands need entity optimization?', 'Yes. Founders, doctors, attorneys and other professionals are entities too. A clear personal entity helps AI connect you to your expertise and your business.')],
        cta_eyebrow='Start with clarity', cta_h='Does AI know <em>exactly</em> who you are?',
        cta_p='Our free AI Visibility Audit shows how AI identifies your business today and every signal that\'s causing confusion.'),

    'schema-structured-data': dict(
        title='Schema & Structured Data',
        h1='Speak the language <em>AI reads first.</em>',
        lead='Structured data gives search engines and AI systems machine-readable facts about your business, services, reviews and content. It removes guesswork, and less guesswork means more confident recommendations.',
        glass_eyebrow='What we mark up', glass_h='Schema built for your business',
        glass_list=['Organization & LocalBusiness', 'Services, products and service areas', 'People, founders and credentials', 'Reviews and ratings', 'FAQs, articles and how-to content'],
        what_h='What is schema markup?',
        what=['Schema markup is a standardized vocabulary (from schema.org) added to your website, usually as JSON-LD, that labels exactly what each page describes: who you are, what you offer, where you operate and what others say about you.',
              'Search engines and AI systems use it to confirm identity, understand relationships and decide which facts they can trust. It doesn\'t guarantee visibility, but it directly reduces ambiguity, and ambiguity is what keeps businesses out of AI answers.'],
        guide=('The role of structured data', '/kb/structured-data-and-ai/'),
        why_h='Machines trust what they can verify.',
        why=[('01', 'It confirms identity', 'Schema ties your website to your profiles and locations so AI knows every mention belongs to you.', None),
             ('02', 'It explains relationships', 'Organization to service to location to person: structured data spells out what humans infer.', None),
             ('03', 'It unlocks rich results', 'Correct markup makes you eligible for enhanced search features that AI summaries often draw from.', None)],
        inc_h='Structured data, done properly.',
        inc=[('AUD', 'Schema audit', 'Find missing, broken, duplicated or misleading markup across your site.', None),
             ('ORG', 'Entity markup', 'Organization, LocalBusiness and Person schema with sameAs links to your official profiles.', None),
             ('SVC', 'Service & area markup', 'Each service and service area described in a way machines can match to real questions.', None),
             ('Q&A', 'FAQ & content markup', 'FAQPage, Article and HowTo markup aligned with what\'s visible on the page.', None),
             ('★', 'Review markup', 'Review and rating data structured correctly and within search engine guidelines.', None),
             ('QA', 'Validation & monitoring', 'Testing in Google\'s tools and ongoing checks when your site changes.', None)],
        proc_h='How we implement structured data.',
        proc=[('Audit', 'We crawl your site and review every existing schema type for errors, gaps and conflicts.'),
              ('Entity model', 'We design how your organization, services, locations and people relate to each other.'),
              ('Implementation', 'We add clean JSON-LD that matches visible page content, on WordPress or any platform.'),
              ('Validation', 'Every template is tested with Google\'s Rich Results Test and schema validators.'),
              ('Maintenance', 'As you add services, locations or content, the markup keeps pace.')],
        related=['entity-optimization', 'ai-content-optimization', 'ai-visibility-audit'],
        faq_title='Schema & structured data FAQs',
        faq=[('Does schema directly improve AI visibility?', 'It reduces ambiguity, which raises the confidence AI systems have in your business. It works best combined with consistent entity data, strong content and trust signals.'),
             ('Can\'t a plugin handle schema?', 'Plugins add basic markup, but it\'s often generic, duplicated or out of sync with your actual services. Custom schema models your real business and relationships.'),
             ('Will it work on my website platform?', 'Yes. JSON-LD works on WordPress, Shopify, Wix, Squarespace and custom sites.'),
             ('Is review schema allowed?', 'Yes, within search engine guidelines. We implement review markup in ways that follow those rules so it helps rather than risks penalties.')],
        cta_eyebrow='Check your markup', cta_h='Is your website <em>machine-readable?</em>',
        cta_p='Our free AI Visibility Audit includes a structured data review showing what\'s missing, broken or holding you back.'),

    'ai-content-optimization': dict(
        title='AI Content Optimization',
        h1='Content AI wants to <em>quote and cite.</em>',
        lead='AI assistants build answers from content that is clear, specific and trustworthy. We create and restructure your pages so ChatGPT, Gemini, Perplexity and Google AI can extract your answers and name your business.',
        glass_eyebrow='Built for answers', glass_h='Every page we optimize is',
        glass_list=['Answer-first: the direct answer up top', 'Specific about services, places and people', 'Structured with clear headings and FAQs', 'Backed by proof: results, credentials, reviews', 'Written for people, readable by machines'],
        what_h='What is AI content optimization?',
        what=['AI content optimization, sometimes called answer engine optimization (AEO), is the practice of writing and structuring content so AI systems can easily understand it, extract it and cite it when answering questions.',
              'It\'s not about stuffing keywords. It\'s about answering the real questions your buyers ask, clearly and specifically, with the proof and context AI needs to trust and repeat your answer.'],
        guide=('AI search vs. traditional SEO', '/kb/ai-search-vs-traditional-seo/'),
        why_h='AI answers questions. Your content should too.',
        why=[('01', 'Questions replaced keywords', 'People ask AI full questions. Pages built around keywords rarely contain the direct answer AI is looking for.', None),
             ('02', 'Specific beats generic', 'Vague service pages get skipped. Clear details about what, where and for whom get cited.', None),
             ('03', 'Proof earns the mention', 'Results, credentials and reviews give AI the confidence to recommend you by name.', None)],
        inc_h='Content engineered for AI answers.',
        inc=[('Q', 'Prompt research', 'The real questions your buyers ask AI, mapped to the pages that should answer them.', None),
             ('SVC', 'Service page rebuilds', 'Clear, specific service and location pages that explain exactly what you do and for whom.', None),
             ('FAQ', 'FAQ & answer blocks', 'Direct answers to high-intent questions, written to be extracted and quoted.', None),
             ('VS', 'Comparison content', 'Honest comparisons and "best option for" pages that AI uses when weighing choices.', None),
             ('PRF', 'Proof integration', 'Case studies, credentials, reviews and data woven into the pages that matter.', None),
             ('REF', 'Content refreshes', 'Existing pages updated for clarity, accuracy and AI readability.', None)],
        proc_h='How we optimize your content.',
        proc=[('Prompt mapping', 'We identify the questions your buyers ask AI and where your site should answer them.'),
              ('Content audit', 'We find thin, vague, outdated and missing pages, and what competitors cited by AI do better.'),
              ('Create & restructure', 'We write and rework pages to be answer-first, specific and backed by proof.'),
              ('Structure & markup', 'Headings, FAQs and schema make each answer easy for machines to extract.'),
              ('Measure & refine', 'We track which prompts start mentioning you and keep improving the pages that matter.')],
        related=['entity-optimization', 'schema-structured-data', 'ai-search-reporting'],
        faq_title='AI content optimization FAQs',
        faq=[('Is this the same as SEO content?', 'It builds on it. SEO content targets keywords and rankings. AI content optimization targets the specific questions people ask AI and the clarity and proof AI needs to cite you.'),
             ('Do you use AI to write the content?', 'We use AI tools to research and speed up drafts, but every page is shaped and reviewed by our team so it\'s accurate, specific to your business and genuinely helpful.'),
             ('How many pages do I need?', 'It depends on your services and markets. Most businesses benefit from strong individual service pages, location pages where relevant, and focused FAQ and comparison content.'),
             ('Will this help my Google rankings too?', 'Usually, yes. Clear, specific, well-structured content tends to perform well in both traditional search and AI answers.')],
        cta_eyebrow='Find your content gaps', cta_h='Is your website answering <em>the right questions?</em>',
        cta_p='Our free AI Visibility Audit tests real buyer prompts and shows which content is missing or holding you back.'),

    'ai-search-reporting': dict(
        title='AI Search Reporting',
        h1='See exactly <em>where AI recommends you.</em>',
        lead='You can\'t improve what you can\'t measure. We track how often your business appears in ChatGPT, Gemini, Google AI Overviews and Google AI Mode for the questions your customers ask, every week.',
        glass_eyebrow='Tracked weekly', glass_h='What your report shows',
        glass_list=['Appearances for every tracked prompt', 'Mentions vs. citations (linked sources)', 'Results by AI platform', 'Visibility trends over time', 'Where competitors appear instead'],
        what_h='What is AI search reporting?',
        what=['Traditional rank tracking tells you where a page ranks. AI search reporting tells you whether AI actually names your business when someone asks a question, on which platform, and whether it links to you as a source.',
              'It\'s the same weekly tracking that powers the live client numbers on our homepage, turned into a clear report with a strategist walking you through what changed and what we\'re doing next.'],
        guide=('How AI decides what to recommend', '/kb/how-ai-decides-what-to-recommend/'),
        why_h='AI visibility is a new metric. <em>Track it.</em>',
        why=[('01', 'Rankings miss the answer', 'You can rank #1 and still be left out of the AI answer customers actually read.', None),
             ('02', 'Every platform differs', 'ChatGPT, Gemini and Google AI often recommend different businesses for the same question.', None),
             ('03', 'Progress should be visible', 'Weekly tracking shows which work is moving the needle, so budget goes where it counts.', None)],
        inc_h='Reporting you can actually read.',
        inc=[('P', 'Prompt tracking', 'A tailored set of buyer questions tracked across every major AI platform.', None),
             ('M/C', 'Mentions & citations', 'Whether AI names you, links to you as a source, or both.', None),
             ('AI', 'Platform breakdown', 'ChatGPT, Gemini, AI Overviews and AI Mode side by side.', None),
             ('VS', 'Competitor visibility', 'Which competitors AI recommends for your prompts, and how that shifts.', None),
             ('TR', 'Trends over time', 'Weekly data rolled into clear trends so you can see momentum.', None),
             ('ST', 'Strategist review', 'A monthly walkthrough of results, what changed and the plan for next month.', None)],
        proc_h='How AI search reporting works.',
        proc=[('Choose prompts', 'Together we pick the questions that matter most to your business and markets.'),
              ('Baseline', 'We record where you appear today on every platform, including competitors.'),
              ('Track weekly', 'Every prompt is checked across all major AI platforms every week.'),
              ('Report monthly', 'You get a clear report and a strategist walkthrough of trends and next steps.'),
              ('Act on it', 'Findings feed straight into entity, content, schema and authority work.')],
        related=['ai-visibility-audit', 'entity-optimization', 'ai-content-optimization'],
        faq_title='AI search reporting FAQs',
        faq=[('Which AI platforms do you track?', 'ChatGPT, Google AI Overviews, Google AI Mode and Gemini.'),
             ('What\'s the difference between a mention and a citation?', 'A mention is when an AI answer names your business. A citation is when it links to your website or a page about you as a source. Both matter, and we track them separately.'),
             ('How often is visibility tracked?', 'Every week. You receive a monthly report and strategist review, with weekly data available along the way.'),
             ('Can agencies white-label these reports?', 'Yes. Certified partners can deliver branded AI visibility reports through our <a href="/white-label-services/">white label program</a>.')],
        cta_eyebrow='Get your baseline', cta_h='Find out where AI <em>recommends you today.</em>',
        cta_p='Start with a free AI Visibility Audit. It becomes the baseline we measure every improvement against.'),
}

for slug, d in PAGES.items():
    page(slug, d)

m = json.load(open('content/manifest.json'))
for slug, d in PAGES.items():
    if not any(x['slug'] == slug and not x.get('parent') for x in m['pages']):
        m['pages'].append({'slug': slug, 'title': d['title'], 'file': f'pages/{slug}.html', 'template': 'page-landing'})
json.dump(m, open('content/manifest.json', 'w'), indent=2, ensure_ascii=False)
print('ok', list(PAGES))
