# Contributing

Thanks for considering a contribution. This guide covers the workflow for non-trivial changes.

## Local setup

You don't need PHP or WordPress installed on your host — everything runs in Docker:

```sh
docker compose -f docker-compose.dev.yml up -d
docker run --rm -v "$PWD":/app -w /app composer:2 install
```

See [`docs/DEV_ENVIRONMENT.md`](docs/DEV_ENVIRONMENT.md) for details.

## Branching and commits

- Branch off `main`.
- Use [Conventional Commits](https://www.conventionalcommits.org/) style: `feat:`, `fix:`, `chore:`, `docs:`, `ci:`, `test:`.
- Keep commits **small and self-contained** — one logical change per commit, each one passing PHPCS and tests on its own.
- Never use `--amend` to collapse pushed commits; create a fix-up commit instead.

## Required checks before opening a PR

```sh
docker run --rm -v "$PWD":/app -w /app composer:2 run lint        # PHPCS clean
docker run --rm -v "$PWD":/app -w /app composer:2 run test:unit   # PHPUnit unit suite green
```

CI runs the same checks across PHP 7.4 / 8.1 / 8.3.

## Adding a new admin setting

1. Add a key + default to `Options::defaults()`.
2. Add a sanitisation branch to `SettingsPage::sanitize()` using a `Sanitizer::*` helper.
3. Add a row to `templates/admin/settings-page.php`.
4. Read the value from your code via `Options::get( 'your_key' )`.

## Releasing

Maintainers only:

1. Update `CHANGELOG.md` (move items from `Unreleased` to a new dated section).
2. Bump the version in **three places**: the `Version:` header in `how-old-are-you.php`, `HOAY_VERSION` constant in the same file, and `Stable tag:` in `readme.txt`.
3. Commit as `chore: release X.Y.Z`.
4. Tag: `git tag -a vX.Y.Z -m "vX.Y.Z"` and push: `git push --tags`.

## Reporting a security issue

Do **not** file a public issue. Email **farzad@wedevelop.nl** — see [`docs/SECURITY.md`](docs/SECURITY.md).
