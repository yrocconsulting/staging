import sys, json
sys.path.insert(0, '/home/user/staging/tools')
from blocks import *
OUT = '/home/user/staging/content/pages/'
AAE = 'https://aiauthorityengine.org'

def steps(items):
    inner = ['<!-- wp:group {"className":"yr-step","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-step">\n' + h(t, 3) + '\n' + p(d) + '\n</div>\n<!-- /wp:group -->' for t, d in items]
    return '<!-- wp:group {"className":"yr-steps","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-steps">\n' + '\n\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'
def glass(inner):
    return '<!-- wp:group {"className":"yr-glass","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-glass">\n' + '\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'
ext = lambda label, url: f'<a href="{url}" target="_blank" rel="noopener">{label} ↗</a>'

parts = [
    hero('White Label Partners', 'Your brand. <em>Our fulfillment team.</em>',
         'Agencies certified through the AI Authority Engine can offer AI search optimization under their own name, delivered behind the scenes by Yroc Consulting\'s in-house team in Sherman, Texas.',
         [('Apply to partner', '/contact/?topic=White%20Label%20Partnership'), ('Get certified ↗', AAE + '/ai-seo-certification/', True)],
         [glass([p('Who partners with us', 'yr-eyebrow'), h('Built for agencies selling AI search', 3),
                 ul(['Certified AI Authority Specialists', 'SEO, web design and PPC agencies adding AI visibility', 'Marketing consultants moving to productized retainers',
                     'Agencies that want to sell more without hiring a delivery team'], 'yr-checks')])]),
    group(head('How it works', 'Sell it under your brand. <em>We deliver it.</em>') + [
        steps([('Get certified', 'Partners complete the ' + ext('AI Authority Engine certification', AAE + '/ai-seo-certification/') + ', so you understand the system, can sell it confidently and speak the same language as our team.'),
               ('Win the client', 'Lead with an AI visibility audit. We help with niche, offer and prospecting, and Cory Long can join your sales conversations to help close.'),
               ('We fulfill, white-labeled', 'Our in-house specialists do the work: entity, schema, content, authority and reputation. Your client only ever sees your brand.'),
               ('Report and retain', 'Branded monthly AI visibility reporting shows your client where they\'re being recommended, so retainers renew.')])],
          'yr-section yr-bg', 900),
    group(head('What we fulfill', 'The complete AI search toolkit, <em>under your name.</em>', 'Offer one service or the full program. Everything is delivered to the same standard we use for our own clients.') + [
        cards([('AUD', 'AI Visibility Audits', 'Branded audits that show prospects what ChatGPT, Gemini and Google AI say about them today.', None),
               ('ID', 'Entity Optimization', 'Consistent facts, profiles and entity signals so AI can clearly identify your client.', None),
               ('{ }', 'Schema & Structured Data', 'Machine-readable markup for organizations, services, locations, reviews and FAQs.', None)], 'yr-cards', 'yr-card yr-card--glow'),
        cards([('Q&A', 'Answer-First Content', 'Service pages, FAQs and comparison content written for AI to extract, quote and cite.', None),
               ('★', 'Authority & Reputation', 'Reviews, citations, digital PR and reputation work that give AI reasons to trust your client.', None),
               ('↗', 'AI Visibility Reporting', 'White-labeled monthly reports tracking mentions and citations across AI platforms.', None)], 'yr-cards', 'yr-card yr-card--glow'),
        p('Need a new website for a client? Our <a href="/website-packages/">AI-ready website builds</a> are available white-labeled too.', 'yr-lead yr-center-text')],
          'yr-section yr-dark yr-services'),
    group(head('Why agencies partner with Yroc', 'Grow your agency <em>without growing payroll.</em>') + [
        cards([('01', 'Invisible to your clients', 'Your brand on every deliverable and report. We never contact your clients without your direction.', None),
               ('02', 'A proven system', 'Work follows the AI Authority Engine framework, so what you sell and what we deliver always match.', None),
               ('03', 'A real team', 'Strategists, engineers and content specialists in our Sherman, Texas office, not a rotating pool of freelancers.', None)], 'yr-cards yr-trio'),
        p('Clients can confirm your credential any time in the ' + ext('Certified AI Authority Specialist directory', AAE + '/certified/') + '.', 'yr-center-text')],
          'yr-section'),
    faq([('Do I need to be certified to partner?', 'Yes. White label partnerships are reserved for agencies certified through the ' + ext('AI Authority Engine', AAE + '/ai-seo-certification/') + '. Certification makes sure partners can sell the service accurately and set the right expectations with clients.'),
         ('Will my clients know Yroc does the work?', 'No. Deliverables, reports and communication carry your brand. We work behind the scenes as your fulfillment team.'),
         ('Can you help me close clients?', 'Yes. We help partners choose a niche, build their offer and run audit-led sales conversations, and Cory Long can join client meetings to help close.'),
         ('How is white label pricing set?', 'Partner pricing depends on the services you offer and your volume. We walk through it with you during partner onboarding so you can price your own packages with healthy margins.'),
         ('I\'m not certified yet. Where do I start?', 'Start with the ' + ext('AI Authority Engine certification', AAE + '/ai-seo-certification/') + ', or ' + ext('talk with Cory first', AAE + '/contact/') + ' if you have questions about whether it\'s a fit.')],
        'White label FAQs'),
    group([p('Become a partner', 'yr-eyebrow'), h('Sell AI search optimization <em>under your own brand.</em>'),
           p('Tell us about your agency and the clients you serve. We\'ll show you how partnering works and what you can offer.', 'yr-lead'),
           buttons(('Apply to partner', '/contact/?topic=White%20Label%20Partnership'), ('Get certified ↗', AAE + '/ai-seo-certification/', True))], 'yr-ctaband', 960),
]
s = '\n\n'.join(parts) + '\n'
assert '—' not in s
s = s.replace('href="https://aiauthorityengine.org/ai-seo-certification/">Get certified ↗</a>', 'href="https://aiauthorityengine.org/ai-seo-certification/" target="_blank" rel="noopener">Get certified ↗</a>')
open(OUT + 'white-label-services.html', 'w').write(s)
m = json.load(open('/home/user/staging/content/manifest.json'))
if not any(x['slug'] == 'white-label-services' for x in m['pages']):
    m['pages'].append({'slug': 'white-label-services', 'title': 'White Label Partners', 'file': 'pages/white-label-services.html', 'template': 'page-landing'})
json.dump(m, open('/home/user/staging/content/manifest.json', 'w'), indent=2, ensure_ascii=False)
print('ok', s.count('target="_blank"'))
