"""Generate the Industries hub and one page per industry (run from the repo root).

Writes content/pages/industry-*.html, adds the pages to content/manifest.json (parent "industries")
and their titles/descriptions to content/seo.json. Re-run after editing the copy below.
House rules: no em dashes, no employee names except Cory Long, never name white-label clients,
never state how many clients we have.
"""
import json
import sys

sys.path.insert(0, 'tools')
from blocks import p, h, ul, buttons, group, column, columns, cards, faq, head, hero, steps  # noqa: E402

OUT = 'content/pages/'
AUDIT = ('Get a free AI Visibility Audit', '/ai-visibility-audit/')
CALL = ('Book a strategy call', '/book-a-strategy-call/', True)

CASES = {
    'blacksmith': ('Blacksmith Roofing', '8× the phone calls from local search.', ('Read the case study', '/case-studies/blacksmith-roofing-case-study/')),
    'irs': ('IRS Trouble Solvers', 'Outranking the IRS for the searches that matter.', ('Read the case study', '/case-studies/irs-trouble-solvers-case-study/')),
    'wakeup': ('Wake Up Skinny', '50–100 leads a month for a medical weight loss clinic.', ('Read the case study', '/case-studies/medical-weight-loss-case-study/')),
    'hormone': ('Hormone Clinic', '$20K in new revenue in 60 days.', ('Read the case study', '/case-studies/hormone-replacement-therapy-clinic-case-study/')),
    'bugguys': ('The Bug Guys', '+285% organic traffic for a pest control company.', ('Read the case study', '/case-studies/the-bug-guys/')),
    'thai': ('Thai Ginger', '#1 in ChatGPT and Google AI Overviews.', ('Read the case study', '/case-studies/thai-ginger-case-study/')),
    'cfo': ('Fractional CFO', 'Page one reclaimed in 90 days.', ('Read the case study', '/case-studies/fractional-cfo-reputation-management-case-study/')),
    'frigiserve': ('FrigiServe Cold Storage', 'Recommended by AI for cold storage in DFW.', ('Read the case study', '/case-studies/frigiserve-ai-visibility-case-study/')),
}


def rows(items, n=3):
    return [cards(items[i:i + n], 'yr-cards yr-link-cards', 'yr-card yr-card--glow') for i in range(0, len(items), n)]


def glass(inner):
    return '<!-- wp:group {"className":"yr-glass","layout":{"type":"default"}} -->\n<div class="wp-block-group yr-glass">\n' + '\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'


