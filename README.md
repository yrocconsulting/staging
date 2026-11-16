# Yroc Consulting — Website (staging)

Custom WordPress block theme + content for **yrocconsulting.com**, deployed automatically to
**https://yrocstaging.yroc.host** (SiteGround) on every push to `main`.

## Layout
| Path | What |
|---|---|
| `theme/yroc/` | The WordPress theme (block theme, editable in Site Editor) |
| `theme/yroc/inc/site.php` | Business info, **mega menu** and nav — edit menus here |
| `theme/yroc/patterns/` | Header, footer, reusable sections |
| `theme/yroc/assets/` | CSS, JS (interactions), fonts, logo |
| `content/manifest.json` | List of pages the deploy creates/updates |
| `content/pages/*.html` | Page content (WordPress block markup) |
| `deploy/import.php` | WP-CLI importer used by the deploy |
| `.github/workflows/deploy.yml` | GitHub Actions deploy over SSH |

## Deploy secrets (Settings → Secrets and variables → Actions)
| Secret | Example |
|---|---|
| `SSH_PRIVATE_KEY` | contents of the private key authorized in SiteGround |
| `SSH_PASSPHRASE` | the key's passphrase (if it has one) |
| `SSH_HOST` | from SiteGround → Devs → SSH Keys Manager → SSH Credentials |
| `SSH_USER` | e.g. `u123-abcdef` |
| `SSH_PORT` | `18765` |
| `DEPLOY_PATH` | `www/yrocstaging.yroc.host/public_html` (relative to the SSH home directory) |

## Editing content
Pages can be edited in WordPress. The deploy **never overwrites a page that was edited in
wp-admin** — it logs `skipped`. To force the repo version, run the workflow manually
(Actions → Deploy to staging → Run workflow) with *Overwrite pages* checked.

Staging is automatically set to "discourage search engines".
