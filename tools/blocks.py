"""Tiny helpers that emit valid WordPress block markup."""
import json, html as H

def _a(d):
    return (' ' + json.dumps(d, separators=(',', ':'))) if d else ''

def p(text, cls=None):
    a = {'className': cls} if cls else {}
    c = f' class="{cls}"' if cls else ''
    return f'<!-- wp:paragraph{_a(a)} -->\n<p{c}>{text}</p>\n<!-- /wp:paragraph -->'

def h(text, level=2, cls=None):
    a = {}
    if level != 2: a['level'] = level
    if cls: a['className'] = cls
    c = 'wp-block-heading' + (f' {cls}' if cls else '')
    return f'<!-- wp:heading{_a(a)} -->\n<h{level} class="{c}">{text}</h{level}>\n<!-- /wp:heading -->'

def ul(items, cls=None):
    a = {'className': cls} if cls else {}
    c = 'wp-block-list' + (f' {cls}' if cls else '')
    lis = '\n'.join(f'<!-- wp:list-item -->\n<li>{i}</li>\n<!-- /wp:list-item -->' for i in items)
    return f'<!-- wp:list{_a(a)} -->\n<ul class="{c}">{lis}</ul>\n<!-- /wp:list -->'

def buttons(*btns, cls=None):
    a = {'className': cls} if cls else {}
    c = 'wp-block-buttons' + (f' {cls}' if cls else '')
    inner = []
    for label, href, *rest in btns:
        outline = rest and rest[0]
        ba = {'className': 'is-style-outline'} if outline else {}
        bc = 'wp-block-button' + (' is-style-outline' if outline else '')
        inner.append(f'<!-- wp:button{_a(ba)} -->\n<div class="{bc}"><a class="wp-block-button__link wp-element-button" href="{href}">{label}</a></div>\n<!-- /wp:button -->')
    return f'<!-- wp:buttons{_a(a)} -->\n<div class="{c}">' + '\n\n'.join(inner) + '</div>\n<!-- /wp:buttons -->'

def group(inner, cls, size=1200, anchor=None):
    a = {'align': 'full', 'className': cls, 'layout': {'type': 'constrained', 'contentSize': f'{size}px'}}
    if anchor: a = {'anchor': anchor, **a}
    idattr = f' id="{anchor}"' if anchor else ''
    return f'<!-- wp:group{_a(a)} -->\n<div{idattr} class="wp-block-group alignfull {cls}">\n' + '\n\n'.join(inner) + '\n</div>\n<!-- /wp:group -->'

def columns(cols, cls=None, valign=None):
    a = {}
    if valign: a['verticalAlignment'] = valign
    if cls: a['className'] = cls
    c = 'wp-block-columns' + (f' are-vertically-aligned-{valign}' if valign else '') + (f' {cls}' if cls else '')
    return f'<!-- wp:columns{_a(a)} -->\n<div class="{c}">\n' + '\n\n'.join(cols) + '\n</div>\n<!-- /wp:columns -->'

def column(inner, cls=None, width=None, valign=None):
    a = {}
    if valign: a['verticalAlignment'] = valign
    if width: a['width'] = width
    if cls: a['className'] = cls
    c = 'wp-block-column' + (f' is-vertically-aligned-{valign}' if valign else '') + (f' {cls}' if cls else '')
    st = f' style="flex-basis:{width}"' if width else ''
    return f'<!-- wp:column{_a(a)} -->\n<div class="{c}"{st}>\n' + '\n'.join(inner) + '\n</div>\n<!-- /wp:column -->'

def raw(htmltext):
    return f'<!-- wp:html -->\n{htmltext}\n<!-- /wp:html -->'

def shortcode(sc):
    return f'<!-- wp:shortcode -->\n{sc}\n<!-- /wp:shortcode -->'

def details(q, a_html):
    return f'<!-- wp:details {{"className":"yr-faq__item"}} -->\n<details class="wp-block-details yr-faq__item"><summary>{q}</summary>' + p(a_html) + '</details>\n<!-- /wp:details -->'

def pattern(slug):
    return f'<!-- wp:pattern {{"slug":"{slug}"}} /-->'

# ---------- composite sections ----------
def hero(pill, title, lead, btns, right, extra=None):
    left = [p(pill, 'yr-pill'), h(title, 1, 'yr-hero__h'), p(lead, 'yr-lead')] + (extra or []) + [buttons(*btns)]
    return group([raw('<canvas class="yr-hero__net" data-yr-net aria-hidden="true"></canvas>'),
                  columns([column(left, width='54%', valign='center'), column(right, width='46%', valign='center')], 'yr-hero__cols', 'center')],
                 'yr-hero yr-phero')

def head(eyebrow, title, lead=None):
    out = [p(eyebrow, 'yr-eyebrow'), h(title, 2, 'yr-h-xl')]
    if lead: out.append(p(lead, 'yr-lead'))
    return out

def cards(items, cls='yr-cards', card_cls='yr-card', ico=True):
    """items: (icon, title, text, link_or_None)"""
    cols = []
    for icon, title, text, link in items:
        inner = []
        if ico: inner.append(p(icon, 'yr-card__ico'))
        inner += [h(title, 3), p(text)]
        if link: inner.append(p(f'<a href="{link[1]}">{link[0]} →</a>', 'yr-card__link'))
        cols.append(column(inner, card_cls))
    return columns(cols, cls)

def steps(items):
    return group([group([h(t, 3), p(d)], 'yr-step', 0).replace('"align":"full",', '').replace('alignfull ', '').replace(',"layout":{"type":"constrained","contentSize":"0px"}', ',"layout":{"type":"default"}') for t, d in items], 'yr-steps', 0).replace('"align":"full",', '').replace('alignfull ', '').replace(',"layout":{"type":"constrained","contentSize":"0px"}', ',"layout":{"type":"default"}')

def faq(items, title='Questions, answered', eyebrow='FAQ'):
    return group(head(eyebrow, title) + [details(q, a) for q, a in items], 'yr-section yr-light yr-faq', 860)

def compare(rows, old='Traditional SEO', new='AI Search Optimization'):
    cells = f'<div class="yr-compare__h yr-compare__h--old">{old}</div><div class="yr-compare__h yr-compare__h--new">{new}</div>'
    for (ot, od), (nt, nd) in rows:
        cells += f'<div class="yr-compare__c"><b>{ot}</b><p>{od}</p></div><div class="yr-compare__c yr-compare__c--new"><b>{nt}</b><p>{nd}</p></div>'
    return raw(f'<div class="yr-compare" data-reveal>{cells}</div>')
