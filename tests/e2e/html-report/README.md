# DLI HTML Validation Report

Scans all site pages and validates their rendered HTML against the **Nu Html Checker (VNU)**.
Each page is loaded via Playwright (full browser render), then the HTML is passed to VNU via stdin.


## Prerequisites

- **Java 8+** installed and available on `PATH`
- **vnu-jar** npm package: `npm install --save-dev vnu-jar`
- **Playwright chromium**: `npx playwright install chromium`


## Commands

```bash
# Run a full scan
npm run html:scan -- https://laboratorio1.local

# Run with gate (exit code 1 if verdict is FAIL)
npm run html:scan:gate -- https://laboratorio1.local

# Compare the two most recent reports
npm run html:compare

# Compare two specific reports
npm run html:compare -- reports/html_report_20260310_1400.json reports/html_report_20260309_0900.json
```


## Options

| Option | Default | Description |
|---|---|---|
| `<baseUrl>` | _(required)_ | Base URL of the site to scan |
| `--sitemap <path>` | `/mappa-sito/` | Path to the sitemap page |
| `--timeout <ms>` | `30000` | Page load timeout |
| `--delay <ms>` | `500` | Wait between pages (0 = no delay) |
| `--out <path>` | auto timestamp | Output path without extension |
| `--gate` | `false` | Exit with code 1 if verdict is FAIL |


## Output

Each scan generates two files in `reports/`:

- `html_report_YYYYMMDD_HHMM.json` — structured data
- `html_report_YYYYMMDD_HHMM.html` — visual report

Each compare generates one file in `reports/`:

- `html_compare_<new>_vs_<old>.html`


## Verdict rules

| Verdict | Condition |
|---|---|
| **PASS** | No errors and no warnings |
| **WARN** | Warnings present, no errors |
| **FAIL** | One or more HTML errors |

VNU **errors** (`type: "error"`) map to FAIL.
VNU **warnings** (`type: "info", subType: "warning"`) map to WARN.
Informational messages are ignored.


## JSON structure

```json
{
  "scannedAt": "ISO timestamp",
  "baseUrl": "https://...",
  "git": { "branch": "dev", "commit": "abc1234" },
  "summary": {
    "pagesScanned": 42,
    "pagesWithErrors": 3,
    "pagesWithWarnings": 5,
    "totalErrors": 12,
    "totalWarnings": 8,
    "verdict": "FAIL"
  },
  "pages": [
    {
      "url": "https://...",
      "errorCount": 2,
      "warningCount": 1,
      "errors": [
        { "message": "...", "line": 42, "col": 10, "extract": "..." }
      ],
      "warnings": [
        { "message": "...", "line": 55, "col": 3, "extract": "..." }
      ],
      "vnuError": null,
      "pageError": null
    }
  ]
}
```


## Notes

- Pages are scanned **sequentially** (no concurrency) because VNU is CPU-intensive.
- The scanner uses Playwright to get the fully rendered HTML (including PHP output), not a static fetch.
- `pageError`: set when Playwright cannot load the page (timeout, network error).
- `vnuError`: set when the VNU process itself fails (Java not found, jar error).
- Historical reports are stored in `archived/`; `reports/` is git-ignored.
