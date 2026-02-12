# CHATGPT.md - ChatGPT/Codex Configuration

## Purpose

This file contains ChatGPT/Codex-specific instructions only.
Shared project workflow, issue handling, and documentation rules are defined in `AGENTS_README.md` and `AI_BEHAVIOR.md`.

## Session Start

1. Read `AGENTS/AGENTS_README.md` first.
2. Follow the required reading order defined there.
3. Apply the behavior and workflow rules from `AGENTS/AI_BEHAVIOR.md`.

## ChatGPT-Specific Settings

### Code Interpreter Preferences

- Read and analyze files when requested.
- Generate code files with proper structure.
- Follow WordPress naming conventions and directory structure.
- Use code interpreter for complex analysis when useful.

### Custom Instructions

Add this to your ChatGPT Custom Instructions:

```text
When working on the Design Laboratori WordPress project:
1. Always read AGENTS/CHATGPT.md and AGENTS/AGENTS_README.md first
2. Follow AGENTS/AI_BEHAVIOR.md and AGENTS/CODING_STANDARDS.md
3. Prioritize security, accessibility, and code quality
4. Update AGENTS/ISSUES_TODO.md and AGENTS/ISSUES_RESOLVED.md when needed
5. Be concise but precise in responses
```

### ChatGPT-Specific Tips

- Keep multi-turn context by referencing previous decisions.
- Break large tasks into clear sub-steps, but complete one objective at a time.
- Summarize milestones after meaningful progress.

## Canonical References

- Workflow and behavior: `AGENTS/AI_BEHAVIOR.md`
- Coding rules and security standards: `AGENTS/CODING_STANDARDS.md`
- Architecture and commands: `AGENTS/ARCHITECTURE.md`
- Open issues: `AGENTS/ISSUES_TODO.md`
- Resolved issues: `AGENTS/ISSUES_RESOLVED.md`

## For Other Projects

When adapting this file:
- Keep only ChatGPT-specific instructions here.
- Keep shared rules in common AGENTS files.
- Update project-specific paths and reminders.
