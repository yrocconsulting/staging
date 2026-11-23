"""Draw the Yroc favicon (YROC with the O in the green map pin) and write every icon size.

Run from the repo root: python3 tools/make_favicon.py
Writes theme/yroc/assets/img/favicon/: favicon.ico (16, 32, 48), favicon-32.png, apple-touch-icon.png (180),
icon-192.png and icon-512.png. Needs Pillow.
"""
import os

from PIL import Image, ImageDraw, ImageFont

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
FONT = os.path.join(ROOT, 'tools', 'blog', 'fonts', 'inter-tight-latin-800-normal.ttf')
OUT = os.path.join(ROOT, 'theme', 'yroc', 'assets', 'img', 'favicon')
GREEN, GREEN_D, INK = (126, 217, 87, 255), (63, 143, 28, 255), (11, 18, 32, 255)


def draw(size=1024):
    im = Image.new('RGBA', (size, size), (0, 0, 0, 0))
    d = ImageDraw.Draw(im)
    fs = size // 2
    while True:
        f = ImageFont.truetype(FONT, fs)
        if d.textlength('YROC', font=f) <= size * 0.96:
            break
        fs -= 4
    w = d.textlength('YROC', font=f)
    x0 = (size - w) / 2
    top, bottom = f.getbbox('YROC')[1], f.getbbox('YROC')[3]
    cap = bottom - top
    y0 = size * 0.40 - cap / 2 - top  # letters sit a little above center, leaving room for the pin tip
    # Centre of the O.
    ox0 = x0 + d.textlength('YR', font=f)
    ob = f.getbbox('O')
    ocx = ox0 + (ob[0] + ob[2]) / 2
    ocy = y0 + (ob[1] + ob[3]) / 2
    r = cap * 0.86
    # Pin: circle plus a point below it.
    tip = ocy + r * 2.05
    d.polygon([(ocx - r * 0.72, ocy + r * 0.68), (ocx + r * 0.72, ocy + r * 0.68), (ocx, tip)], fill=GREEN)
    d.ellipse([ocx - r, ocy - r, ocx + r, ocy + r], fill=GREEN)
    d.ellipse([ocx - r * 0.74, ocy - r * 0.74, ocx + r * 0.74, ocy + r * 0.74], fill=(255, 255, 255, 255))
    d.text((x0, y0), 'YROC', font=f, fill=INK)
    bbox = im.getbbox()
    return im.crop(bbox)


def square(mark, size, pad=0.06, bg=None):
    canvas = Image.new('RGBA', (size, size), bg or (0, 0, 0, 0))
    inner = int(size * (1 - 2 * pad))
    m = mark.copy()
    m.thumbnail((inner, inner), Image.LANCZOS)
    canvas.paste(m, ((size - m.width) // 2, (size - m.height) // 2), m)
    return canvas


def main():
    os.makedirs(OUT, exist_ok=True)
    mark = draw()
    square(mark, 512).save(os.path.join(OUT, 'icon-512.png'), optimize=True)
    square(mark, 192).save(os.path.join(OUT, 'icon-192.png'), optimize=True)
    square(mark, 180, pad=0.12, bg=(255, 255, 255, 255)).convert('RGB').save(os.path.join(OUT, 'apple-touch-icon.png'), optimize=True)
    square(mark, 32, pad=0.0).save(os.path.join(OUT, 'favicon-32.png'), optimize=True)
    big = square(mark, 256, pad=0.0)
    big.save(os.path.join(OUT, 'favicon.ico'), sizes=[(16, 16), (32, 32), (48, 48)])
    print('ok', sorted(os.listdir(OUT)))


if __name__ == '__main__':
    main()