def industry_page(slug, d):
    parts = [
        hero('Industries · ' + d['name'], d['h1'], d['lead'], [AUDIT, CALL],
             [glass([p('Real questions customers ask AI', 'yr-eyebrow'), h(d['prompts_h'], 3), ul(['"' + q + '"' for q in d['prompts']], 'yr-checks')])]),
        group(head('How customers find you now', d['shift_h']) + [p(x, 'yr-lead' if i == 0 else None) for i, x in enumerate(d['shift'])],
              'yr-section yr-intro', 880),
        group(head('What AI looks for', d['signals_h'], d['signals_lead']) + [cards(d['signals'], 'yr-cards yr-trio')], 'yr-section yr-section--tight'),
        group(head('How Yroc helps', d['help_h'], d['help_lead'])
              + [cards(d['help'][:3], 'yr-cards', 'yr-card yr-card--glow'), cards(d['help'][3:], 'yr-cards', 'yr-card yr-card--glow')],
              'yr-section yr-dark yr-services'),
        group(head('Our process', 'From invisible to recommended.') + [steps(d.get('process', [
            ('AI Visibility Audit', f'We ask ChatGPT, Gemini and Google's AI Overviews and AI Mode the questions your customers ask, and record who gets recommended for {d["noun"]} in your market.'),
            ('Entity and reputation fixes', 'We make your business details, services and credentials consistent everywhere AI looks, and close the review and directory gaps holding you back.'),
            ('Answer-ready content', f'We build and refine pages that answer the specific questions people ask about {d["topic"]}, with the schema AI reads first.'),
            ('Authority building', 'We earn the mentions, citations and listings that give AI independent confirmation that you are a trusted choice.'),
            ('Weekly tracking', 'We track your visibility across every major AI platform each week and report what moved and what we are doing next.'),
        ]))], 'yr-section yr-bg', 900),
    ]
    if d.get('cases'):
        parts.append(group(head('Results', 'Proof from the field.') + rows([('CASE',) + CASES[c] for c in d['cases']]), 'yr-section yr-proof'))
    parts += [
        faq(d['faq'], f'AI search for {d["noun"]}: FAQs'),
        group([p('Start with the facts', 'yr-eyebrow'), h(d['cta_h']),
               p(f'Our free AI Visibility Audit shows exactly what ChatGPT, Gemini and Google AI say when customers ask about {d["topic"]} in your area, and what it will take to become the business they recommend.', 'yr-lead'),
               buttons(('Get my free AI Visibility Audit', '/ai-visibility-audit/'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
    ]
    return '\n\n'.join(parts) + '\n'


GROUPS = [
    ('home', 'Home & Construction', 'Contractors and home services.'),
    ('health', 'Health & Wellness', 'Clinics, practices and care providers.'),
    ('legal', 'Legal & Financial', 'Firms people trust with serious decisions.'),
    ('property', 'Real Estate & Property', 'Agents, managers and communities.'),
    ('retail', 'Restaurants & Automotive', 'Local favorites and the shops people rely on.'),
    ('b2b', 'B2B & Agencies', 'Providers other businesses choose.'),
]
AGENCY_CARD = ('AG', 'Marketing Agencies', 'Offer AI search optimization under your own brand with our white label program, training and reporting.', ('Explore white label', '/white-label-services/'))


def hub_page(pages):
    parts = [
        hero('Industries', 'AI search optimization <em>built for your industry.</em>',
             'Every industry gets asked different questions, judged on different signals and found on different platforms. We tailor AI search optimization to how customers in your field actually choose who to call.',
             [AUDIT, CALL],
             [glass([p('Why industry matters', 'yr-eyebrow'), h('AI weighs different proof for different businesses.', 3),
                     ul(['Licenses and certifications for contractors', 'Credentials and patient reviews for clinics', 'Bar status and practice areas for attorneys',
                         'Menus, hours and reviews for restaurants', 'Specialties and track record for professional firms'], 'yr-checks')])]),
    ]
    for i, (key, label, tagline) in enumerate(GROUPS):
        items = [(d['icon'], d['name'], d['card'], ('See how AI search helps', f'/industries/{slug}/')) for slug, d in pages.items() if d['group'] == key]
        if key == 'b2b':
            items.append(AGENCY_CARD)
        parts.append(group(head(label, tagline) + rows(items), 'yr-section' + (' yr-bg' if i % 2 else ''), anchor=key))
    parts.append(group([p('Not listed?', 'yr-eyebrow'), h('Every business is <em>being described by AI.</em>'),
                        p('We help businesses in many other fields too. A free AI Visibility Audit shows what AI says about you today, whatever you do.', 'yr-lead'),
                        buttons(('Get my free AI Visibility Audit', '/ai-visibility-audit/'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960))
    return '\n\n'.join(parts) + '\n'



PAGES = {
    'roofing-construction': dict(
        name='Roofing & Construction', icon='RF', noun='roofing and construction companies', topic='roofing and construction',
        title='AI Search Optimization for Roofing & Construction',
        seo=('AI Search Optimization for Roofers | Yroc Consulting',
             'Get your roofing or construction company recommended by ChatGPT, Gemini and Google AI when homeowners ask who to call after a storm.'),
        card='Be the contractor AI names when homeowners ask who to trust with a roof, a remodel or storm damage.',
        h1='AI search optimization for <em>roofing and construction.</em>',
        lead='After a hailstorm, homeowners now ask AI who to call. The roofers it names get the inspections, and everyone else competes for what is left. We make sure your company is one of the names.',
        prompts_h='Homeowners are asking AI…',
        prompts=['Who is the best roofer near me for hail damage?', 'Which roofing companies work with insurance claims in my city?',
                 'Is it worth replacing my roof or just repairing it?', 'Who are licensed general contractors with good reviews near me?',
                 'What should I look for in a storm restoration company?'],
        shift_h='Storm chasers taught homeowners to be careful.',
        shift=['Homeowners know the stories: out-of-town crews, half-finished jobs, insurance money gone. So before they call anyone, more of them ask ChatGPT, Gemini or Google AI to tell them who is legitimate, local and well reviewed.',
               'AI answers that question by cross-checking your business across the web. It looks for a real local address, licensing and manufacturer credentials, years in business, and a steady record of reviews that mention the work you actually do. Contractors with thin or inconsistent signals simply do not make the shortlist.'],
        signals_h='Trust signals decide who gets named.',
        signals_lead='AI is trying to recommend a contractor that will not embarrass it.',
        signals=[('01', 'Proof you are local and licensed', 'A consistent address, service area, license details and long business history tell AI you are not a storm chaser.', None),
                 ('02', 'Reviews that describe the work', 'Reviews that mention roof replacements, insurance claims, hail repair or remodels give AI the specifics it needs to match you to a question.', None),
                 ('03', 'Credentials and associations', 'Manufacturer certifications, BBB standing and trade associations act as independent confirmation of quality.', None)],
        help_h='Built for contractors.', help_lead='Everything we do points AI toward one conclusion: you are the safe choice.',
        help=[('MAP', 'Service area authority', 'City and service-area pages, Google Business Profile tuning and consistent listings so AI connects you to every town you serve.', None),
              ('REV', 'Review strategy', 'A simple system for collecting detailed, project-specific reviews after every job, and responding the right way.', None),
              ('CRT', 'Credential signals', 'Licenses, certifications, warranties and associations surfaced on your site and in schema so AI can verify them.', None),
              ('Q&A', 'Storm and claims content', 'Clear answers to the questions homeowners ask about hail, insurance claims, repair versus replace and costs.', None),
              ('REP', 'Reputation protection', 'Monitoring and response for the reviews and mentions that shape how AI describes your company.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See exactly when AI starts recommending you for roofing and construction questions in your market.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['blacksmith'],
        faq=[('Does AI really influence who homeowners hire?', 'More homeowners use ChatGPT, Gemini and Google AI Overviews to research contractors before they call, especially after a storm when they are worried about scams. If AI names three roofers and you are not one of them, you are not in the conversation.'),
             ('We already rank in Google Maps. Is that enough?', 'Maps rankings help, but AI answers draw on more than your profile: your website, reviews across platforms, directories, credentials and what others say about you. We align all of it so AI and Maps tell the same story.'),
             ('Can you help with insurance claim related searches?', 'Yes. We create clear, accurate content about how you work with insurance claims and storm damage, so AI can match you to those questions without making promises you cannot keep.'),
             ('How long until we see results?', 'Fixes to listings and reviews can show up in AI answers within weeks. Building authority for competitive cities usually takes a few months. Weekly tracking shows you the progress as it happens.')],
        cta_h='Be the roofer AI <em>recommends after the storm.</em>'),

    'tax-resolution': dict(
        name='Tax Resolution', icon='TX', noun='tax resolution firms', topic='tax resolution',
        title='AI Search Optimization for Tax Resolution Firms',
        seo=('AI Search Optimization for Tax Resolution | Yroc Consulting',
             'Help people with IRS problems find your tax resolution firm in ChatGPT, Gemini and Google AI. Credibility signals, content and tracking.'),
        card='Earn the trust AI requires before it recommends a firm to someone with an IRS problem.',
        h1='AI search optimization for <em>tax resolution firms.</em>',
        lead='People who owe the IRS are scared, skeptical and searching late at night. Increasingly they ask AI who can actually help. We make your firm the credible answer.',
        prompts_h='Taxpayers are asking AI…',
        prompts=['How do I stop an IRS wage garnishment?', 'Are tax relief companies legit or a scam?', 'Who are the best tax resolution firms near me?',
                 'Should I use an enrolled agent or a tax attorney for back taxes?', 'Can I settle my tax debt for less than I owe?'],
        shift_h='The industry has a trust problem. AI knows it.',
        shift=['Tax relief advertising has a reputation for big promises, and people know it. When someone asks AI whether a tax relief company is legitimate, the answer leans heavily on credentials, transparency and independent reviews.',
               'That is good news for real firms. Enrolled agents, CPAs and attorneys with clear credentials, honest explanations of programs like installment agreements and offers in compromise, and a solid review history are exactly what AI looks for when it decides who is safe to recommend.'],
        signals_h='Credibility beats volume.', signals_lead='AI is cautious with financial topics. Give it every reason to trust you.',
        signals=[('01', 'Verifiable credentials', 'Enrolled agent, CPA or attorney status, clearly stated and consistent everywhere, separates you from lead sellers.', None),
                 ('02', 'Honest, accurate explanations', 'Plain-English content about IRS programs, timelines and limits shows AI you inform rather than overpromise.', None),
                 ('03', 'Independent reputation', 'Reviews, BBB standing and third-party mentions confirm real outcomes for real clients.', None)],
        help_h='Built for firms that solve IRS problems.', help_lead='We help AI see the difference between your firm and the late-night ads.',
        help=[('ID', 'Credential and entity work', 'Your firm, your credentialed professionals and your services clearly defined across the web and in schema.', ('Entity optimization', '/entity-optimization/')),
              ('Q&A', 'IRS question content', 'Accurate answers about levies, liens, garnishments, penalty abatement and payment options, written for worried readers.', None),
              ('REV', 'Review and trust signals', 'A compliant review process and visible trust markers that help AI and people believe you.', None),
              ('REP', 'Reputation defense', 'Monitoring and response for complaints and mentions, because one unanswered complaint can shape an AI answer.', ('Business reputation', '/reputation-management/business-reputation-management/')),
              ('LOC', 'Local and national reach', 'Local profiles for your offices plus content that earns visibility for statewide and national questions.', None),
              ('↗', 'Weekly AI tracking', 'Track how AI describes your firm and when it starts recommending you.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['irs'],
        faq=[('Will AI recommend a tax resolution firm at all?', 'Yes. AI assistants regularly answer questions about who can help with back taxes, and they name firms when they find strong, consistent credibility signals. They tend to avoid firms that look like lead generators or make unrealistic promises.'),
             ('We get leads from ads. Why invest in AI search?', 'Ads stop the moment you stop paying, and many prospects now research firms in AI before they ever click an ad. Being the firm AI calls credible makes every other channel work better.'),
             ('Can you write about IRS programs without creating compliance issues?', 'We write accurate, educational content and avoid guarantees about outcomes. Your team reviews anything that touches specific programs or claims before it goes live.'),
             ('Do you work with solo practitioners?', 'Yes. An enrolled agent or CPA with strong credentials and good reviews can absolutely compete with larger firms in AI answers.')],
        cta_h='Become the firm AI <em>calls credible.</em>'),

    'medical-wellness': dict(
        name='Medical & Wellness', icon='MD', noun='medical and wellness practices', topic='your practice and treatments',
        title='AI Search Optimization for Medical & Wellness Practices',
        seo=('AI Search Optimization for Clinics | Yroc Consulting',
             'Get your clinic or wellness practice recommended by ChatGPT, Gemini and Google AI when patients research treatments and providers near them.'),
        card='Help patients find your practice when they ask AI about treatments, providers and clinics near them.',
        h1='AI search optimization for <em>medical and wellness practices.</em>',
        lead='Patients now ask AI about symptoms, treatments and which local clinic to trust. We help your practice show up accurately and credibly in those answers, without cutting corners on privacy.',
        prompts_h='Patients are asking AI…',
        prompts=['Where can I get medical weight loss near me?', 'Is hormone replacement therapy safe, and who offers it locally?', 'Which clinics near me are accepting new patients?',
                 'What should I ask before starting a wellness program?', 'Who are the best reviewed providers for my condition near me?'],
        shift_h='Health questions are held to a higher standard.',
        shift=['AI assistants are careful with health topics. They favor sources that show real medical credentials, clear and accurate information, and a strong patient reputation, and they avoid recommending practices that look vague or overpromise.',
               'For a clinic, that means your providers, credentials, treatments, locations and patient reviews need to be clearly stated and consistent everywhere AI looks, from your website to health directories to your Google Business Profile.'],
        signals_h='AI needs to trust you with patients.', signals_lead='These signals carry extra weight in health answers.',
        signals=[('01', 'Provider credentials', 'Licensed providers, specialties and medical oversight clearly described help AI treat your practice as a legitimate source.', None),
                 ('02', 'Accurate treatment information', 'Clear, balanced explanations of what you offer, who it is for and what to expect earn trust from AI and patients alike.', None),
                 ('03', 'Patient reputation', 'Consistent reviews across Google and health directories confirm real patient experiences.', None)],
        help_h='Built for clinics and wellness practices.', help_lead='Visibility that respects patient privacy and medical accuracy.',
        help=[('MD', 'Provider and practice entities', 'Your practice, locations and providers defined consistently across directories and in medical schema.', ('Schema & structured data', '/schema-structured-data/')),
              ('Q&A', 'Treatment content', 'Patient-friendly pages answering the real questions people ask AI, reviewed by your team for medical accuracy.', ('AI content optimization', '/ai-content-optimization/')),
              ('REV', 'Privacy-aware reviews', 'A review process and response approach that keeps patient privacy front and center.', None),
              ('DIR', 'Health directory alignment', 'Accurate listings on the health and local directories AI draws from.', None),
              ('REP', 'Reputation monitoring', 'Watch and respond to what patients and AI say about your practice.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'Know which treatment and provider questions AI recommends you for.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['wakeup', 'hormone'],
        faq=[('Is it safe to market a medical practice through AI search?', 'Yes, when it is done carefully. We focus on accurate information, real credentials and patient reviews, and we never publish patient details. Your team reviews clinical content before it goes live.'),
             ('How do you handle reviews and patient privacy?', 'We help you ask for reviews in a privacy-conscious way and respond without confirming anyone is a patient or discussing their care.'),
             ('Do you work with cash-pay and wellness clinics?', 'Yes. Medical weight loss, hormone therapy, IV therapy and other wellness practices are a strong fit, because patients research these treatments heavily before choosing a provider.'),
             ('Can AI recommend a specific clinic for a treatment?', 'Often, yes. When someone asks where to get a treatment nearby, AI assistants regularly name local practices that have clear, consistent and well-reviewed information about that treatment.')],
        cta_h='Be the practice AI <em>recommends to patients.</em>'),

    'med-spas': dict(
        name='Med Spas', icon='MS', noun='med spas', topic='your med spa and treatments',
        title='AI Search Optimization for Med Spas',
        seo=('AI Search Optimization for Med Spas | Yroc Consulting',
             'Get your med spa recommended by ChatGPT, Gemini and Google AI when clients research Botox, fillers, laser and skin treatments near them.'),
        card='Show up when clients ask AI where to get Botox, fillers, laser or skin treatments they can trust.',
        h1='AI search optimization for <em>med spas.</em>',
        lead='Aesthetic clients research obsessively, and now they ask AI who is qualified, who has the best results and who is worth the drive. We help your med spa become the confident answer.',
        prompts_h='Clients are asking AI…',
        prompts=['Who does the most natural looking Botox near me?', 'What is the best med spa for laser hair removal in my city?', 'Is it safe to get filler at a med spa?',
                 'How much does microneedling cost near me?', 'Which med spas have a medical director and licensed injectors?'],
        shift_h='Safety and results are the deciding questions.',
        shift=['Clients considering injectables or laser treatments want to know two things: is this place safe, and will I like the results? AI tries to answer both, which is why it leans on medical oversight, injector credentials, before-and-after proof and detailed reviews.',
               'Med spas that clearly show who performs treatments, how they are supervised, what each treatment involves and what clients say afterward give AI the confidence to recommend them by name.'],
        signals_h='Confidence comes from specifics.', signals_lead='Vague marketing gets skipped. Specific proof gets recommended.',
        signals=[('01', 'Qualified providers', 'Medical director, licensed injectors and training clearly stated reassure both AI and nervous first-time clients.', None),
                 ('02', 'Treatment-level detail', 'A clear page for each service, with what it treats, downtime and pricing guidance, matches you to specific questions.', None),
                 ('03', 'Reviews that name treatments', 'Reviews mentioning Botox, filler, laser or facials help AI connect your spa to each service.', None)],
        help_h='Built for aesthetic practices.', help_lead='We help AI understand what you do, who does it and why clients love it.',
        help=[('TRT', 'Service page strategy', 'Answer-first treatment pages that cover the questions clients ask before booking.', ('AI content optimization', '/ai-content-optimization/')),
              ('ID', 'Provider credibility', 'Medical oversight and provider credentials presented consistently on your site, profiles and schema.', ('Entity optimization', '/entity-optimization/')),
              ('REV', 'Treatment-specific reviews', 'A review process that encourages clients to mention the treatments they loved.', None),
              ('DIR', 'Beauty and local directories', 'Accurate, complete listings on the platforms clients and AI check most.', None),
              ('REP', 'Reputation care', 'Respond well to tough reviews and keep your reputation strong where AI looks.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See when AI recommends you for each treatment in your market.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['hormone'],
        faq=[('Can AI really drive bookings for a med spa?', 'Yes. Clients often ask AI for the best place nearby for a specific treatment, and AI names the spas with the clearest, most credible information and the strongest reviews for that service.'),
             ('Should we show prices on our website?', 'Price ranges or starting prices help AI answer cost questions with your spa in the answer. We help you share pricing guidance without locking yourself into exact quotes.'),
             ('Do before and after photos help?', 'They help clients decide, and clear, properly consented galleries with descriptive text support the specific treatment pages AI reads.'),
             ('We have multiple locations. Can you help each one?', 'Yes. Each location gets consistent details, its own profile optimization and local content so AI recommends the right spa for each area.')],
        cta_h='Be the med spa AI <em>trusts with every treatment.</em>'),

    'pest-control': dict(
        name='Pest Control', icon='PC', noun='pest control companies', topic='pest control',
        title='AI Search Optimization for Pest Control Companies',
        seo=('AI Search Optimization for Pest Control | Yroc Consulting',
             'Get your pest control company recommended by ChatGPT, Gemini and Google AI when homeowners need help with termites, rodents or bugs fast.'),
        card='Be the exterminator AI recommends when someone finds termites, roaches or rodents and needs help today.',
        h1='AI search optimization for <em>pest control companies.</em>',
        lead='When someone finds termites or a mouse in the kitchen, they want help today. More of them ask AI who to call. We make your company the name it gives.',
        prompts_h='Homeowners are asking AI…',
        prompts=['Who is the best pest control company near me?', 'How do I get rid of termites, and who treats them locally?', 'Is monthly pest control worth it?',
                 'Which exterminators are pet safe near me?', 'Who can come out today for a wasp nest?'],
        shift_h='Urgent problems, fast decisions.',
        shift=['Pest problems are urgent and a little embarrassing, so people want a quick, confident answer. AI gives them one: a short list of local companies with strong reviews and clear service information.',
               'To make that list, your company needs consistent local details, clear pages for the pests you treat, and reviews that describe real results. Recurring service plans, pet and family safety and licensing all help AI match you to the questions homeowners ask.'],
        signals_h='Specific beats generic.', signals_lead='AI matches companies to the exact pest and situation.',
        signals=[('01', 'Pest-specific service pages', 'Clear pages for termites, rodents, mosquitoes, bed bugs and more let AI match you to each problem.', None),
                 ('02', 'Local service area signals', 'Consistent profiles and city pages show exactly where you work and how fast you respond.', None),
                 ('03', 'Reviews about results', 'Reviews mentioning the pest, the technician experience and follow-up service build AI confidence.', None)],
        help_h='Built for pest control.', help_lead='We help AI see you as the local expert for every pest.',
        help=[('BUG', 'Pest and service content', 'Pages that answer how to deal with each pest, what treatment involves and how plans work.', ('AI content optimization', '/ai-content-optimization/')),
              ('MAP', 'Local authority', 'Google Business Profile, citations and service-area pages for every community you cover.', None),
              ('REV', 'Review engine', 'A simple post-service review process that captures detailed, pest-specific feedback.', None),
              ('{ }', 'Service schema', 'Structured data that spells out your services, areas and hours for AI.', ('Schema & structured data', '/schema-structured-data/')),
              ('REP', 'Reputation monitoring', 'Respond quickly to any complaint before it shapes how AI describes you.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See which pest and city questions AI recommends you for.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['bugguys'],
        faq=[('Do people really ask AI for an exterminator?', 'Yes. Questions like who is the best pest control near me or how to get rid of termites are common, and AI answers increasingly name specific local companies.'),
             ('We serve many small towns. Can AI find us for all of them?', 'Yes. We build consistent service-area signals and useful local content so AI connects your company to each community you serve.'),
             ('Do recurring plan customers matter for AI?', 'Their reviews do. Long-term customers who mention reliable quarterly service give AI strong evidence you deliver over time.'),
             ('How fast can we improve?', 'Listing and review fixes often show up within weeks. Building authority across many towns takes a few months, and weekly tracking shows progress.')],
        cta_h='Be the exterminator AI <em>recommends first.</em>'),

    'law-firms': dict(
        name='Law Firms', icon='LAW', noun='law firms', topic='your practice areas',
        title='AI Search Optimization for Law Firms',
        seo=('AI Search Optimization for Law Firms | Yroc Consulting',
             'Get your law firm recommended by ChatGPT, Gemini and Google AI when people research legal problems and attorneys in their area.'),
        card='Be the attorney AI recommends when someone has a legal problem and does not know where to start.',
        h1='AI search optimization for <em>law firms.</em>',
        lead='Before they ever call a lawyer, people now ask AI what their options are and who handles cases like theirs. We help your firm be the trusted name in that answer.',
        prompts_h='Potential clients are asking AI…',
        prompts=['Who is the best personal injury lawyer near me?', 'Do I need a lawyer for a divorce if we agree on everything?', 'Which estate planning attorneys near me have good reviews?',
                 'What should I do after a car accident that was not my fault?', 'Who are experienced criminal defense attorneys in my county?'],
        shift_h='Legal research starts with a question, not a firm name.',
        shift=['Most people with a legal problem do not know which lawyer they need. They start by describing their situation to AI, and the answer often ends with a few firms that handle that kind of case nearby.',
               'AI chooses those firms by looking at practice area clarity, attorney credentials, bar standing, case experience, legal directory profiles and client reviews. Firms that explain their practice areas clearly and consistently are far easier for AI to recommend.'],
        signals_h='Clarity and credentials win.', signals_lead='AI has to be confident you handle this exact type of case.',
        signals=[('01', 'Clear practice areas', 'Dedicated, detailed pages for each practice area tell AI precisely which cases you take.', None),
                 ('02', 'Attorney credentials', 'Bar admissions, experience and recognitions, consistent across your site and legal directories.', None),
                 ('03', 'Client reputation', 'Reviews and peer ratings that speak to communication, results and trust.', None)],
        help_h='Built for law firms.', help_lead='Visibility that stays within the advertising rules lawyers live by.',
        help=[('LAW', 'Practice area content', 'Clear, educational pages answering the questions people ask about each type of case, reviewed by your attorneys.', ('AI content optimization', '/ai-content-optimization/')),
              ('ID', 'Attorney and firm entities', 'Your firm and attorneys clearly defined across legal directories, profiles and schema.', ('Entity optimization', '/entity-optimization/')),
              ('DIR', 'Legal directory alignment', 'Complete, consistent profiles on the legal directories AI relies on.', None),
              ('REV', 'Ethical review strategy', 'A review process designed with your state bar advertising rules in mind.', None),
              ('REP', 'Reputation management', 'Protect your firm and attorneys from negative results and misleading mentions.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See when AI recommends you for each practice area and city.', ('AI search reporting', '/ai-search-reporting/'))],
        faq=[('Can AI recommend a specific law firm?', 'Yes. When people ask for a lawyer for a specific type of case in their area, AI assistants often name firms with clear practice area information, credentials and strong reviews.'),
             ('How do you handle attorney advertising rules?', 'We write educational content, avoid guarantees and claims about results, and your attorneys approve anything sensitive before it is published. Rules vary by state, so your firm has the final say.'),
             ('We practice in several areas. Is that a problem?', 'Not if each area is explained clearly. We give every practice area its own strong page and signals, so AI does not have to guess what you handle.'),
             ('Do legal directories still matter?', 'Yes. AI cross-checks your firm against trusted legal directories, so complete and consistent profiles there make you easier to recommend.')],
        cta_h='Be the firm AI <em>refers clients to.</em>'),

    'home-services': dict(
        name='Home Services', icon='HS', noun='home service companies', topic='your home services',
        title='AI Search Optimization for Home Service Companies',
        seo=('AI Search Optimization for Home Services | Yroc Consulting',
             'Get your HVAC, plumbing, electrical or moving company recommended by ChatGPT, Gemini and Google AI when homeowners need help.'),
        card='HVAC, plumbing, electrical, moving and more: be the company AI names when homeowners need help now.',
        h1='AI search optimization for <em>home service companies.</em>',
        lead='When the AC dies in July or a pipe bursts, homeowners want a trustworthy answer fast. More of them ask AI. We make your HVAC, plumbing, electrical or moving company the one it recommends.',
        prompts_h='Homeowners are asking AI…',
        prompts=['Who is a reliable AC repair company near me that can come today?', 'What is the best plumber near me for a water heater replacement?', 'Which electricians near me are licensed and insured?',
                 'Who are the best reviewed movers in my city?', 'Should I repair or replace my old furnace?'],
        shift_h='Homeowners want a short list they can trust.',
        shift=['Home service decisions are often urgent, and homeowners are tired of scrolling through ads and lead sites. AI gives them a shortlist of local companies, usually with a sentence about why each one made the cut.',
               'Those sentences come from your reviews, your services, your licensing and how consistently your business appears across the web. Companies with clear service pages, an accurate service area and detailed reviews make the list far more often.'],
        signals_h='Reliability is what AI is trying to judge.', signals_lead='Every signal should answer: will this company show up and do it right?',
        signals=[('01', 'Licensing and insurance', 'Clearly stated licenses, insurance and guarantees reassure AI that you are a safe recommendation.', None),
                 ('02', 'Service and area clarity', 'Specific service pages and accurate service areas match you to the exact job and location.', None),
                 ('03', 'Detailed reviews', 'Reviews that mention the job, response time and technician help AI describe why you are a good choice.', None)],
        help_h='Built for home service pros.', help_lead='We help AI understand what you fix, where you work and why people trust you.',
        help=[('SVC', 'Service page strategy', 'A clear page for every core service, answering the repair, replace and cost questions homeowners ask.', ('AI content optimization', '/ai-content-optimization/')),
              ('MAP', 'Service area authority', 'Profiles, citations and local content for every city and neighborhood you cover.', None),
              ('REV', 'Review system', 'A simple way to collect detailed reviews after each job and respond consistently.', None),
              ('{ }', 'Business schema', 'Structured data for your services, hours, areas and emergency availability.', ('Schema & structured data', '/schema-structured-data/')),
              ('REP', 'Reputation care', 'Handle complaints quickly so one bad day does not define you in AI answers.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See which services and cities AI recommends you for.', ('AI search reporting', '/ai-search-reporting/'))],
        faq=[('Which home service businesses do you work with?', 'HVAC, plumbing, electrical, moving, landscaping, cleaning, garage doors and other trades that serve homeowners locally.'),
             ('We rely on lead services. Can AI search replace them?', 'For many companies it becomes a strong, lower-cost source of customers who already trust you, because AI recommended you by name. It also makes your other marketing work better.'),
             ('Does emergency or same-day service matter to AI?', 'Yes. When you clearly state availability and it is backed up by reviews, AI can match you to urgent questions like who can come today.'),
             ('How do we get more detailed reviews?', 'We set up a simple request process after each job that encourages customers to mention the service, the technician and the result.')],
        cta_h='Be the company AI <em>sends homeowners to.</em>'),

    'real-estate': dict(
        name='Real Estate', icon='RE', noun='real estate agents and teams', topic='buying and selling homes',
        title='AI Search Optimization for Real Estate Agents',
        seo=('AI Search Optimization for Real Estate | Yroc Consulting',
             'Get your real estate team recommended by ChatGPT, Gemini and Google AI when buyers and sellers ask who knows their market best.'),
        card='Be the agent or team AI recommends when buyers and sellers ask who knows the local market best.',
        h1='AI search optimization for <em>real estate agents and teams.</em>',
        lead='Buyers and sellers now ask AI which agent really knows their neighborhood. We help your team become the local expert AI names.',
        prompts_h='Buyers and sellers are asking AI…',
        prompts=['Who is the best real estate agent to sell my house in my city?', 'Which agents know the neighborhoods near me best?', 'What is my home worth in this market?',
                 'Who are the top real estate teams for first-time buyers near me?', 'Is now a good time to sell in my area?'],
        shift_h='Local expertise is the whole game.',
        shift=['People do not ask AI for any agent. They ask for someone who knows their city, their neighborhood or their situation, like first-time buying, relocation or downsizing.',
               'AI looks for proof of that expertise: neighborhood and market content, consistent profiles on major real estate platforms, transaction history signals and reviews from clients in that area. Agents who publish real local knowledge give AI something concrete to recommend.'],
        signals_h='Prove you own your market.', signals_lead='Specific, local and consistent is what AI rewards.',
        signals=[('01', 'Neighborhood expertise', 'Useful content about the areas you serve, from schools to market trends, shows real local knowledge.', None),
                 ('02', 'Consistent agent profiles', 'Aligned profiles across your site, brokerage and real estate platforms make you easy to verify.', None),
                 ('03', 'Client reviews by situation', 'Reviews from buyers, sellers and relocations help AI match you to each type of client.', None)],
        help_h='Built for agents and teams.', help_lead='We turn your local knowledge into signals AI can recommend.',
        help=[('NBH', 'Neighborhood content', 'Area guides and market answers that position you as the go-to local expert.', ('AI content optimization', '/ai-content-optimization/')),
              ('ID', 'Agent and team entity', 'You, your team and your brokerage clearly connected across platforms and schema.', ('Entity optimization', '/entity-optimization/')),
              ('REV', 'Review strategy', 'A review process that captures the type of client and neighborhood for every closing.', None),
              ('DIR', 'Platform alignment', 'Consistent, complete profiles on the real estate platforms AI checks.', None),
              ('REP', 'Personal reputation', 'Protect your name, since in real estate your name is your brand.', ('Personal reputation', '/reputation-management/personal-reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See when AI names you for your cities and neighborhoods.', ('AI search reporting', '/ai-search-reporting/'))],
        faq=[('Can AI recommend individual agents?', 'Yes. When people ask for the best agent in a city or neighborhood, AI assistants often name individual agents and teams that have strong, consistent local signals and reviews.'),
             ('Our brokerage controls the website. Can you still help?', 'Yes. We can strengthen your profiles, reviews, local content and personal entity signals, and work with your brokerage site where possible.'),
             ('Does market content really matter?', 'It is one of the clearest ways to prove local expertise. Helpful, accurate neighborhood and market content gives AI concrete reasons to recommend you.'),
             ('We are a team. Should we promote the team or each agent?', 'Usually both, connected clearly. We help AI understand the team brand and each agent, so whichever name people ask about leads back to you.')],
        cta_h='Be the agent AI <em>calls the local expert.</em>'),

    'professional-services': dict(
        name='Professional Services', icon='PS', noun='professional services firms', topic='your professional services',
        title='AI Search Optimization for Professional Services Firms',
        seo=('AI Search for Professional Services | Yroc Consulting',
             'Get your accounting, consulting, financial or insurance firm recommended by ChatGPT, Gemini and Google AI when clients research advisors.'),
        card='Accountants, consultants, CFOs and insurance agencies: be the firm AI recommends to decision makers.',
        h1='AI search optimization for <em>professional services firms.</em>',
        lead='Business owners now ask AI to recommend accountants, consultants, fractional CFOs and insurance advisors. We help your firm be the trusted name it gives.',
        prompts_h='Decision makers are asking AI…',
        prompts=['Who is a good fractional CFO for a growing small business?', 'Which accounting firms near me specialize in construction companies?', 'What should I look for in a business insurance agent?',
                 'Who are the best business consultants for scaling operations?', 'Do I need a CPA or a bookkeeper?'],
        shift_h='Expertise is invisible unless you publish it.',
        shift=['Professional services are bought on trust and expertise, and both are hard to see from the outside. AI fills that gap by looking for evidence: published insights, credentials, industry specialties, speaking and press mentions, and client reviews.',
               'Firms that clearly state who they serve and share real expertise online give AI the confidence to recommend them. Firms that rely only on referrals often do not appear at all.'],
        signals_h='Show the expertise clients pay for.', signals_lead='AI recommends the advisors it can verify.',
        signals=[('01', 'Clear specialization', 'Industries served, client size and services spelled out so AI can match you to the right questions.', None),
                 ('02', 'Published expertise', 'Insights, guides and answers that demonstrate how you think and what you know.', None),
                 ('03', 'Third-party proof', 'Credentials, press, speaking, associations and reviews confirm your reputation independently.', None)],
        help_h='Built for advisory firms.', help_lead='We make your expertise visible to the systems clients now ask first.',
        help=[('EXP', 'Expertise content', 'Thought leadership and answer-first content built around the questions your ideal clients ask.', ('AI content optimization', '/ai-content-optimization/')),
              ('ID', 'Firm and leader entities', 'Your firm and principals clearly defined across profiles, directories and schema.', ('Entity optimization', '/entity-optimization/')),
              ('PR', 'Authority building', 'Mentions, interviews and association listings that give AI independent confirmation.', None),
              ('REV', 'Client proof', 'Reviews and testimonials that describe outcomes in your specialty.', None),
              ('REP', 'Reputation management', 'Protect your firm and leaders from outdated or negative results.', ('Reputation management', '/reputation-management/')),
              ('↗', 'Weekly AI tracking', 'See when AI recommends your firm for your specialties.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['cfo'],
        faq=[('Do business owners really use AI to find advisors?', 'Increasingly, yes. Many start by asking AI what kind of help they need and who provides it, and AI often names firms with clear specialization and visible expertise.'),
             ('We get most clients through referrals. Why does AI matter?', 'Referred prospects still check you out, and many now ask AI about you. If AI cannot confirm your expertise, or describes you inaccurately, referrals can stall.'),
             ('How do we show expertise without giving away our services?', 'We share the what and why, the questions clients ask and how to think about them, while your hands-on work stays the service they hire you for.'),
             ('Can you help our leaders build personal authority too?', 'Yes. Principals and partners can be connected to the firm as recognized experts, which strengthens how AI sees both.')],
        cta_h='Be the firm AI <em>recommends to decision makers.</em>'),

    'restaurants': dict(
        name='Restaurants', icon='RS', noun='restaurants and caterers', topic='your restaurant',
        title='AI Search Optimization for Restaurants',
        seo=('AI Search Optimization for Restaurants | Yroc Consulting',
             'Get your restaurant or catering business recommended by ChatGPT, Gemini and Google AI when diners ask where to eat tonight.'),
        card='Be the restaurant or caterer AI suggests when someone asks where to eat tonight or who to book for an event.',
        h1='AI search optimization for <em>restaurants and caterers.</em>',
        lead='"Where should we eat tonight?" is now a question people ask AI. We help your restaurant or catering business be the suggestion it makes.',
        prompts_h='Diners are asking AI…',
        prompts=['What is the best Thai food near me?', 'Where can I get great BBQ catering for a company event?', 'Which restaurants near me are good for a date night?',
                 'What places nearby have gluten-free options?', 'Where is open late for dinner near me?'],
        shift_h='AI is the new food critic.',
        shift=['Diners ask AI very specific questions: a cuisine, a dish, a vibe, a dietary need, an occasion. AI answers by combining what it knows about menus, reviews, hours and reputation into a short list of places to try.',
               'Restaurants with complete, accurate information, menus that AI can read, and reviews that mention specific dishes and experiences are the ones that get suggested. Caterers win the same way for events and corporate orders.'],
        signals_h='Details get you on the list.', signals_lead='Specific questions need specific information.',
        signals=[('01', 'Readable menus', 'Menus on your site as real text, not just images, let AI connect your dishes to what people ask for.', None),
                 ('02', 'Accurate hours and details', 'Consistent hours, location, dietary options and services across every platform build trust.', None),
                 ('03', 'Reviews about dishes', 'Reviews that name favorite dishes and occasions help AI recommend you for exactly those things.', None)],
        help_h='Built for restaurants and caterers.', help_lead='We help AI know what you serve and why people love it.',
        help=[('MNU', 'Menu and dish content', 'Readable menus and dish descriptions that match the way diners ask questions.', ('AI content optimization', '/ai-content-optimization/')),
              ('MAP', 'Listing accuracy', 'Hours, location, photos and attributes kept consistent on every platform AI checks.', None),
              ('REV', 'Review momentum', 'Simple ways to turn happy guests into detailed, dish-specific reviews.', None),
              ('{ }', 'Restaurant schema', 'Structured data for menus, cuisine, hours and catering services.', ('Schema & structured data', '/schema-structured-data/')),
              ('CAT', 'Catering visibility', 'Dedicated catering content so AI recommends you for events and corporate orders.', None),
              ('↗', 'Weekly AI tracking', 'See which cuisine, dish and occasion questions AI recommends you for.', ('AI search reporting', '/ai-search-reporting/'))],
        cases=['thai'],
        faq=[('Do people really use AI to choose a restaurant?', 'Yes. Questions like the best tacos near me or where to take a client for dinner are common, and AI answers name specific restaurants.'),
             ('Our menu is a PDF. Is that a problem?', 'It can be. Menus as text on your website are much easier for AI to read and match to dishes people ask about. We help you publish yours properly.'),
             ('Can you help with catering as well?', 'Yes. Catering gets its own content and signals so AI can recommend you for events, offices and celebrations.'),
             ('How do reviews affect AI recommendations?', 'AI uses reviews to understand what you are known for. Reviews that mention specific dishes and experiences help it recommend you for those exact requests.')],
        cta_h='Be the restaurant AI <em>tells people to try.</em>'),
}


# Hub sections for the first batch; the second batch sets 'group' itself.
for _slug, _g in {'roofing-construction': 'home', 'home-services': 'home', 'pest-control': 'home', 'medical-wellness': 'health',
                  'med-spas': 'health', 'law-firms': 'legal', 'tax-resolution': 'legal', 'professional-services': 'legal',
                  'real-estate': 'property', 'restaurants': 'retail'}.items():
    PAGES[_slug]['group'] = _g

from industries_batch2 import PAGES2  # noqa: E402
PAGES.update(PAGES2)


def main():
    m = json.load(open('content/manifest.json'))
    seo = json.load(open('content/seo.json'))
    files = {'industries': hub_page(PAGES)}
    for slug, d in PAGES.items():
        files['industry-' + slug] = industry_page(slug, d)
    for name, html in files.items():
        assert '\u2014' not in html, name
        open(f'{OUT}{name}.html', 'w').write(html)

    def upsert(entry):
        for i, x in enumerate(m['pages']):
            if x['slug'] == entry['slug'] and x.get('parent') == entry.get('parent'):
                m['pages'][i] = {**x, **entry}
                return
        m['pages'].append(entry)

    upsert({'slug': 'industries', 'title': 'Industries', 'file': 'pages/industries.html', 'template': 'page-landing'})
    seo['/industries/'] = ['AI Search Optimization by Industry | Yroc Consulting',
                           'AI search optimization tailored to your industry: contractors, clinics, law and financial firms, real estate, restaurants, auto and more.']
    for order, (slug, d) in enumerate(PAGES.items()):
        upsert({'slug': slug, 'title': d['title'], 'parent': 'industries', 'file': f'pages/industry-{slug}.html', 'template': 'page-landing', 'order': order})
        seo[f'/industries/{slug}/'] = list(d['seo'])
    for path, (t, desc) in seo.items():
        if path.startswith('/industries/'):
            assert len(t) <= 60, (path, len(t))
            assert 110 <= len(desc) <= 160, (path, len(desc))
            assert '\u2014' not in t + desc, path
    for path, data in (('content/manifest.json', m), ('content/seo.json', seo)):
        with open(path, 'w') as f:
            f.write(json.dumps(data, indent=2, ensure_ascii=False) + '\n')
    print('ok', len(files), 'pages')


if __name__ == '__main__':
    main()
