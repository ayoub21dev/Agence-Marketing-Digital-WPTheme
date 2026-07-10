#!/usr/bin/env node
/**
 * Release tool — bumps the version, writes CHANGELOG.md, commits and tags.
 *
 *   npm run release -- --dry-run     preview, write nothing  (do this first)
 *   npm run release                  cut the release
 *   npm run release -- --bump minor  force the bump level
 *   npm run release -- --check       audit only: which commits lack a changes/ doc
 *
 * It NEVER pushes. Pushing to `main` is a production FTP deploy; that call is
 * the owner's. The script prints the command and stops.
 *
 * How the version is decided
 *   Commits since the last tag are read as Conventional Commits:
 *     `!` or a `BREAKING CHANGE:` body  -> major
 *     `feat:`                           -> minor
 *     anything else conventional        -> patch
 *   A commit it cannot parse is NOT silently treated as a patch — the script
 *   stops and lists them, because a missed `feat:` means a wrong version. Pass
 *   --allow-unconventional to count them as patches, or --bump to decide yourself.
 *
 * How the changelog is written
 *   One entry per `changes/*.md` file ADDED since the last tag: its `# H1` is the
 *   entry text. The Keep a Changelog section is taken from an optional marker in
 *   the doc, `<!-- changelog: Fixed -->`, else from the type of the commit that
 *   added the doc (feat->Added, fix->Fixed, perf->Performance, docs->Documentation,
 *   everything else->Changed).
 *
 * Zero dependencies: Node >= 18, git on PATH.
 */

import { execFileSync } from 'node:child_process';
import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, join, resolve } from 'node:path';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const STYLE = join(ROOT, 'style.css');
const PKG = join(ROOT, 'package.json');
const CHANGELOG = join(ROOT, 'CHANGELOG.md');

// ---------------------------------------------------------------- utilities

const git = (...args) =>
    execFileSync('git', args, { cwd: ROOT, encoding: 'utf8', maxBuffer: 32 * 1024 * 1024 }).trim();

const gitOrNull = (...args) => {
    try {
        return git(...args);
    } catch {
        return null;
    }
};

const die = (msg) => {
    console.error(`\n  \x1b[31merror\x1b[0m  ${msg}\n`);
    process.exit(1);
};

const info = (msg) => console.log(`  ${msg}`);
const ok = (msg) => console.log(`  \x1b[32m✔\x1b[0m ${msg}`);
const warn = (msg) => console.log(`  \x1b[33m!\x1b[0m ${msg}`);

// Record separators — commit subjects and bodies contain newlines, so we cannot
// split on them.
const REC = '\x1e';
const FIELD = '\x1f';

// ---------------------------------------------------------------- arguments

function parseArgs(argv) {
    const opts = { dryRun: false, check: false, bump: null, allowUnconventional: false };
    for (let i = 0; i < argv.length; i++) {
        const a = argv[i];
        if (a === '--dry-run' || a === '-n') opts.dryRun = true;
        else if (a === '--check') opts.check = true;
        else if (a === '--allow-unconventional') opts.allowUnconventional = true;
        else if (a === '--bump') opts.bump = argv[++i];
        else if (a.startsWith('--bump=')) opts.bump = a.slice(7);
        else die(`unknown flag: ${a}`);
    }
    if (opts.bump && !['major', 'minor', 'patch'].includes(opts.bump)) {
        die(`--bump must be major, minor or patch (got "${opts.bump}")`);
    }
    return opts;
}

// ---------------------------------------------------------------- versioning

/** The theme version lives in style.css. package.json only mirrors it. */
function readThemeVersion() {
    const css = readFileSync(STYLE, 'utf8');
    const m = css.match(/^Version:\s*(\d+\.\d+\.\d+)\s*$/m);
    if (!m) die('no `Version: x.y.z` header in style.css');
    return m[1];
}

function bumpVersion(version, level) {
    const [maj, min, pat] = version.split('.').map(Number);
    if (level === 'major') return `${maj + 1}.0.0`;
    if (level === 'minor') return `${maj}.${min + 1}.0`;
    return `${maj}.${min}.${pat + 1}`;
}

