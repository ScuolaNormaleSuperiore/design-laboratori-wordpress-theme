# AGENTS.md

ChatGPT/Codex-specific entry point.

## Bootstrap
Read this file first. Then read `AGENTS/AI_BOOTSTRAP.md` and follow its
`Session bootstrap` sequence exactly.

## Codex Notes
- Execution environment policy:
  - Try commands in WSL first.
  - If required tools are missing (`php`, `node`), switch quickly to the approved Windows fallback (`powershell.exe`) and explicitly report that fallback was used.
  - If a tool is still unavailable, report it instead of installing it.
- Lint policy:
  - Use the canonical quality-check command list in `AGENTS/ARCHITECTURE.md`.
  - If `composer run lint:php` is blocked in WSL, use the Windows PHP fallback with `vendor/bin/phpcs` and always report the outcome.
- Response format preference:
  - Keep outputs brief, numbered, and findings-first.
  - For URL audits, report only high-impact issues unless explicitly asked for exhaustive findings.
