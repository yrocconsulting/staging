"""Generate the reputation management "Who we help" hub and audience pages (run from the repo root).

Writes content/pages/rep-who-*.html, adds the pages to content/manifest.json under reputation-management,
and their titles/descriptions to content/seo.json. Re-run after editing the copy below.
House rules: no em dashes, no employee names except Cory Long, never name clients, never promise removal.
"""
import json
import sys

sys.path.insert(0, 'tools')
from blocks import p, h, ul, buttons, group, cards, faq, head, hero, steps  # noqa: E402
from gen_industries import rows, glass  # noqa: E402

OUT = 'content/pages/'
CONSULT = ('Book a confidential consultation', '/book-a-strategy-call/')
AUDIT = ('See what AI says about you', '/ai-visibility-audit/', True)

NEG = ('Negative search results', '/reputation-management/negative-search-results/')
KP = ('Knowledge panel & repair', '/reputation-management/knowledge-panel-repair/')
PERS = ('Personal reputation', '/reputation-management/personal-reputation-management/')
BIZ = ('Business reputation', '/reputation-management/business-reputation-management/')
REB = ('Rebrand strategy', '/reputation-management/rebrand-strategy/')
ENT = ('Entity optimization', '/entity-optimization/')

PROCESS = [
    ('Confidential audit', 'We search your name the way others do: Google, image results, news, social, review sites and every major AI assistant. You see exactly what people find and what AI says about you.'),
    ('Strategy', 'We decide what to build, what to strengthen and what to push down, and set clear priorities for the first 90 days.'),
    ('Build your owned presence', 'A personal site, profiles and structured data that give Google and AI an accurate, authoritative source about you.'),
    ('Earn credible mentions', 'Interviews, features, podcasts and listings that tell your story from independent sources.'),
    ('Monitor and protect', 'Ongoing monitoring of search results, mentions and AI answers, so problems are caught early.'),
]