// ---------------------------------------------------------------- commits

const CONVENTIONAL = /^(?<type>[a-z]+)(?:\((?<scope>[^)]+)\))?(?<breaking>!)?:\s+(?<subject>.+)$/;

function commitsSince(range) {
    const raw = gitOrNull(
        'log', ...(range ? [range] : []), '--no-merges',
        `--pretty=format:%H${FIELD}%s${FIELD}%b${REC}`
    );
    if (!raw) return [];
    return raw
        .split(REC)
        .map((s) => s.trim())
        .filter(Boolean)
        .map((rec) => {
            const [sha, subject, body = ''] = rec.split(FIELD);
            const m = subject.match(CONVENTIONAL);
            const breaking = Boolean(m?.groups.breaking) || /^BREAKING[ -]CHANGE:/m.test(body);
            return {
                sha,
                subject,
                body,
                type: m?.groups.type ?? null,
                scope: m?.groups.scope ?? null,
                description: m?.groups.subject ?? subject,
                breaking,
                conventional: Boolean(m),
            };
        });
}

/** major > minor > patch, from the commit types. */
function decideBump(commits, opts) {
    const unparsed = commits.filter((c) => !c.conventional);
    if (unparsed.length && !opts.allowUnconventional && !opts.bump) {
        console.error(`\n  \x1b[31merror\x1b[0m  ${unparsed.length} commit(s) are not Conventional Commits:\n`);
        for (const c of unparsed) console.error(`      ${c.sha.slice(0, 7)}  ${c.subject}`);
        console.error(`
  Refusing to guess the version: a missed \x1b[1mfeat:\x1b[0m silently ships as a patch.

  Choose one:
      npm run release -- --bump minor           decide yourself
      npm run release -- --allow-unconventional count them as patches
      git rebase -i --reword                    fix the messages
`);
        process.exit(1);
    }
    if (opts.bump) return { level: opts.bump, reason: `--bump ${opts.bump}` };

    const breaking = commits.find((c) => c.breaking);
    if (breaking) return { level: 'major', reason: `breaking change in ${breaking.sha.slice(0, 7)}` };

    const feat = commits.find((c) => c.type === 'feat');
    if (feat) return { level: 'minor', reason: `feat: in ${feat.sha.slice(0, 7)}` };

    return { level: 'patch', reason: 'no feat:, no breaking change' };
}

// ---------------------------------------------------------------- changelog

const SECTION_BY_TYPE = {
    feat: 'Added',
    fix: 'Fixed',
    perf: 'Performance',
    docs: 'Documentation',
    revert: 'Removed',
};
const SECTION_ORDER = ['Added', 'Changed', 'Fixed', 'Performance', 'Removed', 'Documentation', 'Security'];
const VALID_SECTIONS = new Set(SECTION_ORDER);

/** `changes/*.md` files added in the range, newest first. */
function changeDocsAdded(range) {
    const out = gitOrNull(
        'log', ...(range ? [range] : []), '--diff-filter=A', '--name-only',
        '--pretty=format:', '--', 'changes/'
    );
    if (!out) return [];
    return [...new Set(out.split('\n').map((s) => s.trim()).filter((f) => f.endsWith('.md')))];
}

