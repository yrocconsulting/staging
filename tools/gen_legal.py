"""Generate updated legal pages (run from repo root).

Usage: python3 tools/gen_legal.py <dir containing terms-and-conditions.json from the old site>

Privacy, Accessibility and Disclaimer are rewritten for the new site. The client service terms and
affiliate terms in Terms & Conditions are carried over verbatim from the old site (formatting and
em dashes only), because they are referenced by signed client agreements.
"""
import html
import json
import re
import sys

from bs4 import BeautifulSoup

sys.path.insert(0, 'tools')
from blocks import p, h, ul, group  # noqa: E402

SRC = sys.argv[1]
UPDATED = 'November 16, 2026'
CONTACT = ('Yroc Consulting, LLC<br>1800 Teague Dr. Ste. 416<br>Sherman, TX 75090<br>'
           '<a href="mailto:info@yrocconsulting.com">info@yrocconsulting.com</a> · <a href="tel:+19033005323">903.300.5323</a>')


def no_emdash(s):
    s = re.sub(r'\s*—\s*(?=(?:<[^>]+>)*[A-Z0-9"“])', ': ', s)
    return re.sub(r'\s*—\s*', ', ', s)


def freeform(x):
    return f'<!-- wp:freeform -->\n{x}\n<!-- /wp:freeform -->'


def page(slug, title, lead, body_parts):
    hero = group([p('Legal', 'yr-eyebrow'), h(title, 1), p(lead, 'yr-lead'), p(f'Last updated {UPDATED}', 'yr-meta')], 'yr-pagehero yr-casehero', 900)
    body = ('<!-- wp:group {"className":"yr-casebody yr-legal","layout":{"type":"constrained","contentSize":"780px"}} -->\n<div class="wp-block-group yr-casebody yr-legal">\n'
            + '\n\n'.join(body_parts) + '\n</div>\n<!-- /wp:group -->')
    s = no_emdash(hero + '\n\n' + body + '\n')
    assert '—' not in s, slug
    open(f'content/pages/legal-{slug}.html', 'w').write(s)
    return {'slug': slug, 'title': title, 'file': f'pages/legal-{slug}.html', 'template': 'page-landing'}


pages = []

# ---------------- Privacy Policy ----------------
pages.append(page('privacy-policy', 'Privacy Policy',
    'How Yroc Consulting, LLC collects, uses and protects information when you use yrocconsulting.com.', [
    h('Who we are'),
    p('Yroc Consulting, LLC ("Yroc," "we," "us") operates yrocconsulting.com (the "Site"). This policy explains what information we collect through the Site, how we use it and the choices you have.'),
    h('Information you give us'),
    p('When you submit a form on the Site (for example a contact request, an AI Visibility Audit request or a strategy call request) we collect the information you enter, which may include:'),
    ul(['Your name, email address and phone number', 'Your business name, website and city or market', 'Your preferred meeting time and type', 'Any message or details you choose to include']),
    p('If you enter a business name and city in the "See what AI says" preview on our homepage, that preview runs in your browser and is not stored by us unless you go on to submit an audit request.'),
    h('Information collected automatically'),
    p('Like most websites, our hosting provider records standard server logs, including IP address, browser type, pages requested and the date and time of each request. We use these logs to keep the Site secure, prevent abuse (including spam form submissions) and fix problems.'),
    h('Cookies'),
    p('The public pages of the Site do not use advertising cookies. WordPress may set cookies needed for logged-in administrators, and our hosting provider may set a cookie used for performance and security. If we add analytics or advertising tools in the future, we will update this policy first. You can block or delete cookies in your browser settings.'),
    h('How we use information'),
    ul(['To respond to your request, schedule calls and provide the services you ask about', 'To send you a confirmation of your request', 'To operate, secure and improve the Site', 'To comply with legal obligations']),
    p('We do not sell your personal information, and we do not share it with third parties for their own marketing.'),
    h('How information is stored and shared'),
    p('Form submissions are stored in our website\'s administration area and sent to our team by email. We share information only with service providers who help us run our business, such as our website host and email provider, and only as needed to provide their services. We may also disclose information if required by law or to protect our rights, our clients or the public.'),
    h('Third-party content'),
    p('Some pages include content from other services, such as an embedded Google Map on our contact and about pages and YouTube videos in some articles. When you view that content, those providers may collect information according to their own privacy policies. Our reviews section displays reviews published on Trustindex, Clutch and Trustpilot; links to those sites are governed by their policies.'),
    h('Data retention'),
    p('We keep form submissions for as long as needed to respond to you and manage any resulting business relationship, and then delete them unless we are required to keep them longer.'),
    h('Your choices'),
    p('You can ask us to access, correct or delete the personal information you submitted through the Site by contacting us using the details below. You can unsubscribe from any marketing email using the link in that email.'),
    h('Children\'s privacy'),
    p('The Site is intended for businesses and is not directed to children under 13. We do not knowingly collect information from children.'),
    h('Security'),
    p('We use reasonable administrative and technical safeguards, including encrypted connections (HTTPS), to protect information. No method of transmission or storage is completely secure, so we cannot guarantee absolute security.'),
    h('Changes to this policy'),
    p('We may update this policy from time to time. The "last updated" date at the top shows when it last changed.'),
    h('Contact us'),
    p(CONTACT),
]))