def page(d):
    parts = [
        hero('Reputation · ' + d['name'], d['h1'], d['lead'], [CONSULT, AUDIT],
             [glass([p(d['glass_eyebrow'], 'yr-eyebrow'), h(d['glass_h'], 3), ul(d['glass'], 'yr-checks')])]),
        group(head('Why it matters', d['why_h']) + [p(x, 'yr-lead' if i == 0 else None) for i, x in enumerate(d['why'])], 'yr-section yr-intro', 880),
        group(head('What people and AI look at', d['signals_h'], d['signals_lead']) + [cards(d['signals'], 'yr-cards yr-trio')], 'yr-section yr-section--tight'),
        group(head('How we help', d['help_h'], d['help_lead'])
              + [cards(d['help'][:3], 'yr-cards', 'yr-card yr-card--glow'), cards(d['help'][3:], 'yr-cards', 'yr-card yr-card--glow')],
              'yr-section yr-dark yr-services'),
        group(head('Our process', 'Discreet, steady and measurable.') + [steps(PROCESS)], 'yr-section yr-bg', 900),
        faq(d['faq'], f'Reputation management for {d["plural"]}: FAQs'),
        group([p('Private and confidential', 'yr-eyebrow'), h(d['cta_h']),
               p('Start with a confidential review of what Google and AI show when someone searches your name. No obligation, and nothing is shared.', 'yr-lead'),
               buttons(('Book a confidential consultation', '/book-a-strategy-call/'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
    ]
    return '\n\n'.join(parts) + '\n'


def hub(pages):
    items = [(d['icon'], d['name'], d['card'], ('Learn more', f'/reputation-management/{slug}/')) for slug, d in pages.items()]
    parts = [
        hero('Reputation management', 'Reputation management for <em>people with a name to protect.</em>',
             'When someone hears your name, they search it, and more and more they ask AI. Whether you write, speak, compete, lead or simply live a public life, we make sure what they find reflects who you really are.',
             [CONSULT, AUDIT],
             [glass([p('Who searches your name', 'yr-eyebrow'), h('The people who matter most look you up.', 3),
                     ul(['Event planners and publishers', 'Investors, boards and business partners', 'Clients, patients and fans',
                         'Journalists and producers', 'New friends, neighbors and introductions'], 'yr-checks')])]),
        group(head('Who we help', 'Built around your world.', 'Every public life has its own platforms, risks and opportunities. Choose yours to see how we protect and build your reputation.')
              + rows(items), 'yr-section'),
        group([p('Not listed?', 'yr-eyebrow'), h('If people search your name, <em>this matters.</em>'),
               p('We help anyone whose name carries weight. Start with a confidential review of what Google and AI say about you today.', 'yr-lead'),
               buttons(('Book a confidential consultation', '/book-a-strategy-call/'), ('Call 903.300.LEADS', 'tel:+19033005323', True))], 'yr-ctaband', 960),
    ]
    return '\n\n'.join(parts) + '\n'


PAGES = {
    'authors': dict(
        name='Authors', plural='authors', icon='BK',
        seo=('Reputation Management for Authors | Yroc Consulting',
             'Build an author presence that sells books and wins speaking invitations. Knowledge panels, AI answers, reviews and search results.'),
        card='Make sure readers, publishers and event hosts find the author you are, not one bad review or a stranger with your name.',
        h1='Reputation management <em>for authors.</em>',
        lead='Readers, agents, publishers and podcast hosts all search your name before they commit. We build an author presence that sells books and opens doors.',
        glass_eyebrow='What readers find', glass_h='When they search your name, do they see…',
        glass=['A Knowledge Panel with your photo and books?', 'Accurate answers when they ask AI about you?', 'Your own site before retailer pages?',
               'Interviews and features, not just reviews?', 'You, and not someone else with your name?'],
        why_h='Your name is your brand, and your next book depends on it.',
        why=['Every launch, speaking request and media booking starts with a search. If the first page shows a thin bio, an old controversy or a different person with your name, opportunities quietly go elsewhere.',
             'AI assistants now answer "who is this author?" too. They pull from retailer pages, review sites, interviews and knowledge sources. We make sure they find a clear, accurate and impressive story.'],
        signals_h='Where your author reputation lives.', signals_lead='Google and AI piece you together from many sources.',
        signals=[('01', 'Knowledge sources', 'Knowledge Panels, Wikidata and author profiles that confirm who you are and what you have written.', None),
                 ('02', 'Retailer and review pages', 'Amazon author pages, Goodreads and reviews that shape first impressions.', None),
                 ('03', 'Media and interviews', 'Podcasts, articles and features that add credibility beyond your own words.', None)],
        help_h='A presence that matches your work.', help_lead='We build, strengthen and protect your author reputation.',
        help=[('KP', 'Knowledge Panel', 'Claim and enrich your panel with your photo, books and links.', KP),
              ('WEB', 'Author website and schema', 'A fast author site with Person and Book markup that Google and AI trust.', ENT),
              ('PR', 'Media placements', 'Interviews and features that tell your story from credible outside sources.', None),
              ('REV', 'Review strategy', 'Ethical ways to grow genuine reviews and respond to unfair ones.', None),
              ('NEG', 'Suppress what hurts', 'Push down outdated or misleading results with stronger, accurate content.', NEG),
              ('AI', 'AI answer monitoring', 'Track and improve what ChatGPT, Gemini and Google AI say about you.', None)],
        faq=[('Can you get me a Google Knowledge Panel?', 'We build the signals Google uses to create and enrich panels, such as consistent profiles, structured data and credible mentions, and help you claim and update yours. Google makes the final decision, so no one can guarantee a panel, but we greatly improve the odds.'),
             ('Can you remove bad reviews of my book?', 'We cannot remove honest reviews, and we would not try. We help you earn more genuine reviews, respond professionally and make sure your overall presence tells the full story.'),
             ('Another author has my name. Can you help?', 'Yes. Clear entity signals, consistent profiles and your own site help Google and AI tell you apart.'),
             ('Do I need a website if I am on Amazon?', 'Yes. Your own site is the one source you control, and it is often what Google and AI treat as the authoritative source about you.')],
        cta_h='Let your name <em>sell your next book.</em>'),

    'speakers': dict(
        name='Speakers', plural='speakers', icon='SPK',
        seo=('Reputation Management for Speakers | Yroc Consulting',
             'Win more keynotes and higher fees. Build a speaker reputation that event planners and AI trust, with knowledge panels and media.'),
        card='Event planners search you before they book you. Make sure what they find wins the keynote.',
        h1='Reputation management <em>for speakers.</em>',
        lead='Before an event planner books you, they search your name and ask AI about you. We make sure what they find says you are worth the fee.',
        glass_eyebrow='What planners check', glass_h='Before they book, they look for…',
        glass=['Video of you on real stages', 'Topics and audiences you are known for', 'Credible media and podcast appearances',
               'Testimonials from respected organizations', 'Nothing that could embarrass their event'],
        why_h='Planners are risk averse. Your search results decide.',
        why=['An event planner is putting their reputation on the line when they book you. They want proof you deliver and assurance there is nothing in your past that will surprise their audience or their boss.',
             'They search Google, check speaker bureaus, watch video and increasingly ask AI to summarize who you are. A strong, consistent presence makes the decision easy. A thin or messy one sends them to the next name on the list.'],
        signals_h='What makes a speaker bookable online.', signals_lead='Proof, authority and safety.',
        signals=[('01', 'Stage proof', 'Video, event pages and testimonials that show you deliver in front of real audiences.', None),
                 ('02', 'Topic authority', 'Articles, interviews and podcasts that tie your name to the subjects you speak on.', None),
                 ('03', 'A clean first page', 'Search results and AI answers free of outdated or misleading surprises.', None)],
        help_h='Become the obvious choice.', help_lead='We build the presence that earns bigger stages.',
        help=[('KP', 'Knowledge Panel', 'A panel that shows your photo, topics and links when planners search you.', KP),
              ('WEB', 'Speaker site and schema', 'A booking-ready site with topics, video and structured data.', ENT),
              ('PR', 'Media and podcasts', 'Appearances that build authority on your topics.', None),
              ('BUR', 'Bureau and directory profiles', 'Consistent profiles on the speaker platforms planners use.', None),
              ('NEG', 'Clean up the first page', 'Push down results that do not reflect who you are today.', NEG),
              ('AI', 'AI answer monitoring', 'Make sure AI describes your expertise accurately.', None)],
        faq=[('Will this help me get booked more?', 'A strong online presence removes doubt for planners and bureaus. It does not replace a great talk, but it makes sure your search results never cost you a booking.'),
             ('Can AI really affect speaking bookings?', 'Yes. Planners increasingly ask AI to suggest or vet speakers on a topic. Clear authority signals make you more likely to be suggested and described well.'),
             ('I have an old controversy in my results. Can you help?', 'Yes. We cannot erase legitimate news, but we build stronger, current content that tells the full story and moves the old result down.'),
             ('Do you help with speaker reels and websites?', 'We build and optimize your speaker site and make sure your video and topics are found. Our web team can design the site with you.')],
        cta_h='Let your search results <em>book the next stage.</em>'),

    'coaches-consultants': dict(
        name='Coaches & Consultants', plural='coaches and consultants', icon='CO',
        seo=('Reputation Management for Coaches | Yroc Consulting',
             'Coaches and consultants are hired on trust. Build a reputation that turns searches and AI answers into clients, and protect it.'),
        card='Clients hire you, not a company. Make sure your name earns their trust the moment they search it.',
        h1='Reputation management <em>for coaches and consultants.</em>',
        lead='When you are the product, your name is everything. Prospective clients search you and ask AI if you are legit. We make sure the answer is a confident yes.',
        glass_eyebrow='What prospects check', glass_h='Before they hire you, they want to know…',
        glass=['Are you credible and experienced?', 'Do you get real results for people like them?', 'What do former clients say?',
               'Have others featured or endorsed you?', 'Is there anything they should worry about?'],
        why_h='Trust is the whole sale.',
        why=['Coaching and consulting are bought on trust. Prospects cannot try you before they buy, so they look for proof: credentials, results, testimonials, media and what others say about you.',
             'They also ask AI whether you are worth it. If your presence is thin, or a single complaint dominates, AI may hedge or suggest someone else. A strong, consistent reputation does the selling for you.'],
        signals_h='How prospects judge you online.', signals_lead='Proof that you deliver.',
        signals=[('01', 'Credentials and story', 'Your background, certifications and approach, clearly and consistently presented.', None),
                 ('02', 'Client proof', 'Reviews and testimonials that describe real outcomes.', None),
                 ('03', 'Outside validation', 'Podcasts, articles and features that confirm your expertise.', None)],
        help_h='Make your expertise impossible to miss.', help_lead='A reputation that brings in the right clients.',
        help=[('WEB', 'Personal brand site', 'A site and profiles that present your expertise with authority.', ENT),
              ('REV', 'Testimonial strategy', 'Collect and showcase genuine client results ethically.', None),
              ('PR', 'Podcasts and features', 'Appearances that build authority and reach new clients.', None),
              ('KP', 'Knowledge Panel', 'A panel that confirms who you are when prospects search.', KP),
              ('NEG', 'Protect against attacks', 'Respond to unfair reviews and push down misleading results.', NEG),
              ('AI', 'AI answer monitoring', 'Make sure AI calls you credible.', None)],
        faq=[('A former client posted an unfair review. What can we do?', 'We help you respond professionally, check whether it violates platform rules, and build enough genuine positive proof that one review no longer defines you.'),
             ('Does reputation management help me get clients?', 'Yes. A strong presence shortens the trust-building process, so more of the people who discover you actually book a call.'),
             ('I run a group program and a personal brand. Which do we focus on?', 'Usually both, connected. We make sure your name and your program reinforce each other in search and AI.'),
             ('How long does it take?', 'Most clients see a meaningfully stronger first page within three to six months, with ongoing improvements after that.')],
        cta_h='Let your reputation <em>close the sale.</em>'),

    'athletes': dict(
        name='Athletes', plural='athletes', icon='ATH',
        seo=('Reputation Management for Athletes | Yroc Consulting',
             'Protect and grow your name for sponsors, recruiters and fans. Reputation management for pro, college and retired athletes.'),
        card='Sponsors, recruiters and fans search your name. Control the story beyond the box score.',
        h1='Reputation management <em>for athletes.</em>',
        lead='Your career happens in public. Sponsors, recruiters, teams and fans search your name constantly. We help you control the story on and off the field.',
        glass_eyebrow='Who is searching', glass_h='Your name gets searched by…',
        glass=['Sponsors and brand partners', 'Coaches, scouts and recruiters', 'Agents and teams', 'Media and fans', 'Future employers after your playing days'],
        why_h='Your name is worth more than your stats.',
        why=['Endorsements, NIL deals and life-after-sports opportunities all depend on how your name looks online. Brands want partners who are marketable and safe, and they check carefully.',
             'One bad headline, an old post or a misleading story can follow you for years and show up in AI answers about you. We build a presence that highlights your character, your story and your value.'],
        signals_h='What brands and teams see.', signals_lead='Performance is only part of the picture.',
        signals=[('01', 'Your story', 'An owned site and profiles that tell who you are beyond the stat sheet.', None),
                 ('02', 'Media coverage', 'Features about your community work, journey and character.', None),
                 ('03', 'A clean digital footprint', 'Search, image and social results that do not raise red flags.', None)],
        help_h='Built for public careers.', help_lead='Grow your value and protect it.',
        help=[('KP', 'Knowledge Panel', 'An accurate panel with your photo, team history and achievements.', KP),
              ('WEB', 'Personal brand site', 'Your hub for sponsors, media and fans, with structured data.', ENT),
              ('PR', 'Story-driven media', 'Features that showcase your character and community impact.', None),
              ('SOC', 'Social footprint review', 'Find and address old posts and content that could hurt deals.', None),
              ('NEG', 'Crisis and suppression', 'Respond to negative stories and push down misleading results.', NEG),
              ('NEXT', 'Life after sports', 'Reposition your name for business, broadcasting or coaching.', REB)],
        faq=[('Do you work with college athletes for NIL?', 'Yes. A strong, clean presence helps college athletes attract better NIL partners and protects them as their profile grows.'),
             ('Can you delete old social posts or articles?', 'You can delete your own posts, and we help you find the ones that matter. News articles usually cannot be removed, but we can push them down with stronger, current content.'),
             ('Is this only for professionals?', 'No. We help high school and college athletes heading to the next level, professionals and retired athletes building their next career.'),
             ('How quickly can you respond to a crisis?', 'Fast. We help you respond publicly, then build the content that keeps one moment from defining your search results.')],
        cta_h='Own your story <em>beyond the game.</em>'),

    'executives': dict(
        name='Executives & CEOs', plural='executives', icon='CEO',
        seo=('Reputation Management for Executives | Yroc Consulting',
             'Boards, investors and recruiters search executives first. Build a leadership reputation that Google and AI describe accurately.'),
        card='Boards, investors, recruiters and reporters search you first. Make sure your leadership story leads.',
        h1='Reputation management <em>for executives and CEOs.</em>',
        lead='Before a board meeting, a funding round, an acquisition or a job offer, people search your name and ask AI about you. We make sure they find the leader you are.',
        glass_eyebrow='When they search you', glass_h='Executives get searched by…',
        glass=['Boards and investors', 'Executive recruiters', 'Acquirers and partners', 'Journalists', 'Employees and future hires'],
        why_h='Your personal reputation is a company asset.',
        why=['A CEO\'s name moves with the company. Investors, boards and partners judge leadership by what they find, and a single unflattering story can overshadow years of results.',
             'AI assistants now summarize executives in seconds, pulling from news, profiles and company pages. We make sure that summary is accurate, current and reflects your track record.'],
        signals_h='How leaders are judged online.', signals_lead='Credibility, track record and judgment.',
        signals=[('01', 'Leadership profile', 'Consistent bios and profiles that reflect your roles and results.', None),
                 ('02', 'Thought leadership', 'Articles, interviews and talks that show how you think.', None),
                 ('03', 'News and AI summaries', 'Coverage and AI answers that tell the full, current story.', None)],
        help_h='Executive presence, online.', help_lead='Discreet work for leaders with a lot at stake.',
        help=[('KP', 'Knowledge Panel', 'An accurate panel with your title, company and photo.', KP),
              ('TL', 'Thought leadership', 'Bylines, interviews and talks that build authority in your field.', None),
              ('WEB', 'Executive bio and site', 'An authoritative source about you, with Person markup.', ENT),
              ('NEG', 'Negative result strategy', 'Push down outdated, misleading or unfair results.', NEG),
              ('TRN', 'Transitions', 'Support through role changes, exits and new ventures.', REB),
              ('CO', 'Company alignment', 'Keep your personal and company reputation working together.', BIZ)],
        faq=[('Is this confidential?', 'Completely. Many executives prefer that no one knows they work on their reputation. We work discreetly and never reference clients.'),
             ('Can you help during a leadership transition?', 'Yes. We help reposition your name ahead of a new role, an exit or a new venture, so the story is yours to tell.'),
             ('There is a negative article about me. Can it be removed?', 'Legitimate news usually cannot be removed. We build strong, accurate content that ranks above it and gives Google and AI a fuller picture.'),
             ('Do you work with company communications teams?', 'Yes. We coordinate with your communications or PR team so personal and company messaging stay aligned.')],
        cta_h='Make sure your name <em>leads like you do.</em>'),

    'founders-entrepreneurs': dict(
        name='Founders & Entrepreneurs', plural='founders and entrepreneurs', icon='FDR',
        seo=('Reputation Management for Founders | Yroc Consulting',
             'Investors, partners and customers search founders first. Build a founder reputation that helps you raise, sell and recruit.'),
        card='Investors, partners and early customers bet on you. Give them every reason to.',
        h1='Reputation management <em>for founders and entrepreneurs.</em>',
        lead='In the early days, people are betting on you as much as the business. Investors, partners, hires and customers search your name. We make sure it inspires confidence.',
        glass_eyebrow='Who bets on you', glass_h='Founders are vetted by…',
        glass=['Investors and lenders', 'Co-founders and early hires', 'Partners and acquirers', 'Press and podcast hosts', 'Your first customers'],
        why_h='Founder reputation is fundraising, hiring and sales.',
        why=['A founder\'s name opens doors or quietly closes them. Investors do diligence, candidates check you out before they join, and customers want to know who is behind the brand.',
             'A thin presence can look like inexperience, and an old failure or lawsuit can look worse than it was. We build a story of credibility, momentum and expertise that works for you every time someone searches.'],
        signals_h='How founders are vetted.', signals_lead='Track record, vision and integrity.',
        signals=[('01', 'Your founder story', 'A clear, consistent story across your site, profiles and press.', None),
                 ('02', 'Credible coverage', 'Features, podcasts and interviews that validate you and your company.', None),
                 ('03', 'A clean history', 'Past ventures, disputes or failures put in honest, current context.', None)],
        help_h='A name investors trust.', help_lead='We build the founder presence your company deserves.',
        help=[('WEB', 'Founder site and profiles', 'An authoritative home for your story with structured data.', ENT),
              ('PR', 'Press and podcasts', 'Coverage that builds credibility with investors and customers.', None),
              ('KP', 'Knowledge Panel', 'A panel that ties you to your company and achievements.', KP),
              ('NEG', 'Context for the past', 'Push down misleading results and add context to old setbacks.', NEG),
              ('CO', 'Company reputation', 'Align your name with your brand as the company grows.', BIZ),
              ('AI', 'AI answer monitoring', 'Make sure AI describes you and your company accurately.', None)],
        faq=[('I am raising soon. When should we start?', 'Now. Building credible content and coverage takes a few months, and you want it in place before investors start their diligence.'),
             ('A previous company failed. Will that hurt me?', 'Not if it is told well. Many investors respect experience. We make sure the story includes what you learned and what you built next.'),
             ('Should the focus be me or my company?', 'Both, connected. Early on your name often carries the company, so we strengthen both and link them clearly.'),
             ('Can you help me get on podcasts?', 'Yes. Podcast and media appearances are one of the most effective ways to build founder authority, and we help secure them.')],
        cta_h='Give investors <em>every reason to believe.</em>'),

    'physicians': dict(
        name='Physicians', plural='physicians', icon='DR',
        seo=('Reputation Management for Physicians | Yroc Consulting',
             'Protect your name as a doctor. Manage ratings, search results and AI answers about you while respecting patient privacy rules.'),
        card='Patients research doctors before they book. Protect the reputation you spent a career earning.',
        h1='Reputation management <em>for physicians.</em>',
        lead='Patients look up their doctor before the first appointment, and many now ask AI. We help physicians protect and present a reputation that reflects their skill and care.',
        glass_eyebrow='What patients check', glass_h='Before an appointment, patients look at…',
        glass=['Doctor rating sites and reviews', 'Your credentials and specialties', 'Your practice and hospital affiliations', 'News or board information', 'What AI says about you'],
        why_h='One unfair review can outweigh a career.',
        why=['Physicians are judged on rating sites where a handful of reviews, often about parking or wait times, can define them. Patients rarely see the full picture of your training and outcomes.',
             'Responding is complicated by privacy rules, and many doctors simply do not have time. We help you build an accurate, professional presence and respond the right way, so your reputation reflects your care.'],
        signals_h='Where a physician reputation lives.', signals_lead='Patients and AI read all of it.',
        signals=[('01', 'Rating platforms', 'Doctor review and directory sites patients check first.', None),
                 ('02', 'Credentials and profile', 'Board certification, training and specialties shown consistently.', None),
                 ('03', 'Professional content', 'Articles, interviews and talks that show your expertise.', None)],
        help_h='Care for your professional reputation.', help_lead='Built around patient privacy.',
        help=[('PRF', 'Profile cleanup', 'Accurate, complete profiles on the doctor directories patients use.', None),
              ('REV', 'Privacy-aware reviews', 'Encourage genuine patient reviews and respond without disclosing care.', None),
              ('WEB', 'Physician bio and schema', 'An authoritative source about you with Physician markup.', ENT),
              ('PR', 'Expert visibility', 'Media and articles that position you as a trusted expert.', None),
              ('NEG', 'Negative result strategy', 'Push down misleading or unfair results.', NEG),
              ('AI', 'AI answer monitoring', 'Make sure AI describes your credentials accurately.', None)],
        faq=[('How do we respond to reviews without violating privacy?', 'We help you respond in general, professional terms that never confirm someone was a patient or discuss their care.'),
             ('Can negative reviews be removed?', 'Only when they break a platform\'s rules, such as fake or abusive reviews, and we help you report those. Otherwise we build enough genuine positive feedback to balance the picture.'),
             ('I am employed by a hospital. Can I still do this?', 'Yes. Your personal professional reputation follows you across employers, and we work within any policies your organization has.'),
             ('Does this help my practice too?', 'Yes. Patients often choose a practice because of a specific doctor, so a strong personal reputation supports the whole practice.')],
        cta_h='Let your reputation <em>reflect your care.</em>'),

    'creators-influencers': dict(
        name='Creators & Influencers', plural='creators and influencers', icon='CR',
        seo=('Reputation Management for Influencers | Yroc Consulting',
             'Brands vet creators before they sign. Protect your name from impersonators and drama, and build a presence beyond the algorithm.'),
        card='Brands vet you before every deal. Build a reputation that lasts longer than any algorithm.',
        h1='Reputation management <em>for creators and influencers.</em>',
        lead='Your audience lives on platforms you do not control. Brands, agencies and press search you off-platform before they sign. We build a reputation that lasts beyond the feed.',
        glass_eyebrow='Before the deal', glass_h='Brands and agencies check…',
        glass=['What comes up when they search your name', 'Old posts, drama or controversies', 'Press and features about you', 'Impersonator or fan accounts', 'What AI says about you'],
        why_h='Brand safety decides your next deal.',
        why=['Brand partners are careful about who represents them. Their teams search your name, scroll your history and read what others say. One viral moment or old post can cost you a deal you never hear about.',
             'Creators also face impersonators, scam accounts and misinformation. A strong presence off-platform, on your own site, in the press and in knowledge sources, protects your name and your income.'],
        signals_h='Your reputation beyond the feed.', signals_lead='What brands see when they look closer.',
        signals=[('01', 'Owned presence', 'A personal site and consistent profiles you control.', None),
                 ('02', 'Press and credibility', 'Features and interviews that legitimize you as a creator and business.', None),
                 ('03', 'Brand safety', 'Search and AI results free of misleading drama or impersonators.', None)],
        help_h='Protect the brand that is you.', help_lead='Build value that no algorithm can take away.',
        help=[('KP', 'Knowledge Panel', 'A verified panel that confirms which accounts are really you.', KP),
              ('WEB', 'Creator site and media kit', 'A home for brands and press, with structured data.', ENT),
              ('IMP', 'Impersonator response', 'Find and report fake accounts and scams using your name.', None),
              ('PR', 'Press features', 'Coverage that positions you as a creator brands want to work with.', None),
              ('NEG', 'Drama and crisis response', 'Respond well and push down misleading results.', NEG),
              ('AI', 'AI answer monitoring', 'Make sure AI describes you accurately.', None)],
        faq=[('Can you get me verified?', 'We cannot grant platform verification, but we build the press, knowledge panel and consistent profiles that platforms and Google look for, which improves your chances.'),
             ('Someone is impersonating me. Can you help?', 'Yes. We help identify fake accounts, report them, and strengthen your official presence so fans and brands can tell what is real.'),
             ('I had a controversy. Can it be erased?', 'Usually not entirely. We help you respond, then build strong, current content so one moment does not define your name.'),
             ('Why do I need a website if my audience is on social?', 'Platforms change and accounts get suspended. Your own site is the one place you control, and it is what Google and AI trust most about you.')],
        cta_h='Build a name that <em>outlasts the algorithm.</em>'),

    'entertainers': dict(
        name='Entertainers', plural='entertainers', icon='ENT',
        seo=('Reputation Management for Entertainers | Yroc Consulting',
             'Musicians, actors and performers: control your story across Google, AI, IMDb, streaming profiles and the press.'),
        card='Musicians, actors and performers: make sure fans, producers and venues find your story, told right.',
        h1='Reputation management <em>for musicians, actors and performers.</em>',
        lead='Fans, producers, venues and casting teams search you constantly. We make sure your story is accurate, impressive and yours to tell.',
        glass_eyebrow='Who looks you up', glass_h='Your name is searched by…',
        glass=['Fans discovering your work', 'Producers, labels and casting teams', 'Venues and promoters', 'Journalists and reviewers', 'Brands looking for partners'],
        why_h='Your story should be told your way.',
        why=['Entertainment careers live on discovery. When someone hears your song or sees your work, they search your name. What they find shapes whether they become a fan, book you or cast you.',
             'Inaccurate bios, outdated credits, old gossip and fan wikis often show up first, and AI repeats them. We make sure the official story is clear, current and easy for Google and AI to find.'],
        signals_h='Where your story lives.', signals_lead='Many platforms, one consistent identity.',
        signals=[('01', 'Official sources', 'Your site, Knowledge Panel and profiles that confirm the real you.', None),
                 ('02', 'Credits and catalogs', 'Accurate credits and streaming profiles across the platforms that matter.', None),
                 ('03', 'Press and reviews', 'Features and reviews that celebrate your work.', None)],
        help_h='A story worth the spotlight.', help_lead='Accuracy, prestige and protection.',
        help=[('KP', 'Knowledge Panel', 'A panel with your photo, works and official links.', KP),
              ('CRD', 'Credits and profile cleanup', 'Accurate bios and credits across entertainment and streaming profiles.', ENT),
              ('PR', 'Press and features', 'Coverage that builds your story and your audience.', None),
              ('WEB', 'Official site', 'A home for fans and industry with structured data.', None),
              ('NEG', 'Gossip and misinformation', 'Push down rumors and correct what is wrong.', NEG),
              ('AI', 'AI answer monitoring', 'Make sure AI tells your story accurately.', None)],
        faq=[('Can you fix my Wikipedia page?', 'Wikipedia has strict rules against paid editing, so we never edit it on your behalf. We help build the independent, reliable coverage editors rely on, and correct errors in sources we can influence properly.'),
             ('My bio on Google is wrong. Can you fix it?', 'Yes. We correct the sources Google draws from, help you claim your Knowledge Panel and suggest edits directly.'),
             ('Do you work with independent artists?', 'Yes. Independent artists often benefit most, because a strong presence helps them stand out without a label\'s publicity team.'),
             ('Can you help with a career change or rebrand?', 'Yes. We help you reposition your name for a new genre, role or direction while keeping your history in context.')],
        cta_h='Let the world find <em>the real you.</em>'),

    'public-figures': dict(
        name='High-Profile Individuals', plural='high-profile individuals', icon='VIP',
        seo=('Reputation Management for Public Figures | Yroc Consulting',
             'A public presence that matches your stature: Knowledge Panel, accurate AI answers and first-page results that tell your story well.'),
        card='You have built a remarkable life. Make sure your online presence reflects it, from Google to AI.',
        h1='An online presence <em>that matches your stature.</em>',
        lead='You have built something remarkable. When people search your name or ask AI who you are, they should see it: a polished Knowledge Panel, accurate answers and a first page that tells your story the way it deserves to be told.',
        glass_eyebrow='The first impression', glass_h='When someone searches your name, they should see…',
        glass=['A Knowledge Panel with your photo and accomplishments', 'An impressive, accurate AI answer to "who is…"', 'Features and profiles that reflect your achievements',
               'Your philanthropy, ventures and story, front and center', 'Nothing outdated, inaccurate or beneath you'],
        why_h='Your reputation arrives before you do.',
        why=['Before a dinner, a board seat, a gala, a deal or a first meeting, people look you up. In a few seconds, Google and AI form an impression of who you are, and that impression should match the accomplished person who walks into the room.',
             'Many successful people discover their search results are thin, out of date or dominated by someone else with their name, or that AI has the details wrong. We build a presence worthy of your legacy and keep it that way.'],
        signals_h='What signals stature online.', signals_lead='Recognition, credibility and accuracy.',
        signals=[('01', 'Recognized identity', 'A Knowledge Panel and knowledge sources that confirm who you are.', None),
                 ('02', 'Prestigious coverage', 'Profiles, features and interviews in respected publications.', None),
                 ('03', 'Your legacy, curated', 'Ventures, philanthropy, boards and honors presented clearly.', None)],
        help_h='Discreet, white-glove reputation work.', help_lead='Your story, told with the polish it deserves.',
        help=[('KP', 'Knowledge Panel', 'Claim and refine a panel with your photo, roles and accomplishments.', KP),
              ('WEB', 'Personal website', 'An elegant, authoritative home for your story and legacy.', ENT),
              ('PR', 'Features and profiles', 'Thoughtful coverage that reflects your achievements and values.', None),
              ('LEG', 'Legacy and philanthropy', 'Showcase giving, boards and community impact with dignity.', None),
              ('NEG', 'Privacy and protection', 'Suppress what is outdated or unwanted, and reduce exposure of personal details.', NEG),
              ('AI', 'AI answer curation', 'Make sure AI describes you accurately and impressively.', PERS)],
        faq=[('I am not a celebrity. Is this for me?', 'Yes. Business owners, investors, philanthropists, community leaders and anyone whose name carries weight benefit from a presence that reflects their accomplishments.'),
             ('Can you get me a Google Knowledge Panel?', 'We build the signals Google relies on, including your own site, structured data, knowledge sources and credible coverage, and help you claim and refine your panel. Google decides when to show one, but we greatly improve the odds.'),
             ('How discreet is this?', 'Completely. We never name clients, and our work is designed to look natural, because it is: real coverage, real profiles and real accomplishments, presented well.'),
             ('Can you also protect my privacy?', 'Yes. We can reduce the visibility of personal details like home addresses on data broker sites while strengthening the public story you want people to see.')],
        cta_h='Be seen <em>the way you deserve.</em>'),
}


def main():
    m = json.load(open('content/manifest.json'))
    seo = json.load(open('content/seo.json'))
    files = {'rep-who-we-help': hub(PAGES)}
    for slug, d in PAGES.items():
        files['rep-who-' + slug] = page(d)
    for name, html in files.items():
        assert '—' not in html, name
        open(f'{OUT}{name}.html', 'w').write(html)

    def upsert(entry):
        for i, x in enumerate(m['pages']):
            if x['slug'] == entry['slug'] and x.get('parent') == entry.get('parent'):
                m['pages'][i] = {**x, **entry}
                return
        m['pages'].append(entry)

    upsert({'slug': 'who-we-help', 'title': 'Who We Help: Reputation Management', 'parent': 'reputation-management',
            'file': 'pages/rep-who-we-help.html', 'template': 'page-landing', 'order': 10})
    seo['/reputation-management/who-we-help/'] = ['Who We Help: Reputation Management | Yroc Consulting',
                                                  'Reputation management for authors, speakers, coaches, athletes, executives, physicians, creators and other people with a name to protect.']
    for order, (slug, d) in enumerate(PAGES.items(), start=11):
        upsert({'slug': slug, 'title': 'Reputation Management for ' + d['name'], 'parent': 'reputation-management',
                'file': f'pages/rep-who-{slug}.html', 'template': 'page-landing', 'order': order})
        seo[f'/reputation-management/{slug}/'] = list(d['seo'])
    ours = ['/reputation-management/who-we-help/'] + [f'/reputation-management/{slug}/' for slug in PAGES]
    for path in ours:
        t, desc = seo[path]
        if True:
            assert len(t) <= 60, (path, len(t))
            assert 110 <= len(desc) <= 160, (path, len(desc))
            assert '—' not in t + desc, path
    # "Who we help" section on the main reputation page, kept between markers so re-runs replace it.
    rp = f'{OUT}reputation-management.html'
    page_html = open(rp).read()
    items = [(d['icon'], d['name'], d['card'], ('Learn more', f'/reputation-management/{slug}/')) for slug, d in PAGES.items()]
    section = ('<!-- yroc:who-we-help -->\n'
               + group(head('Who we help', 'For people with a name to protect.', 'Authors, speakers, athletes, executives, physicians and other public lives each face different platforms and risks.')
                       + rows(items) + [buttons(('See everyone we help', '/reputation-management/who-we-help/', True))], 'yr-section')
               + '\n<!-- /yroc:who-we-help -->\n\n')
    if '<!-- yroc:who-we-help -->' in page_html:
        a = page_html.index('<!-- yroc:who-we-help -->')
        b = page_html.index('<!-- /yroc:who-we-help -->') + len('<!-- /yroc:who-we-help -->\n\n')
        page_html = page_html[:a] + section + page_html[b:]
    else:
        anchor = '<!-- wp:group {"align":"full","className":"yr-section yr-light yr-faq"'
        assert anchor in page_html
        page_html = page_html.replace(anchor, section + anchor, 1)
    assert '\u2014' not in section
    open(rp, 'w').write(page_html)

    for path, data in (('content/manifest.json', m), ('content/seo.json', seo)):
        with open(path, 'w') as f:
            f.write(json.dumps(data, indent=2, ensure_ascii=False) + '\n')
    print('ok', len(files), 'pages')


if __name__ == '__main__':
    main()
