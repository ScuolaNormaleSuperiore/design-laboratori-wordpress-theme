# DLI Static Quality Report

Report statico qualità codice per confrontare versioni del tema oltre al report runtime dei link.

## Cosa misura

- Sintassi PHP (`php -l` su tutti i file PHP)
- PHPCS (`phpcs` con `phpcs.xml.dist`)
- PHPStan (se installato)
- Sicurezza dipendenze Composer (`composer audit`)
- Sicurezza dipendenze NPM (`npm audit --omit=dev`)
- Indicatore manutentabilità: file PHP grandi (>1000 LOC)

## Output

Lo scanner genera due file in `tests/e2e/quality-report/reports/`:

- `quality_report_YYYYMMDD_HHMM.json`
- `quality_report_YYYYMMDD_HHMM.html`

## Comandi npm

- `npm run quality:scan`
- `npm run quality:scan:gate` (exit code 1 se il verdict è `FAIL`)
- `npm run quality:compare` (confronta automaticamente gli ultimi 2 report)

## Cosa esegue `scan.js` (dettaglio)

Lo scanner lancia questi controlli:

1. `php -l` su tutti i file PHP nello scope di `phpstan.neon` (`paths` + `excludePaths`)
2. `phpcs --report=json --standard=phpcs.xml.dist --extensions=php <paths>`
3. `phpstan analyse --configuration=phpstan.neon --error-format=json --no-progress --memory-limit=512M`
4. `composer audit --format=json`
5. `npm audit --json --omit=dev`
6. scansione interna per file PHP molto grandi (>1000 righe)

Note importanti:

- I controlli 1, 2 e 6 usano lo stesso scope di `phpstan.neon`.
- `composer audit` e `npm audit` sono globali a livello dipendenze progetto.
- Se un comando non è disponibile nel PATH, il check viene segnato `SKIP`/`WARN` con nota nel report.

## Uso diretto

```bash
node tests/e2e/quality-report/scan.js
node tests/e2e/quality-report/scan.js --timeout 180000
node tests/e2e/quality-report/scan.js --out ./tests/e2e/quality-report/reports/my_quality --gate

node tests/e2e/quality-report/compare.js
node tests/e2e/quality-report/compare.js <new_report.json> <old_report.json>
node tests/e2e/quality-report/compare.js <new_report.json> <old_report.json> --out ./tests/e2e/quality-report/reports/my_compare
```

## Esecuzione manuale step-by-step

Esempio pratico (stesso output dello scanner, ma comando per comando):

```bash
# 1) Sintassi PHP
php -l functions.php

# 2) PHPCS sullo scope principale
vendor/bin/phpcs --report=summary --standard=phpcs.xml.dist --extensions=php inc page-templates template-parts archive.php page.php header.php footer.php functions.php

# 3) PHPStan
vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress --memory-limit=512M

# 4) Audit Composer
composer audit --format=summary

# 5) Audit NPM
npm audit --omit=dev
```

Esempio comando unico per generare il report:

```bash
node tests/e2e/quality-report/scan.js --out tests/e2e/quality-report/reports/quality_report_manual
```

Su Windows, se `node` non è nel PATH della shell corrente:

```powershell
& 'C:\Program Files\nodejs\node.exe' tests/e2e/quality-report/scan.js --out tests/e2e/quality-report/reports/quality_report_manual
```

## Regole verdict

- `FAIL`: almeno un check `critical` in stato `FAIL`
- `WARN`: nessun `critical FAIL`, ma ci sono `FAIL` non critical o `WARN`
- `PASS`: solo `PASS`/`SKIP`

## Stati per singolo check

| Check | PASS | WARN | FAIL | SKIP |
| --- | --- | --- | --- | --- |
| `php_syntax` | Nessun errore di sintassi (`syntaxErrors = 0`) | - | Almeno un errore di sintassi (`syntaxErrors > 0`) | `php` non eseguibile |
| `phpcs` | `errors = 0` e `warnings = 0` | Solo warning (`warnings > 0`) oppure output non parsabile con exit code non bloccante | Almeno un errore (`errors > 0`) | `phpcs` non eseguibile o nessun target valido |
| `phpstan` | `errors = 0` | Output non parsabile con exit code non bloccante | Almeno un errore (`errors > 0`) | `phpstan` non eseguibile |
| `composer_audit` | Nessuna advisory | Advisory solo non-critiche oppure output non parsabile con exit code non bloccante | Advisory `high` o `critical` | `composer` non eseguibile |
| `npm_audit` | Nessuna vulnerabilità | Vulnerabilità solo non-critiche oppure output non parsabile con exit code non bloccante | Vulnerabilità `high` o `critical` | `npm` non eseguibile |
| `php_large_files` | Nessun file PHP oltre soglia (>1000 LOC) | Almeno un file oltre soglia | - | - |

## Note

- Se un tool non è disponibile nel PATH (es. `php`, `composer`, `phpstan`) il check va in `SKIP`.
- Il comparator confronta stati check (`FIXED`/`REGRESSION`) e delta metriche numeriche.
- `php -l`, `phpcs` e il controllo file PHP grandi usano lo stesso scope definito in `phpstan.neon` (`paths` e `excludePaths`).
- `composer audit` e `npm audit` restano globali a livello progetto (non supportano filtro path equivalente).

## Setup minimo PHPStan

Per tenere il setup leggero:

```bash
composer require --dev phpstan/phpstan:^1.12
```

Questo repository usa `phpstan.neon` con livello basso e scope ridotto, adatto a una prima adozione senza carico eccessivo.

Per ridurre i falsi positivi WordPress senza introdurre dipendenze pesanti, usa anche stubs locali minimali in:

- `tests/phpstan/wordpress-lite-stubs.php`
