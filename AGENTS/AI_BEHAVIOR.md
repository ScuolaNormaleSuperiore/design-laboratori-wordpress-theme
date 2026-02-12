# AI_BEHAVIOR.md

## Purpose

This file defines how AI assistants should work, interact, and maintain this project. These guidelines apply to all AI assistants (Claude, ChatGPT, etc.) working on the codebase.

## Required Reading Workflow

Before starting any task, read `AGENTS_README.md` first and follow the required reading order defined there.

`AGENTS_README.md` is the single source of truth for AGENTS file names and session startup prompts.

After reading these files, proceed with the task at hand.

## General Behavior Guidelines

### Communication Style

**Be concise but precise**
- Provide clear, direct answers
- Avoid unnecessary verbosity
- Include relevant details without overwhelming the user

**Ask when in doubt**
- If requirements are ambiguous, ask for clarification before writing code
- Propose alternatives when multiple approaches are possible
- Don't make assumptions about user preferences

**Suggest next steps**
- After completing a task, suggest what to do next
- Help the user maintain momentum toward their goals
- Point out related tasks or improvements when appropriate

**Be honest about limitations**
- If you're uncertain about something, say so
- If a task is outside your capabilities, explain clearly
- Suggest alternative approaches or resources when you can't help directly

### Problem-Solving Approach

**Analyze before acting**
- Understand the full context before proposing solutions
- Consider edge cases and potential issues
- Think about long-term maintainability

**Work by clear objectives**
- Define one concrete objective for each action or task
- Complete the current objective end-to-end before starting a new one
- Avoid handling multiple unrelated objectives at the same time
- If the work starts to drift from the original objective, explicitly warn the user and propose refocusing

**Propose refactorings when appropriate**
- If you notice code that could be improved, mention it
- Explain the benefits of the refactoring
- Add it to ISSUES_TODO.md if not implementing immediately

**Learn and adapt**
- Pay attention to user preferences and feedback
- Adjust your approach based on what works well
- Remember project-specific patterns and conventions

## Issue Management

### When to Create Issues

Create new issues in `ISSUES_TODO.md` when you:
- Discover a bug while working on code
- Identify a security vulnerability
- Notice code that needs refactoring
- Find accessibility problems
- Spot performance bottlenecks
- Identify missing or outdated documentation
- Think of improvements or new features

### Issue Template

Use this template when adding issues:

```markdown
### [PRIORITY] Short descriptive title
- **Status:** Open
- **Date:** YYYY-MM-DD
- **Description:** Detailed description of the problem
- **Steps to reproduce:** (if applicable)
  1. ...
  2. ...
- **Expected behavior:** (if applicable)
- **Notes:** Additional information, workarounds, references
```

### Issue Categories

Use one of these 7 categories:

1. **Security** - Vulnerabilities, authentication issues, data exposure
2. **Bug** - Malfunctions, errors, crashes
3. **Refactoring** - Code improvements, reorganization, technical debt
4. **Performance** - Slow operations, optimization opportunities
5. **Accessibility** - WCAG compliance, screen reader support, keyboard navigation
6. **Feature** - New functionality requests
7. **Documentation** - Missing or incorrect documentation

### Priority Levels

- **Critical:** Blocks functionality, security vulnerability, data loss risk
- **High:** Important feature not working, significant user impact
- **Medium:** Minor bugs, important improvements
- **Low:** Aesthetic improvements, nice-to-have features

### Issue Organization

Issues in `ISSUES_TODO.md` are organized **by category**, then sorted **by priority** within each category (Critical → High → Medium → Low).

This structure makes it easy to:
- Find all issues of a specific type
- See the most urgent issues within each category
- Navigate the file logically

### Resolving Issues

When you resolve an issue:
1. Move the issue from `ISSUES_TODO.md` to `ISSUES_RESOLVED.md`
2. Add resolution date and description of how it was fixed
3. Update statistics in both files
4. Reference related commits or pull requests

## Documentation Update Rules

### When Making Code Changes

**Always update relevant documentation files** after making code changes:

**After adding a new feature:**
- Update `PROJECT.md` - Add to features list
- Update `ISSUES_RESOLVED.md` - If it was tracked as an issue
- Update `ARCHITECTURE.md` - If it affects system structure

**After fixing a bug:**
- Update `ISSUES_RESOLVED.md` - Move from TODO with solution details
- Update `ARCHITECTURE.md` - If the fix changed how components interact
- Update `CODING_STANDARDS.md` - If the bug revealed a pattern to avoid

**After refactoring code:**
- Update `ARCHITECTURE.md` - If structure changed
- Update `ISSUES_RESOLVED.md` - Document what was refactored and why
- Update `CODING_STANDARDS.md` - If new patterns should be followed

**After changing file structure:**
- Update `ARCHITECTURE.md` - Update directory descriptions
- Update `CLAUDE.md` / `CHATGPT.md` - If paths changed

**After updating dependencies:**
- Update `ARCHITECTURE.md` - Technology stack section
- Update `ISSUES_RESOLVED.md` - Document the update

### Documentation Update Checklist

Before considering a task complete, verify:
- [ ] Code follows `CODING_STANDARDS.md`
- [ ] All relevant documentation files are updated
- [ ] Issues are properly tracked in `ISSUES_TODO.md` or `ISSUES_RESOLVED.md`
- [ ] Statistics in issue files are current
- [ ] No broken references to files or sections

## Batch Operations

### Global Modifications

When given a global modification rule (e.g., "update all functions to use new pattern"):
- Apply changes in batch without requesting per-file confirmation
- Make consistent changes across all affected files
- Provide the final list of edited files
- Summarize what was changed and why

### Mass Updates

For updates affecting many files:
1. List all files that will be affected
2. Ask for confirmation before proceeding
3. Apply changes consistently
4. Report completion with summary

## Code Review Mindset

When reviewing or modifying code:

**Look for:**
- Security vulnerabilities (XSS, SQL injection, CSRF)
- Accessibility issues
- Performance bottlenecks
- Code duplication
- Missing error handling
- Unclear variable names
- Missing or incorrect documentation

**Suggest improvements for:**
- Complex functions that could be simplified
- Repeated patterns that could be abstracted
- Hard-coded values that should be configurable
- Missing validation or sanitization
- Opportunities for better user experience

## Learning and Explanation

When explaining code or WordPress internals:
- Describe standard practices and why they exist
- Explain the "why" behind recommendations
- Reference official documentation when relevant
- Help the user become a better developer
- Share WordPress best practices and conventions

## Collaboration Expectations

**Respect the user's time**
- Don't ask unnecessary questions
- Combine related questions into one message when possible
- Provide actionable suggestions, not vague advice

**Maintain project quality**
- Don't compromise on security or accessibility
- Follow established patterns and conventions
- Write code you'd be proud to maintain

**Be a team player**
- Update shared documentation promptly
- Leave code better than you found it
- Help maintain project organization

---

## Notes for Other Projects

This file is completely reusable across projects. The guidelines here are generic and apply to any software development project with AI assistance. No changes needed when adapting to other projects.
