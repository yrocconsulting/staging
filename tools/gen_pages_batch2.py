import sys, json
sys.path.insert(0, '/home/user/staging/tools')
from blocks import *

OUT = '/home/user/staging/content/pages/'
IMG = '/wp-content/themes/yroc/assets/img/about/'


def steps(items):
    inner = []
    for t, d in items:
        inner.append('<!-- wp:group {"className":"yr-step","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-step">\n' + h(t, 3) + '\n' + p(d) + '\n</div>\n<!-- /wp:group -->')
    return '<!-- wp:group {"className":"yr-steps","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-steps">\n' + '\n\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'


def glass(inner):
    return '<!-- wp:group {"className":"yr-glass","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-glass">\n' + '\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'


def write(name, parts):
    s = '\n\n'.join(parts) + '\n'
    assert '—' not in s, name + ' has an em dash'
    open(OUT + name + '.html', 'w').write(s)


# ======================= WEBSITE PACKAGES =======================
plans = [
    ('Essential', 'Essential Website', '$4,000', 'A professional, AI-ready website for businesses that need a strong digital foundation.',
     ['Custom professional design', 'WordPress CMS', 'Up to 12 pages', 'Fully mobile responsive', 'Professional imagery & graphics', 'Existing content integration',
      'Contact & inquiry forms', 'Basic SEO setup', 'AI search optimization', 'Basic speed & security', 'Google Analytics integration'], 'Get started', False),
    ('Growth', 'Growth Website', '$7,500', 'For established companies that want stronger visibility, better conversion and room to grow.',
     ['Advanced UI/UX & branding', 'WordPress CMS', 'Up to 25 pages', 'Mega menu & advanced navigation', 'Advanced lead forms', 'Advanced SEO optimization',
      'Advanced AI search optimization', 'A/B testing & heatmaps', 'ADA compliance ready', 'Embedded video & chatbot integration', 'Automatic backups', 'Monthly analytics reporting'], 'Build my Growth website', True),
    ('Enterprise', 'Enterprise Website', '$14,500', 'A fully custom digital platform for organizations that need advanced integrations, automation and performance.',
     ['Fully custom enterprise solution', 'WordPress or custom CMS', '50+ pages', 'Custom interactive navigation & graphics', 'Custom content creation', 'WooCommerce / Shopify integration',
      'CRM, ERP & payment integrations', 'Advanced AI search architecture', 'AI-driven conversion optimization', 'AI chatbot & automated responses', 'Enterprise-level speed & security', 'AI-driven analytics & reporting'], 'Discuss Enterprise', False),
]
plan_html = '<div class="yr-pricing">'
for tier, name, price, desc, feats, cta, pop in plans:
    plan_html += (f'<div class="yr-plan{" yr-plan--pop" if pop else ""} yr-reveal">' + ('<span class="yr-plan__badge">Most popular</span>' if pop else '')
                  + f'<span class="yr-plan__tier">{tier}</span><h3>{name}</h3><div class="yr-plan__price">{price}<small> starting</small></div><p>{desc}</p><ul>'
                  + ''.join(f'<li>{f}</li>' for f in feats) + f'</ul><a class="yr-btn" href="/contact/?topic=Website%20Design">{cta}</a></div>')
plan_html += '</div>'

rows = [
    ('Design', 'Custom design', 'Advanced UI/UX', 'Fully custom'),
    ('Pages', 'Up to 12', 'Up to 25', '50+'),
    ('Mobile responsive', '✓', '✓', '✓'),
    ('AI search optimization', 'Included', 'Advanced', 'Enterprise architecture'),
    ('SEO', 'Basic setup', 'Advanced', 'AI-powered strategy'),
    ('Forms', 'Contact forms', 'Advanced lead forms', 'CRM automation'),
    ('E-commerce', None, None, 'WooCommerce / Shopify'),
    ('Conversion optimization', 'Standard CTAs', 'A/B testing', 'AI-driven CRO'),
    ('Chatbot', None, 'Chatbot integration', 'AI automation'),
    ('Analytics', 'Basic integration', 'Monthly reporting', 'AI-driven reporting'),
    ('Security & backups', 'Basic', 'Advanced', 'Enterprise protection'),
]
tbl = '<div class="yr-table-wrap yr-reveal"><table class="yr-table"><thead><tr><th scope="col">Deliverable</th><th scope="col">Essential<small>$4,000</small></th><th scope="col" class="is-pop">Growth<small>$7,500</small></th><th scope="col">Enterprise<small>$14,500</small></th></tr></thead><tbody>'
for r in rows:
    cells = ''.join((f'<td class="no">Not included</td>' if v is None else f'<td{" class=\"is-pop\"" if i == 1 else ""}>{v}</td>') for i, v in enumerate(r[1:]))
    tbl += f'<tr><th scope="row">{r[0]}</th>{cells}</tr>'