# ---------------- Accessibility ----------------
pages.append(page('accessibility-statement', 'Accessibility Statement',
    'Our commitment to making yrocconsulting.com usable by everyone.', [
    h('Our commitment'),
    p('Yroc Consulting is committed to making our website accessible to people of all abilities. We aim to meet the Web Content Accessibility Guidelines (WCAG) 2.2 at Level AA, and we treat accessibility as part of how the Site is designed and built, not an add-on.'),
    h('What we\'ve done'),
    ul(['Semantic page structure with clear headings and landmarks', 'Full keyboard navigation, including menus, tabs, sliders and forms, with visible focus indicators',
        'Text alternatives for meaningful images', 'Color contrast chosen to support readability',
        'Background video that can be paused, and that is replaced with a still image for visitors who turn on reduced motion in their device settings',
        'Animations that respect the "reduce motion" setting', 'Form fields with labels, clear error messages and status updates announced to screen readers',
        'Layouts that adapt to screen size and browser zoom']),
    h('Known limitations'),
    p('Some content comes from other services, such as embedded maps, videos and older blog articles, and may not fully meet the same standard. Interactive demonstrations on the Site (such as the AI answer preview and the entity graph) are illustrative; the same information is always available in text nearby. We review and improve the Site on an ongoing basis.'),
    h('Feedback and assistance'),
    p('If you have difficulty using any part of the Site, or need information in another format, please contact us. We\'ll do our best to respond within two business days and provide the information you need.'),
    p(CONTACT),
]))

# ---------------- Disclaimer ----------------
pages.append(page('disclaimer', 'Disclaimer',
    'Important information about the content, results and examples on yrocconsulting.com.', [
    h('General information'),
    p('The content on this Site, including articles, Learning Center guides and tools, is provided for general information. It is not legal, financial or other professional advice for your specific situation.'),
    h('No guarantee of results'),
    p('AI platforms and search engines decide what to show using systems we do not control, and those systems change often. Yroc Consulting does not guarantee any specific placement, mention, citation, ranking, traffic, leads or revenue in ChatGPT, Gemini, Google AI Overviews, Google AI Mode, Microsoft Copilot, Perplexity, Google Search or any other platform. Our work improves the signals these systems use, which can increase the likelihood of being recommended over time.'),
    h('Case studies, statistics and reviews'),
    p('Case studies and testimonials describe results achieved by specific clients in their markets and circumstances. They are not typical and are not a promise of similar results. AI visibility statistics shown on the Site are aggregated from weekly third-party tracking reports for current clients and may change as reports are updated. Reviews are shown from Trustindex, Clutch and Trustpilot.'),
    h('AI Authority Engine'),
    p('The AI Authority Engine certification and training program is operated by Yroc Consulting at aiauthorityengine.org. Student and partner results depend on each person\'s effort, market and business decisions, and no income or client outcome is promised.'),
    h('Previews and interactive tools'),
    p('The "See what AI says" preview and other interactive demonstrations on the Site are illustrative examples, not live AI responses about your business. A real AI Visibility Audit tests live prompts on each platform.'),
    h('Third-party names and links'),
    p('ChatGPT, Gemini, Google, Microsoft Copilot, Perplexity and other product names are trademarks of their respective owners and are used only to describe the platforms we optimize for. Their use does not imply endorsement. Links to other websites are provided for convenience; we are not responsible for their content.'),
    h('Contact'),
    p(CONTACT),
]))

