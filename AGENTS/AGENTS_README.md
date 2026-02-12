# AGENTS Folder - Documentation Guide

This folder contains all documentation and configuration files for AI assistants working on this project.

## 📋 File Overview

### Core Documentation Files

**PROJECT.md**
- **Purpose:** Complete project description, features, and scope
- **Contains:** Project overview, main features, documentation links
- **Update when:** Adding new features, changing project scope, updating documentation links
- **Reusable:** Yes - replace project-specific sections for other projects

**ARCHITECTURE.md**
- **Purpose:** Technical architecture and code organization
- **Contains:** Architecture notes, directory structure, main files description
- **Update when:** Adding new directories, restructuring code, adding major components
- **Reusable:** Yes - replace architecture details for other projects

**CODING_STANDARDS.md**
- **Purpose:** Code writing rules and style guidelines
- **Contains:** Code style rules, code standards, naming conventions, formatting rules
- **Update when:** Adopting new coding standards, adding framework-specific rules
- **Reusable:** Yes - generic programming standards with framework-specific sections

**AI_BEHAVIOR.md**
- **Purpose:** How AI assistants should work and interact
- **Contains:** Workflow guidelines, file update rules, communication style, issue management
- **Update when:** Changing AI workflows, adding new procedures
- **Reusable:** Yes - completely generic AI behavior guidelines

### Issue Tracking Files

**ISSUES_TODO.md**
- **Purpose:** Track all open issues, bugs, and improvements
- **Contains:** Categorized list of pending tasks with priorities
- **Organization:** Issues are organized by category (Security, Bug, Refactoring, Performance, Accessibility, Feature, Documentation), then sorted by priority within each category (Critical → High → Medium → Low)
- **Update when:** Finding bugs, planning features, identifying improvements, resolving issues
- **Reusable:** Template structure yes, content no (project-specific)

**ISSUES_RESOLVED.md**
- **Purpose:** Archive of completed issues
- **Contains:** Historical record of resolved issues with solutions
- **Update when:** Closing issues, documenting solutions
- **Reusable:** Template structure yes, content no (project-specific)

### AI-Specific Configuration Files

**AI_RULES_CLAUDE.md**
- **Purpose:** Configuration and instructions for Claude Code
- **Contains:** Required reading list, Claude-specific settings, workflow reminders
- **Update when:** Changing documentation structure, adding new files, modifying workflows
- **Reusable:** Partially - update file references for each project

**AI_RULES_CHATGPT.md**
- **Purpose:** Configuration and instructions for ChatGPT/Codex
- **Contains:** Required reading list, ChatGPT-specific settings, workflow reminders
- **Update when:** Changing documentation structure, adding new files, modifying workflows
- **Reusable:** Partially - update file references for each project

## 🔄 File Interdependencies

```
AI_RULES_CLAUDE.md / AI_RULES_CHATGPT.md (entry point)
    ↓
    ├─→ AGENTS_README.md (index and usage guide)
    ├─→ PROJECT.md (what we're building)
    ├─→ ARCHITECTURE.md (how it's structured)
    ├─→ CODING_STANDARDS.md (how to write code)
    ├─→ AI_BEHAVIOR.md (how to work)
    └─→ ISSUES_TODO.md (what needs doing)

ISSUES_RESOLVED.md is not read at startup. Read it on demand when resolving issues.
```

## 🎯 Quick Start for AI Assistants

1. **Claude Code:** Reads `CLAUDE.md` in the project root automatically, which redirects to `AGENTS/AI_RULES_CLAUDE.md`
2. **ChatGPT Codex:** Reads `AGENTS.md` in the project root automatically, which redirects to `AGENTS/AI_RULES_CHATGPT.md`
3. Follow the reading order specified in your config file
4. Always check `ISSUES_TODO.md` before starting work
5. Update relevant files when making code changes

## 🚀 Starting a Work Session

### Automatic Bootstrap

Both AI assistants read their entry point automatically at session start:

- **Claude Code** → `CLAUDE.md` (project root) → `AGENTS/AI_RULES_CLAUDE.md`
- **ChatGPT Codex** → `AGENTS.md` (project root) → `AGENTS/AI_RULES_CHATGPT.md`

No manual prompt is needed to start. The entry point files redirect to the full configuration in the `AGENTS/` folder.

### For ChatGPT Web Interface (non-Codex)

If using ChatGPT web without Codex, add this to your Custom Instructions (Settings → Personalization → Custom Instructions):

```
When working on projects with an AGENTS folder:
1. Always start by reading AGENTS/AI_RULES_CHATGPT.md
2. Read all referenced files in the order specified
3. Check AGENTS/ISSUES_TODO.md for current priorities
4. Follow all guidelines in AGENTS/AI_BEHAVIOR.md
5. Update documentation files when making code changes
```

### Quick Session Restart

If you're continuing work in the same session and the AI seems to have forgotten the context:

**For both Claude and ChatGPT:**
```
Refresh context: read AGENTS/AI_RULES_CLAUDE.md, AGENTS/AI_RULES_CHATGPT.md, AGENTS/AGENTS_README.md, and AGENTS/ISSUES_TODO.md
```

## ✏️ When to Update Each File

See `AI_BEHAVIOR.md` § "Documentation Update Rules" for the complete list of which files to update after each type of change.

## 🔁 Using These Files in Other Projects

To adapt this documentation system for a new project:

1. **Keep as-is:**
   - `AI_BEHAVIOR.md` — completely reusable, no changes needed
   - `AGENTS_README.md` — this file, reusable as-is

2. **Adjust framework-specific sections:**
   - `CODING_STANDARDS.md` — keep general guidelines; replace WordPress-specific sections with framework/language-specific standards; maintain emphasis on security, accessibility, and code quality; update code examples to match the target stack
   - `AI_RULES_CLAUDE.md` / `AI_RULES_CHATGPT.md` — keep only AI-specific instructions; update project-specific paths and tool notes

3. **Replace content:**
   - `PROJECT.md` — replace with: project overview, main features, documentation links, testing/demo instructions, license reference
   - `ARCHITECTURE.md` — replace with: architecture notes, key directories, main files, technology stack, setup info, CLI commands

4. **Clear and restart:**
   - `ISSUES_TODO.md` — clear old issues, keep category structure
   - `ISSUES_RESOLVED.md` — start fresh, keep archive format template

## 📝 Maintenance Guidelines

- Keep files concise but complete
- Remove outdated information promptly
- Use consistent formatting across all files
- Link between files when referencing related content
- Review and update at least monthly or after major changes