tbl += '</tbody></table></div>'

write('website-packages', [
    hero('Website Packages', 'Your website shouldn\'t just rank. <em>It should be recommended.</em>',
         'Professional websites built from the ground up for Google, AI search engines and the way customers find businesses today. Every Yroc website includes AI search optimization as part of the build.',
         [('View packages', '#packages'), ('Talk with Yroc', '/contact/?topic=Website%20Design', True)],
         [glass([p('Built for humans, Google and AI', 'yr-eyebrow'), h('Search is changing. So is what a website needs to do.', 3),
                 p('Your customers aren\'t only using Google anymore. They\'re asking ChatGPT, Google AI Overviews, Perplexity and Copilot which companies to trust. Every Yroc website is structured so those systems understand who you are, what you do, where you operate and why you\'re relevant.')])]),
    group(head('Packages', 'Choose the website that fits your business.', 'Transparent starting prices. Every package includes AI search optimization, mobile-first design and a team that builds it in-house.') + [raw(plan_html)],
          'yr-section', 1200, anchor='packages'),
    group(head('The Yroc difference', 'AI search optimization isn\'t an add-on. <em>It\'s built in.</em>',
               'Most websites are built first and optimized later. Search visibility and AI discoverability shape the structure of a Yroc website from day one.') + [
        cards([('AI', 'AI-readable content', 'Pages structured so AI platforms can clearly identify your services, expertise, locations and business relationships.', None),
               ('ID', 'Entity optimization', 'We strengthen the signals that connect your brand, people, services and locations across the web.', None),
               ('IA', 'Search-focused structure', 'Page architecture, headings and internal linking built around how people actually search for your services.', None)], 'yr-cards', 'yr-card yr-card--glow'),
        cards([('SEO', 'Technical foundations', 'Crawlability, speed, mobile performance and technical structure considered during development, not after.', None),
               ('{ }', 'Schema-ready architecture', 'Structured data that helps machines understand your organization, services, reviews and FAQs.', None),
               ('→', 'Built for what\'s next', 'Designed for a world where customers discover companies through AI-generated recommendations.', None)], 'yr-cards', 'yr-card yr-card--glow')],
          'yr-section yr-dark yr-services'),
    group(head('Compare packages', 'Side by side.') + [raw(tbl)], 'yr-section yr-bg', 1100),
    faq([('Are these prices fixed?', 'They\'re starting prices for each package. After a short discovery call we confirm scope and send a fixed proposal before any work begins.'),
         ('Is AI search optimization really included?', 'Yes. Every package includes AI-readable page structure, entity signals and schema-ready architecture. Growth and Enterprise add more advanced AI optimization and reporting.'),
         ('Do you build on WordPress?', 'Most of our websites are built on WordPress so your team can easily edit content. Enterprise projects can use a custom CMS when the business needs it.'),
         ('Can you redesign my existing website?', 'Yes. We can bring over your existing content, improve it for search and AI, and preserve the rankings you\'ve already earned with proper redirects.'),
         ('Can I add ongoing AI search optimization after launch?', 'Yes. Many clients pair a new website with an ongoing <a href="/ai-search-optimization/">AI Search Optimization</a> program so the site keeps gaining visibility after launch.')],
        'Website FAQs'),
    group([p('Start your website project', 'yr-eyebrow'), h('Don\'t build another website for <em>yesterday\'s search engine.</em>'),
           p('Build one designed for how customers search now: Google, AI Overviews, ChatGPT, Perplexity and what comes next.', 'yr-lead'),
           buttons(('Start your website project', '/contact/?topic=Website%20Design'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
])

# ======================= REPUTATION MANAGEMENT =======================
rep_children = [
    ('negative-search-results', 'Negative Search Results', 'Push down what hurts you', 'Push negative search results <em>off page one.</em>',
     'Bad articles, complaint sites and outdated stories don\'t have to define you. We suppress harmful results and build a page one you\'d be proud to show a customer.',
     [('Audit', 'Map every result for your name or brand across Google and AI answers, and score what helps and what hurts.'),
      ('Build', 'Create and strengthen positive, authoritative properties: profiles, articles, press and owned content.'),
      ('Suppress', 'Promote those assets so they outrank negative results, and pursue removal where policies allow.'),
      ('Protect', 'Monitor new mentions and keep page one, and the AI answers built from it, working in your favor.')]),
    ('personal-reputation-management', 'Personal Reputation Management', 'Protect your name', 'Protect your name <em>online and in AI answers.</em>',
     'For executives, founders, physicians, attorneys, real estate professionals and anyone whose name is their business. We shape what people and AI find when they look you up.',
     [('Discover', 'Search your name the way clients, employers and AI tools do, and document what they see.'),
      ('Clarify', 'Build a clear personal entity: consistent bios, profiles and credentials AI can verify.'),
      ('Strengthen', 'Publish authoritative content, interviews and features that reflect who you really are.'),
      ('Defend', 'Suppress harmful results and monitor your name so problems are caught early.')]),
    ('business-reputation-management', 'Business Reputation Management', 'Protect your brand', 'Protect the brand <em>customers and AI see.</em>',
     'Reviews, complaint sites, news coverage and AI summaries all shape whether a customer calls you. We manage every signal so your brand is described accurately and favorably.',
     [('Assess', 'Review ratings, complaint sites, news coverage and how AI platforms describe and compare your business.'),
      ('Generate', 'Build a steady flow of authentic reviews on the platforms that matter for your industry.'),
      ('Respond', 'Create a response strategy for negative reviews that protects trust and shows prospects you care.'),
      ('Amplify', 'Promote positive proof, case studies and third-party mentions so AI has better sources to cite.')]),
    ('knowledge-panel-repair', 'Knowledge Panel & Repair', 'Claim, fix & enhance', 'Claim, fix and enhance <em>your Knowledge Panel.</em>',
     'Your Google Knowledge Panel is often the first thing people see, and a key source AI uses to understand you. We help you get one, correct it and keep it accurate.',
     [('Evaluate', 'Check whether you or your business have a Knowledge Panel and what information Google is using.'),
      ('Claim', 'Verify ownership where possible so you can suggest edits and keep information current.'),
      ('Correct', 'Fix wrong images, descriptions, dates, links and associations at the source data level.'),
      ('Enhance', 'Strengthen the entity signals, schema and authoritative sources that keep your panel accurate.')]),
    ('rebrand-strategy', 'Rebrand & Change Strategy', 'Transition without losing trust', 'Rebrand or change direction <em>without losing trust.</em>',
     'New name, new ownership, a merger or a fresh start: we make sure search engines and AI platforms connect your new identity to the reputation you\'ve already earned.',
     [('Plan', 'Map every place your old name, address or offering appears, including AI answers and knowledge sources.'),
      ('Connect', 'Use redirects, schema and entity signals to tell search engines and AI the old and new brands are related.'),
      ('Update', 'Refresh profiles, citations, directories and reviews so information is consistent everywhere.'),
      ('Reinforce', 'Publish announcements and authoritative coverage that establish the new identity quickly.')]),
]

rep_cards = [(['NEG', 'PER', 'BIZ', 'KP', 'NEW'][i], c[1], c[2] + '.', (f'{c[1]}', f'/reputation-management/{c[0]}/')) for i, c in enumerate(rep_children)]
write('reputation-management', [
    hero('AI Reputation Management', 'Control what Google <em>and AI</em> say about you.',
         'AI systems summarize your reputation from reviews, articles, profiles and search results. If those sources are negative, outdated or unclear, that\'s the story AI tells. We change the sources.',
         [('Get a confidential review', '/contact/?topic=Reputation%20Management'), ('See reputation results', '/case-studies/#reputation', True)],
         [glass([p('Confidential consultation', 'yr-eyebrow'), h('What we protect', 3),
                 ul(['Page one of Google for your name or brand', 'How ChatGPT, Gemini and AI Overviews describe you', 'Review ratings and review volume',
                     'Your Google Knowledge Panel', 'News coverage, complaint sites and forums'], 'yr-checks')])]),
    group(head('Why it matters now', 'Your reputation is now summarized for you.',
               'Customers used to scroll and judge for themselves. Now they ask AI, and AI condenses everything it finds into a few sentences. One bad article or a wave of old complaints can become the summary.') + [
        cards([('01', 'AI repeats what it finds', 'AI answers are built from the same reviews, articles and profiles that rank in search. Weak sources become weak summaries.', None),
               ('02', 'First impressions are instant', 'Prospects, partners and employers check you before they call. Many decide based on page one alone.', None),
               ('03', 'Small businesses feel it most', 'A handful of negative results can do more damage to a local business than a national brand would ever notice.', None)], 'yr-cards yr-trio')],
          'yr-section'),
    group(head('Reputation services', 'Every part of your reputation, managed.', 'Choose a focused service or a complete reputation program.') + [
        cards(rep_cards[:3], 'yr-cards yr-link-cards', 'yr-card yr-card--glow'), cards(rep_cards[3:], 'yr-cards yr-link-cards', 'yr-card yr-card--glow')],
          'yr-section yr-dark yr-services'),
    group(head('Our process', 'How we rebuild and protect a reputation.') + [
        steps([('Confidential audit', 'We document what Google, review sites and AI platforms show for your name or brand, and score each result.'),
               ('Strategy', 'You get a clear plan: what we\'ll suppress, what we\'ll build, what can be removed and realistic timelines.'),
               ('Build positive authority', 'We create and strengthen owned profiles, articles, press and reviews that deserve to rank.'),
               ('Suppress & correct', 'We promote positive assets above harmful results and fix inaccurate information at the source.'),
               ('Monitor & protect', 'Ongoing monitoring catches new mentions early, across search and AI answers.')])],
          'yr-section yr-bg', 900),
    group([columns([
        column([p('Reputation result', 'yr-eyebrow'), h('80% of negative results suppressed. <em>Page one reclaimed in 90 days.</em>'),
                p('Negative press buried a financial professional\'s brand. We built authoritative positive assets, suppressed the damaging results and gave him back control of his online narrative.', 'yr-lead'),
                buttons(('Read the case study', '/case-studies/fractional-cfo-reputation-management-case-study/'))], width='60%', valign='center'),
        column([raw('<div class="yr-casestats"><div class="yr-casestat"><b>80%</b><span>Negative results suppressed</span></div><div class="yr-casestat"><b>90 days</b><span>To own page one</span></div></div>')], width='40%', valign='center')],
        valign='center')], 'yr-section'),
    faq([('Can you remove negative results completely?', 'Sometimes. Content that violates a platform\'s policies, or certain legal and privacy cases, can be removed. Most of the time the most reliable approach is suppression: building stronger positive results that outrank the negative ones.'),
         ('How long does reputation management take?', 'Many clients see meaningful movement within 60 to 90 days. Timelines depend on how strong the negative results are and how much positive authority already exists.'),
         ('Does this affect what ChatGPT and AI Overviews say?', 'Yes. AI answers draw on the sources that rank and the entities they can verify. Improving those sources improves how AI describes you.'),
         ('Is my inquiry confidential?', 'Completely. Reputation work often involves sensitive situations, and we treat every consultation and engagement as confidential.'),
         ('Do you work with individuals as well as businesses?', 'Yes. We work with executives, professionals and public figures through <a href="/reputation-management/personal-reputation-management/">Personal Reputation Management</a>, and with companies of every size.')],
        'Reputation management FAQs'),
    group([p('Confidential consultation', 'yr-eyebrow'), h('Take back <em>your story.</em>'),
           p('Tell us what you\'re seeing in search and AI answers. We\'ll show you what can be done, and how fast.', 'yr-lead'),
           buttons(('Request a confidential review', '/contact/?topic=Reputation%20Management'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
])

for slug, title, short, h1, lead, process in rep_children:
    write('rep-' + slug, [
        hero('Reputation Management', h1, lead,
             [('Get a confidential review', '/contact/?topic=Reputation%20Management'), ('All reputation services', '/reputation-management/', True)],
             [glass([p('How it works', 'yr-eyebrow')] + [ul([f'<strong>{t}.</strong> {d}' for t, d in process], 'yr-checks')])]),
        group(head('Why it matters', 'AI tells your story <em>from the sources it finds.</em>',
                   'ChatGPT, Gemini and Google AI Overviews summarize people and businesses from search results, reviews, profiles and news. Fixing those sources is how you fix the summary.') + [
            cards([('01', 'Seen first', 'Most people never look past page one, and many now read only the AI summary.', None),
                   ('02', 'Repeated by AI', 'Negative or inaccurate sources get condensed into the answer customers read.', None),
                   ('03', 'Fixable', 'With the right strategy, authority and time, results and summaries can change.', None)], 'yr-cards yr-trio')],
              'yr-section'),
        group(head('Our process', f'How {title.lower()} works.') + [steps(process)], 'yr-section yr-bg', 900),
        group(head('Related services', 'Complete reputation protection.') + [
            cards([c for c in rep_cards if c[1] != title][:3], 'yr-cards yr-link-cards', 'yr-card yr-card--glow')], 'yr-section yr-dark yr-services'),
        pattern('yroc/cta-band'),
    ])

# ======================= ABOUT =======================
gallery = raw('<div class="yr-gallery">' + ''.join(
    f'<figure><img src="{IMG}{f}" alt="{a}" loading="lazy"><figcaption>{c}</figcaption></figure>' for f, a, c in [
        ('stage-magnify.jpg', 'Cory Long speaking on stage at the Magnify Your Wealth Summit', 'Magnify Your Wealth Summit'),
        ('crowd.jpg', 'A full room of business owners at a Yroc session', 'Teaching AI visibility to business owners'),
        ('eforge-training.jpg', 'Cory Long leading the eForge Sales Competition Training', 'eForge Sales Competition Training'),
        ('audience-energy.jpg', 'Attendees celebrating during a Yroc presentation', 'Hands-on, high-energy sessions'),
        ('with-attendees.jpg', 'Cory Long working with attendees at their tables', 'Working the room'),
    ]) + '</div>')

write('about-us', [
    hero('About Yroc Consulting', 'We help businesses become <em>the answer.</em>',
         'Yroc Consulting is an AI search optimization firm headquartered in Sherman, Texas. Our team helps businesses across the country get found, trusted and recommended by Google and the AI platforms their customers now ask.',
         [('Meet with our team', '/contact/'), ('See our results', '/case-studies/', True)],
         [raw(f'<figure class="yr-glass" style="padding:0!important;overflow:hidden"><img src="{IMG}stage-2026.jpg" alt="Cory Long on stage at the Magnify Your Wealth Summit" style="display:block;width:100%"></figure>')]),
    pattern('yroc/stats'),
    group([raw(f'''<div class="yr-founder">
  <img src="{IMG}founder.jpg" alt="Cory Long, founder of Yroc Consulting, speaking on stage" loading="lazy">
  <div>
    <p class="yr-eyebrow">Our story</p>
    <h2 class="yr-h-xl">Built by an entrepreneur, <em>for business owners.</em></h2>
    <p class="yr-lead">Founder Cory Long grew up in small business. In 1982 his father bought a local business in West Texas with his last paycheck in hand, and Cory helped it grow into a multi-million dollar company over more than 30 years.</p>
    <p>He went on to rebuild and own multiple businesses, work with Fortune 500 companies and lead nonprofits, including serving as CEO of a children's home in Wichita, Kansas. Through every one of them he was fascinated by how marketing works, and how often it doesn't.</p>
    <p>When he started a new company in North Texas in 2014, he went looking for marketers who could drive real customers, not just page-one rankings. He found that approach, mastered it, and built Yroc Consulting around it. Since then Yroc has advised more than 400 companies, and today we bring that same results-first approach to the AI search era.</p>
  </div>
</div>''')], 'yr-section'),
    group(head('Our journey', 'From local marketing to <em>AI search.</em>') + [raw('''<div class="yr-timeline">
  <div class="yr-reveal"><b>2005</b><p>Cory begins advising businesses on growth and marketing.</p></div>
  <div class="yr-reveal"><b>2014</b><p>Yroc Consulting is founded to deliver measurable, results-first local marketing.</p></div>
  <div class="yr-reveal"><b>400+</b><p>Companies advised across Texas, the U.S. and internationally.</p></div>
  <div class="yr-reveal"><b>Today</b><p>A full AI search optimization firm, plus the AI Authority Engine certification for agencies.</p></div>
</div>''')], 'yr-section yr-bg'),
    pattern('yroc/team'),
    group(head('On stage', 'We teach what we do.', 'Our founder regularly speaks to business owners, real estate professionals, apartment associations and sales teams about AI visibility and growth.') + [gallery],
          'yr-section yr-section--tight', 1200, anchor='speaking'),
    pattern('yroc/speaking'),
    group(head('What we believe', 'How we work with every client.') + [
        cards([('01', 'Results over rankings', 'We measure success in customers, calls and revenue, not vanity metrics.', None),
               ('02', 'Honest advice', 'You\'ll always get a straight answer, including when something isn\'t worth your money.', None),
               ('03', 'Partnership', 'Our business only grows when yours does. We plan every engagement together, as a team.', None)], 'yr-cards yr-trio')],
          'yr-section'),
    pattern('yroc/reviews'),
    pattern('yroc/office'),
    pattern('yroc/cta-band'),
])

# ======================= CASE STUDIES INDEX =======================
write('case-studies', [
    group([p('Case studies', 'yr-eyebrow'), h('Real businesses. <em>Measurable results.</em>', 1),
           p('From AI search visibility to reputation recovery and local growth, here\'s what happens when businesses partner with Yroc. Filter by the result you care about most.', 'yr-lead')],
          'yr-pagehero yr-casehero', 1200),
    pattern('yroc/case-grid'),
    pattern('yroc/reviews'),
    pattern('yroc/cta-band'),
])

# manifest additions
m = json.load(open('/home/user/staging/content/manifest.json'))
have = {(x.get('parent', ''), x['slug']) for x in m['pages']}
def add(entry):
    if (entry.get('parent', ''), entry['slug']) not in have:
        m['pages'].append(entry)
add({'slug': 'website-packages', 'title': 'Website Packages', 'file': 'pages/website-packages.html', 'template': 'page-landing'})
add({'slug': 'reputation-management', 'title': 'AI Reputation Management', 'file': 'pages/reputation-management.html', 'template': 'page-landing'})
for i, c in enumerate(rep_children):
    add({'slug': c[0], 'title': c[1], 'parent': 'reputation-management', 'file': f'pages/rep-{c[0]}.html', 'template': 'page-landing', 'order': i})
add({'slug': 'about-us', 'title': 'About Yroc Consulting', 'file': 'pages/about-us.html', 'template': 'page-landing'})
add({'slug': 'case-studies', 'title': 'Case Studies', 'file': 'pages/case-studies.html', 'template': 'page-landing'})
cases = json.load(open('/tmp/claude-0/-home-user/543539b5-1e37-52de-bf29-9e17c63fb546/scratchpad/src/cases_manifest.json'))
for c in cases:
    add({k: c[k] for k in ('slug', 'title', 'parent', 'file', 'template', 'excerpt')})
json.dump(m, open('/home/user/staging/content/manifest.json', 'w'), indent=2, ensure_ascii=False)
print([x['slug'] for x in m['pages']])
