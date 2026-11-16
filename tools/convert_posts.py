"""One-time converter: old yrocconsulting.com posts (WP REST JSON) -> clean HTML files + posts.json.

Usage: python3 tools/convert_posts.py <dir-with posts.json cats.json tags.json media.json> content/
"""
import base64, html, json, re, sys, urllib.parse
from bs4 import BeautifulSoup, Comment

src, out = sys.argv[1], sys.argv[2]
posts = json.load(open(f'{src}/posts.json'))
cats = {c['id']: c for c in json.load(open(f'{src}/cats.json'))}
tags = {t['id']: t for t in json.load(open(f'{src}/tags.json'))}
media = {m['id']: m['source_url'] for m in json.load(open(f'{src}/media.json'))}

Q = '[”“″"\']'  # WP texturizes shortcode quotes


def attr(sc, name):
    m = re.search(name + r'=' + Q + r'(.*?)' + Q + r'(?:\s|\])', sc + ']')
    return m.group(1) if m else ''


def shortcodes(c):
    # Raw HTML blocks are base64 + urlencoded.
    def raw(m):
        body = re.sub(r'<[^>]+>', '', m.group(1)).strip()
        try:
            return '\n' + urllib.parse.unquote(base64.b64decode(html.unescape(body)).decode('utf-8')) + '\n'
        except Exception:
            return ''
    c = re.sub(r'\[vc_raw_html[^\]]*\](.*?)\[/vc_raw_html\]', raw, c, flags=re.S)
    c = re.sub(r'\[vc_video[^\]]*\]', lambda m: '\n<p>' + attr(html.unescape(m.group(0)), 'link') + '</p>\n', c)
    c = re.sub(r'\[vc_single_image[^\]]*\]', lambda m: '<figure><img src="%s" alt=""></figure>' % media.get(int(attr(html.unescape(m.group(0)), 'image') or 0), ''), c)
    c = re.sub(r'\[vc_tta_section[^\]]*\]', lambda m: '<h3>%s</h3>' % html.escape(attr(html.unescape(m.group(0)), 'title')), c)
    c = re.sub(r'\[vc_headings[^\]]*\]', lambda m: '<h2>%s</h2>' % html.escape(attr(html.unescape(m.group(0)), 'title')), c)
    c = re.sub(r'\[embeddoc[^\]]*\]', lambda m: '<p><a href="%s">Download the spreadsheet</a></p>' % attr(html.unescape(m.group(0)), 'url'), c)
    c = re.sub(r'\[/?(vc_[a-z_0-9]+|gravityform)[^\]]*\]', '', c)
    return c


KEEP_ATTR = {'a': ['href', 'target', 'rel', 'name', 'id'], 'img': ['src', 'alt', 'width', 'height'], 'iframe': ['src', 'width', 'height', 'allow', 'allowfullscreen', 'title'],
             'td': ['colspan', 'rowspan'], 'th': ['colspan', 'rowspan', 'scope'], 'h2': ['id'], 'h3': ['id'], 'h4': ['id']}


def clean(c):
    s = BeautifulSoup(c, 'html.parser')
    for x in s.find_all(string=lambda t: isinstance(t, Comment)):
        x.extract()
    for t in s.find_all(['script', 'noscript']):
        t.decompose()
    raw_blocks = s.select('style')  # keep styled raw-html sections untouched
    protected = set()
    for st in raw_blocks:
        for el in [st.parent] + list(st.parent.find_all(True)) if st.parent else []:
            protected.add(id(el))
    for t in s.find_all(True):
        if id(t) in protected:
            continue
        if t.name in ('div', 'span', 'section', 'font'):
            t.unwrap()
            continue
        if t.name == 'img':
            srcv = t.get('data-src') or t.get('src') or ''
            srcv = re.sub(r'-\d+x\d+(\.\w+)$', r'\1', srcv)  # full-size original
            t['src'] = srcv
        keep = KEEP_ATTR.get(t.name, [])
        for a in list(t.attrs):
            if a not in keep:
                del t[a]
    for p in s.find_all('p'):
        if p.find(['p', 'h2', 'h3', 'ul', 'ol', 'figure', 'table', 'blockquote']):
            p.unwrap()
    for p in s.find_all(['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'em', 'li']):
        if not p.get_text(strip=True) and not p.find(['img', 'iframe', 'a', 'br']):
            p.decompose()
    out = str(s)
    # Internal links -> root-relative so they work on staging and production.
    out = re.sub(r'href="https?://(?:www\.)?yrocconsulting\.com/(?!wp-content/)', 'href="/', out)
    out = re.sub(r'\n{3,}', '\n\n', out).strip()
    return out


manifest = []
for p in posts:
    c = shortcodes(p['content']['rendered'])
    body = clean(c)
    fname = f"posts/{p['slug']}.html"
    open(f'{out}/{fname}', 'w').write(body + '\n')
    ex = BeautifulSoup(p['excerpt']['rendered'], 'html.parser').get_text(' ', strip=True)
    ex = re.sub(r'\[[^\]]*\]', '', html.unescape(ex)).replace('[…]', '').replace('[&hellip;]', '').strip()
    manifest.append({
        'slug': p['slug'],
        'title': html.unescape(p['title']['rendered']),
        'date': p['date'],
        'modified': p['modified'],
        'categories': [cats[i]['slug'] for i in p['categories'] if i in cats],
        'tags': [tags[i]['name'] for i in p['tags'] if i in tags],
        'excerpt': '',  # let WordPress generate excerpts
        'featured': media.get(p['featured_media'], ''),
        'file': fname,
    })
manifest.sort(key=lambda m: m['date'])
json.dump({'categories': [{'slug': c['slug'], 'name': html.unescape(c['name']), 'description': c.get('description', '')} for c in cats.values() if c['slug'] != 'uncategorized'],
           'posts': manifest}, open(f'{out}/posts.json', 'w'), indent=1, ensure_ascii=False)
print(len(manifest), 'posts converted')
