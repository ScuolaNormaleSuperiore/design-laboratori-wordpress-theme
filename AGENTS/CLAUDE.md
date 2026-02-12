# CLAUDE.md - Claude Code Configuration

## Purpose

This file contains Claude-specific instructions only.
Shared project workflow, issue handling, and documentation rules are defined in `AGENTS_README.md` and `AI_BEHAVIOR.md`.

## Session Start

1. Read `AGENTS/AGENTS_README.md` first.
2. Follow the required reading order defined there.
3. Apply the behavior and workflow rules from `AGENTS/AI_BEHAVIOR.md`.

## Claude-Specific Settings

### Tools and Preferences

- Create files directly when requested.
- Follow WordPress naming conventions and directory structure.
- Use shell tooling for code checks where appropriate.
- Run `composer lint` before finalizing code changes when possible.
- Use available MCP servers when relevant and within boundaries.

## Canonical References

- Workflow and behavior: `AGENTS/AI_BEHAVIOR.md`
- Coding rules and security standards: `AGENTS/CODING_STANDARDS.md`
- Architecture and commands: `AGENTS/ARCHITECTURE.md`
- Open issues: `AGENTS/ISSUES_TODO.md`
- Resolved issues: `AGENTS/ISSUES_RESOLVED.md`

## For Other Projects

When adapting this file:
- Keep only Claude-specific instructions here.
- Keep shared rules in common AGENTS files.
- Update project-specific paths and tool notes.
