#!/usr/bin/env python3
"""Read the weekly ProRankTracker "AI Visibility" PDF and output homepage stats as JSON.

Usage:
  prt_report.py --pdf report.pdf                 # parse a local PDF
  prt_report.py --drive-folder <folder-id>       # download the newest report from a public Drive folder

Needs `pdftotext` (poppler-utils). Output: client-only totals, plus a "showcase" of strong per-prompt
results and per-client numbers (clients have agreed to be named; display names live in
theme/yroc/data/clients.json, and domains not listed there are never shown).
Yroc's own domain is excluded so the numbers reflect client results only.
"""
import argparse
import json
import re
import subprocess
import sys
import tempfile
import urllib.request

OWN_DOMAINS = {'yrocconsulting.com'}
CLIENTS_JSON = __import__('os').path.join(__import__('os').path.dirname(__import__('os').path.abspath(__file__)), '..', 'theme', 'yroc', 'data', 'clients.json')


def named_clients():
    """Domains that may be shown by name (white-label clients are never listed)."""
    try:
        with open(CLIENTS_JSON) as f:
            return {k for k in json.load(f) if not k.startswith('_')}
    except (OSError, ValueError):
        return set()
COLS = ['aio', 'ai_mode', 'chatgpt', 'copilot', 'gemini', 'perplexity']
HEAD = ['AIO', 'AIM', 'ChatGPT', 'Copilot', 'Gemini', 'Perplexity']


def newest_report(folder_id):
    html = urllib.request.urlopen(f'https://drive.google.com/embeddedfolderview?id={folder_id}', timeout=30).read().decode('utf-8', 'ignore')
    files = re.findall(r'id="entry-([A-Za-z0-9_-]+)".*?flip-entry-title">([^<]*)', html, re.S)
    dated = []
    for fid, name in files:
        m = re.search(r'(\d{2})(\d{2})(\d{4})\.pdf$', name)
        if m:
            dated.append((m.group(3) + m.group(1) + m.group(2), fid, name))
    if not dated:
        sys.exit('No dated PDF reports found in the Drive folder.')
    dated.sort()
    ymd, fid, name = dated[-1]
    tmp = tempfile.NamedTemporaryFile(suffix='.pdf', delete=False)
    tmp.write(urllib.request.urlopen(f'https://drive.usercontent.google.com/download?id={fid}&export=download&confirm=t', timeout=120).read())
    tmp.close()
    return tmp.name, f'{ymd[:4]}-{ymd[4:6]}-{ymd[6:]}'


