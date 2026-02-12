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

**CLAUDE.md**
- **Purpose:** Configuration and instructions for Claude Code
- **Contains:** Required reading list, Claude-specific settings, workflow reminders
- **Update when:** Changing documentation structure, adding new files, modifying workflows
- **Reusable:** Partially - update file references for each project

**CHATGPT.md**
- **Purpose:** Configuration and instructions for ChatGPT/Codex
- **Contains:** Required reading list, ChatGPT-specific settings, workflow reminders
- **Update when:** Changing documentation structure, adding new files, modifying workflows
- **Reusable:** Partially - update file references for each project

## 🔄 File Interdependencies

```
CLAUDE.md / CHATGPT.md (entry point)
    ↓
    ├─→ AGENTS_README.md (index and usage guide)
    ├─→ PROJECT.md (what we're building)
    ├─→ ARCHITECTURE.md (how it's structured)
    ├─→ CODING_STANDARDS.md (how to write code)
    ├─→ AI_BEHAVIOR.md (how to work)
    ├─→ ISSUES_TODO.md (what needs doing)
    └─→ ISSUES_RESOLVED.md (issues already solved)
```

## 🎯 Quick Start for AI Assistants

1. **Claude Code users:** Start with `CLAUDE.md`
2. **ChatGPT users:** Start with `CHATGPT.md`
3. Follow the reading order specified in your config file
4. Always check `ISSUES_TODO.md` and `ISSUES_RESOLVED.md` before starting work
5. Update relevant files when making code changes

## 🚀 Starting a Work Session

### For Claude Code Users

At the beginning of each work session, start with this prompt:

```
Read AGENTS/CLAUDE.md and all the files it references in the specified order. 
Ensure AGENTS/AGENTS_README.md, AGENTS/ISSUES_TODO.md, and AGENTS/ISSUES_RESOLVED.md are included.
Then check AGENTS/ISSUES_TODO.md for current priority tasks.
```

Or if using `.clinerules` file in project root, add:
```
Read AGENTS/CLAUDE.md and follow all instructions and referenced files
```

### For ChatGPT Users

At the beginning of each work session, start with this prompt:

```
Read AGENTS/CHATGPT.md and all the files it references in the specified order.
Ensure AGENTS/AGENTS_README.md, AGENTS/ISSUES_TODO.md, and AGENTS/ISSUES_RESOLVED.md are included.
Then check AGENTS/ISSUES_TODO.md for current priority tasks.
```

**Alternative: Use Custom Instructions**

Add this to your ChatGPT Custom Instructions (under Settings → Personalization → Custom Instructions):

```
When working on projects with an AGENTS folder:
1. Always start by reading AGENTS/CHATGPT.md (or AGENTS/CLAUDE.md if specified)
2. Read all referenced files in the order specified
3. Check AGENTS/ISSUES_TODO.md and AGENTS/ISSUES_RESOLVED.md
4. Follow all guidelines in AGENTS/AI_BEHAVIOR.md
5. Update documentation files when making code changes
```

### Quick Session Restart

If you're continuing work in the same session and the AI seems to have forgotten the context:

**For both Claude and ChatGPT:**
```
Refresh context: read AGENTS/[CLAUDE or CHATGPT].md, AGENTS/AGENTS_README.md, AGENTS/ISSUES_TODO.md, and AGENTS/ISSUES_RESOLVED.md
```

## ✏️ When to Update Each File

**After adding a feature:**
- Update `PROJECT.md` (features list)
- Update `ISSUES_RESOLVED.md` (if it was tracked)

**After changing architecture:**
- Update `ARCHITECTURE.md` (structure changes)
- Update `ISSUES_RESOLVED.md` (if it was tracked)

**After fixing a bug:**
- Update `ISSUES_RESOLVED.md` (move from TODO)
- Update `ARCHITECTURE.md` if structure changed

**After adding coding rules:**
- Update `CODING_STANDARDS.md` (new rules)

**After changing AI workflow:**
- Update `AI_BEHAVIOR.md` (new procedures)
- Update `CLAUDE.md` / `CHATGPT.md` if needed

## 🔁 Using These Files in Other Projects

To adapt this documentation system for a new project:

1. Keep as-is:
   - `CODING_STANDARDS.md` (adjust framework-specific sections)
   - `AI_BEHAVIOR.md` (completely reusable)
   - `AGENTS_README.md` (this file)
   - File templates in `ISSUES_TODO.md` and `ISSUES_RESOLVED.md`

2. Replace content:
   - `PROJECT.md` (new project description)
   - `ARCHITECTURE.md` (new architecture)
   - `CLAUDE.md` / `CHATGPT.md` (verify file paths)

3. Clear and restart:
   - `ISSUES_TODO.md` (clear old issues, keep structure)
   - `ISSUES_RESOLVED.md` (start fresh)

## 📝 Maintenance Guidelines

- Keep files concise but complete
- Remove outdated information promptly
- Use consistent formatting across all files
- Link between files when referencing related content
- Review and update at least monthly or after major changes