# ---------------- Terms ----------------
raw_html = html.unescape(json.load(open(f'{SRC}/terms-and-conditions.json'))[0]['content']['rendered'])
raw_html = re.sub(r'\[/?[a-z_]+[^\]]*\]', '', raw_html)
soup = BeautifulSoup(raw_html, 'html.parser')
for t in soup.find_all(['script', 'style']):
    t.decompose()
for t in soup.find_all(True):
    if t.name in ('div', 'span', 'section', 'font'):
        t.unwrap()
        continue
    for a in list(t.attrs):
        if not (t.name == 'a' and a == 'href'):
            del t[a]
for t in soup.find_all('h1'):
    t.name = 'h2'
for t in soup.find_all('p'):
    if not t.get_text(strip=True):
        t.decompose()
old_terms = str(soup).strip()
old_terms = old_terms.replace('<h2>Terms and Conditions for Yroc Consulting, LLC</h2>', '<h2 id="client-terms">Part 2: Client Service Terms</h2>', 1)
old_terms = old_terms.replace('<h2>Yroc Consulting Affiliate Program Terms of Service</h2>', '<h2 id="affiliate-terms">Part 3: Affiliate Program Terms of Service</h2>', 1)
old_terms = re.sub(r'href="https?://(?:www\.)?yrocconsulting\.com/', 'href="/', old_terms)

pages.append(page('terms-and-conditions', 'Terms and Conditions',
    'The terms that apply to using yrocconsulting.com and to services provided by Yroc Consulting, LLC.', [
    p('<strong>Contents:</strong> <a href="#website-terms">Part 1: Website Terms of Use</a> · <a href="#client-terms">Part 2: Client Service Terms</a> · <a href="#affiliate-terms">Part 3: Affiliate Program Terms</a>'),
    '<!-- wp:heading -->\n<h2 class="wp-block-heading" id="website-terms">Part 1: Website Terms of Use</h2>\n<!-- /wp:heading -->',
    p('By using yrocconsulting.com (the "Site") you agree to these website terms. If you do not agree, please do not use the Site.'),
    ul(['<strong>Content.</strong> Site content is provided for general information and may change without notice. See our <a href="/disclaimer/">Disclaimer</a>.',
        '<strong>Intellectual property.</strong> The Site\'s text, design, graphics, video and the Yroc Method and AI Authority Engine materials are owned by Yroc Consulting, LLC or its licensors. You may share links to the Site, but may not copy or republish its content without permission.',
        '<strong>Acceptable use.</strong> You agree not to misuse the Site, including submitting false or automated form entries, attempting to gain unauthorized access, or interfering with its operation.',
        '<strong>Forms and communications.</strong> Information you submit is handled under our <a href="/privacy-policy/">Privacy Policy</a>. Submitting a form does not create a client relationship; services are provided only under a signed Agreement.',
        '<strong>Third-party links.</strong> Links to other websites, including aiauthorityengine.org and review platforms, are governed by those sites\' own terms.',
        '<strong>Limitation of liability.</strong> The Site is provided "as is." To the fullest extent permitted by law, Yroc Consulting is not liable for any damages arising from use of the Site.',
        '<strong>Governing law.</strong> These website terms are governed by the laws of the State of Texas.']),
    p('The client service terms and affiliate program terms below are unchanged from their previous publication and continue to apply to existing and new Agreements.'),
    freeform(old_terms),
]))

m = json.load(open('content/manifest.json'))
for e in pages:
    m['pages'] = [x for x in m['pages'] if x['slug'] != e['slug']]
    m['pages'].append(e)
json.dump(m, open('content/manifest.json', 'w'), indent=2, ensure_ascii=False)
print('ok', [e['slug'] for e in pages])