def parse(pdf):
    text = subprocess.run(['pdftotext', '-layout', pdf, '-'], capture_output=True, text=True, check=True).stdout
    lines = text.splitlines()
    sites, cur, cols = [], None, None
    for line in lines:
        m = re.match(r'^(\S+\.[a-z]{2,})\s+done_all\s+(\d+)\s+format_quote\s+(\d+)\s+link\s+(\d+)\s+visibility\s+(\d+)%', line)
        if m:
            cur = {'domain': m.group(1), 'appearances': int(m.group(2)), 'mentions': int(m.group(3)), 'links': int(m.group(4)),
                   'visibility': int(m.group(5)), 'prompts': 0, 'platforms': dict.fromkeys(COLS, 0)}
            sites.append(cur)
            cols = None
            continue
        if cur is None:
            continue
        if re.search(r'\bAIO\b.*\bAIM\b.*\bChatGPT\b', line):
            cols = [line.index(h) + len(h) // 2 for h in HEAD]
            continue
        if re.search(r', [A-Z]{2}$', line.strip()) and re.search(r'United States|Canada|United Kingdom|Australia', line):
            cur['prompts'] += 1
        if cols and ('check_circle' in line or 'cancel' in line):
            for m in re.finditer(r'check_circle\s+(\d+)', line):
                center = m.start() + len(m.group(0)) // 2
                idx = min(range(len(cols)), key=lambda i: abs(cols[i] - center))
                cur['platforms'][COLS[idx]] += int(m.group(1))
    return sites


def parse_prompts(pdf):
    """Per-prompt rows: domain, prompt text, platforms that named the client, appearances, visibility %.

    Each prompt is a block of lines separated from the next by two or more blank lines; the prompt text
    sits left of the first platform column and may wrap above and below the row of results.
    """
    text = subprocess.run(['pdftotext', '-layout', pdf, '-'], capture_output=True, text=True, check=True).stdout
    rows, domain, cols, block, blanks = [], None, None, [], 0

    def flush():
        nonlocal block
        if domain and cols and block:
            words, row = [], None
            for line in block:
                left = line[:cols[0] - 6].strip()
                if left and 'United States' not in left and not left.startswith(('check_circle', 'cancel')):
                    words.append(left)
                if 'check_circle' in line or re.search(r'\bcancel\b', line):
                    row = line
            if row and words:
                plats = []
                for mm in re.finditer(r'check_circle\s+(\d+)', row):
                    center = mm.start() + len(mm.group(0)) // 2
                    plats.append(COLS[min(range(len(cols)), key=lambda i: abs(cols[i] - center))])
                vm = re.search(r'(\d+)\s+(\d+)%\s*\S*\s*$', row.rstrip())
                prompt = re.sub(r'\s+', ' ', ' '.join(words)).strip()
                prompt = re.sub(r'^\d+\.\s*', '', prompt)
                if not prompt.endswith('?'):
                    prompt += '?'
                rows.append({'domain': domain, 'prompt': prompt[0].upper() + prompt[1:], 'platforms': plats,
                             'appearances': int(vm.group(1)) if vm else 0, 'visibility': int(vm.group(2)) if vm else 0})
        block = []

    for line in text.replace('\f', '\n').splitlines():
        m = re.match(r'^(\S+\.[a-z]{2,})\s+done_all', line)
        if m:
            flush()
            domain, cols = m.group(1), None
            continue
        if domain is None:
            continue
        if re.search(r'\bAIO\b.*\bAIM\b.*\bChatGPT\b', line):
            cols = [line.index(h) + len(h) // 2 for h in HEAD]
            block = []
            continue
        if not line.strip():
            blanks += 1
            continue
        if blanks >= 2:
            flush()
        blanks = 0
        block.append(line)
    flush()
    return rows


def showcase(rows):
    """Best proof rows for the homepage: client domains only, highest visibility first, one or two per client."""
    named = named_clients()
    good = sorted((r for r in rows if r['domain'] not in OWN_DOMAINS and r['domain'] in named and r['visibility'] >= 75 and r['platforms']),
                  key=lambda r: (-r['visibility'], -len(r['platforms']), -r['appearances']))
    out, per = [], {}
    for r in good:
        if per.get(r['domain'], 0) >= 2:
            continue
        per[r['domain']] = per.get(r['domain'], 0) + 1
        out.append(r)
    return out[:12]


def summarize(sites, date):
    clients = [s for s in sites if s['domain'] not in OWN_DOMAINS]
    plat = dict.fromkeys(COLS, 0)
    for s in clients:
        for k, v in s['platforms'].items():
            plat[k] += v
    total = sum(s['appearances'] for s in clients)
    out = {
        'appearances': total,
        'mentions': sum(s['mentions'] for s in clients),
        'links': sum(s['links'] for s in clients),
        'prompts': sum(s['prompts'] for s in clients),
        'clients': len(clients),
        'visibility': round(sum(s['visibility'] for s in clients) / len(clients)) if clients else 0,
        'platforms': plat,
        'report_date': date,
    }
    # Sanity check: per-platform counts must add up to the report's own totals.
    if sum(plat.values()) != total:
        sys.exit(f'Platform breakdown ({sum(plat.values())}) does not match total appearances ({total}); refusing to publish.')
    return out


if __name__ == '__main__':
    ap = argparse.ArgumentParser()
    ap.add_argument('--pdf')
    ap.add_argument('--drive-folder')
    ap.add_argument('--date', default='')
    a = ap.parse_args()
    if a.drive_folder:
        pdf, date = newest_report(a.drive_folder)
    else:
        pdf, date = a.pdf, a.date
    out = summarize(parse(pdf), date)
    out['showcase'] = showcase(parse_prompts(pdf))
    out['sites'] = {x['domain']: {k: x[k] for k in ('appearances', 'mentions', 'links', 'visibility', 'prompts')} for x in parse(pdf) if x['domain'] not in OWN_DOMAINS}
    print(json.dumps(out))
