#!/usr/bin/env python3
"""
Strip non-essential single-line and short block comments from source files.
Heuristics:
- Removes // comments that are standalone lines (not inline) unless they contain license keywords or linter directives.
- Removes blade {{-- ... --}} comments that are standalone and short.
- Removes short  blocks (<=6 lines) unless they are PHPDoc (/**) or contain license/copyright.
- Preserves files under vendor, node_modules, .git, storage, public/build, and skips binary files.

Run from repository root: python scripts/strip_comments.py
"""
import os
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
SKIP_DIRS = {'.git', 'vendor', 'node_modules', 'storage', 'public/build', 'public/hot'}
EXTENSIONS = {'.php', '.js', '.jsx', '.ts', '.tsx', '.vue', '.css', '.scss', '.html', '.blade.php', '.py', '.sh'}
LICENSE_KEYWORDS = re.compile(r'copyright|@license|license|mit|gpl|apache|mozilla', re.I)


def should_skip_path(p: Path):
    parts = set(p.parts)
    if parts & SKIP_DIRS:
        return True
    return False


def is_text_file(path: Path):
    try:
        data = path.read_bytes()
        if b"\x00" in data[:1024]:
            return False
        return True
    except Exception:
        return False


def process_file(path: Path):
    try:
        text = path.read_text(encoding='utf-8')
    except Exception:
        return False

    original = text

    def remove_blade_comments(s):
        def repl(m):
            content = m.group(1)
            if LICENSE_KEYWORDS.search(content):
                return m.group(0)
            return ''
        return re.sub(r"^\s*\{\-\-\s*(.*?)\s*\-\-\}\s*$", repl, s, flags=re.M | re.S)

    text = remove_blade_comments(text)

    def remove_line_comments(s):
        def repl(m):
            comment = m.group(0)
            if LICENSE_KEYWORDS.search(comment):
                return m.group(0)

            if re.search(r'eslint|tslint|jshint|globals|@ts-ignore|@noinspection', comment, re.I):
                return m.group(0)
            return ''
        return re.sub(r"^\s*//(?!/).*?$", repl, s, flags=re.M)

    text = remove_line_comments(text)

    text = re.sub(r"^(?!#!)\s*#(?!\!).*$", lambda m: '' if not LICENSE_KEYWORDS.search(m.group(0)) else m.group(0), text, flags=re.M)

    # Remove short  blocks except PHPDoc /** and blocks with license keywords
    def remove_short_blocks(s):
        def repl(m):
            block = m.group(0)
            inner = m.group(1)

            if block.strip().startswith('/**'):
                return block
            if LICENSE_KEYWORDS.search(inner):
                return block

            lines = inner.count('\n') + 1
            if lines <= 6:
                return ''
            return block
        return re.sub(r"/\*(.*?)\*/", repl, s, flags=re.S)

    text = remove_short_blocks(text)

    if text != original:
        path.write_text(text, encoding='utf-8')
        return True
    return False


def main():
    changed = []
    for dirpath, dirnames, filenames in os.walk(ROOT):

        dirnames[:] = [d for d in dirnames if d not in SKIP_DIRS]
        for fname in filenames:
            p = Path(dirpath) / fname
            if should_skip_path(p):
                continue
            if not is_text_file(p):
                continue
            if any(str(p).endswith(ext) for ext in EXTENSIONS) or p.suffix in EXTENSIONS or p.name.endswith('.blade.php'):
                ok = process_file(p)
                if ok:
                    changed.append(str(p.relative_to(ROOT)))

    print('Files changed:', len(changed))
    for c in changed[:200]:
        print(' -', c)


if __name__ == '__main__':
    main()