/** First `# heading` of the doc, minus a trailing ` (2026-07-10)`. */
function docTitle(file) {
    const path = join(ROOT, file);
    if (!existsSync(path)) return null; // added then deleted in the same range
    const text = readFileSync(path, 'utf8');
    const m = text.match(/^#\s+(.+?)\s*$/m);
    if (!m) return null;
    return m[1].replace(/\s*\(\d{4}-\d{2}-\d{2}\)\s*$/, '').trim();
}

/**
 * Optional `<!-- changelog: Fixed -->` override, which must sit in the doc's
 * first few lines.
 *
 * Scoped to the header on purpose: a doc that *documents this tool* quotes the
 * marker syntax in its prose (including a deliberately invalid example). A
 * whole-file search would pick up whichever example appeared first and either
 * mis-file the entry or abort the release.
 */
const MARKER_SCAN_LINES = 10;

function docSectionOverride(file) {
    const path = join(ROOT, file);
    if (!existsSync(path)) return null;
    const head = readFileSync(path, 'utf8').split(/\r?\n/, MARKER_SCAN_LINES).join('\n');
    const m = head.match(/<!--\s*changelog:\s*([A-Za-z]+)\s*-->/);
    if (!m) return null;
    const section = m[1][0].toUpperCase() + m[1].slice(1).toLowerCase();
    if (!VALID_SECTIONS.has(section)) {
        die(`${file}: "<!-- changelog: ${m[1]} -->" is not one of ${SECTION_ORDER.join(', ')}`);
    }
    return section;
}

/** The commit that introduced the doc, so we can read its conventional type. */
function addingCommit(file, range) {
    const raw = gitOrNull(
        'log', ...(range ? [range] : []), '--diff-filter=A', '-1',
        `--pretty=format:%H${FIELD}%s`, '--', file
    );
    if (!raw) return null;
    const [sha, subject] = raw.split(FIELD);
    const m = subject.match(CONVENTIONAL);
    return { sha, subject, type: m?.groups.type ?? null };
}

function buildEntries(range) {
    const sections = new Map();
    for (const file of changeDocsAdded(range)) {
        const title = docTitle(file);
        if (!title) continue;
        const commit = addingCommit(file, range);
        const section =
            docSectionOverride(file) ?? SECTION_BY_TYPE[commit?.type] ?? 'Changed';
        if (!sections.has(section)) sections.set(section, []);
        sections.get(section).push({ title, file });
    }
    return sections;
}

function renderSection(version, date, sections) {
    const lines = [`## [${version}] - ${date}`, ''];
    for (const name of SECTION_ORDER) {
        const items = sections.get(name);
        if (!items?.length) continue;
        lines.push(`### ${name}`, '');
        for (const { title, file } of items) lines.push(`- ${title} — [\`${file}\`](${file})`);
        lines.push('');
    }
    return lines.join('\n');
}

function repoUrl() {
    const remote = gitOrNull('remote', 'get-url', 'origin');
    if (!remote) return null;
    return remote
        .replace(/^git@([^:]+):/, 'https://$1/')
        .replace(/\.git$/, '');
}

/** Insert the new release directly under `## [Unreleased]`, keep link refs at the bottom. */
function updateChangelog(text, version, date, sections, prevTag) {
    const section = renderSection(version, date, sections);
    // Anchor on the heading as a whole line. A plain indexOf() also matches the
    // words "## [Unreleased]" quoted inside the file's own intro prose, and
    // splices the release into the middle of that sentence.
    const m = text.match(/^## \[Unreleased\][ \t]*$/m);
    if (!m) die('CHANGELOG.md has no `## [Unreleased]` heading on its own line');
    const after = m.index + m[0].length;
    let body = `${text.slice(0, after)}\n\n${section}${text.slice(after).replace(/^\n+/, '\n')}`;

    const url = repoUrl();
    if (url) {
        const tag = `v${version}`;
        const compare = prevTag
            ? `${url}/compare/${prevTag}...${tag}`
            : `${url}/releases/tag/${tag}`;
        const refLine = `[${version}]: ${compare}`;
        if (!body.includes(refLine)) body = `${body.replace(/\s*$/, '')}\n${refLine}\n`;
        body = body.replace(
            /^\[Unreleased\]:.*$/m,
            `[Unreleased]: ${url}/compare/${tag}...HEAD`
        );
    }
    return body;
}

// ---------------------------------------------------------------- --check

/** Commits that touched shipped code but added no changes/ doc. */
function auditMissingDocs(range) {
    const commits = commitsSince(range);
    const missing = [];
    for (const c of commits) {
        const files = (gitOrNull('show', '--name-only', '--pretty=format:', c.sha) || '')
            .split('\n').map((s) => s.trim()).filter(Boolean);
        const touchedCode = files.some((f) => /\.(php|js|css)$/.test(f) && !f.startsWith('changes/'));
        const addedDoc = files.some((f) => f.startsWith('changes/') && f.endsWith('.md'));
        if (touchedCode && !addedDoc) missing.push(c);
    }
    return { commits, missing };
}

// ---------------------------------------------------------------- main

function main() {
    const opts = parseArgs(process.argv.slice(2));

    if (!gitOrNull('rev-parse', '--git-dir')) die('not a git repository');

    const prevTag = gitOrNull('describe', '--tags', '--abbrev=0');
    const range = prevTag ? `${prevTag}..HEAD` : null;

    console.log('');
    info(`repo    ${ROOT}`);
    info(`branch  ${git('rev-parse', '--abbrev-ref', 'HEAD')}`);
    info(`since   ${prevTag ?? '(no tag yet — reading all history)'}`);
    console.log('');

    if (opts.check) {
        const { commits, missing } = auditMissingDocs(range);
        info(`${commits.length} commit(s) since ${prevTag ?? 'the first commit'}`);
        if (!missing.length) {
            ok('every code commit ships a changes/ doc');
            return;
        }
        warn(`${missing.length} commit(s) changed code without adding a changes/ doc:`);
        for (const c of missing) console.log(`      ${c.sha.slice(0, 7)}  ${c.subject}`);
        console.log('\n  These will not appear in the changelog.\n');
        return;
    }

    const commits = commitsSince(range);
    if (!commits.length) die(`no commits since ${prevTag}. Nothing to release.`);

    const dirty = git('status', '--porcelain');
    if (dirty && !opts.dryRun) {
        die('working tree is dirty — commit or stash first:\n\n' + dirty.split('\n').map((l) => '      ' + l).join('\n'));
    }

    const current = readThemeVersion();
    const { level, reason } = decideBump(commits, opts);
    const next = bumpVersion(current, level);
    const date = new Date().toISOString().slice(0, 10);
    const tag = `v${next}`;

    if (gitOrNull('rev-parse', '-q', '--verify', `refs/tags/${tag}`)) {
        die(`tag ${tag} already exists`);
    }

    const sections = buildEntries(range);
    const entryCount = [...sections.values()].reduce((n, a) => n + a.length, 0);

    info(`${commits.length} commit(s), ${entryCount} changes/ doc(s)`);
    info(`bump    ${level}  (${reason})`);
    info(`version ${current} -> \x1b[1m${next}\x1b[0m`);
    console.log('');

    if (!entryCount) {
        warn('no changes/*.md added since the last tag — the release section would be empty.');
        warn('write one, or run with --check to see which commits lack a doc.');
        if (!opts.dryRun) process.exit(1);
    }

    console.log(renderSection(next, date, sections).split('\n').map((l) => '  │ ' + l).join('\n'));

    if (opts.dryRun) {
        console.log('');
        warn('--dry-run: nothing written, nothing committed, nothing tagged.');
        console.log('');
        return;
    }

    if (!existsSync(CHANGELOG)) die('CHANGELOG.md is missing');
    writeFileSync(CHANGELOG, updateChangelog(readFileSync(CHANGELOG, 'utf8'), next, date, sections, prevTag));
    ok('CHANGELOG.md');

    writeFileSync(STYLE, readFileSync(STYLE, 'utf8').replace(/^Version:\s*\d+\.\d+\.\d+\s*$/m, `Version: ${next}`));
    ok(`style.css        Version: ${next}`);

    const pkg = JSON.parse(readFileSync(PKG, 'utf8'));
    pkg.version = next;
    writeFileSync(PKG, JSON.stringify(pkg, null, 2) + '\n');
    ok(`package.json     "version": "${next}"`);

    git('add', 'CHANGELOG.md', 'style.css', 'package.json');
    git('commit', '-m', `chore(release): ${tag}`);
    ok(`commit  chore(release): ${tag}`);
    git('tag', '-a', tag, '-m', tag);
    ok(`tag     ${tag}`);

    console.log(`
  \x1b[1mNot pushed.\x1b[0m Pushing \`main\` deploys to production. When you are ready:

      git push --follow-tags origin ${git('rev-parse', '--abbrev-ref', 'HEAD')}
`);
}

main();
