# AI_RULES_CLAUDE.md - Claude Code Configuration


## Purpose

This file contains **Claude-Code** specific instructions only.
After reading this file, read the files with detailed collaboration rules listed in the following section.


## Session Start

1. Read this file first (it is specific for Claude Code).
2. In the AGENTS folder, read the files common to all AI agents:
	``` AI_RULES_CLAUDE.md (entry point)
			↓
			├─→ AGENTS_README.md (index and usage guide)
			├─→ PROJECT.md (what we're building)
			├─→ ARCHITECTURE.md (how it's structured)
			├─→ CODING_STANDARDS.md (how to write code)
			├─→ AI_BEHAVIOR.md (how to work)
			└─→ ISSUES_TODO.md (what needs doing)
	```
3. Apply the behavior and workflow rules from `AGENTS/AI_BEHAVIOR.md`.


## Claude-Specific Settings

### Tools and Preferences

- Create files directly when requested.
- Follow WordPress naming conventions and directory structure.
- Use shell tooling for code checks where appropriate.
- Run `composer lint` before finalizing code changes when possible.
- Use available MCP servers when relevant and within boundaries.


## Canonical References

See `AGENTS/AGENTS_README.md` for the full file index and descriptions.


