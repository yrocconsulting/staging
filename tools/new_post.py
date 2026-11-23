#!/usr/bin/env python3
"""Add a blog post: writes content/posts/<slug>.html, a branded featured image, and the posts.json entry.

Usage:
  tools/new_post.py --title "..." --excerpt "..." --body draft.html \
      [--slug my-post] [--category ai-news] [--tags "AI, ChatGPT"] [--eyebrow "AI News & Tips"]

The body is plain HTML (h2/h3/p/ul/ol/blockquote/a/strong), same as the other posts, and must open with
the TL;DR block (<div class="yr-tldr">, styled in main.css).
The post date is now (US Central). Checks the house rules (no em dashes, no staff names but Cory Long)
and refuses to publish if they fail. Needs Pillow (pip install pillow).
"""
import argparse
import datetime
import json
import os
import re
import sys
import textwrap
from zoneinfo import ZoneInfo

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
CONTENT = os.path.join(ROOT, 'content')
FONTS = os.path.join(ROOT, 'tools', 'blog', 'fonts')
IMG_DIR = os.path.join(CONTENT, 'blog-images')
RAW = 'https://raw.githubusercontent.com/yrocconsulting/staging/main/content/blog-images/'

INK, GREEN, STEEL = (11, 18, 32), (126, 217, 87), (84, 117, 144)


def slugify(s):
    s = re.sub(r'[^a-z0-9]+', '-', s.lower()).strip('-')
    return re.sub(r'-+', '-', s)[:70].strip('-')


def check_rules(label, text):
    problems = []
    if '\u2014' in text:
        problems.append(f'{label}: contains an em dash (use a comma, colon or period)')
    if re.search(r'\b(Bradley|Brad)\b', text):
        problems.append(f'{label}: names an employee (only Cory Long may be named)')
    return problems


def featured_image(title, eyebrow, path):
    from PIL import Image, ImageDraw, ImageFilter, ImageFont
    W, H = 1200, 630
    im = Image.new('RGB', (W, H), INK)
    d = ImageDraw.Draw(im)
    for x in range(0, W, 60):  # faint grid, like the site hero
        d.line([(x, 0), (x, H)], fill=(20, 28, 44))
    for y in range(0, H, 60):
        d.line([(0, y), (W, y)], fill=(20, 28, 44))
    glow = Image.new('L', (W, H), 0)
    ImageDraw.Draw(glow).ellipse([W - 460, -300, W + 160, 300], fill=90)
    im.paste(Image.new('RGB', (W, H), GREEN), (0, 0), glow.filter(ImageFilter.GaussianBlur(110)))
    d = ImageDraw.Draw(im)
    d.rectangle([80, 96, 88, 140], fill=GREEN)
    mono = ImageFont.truetype(os.path.join(FONTS, 'jetbrains-mono-latin-600-normal.ttf'), 26)
    d.text((108, 103), eyebrow.upper(), font=mono, fill=GREEN)
    size = 72
    while size > 40:
        head = ImageFont.truetype(os.path.join(FONTS, 'inter-tight-latin-800-normal.ttf'), size)
        width = max(10, int(1040 / (size * 0.52)))
        lines = textwrap.wrap(title, width=width)
        if len(lines) <= 4 and all(d.textlength(l, font=head) <= 1040 for l in lines):
            break
        size -= 4
    y = 180
    for line in lines:
        d.text((80, y), line, font=head, fill=(255, 255, 255))
        y += int(size * 1.12)
    logo = Image.open(os.path.join(ROOT, 'theme', 'yroc', 'assets', 'img', 'logo-white.png')).convert('RGBA')
    logo = logo.resize((int(logo.width * 64 / logo.height), 64))
    im.paste(logo, (80, H - 64 - 56), logo)
    body = ImageFont.truetype(os.path.join(FONTS, 'inter-latin-500-normal.ttf'), 24)
    url = 'yrocconsulting.com'
    d.text((W - 80 - d.textlength(url, font=body), H - 56 - 44), url, font=body, fill=(170, 186, 204))
    im.save(path, optimize=True)


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--title', required=True)
    ap.add_argument('--excerpt', required=True, help='110 to 160 characters; used as the meta description')
    ap.add_argument('--body', required=True, help='path to the post body HTML')
    ap.add_argument('--slug')
    ap.add_argument('--category', default='ai-news')
    ap.add_argument('--tags', default='AI')
    ap.add_argument('--eyebrow', default='AI News & Tips')
    a = ap.parse_args()

    body = open(a.body, encoding='utf-8').read().strip()
    slug = a.slug or slugify(a.title)
    problems = check_rules('title', a.title) + check_rules('excerpt', a.excerpt) + check_rules('body', body)
    if not 110 <= len(a.excerpt) <= 160:
        problems.append(f'excerpt is {len(a.excerpt)} characters (needs 110 to 160)')
    if not body.startswith('<div class="yr-tldr">'):
        problems.append('body must start with the TL;DR block: <div class="yr-tldr"><h2>TL;DR</h2><p>...</p><ul>...</ul></div>')
    if len(a.title) > 70:
        problems.append(f'title is {len(a.title)} characters (keep it under 70)')
    if re.search(r'<h1\b', body):
        problems.append('body must not contain <h1> (the theme prints the title)')

    path = os.path.join(CONTENT, 'posts.json')
    data = json.load(open(path, encoding='utf-8'))
    if any(p['slug'] == slug for p in data['posts']):
        problems.append(f'a post with slug "{slug}" already exists')
    if a.category not in {c['slug'] for c in data['categories']}:
        problems.append(f'unknown category "{a.category}"')
    if problems:
        sys.exit('Not published:\n  - ' + '\n  - '.join(problems))

    os.makedirs(IMG_DIR, exist_ok=True)
    featured_image(a.title, a.eyebrow, os.path.join(IMG_DIR, slug + '.png'))
    with open(os.path.join(CONTENT, 'posts', slug + '.html'), 'w', encoding='utf-8') as f:
        f.write(body + '\n')
    now = datetime.datetime.now(ZoneInfo('America/Chicago')).strftime('%Y-%m-%dT%H:%M:%S')
    data['posts'].append({
        'slug': slug,
        'title': a.title,
        'date': now,
        'modified': now,
        'categories': [a.category],
        'tags': [t.strip() for t in a.tags.split(',') if t.strip()],
        'excerpt': a.excerpt,
        'featured': RAW + slug + '.png',
        'file': f'posts/{slug}.html',
    })
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=1, ensure_ascii=False)
    print(f'Added post "{a.title}" -> /{a.category}/{slug}/')


if __name__ == '__main__':
    main()
