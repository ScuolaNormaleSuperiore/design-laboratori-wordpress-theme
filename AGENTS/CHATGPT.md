# CHATGPT.md - ChatGPT/Codex Configuration

## 📚 Required Reading (in order)

Before starting any task, read these files in sequence:

1. **[PROJECT.md](./PROJECT.md)** - Project description and features
2. **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Technical architecture and structure
3. **[CODING_STANDARDS.md](./CODING_STANDARDS.md)** - Code writing rules
4. **[AI_BEHAVIOR.md](./AI_BEHAVIOR.md)** - AI workflow and behavior guidelines
5. **[ISSUES_TODO.md](./ISSUES_TODO.md)** - Current open issues

Reading these files provides complete project context and ensures you understand how to work effectively on this codebase.

## 🔧 ChatGPT-Specific Settings

### Code Interpreter Preferences

**File Operations:**
- Read and analyze files when requested
- Generate code files with proper structure
- Follow WordPress file naming conventions
- Respect the directory structure in ARCHITECTURE.md

**Code Analysis:**
- Use code interpreter for complex analysis tasks
- Verify code syntax and logic
- Run simple tests when appropriate

### Custom Instructions

Add this to your ChatGPT Custom Instructions:

```
When working on the Design Laboratori WordPress project:
1. Always read AGENTS/CHATGPT.md and referenced files first
2. Follow WordPress Coding Standards strictly
3. Prioritize security, accessibility, and code quality
4. Update documentation files when making code changes
5. Be concise but precise in responses
```

### Issue Management

When working with issues:

**Creating Issues:**
- Add new issues to [ISSUES_TODO.md](./ISSUES_TODO.md) when you discover bugs, vulnerabilities, or improvement opportunities
- Use the issue template from AI_BEHAVIOR.md
- Assign appropriate category: Security, Bug, Refactoring, Performance, Accessibility, Feature, or Documentation
- Assign appropriate priority (Critical/High/Medium/Low)
- Update both priority and category statistics at the top of the file

**Resolving Issues:**
- Move completed issues from [ISSUES_TODO.md](./ISSUES_TODO.md) to [ISSUES_RESOLVED.md](./ISSUES_RESOLVED.md)
- Place in the appropriate category section
- Add resolution date and description
- Reference related commits or pull requests
- Update statistics in both files

**Issue Organization:**
Issues are organized by category type, then sorted by priority within each category (Critical → High → Medium → Low).

### Communication Style

**Be concise but precise:**
- Provide clear explanations without unnecessary verbosity
- Include relevant code context
- Suggest next steps after completing tasks

**Ask when uncertain:**
- Don't make assumptions about requirements
- Propose alternatives when multiple approaches exist
- Clarify ambiguous requests before coding

**Explain your work:**
- Describe what the code does and why
- Reference WordPress best practices
- Help the user learn and understand

## 📝 Workflow

Follow this workflow for every task:

1. **Check ISSUES_TODO.md** for priority tasks related to your current work
2. **Read relevant documentation** (PROJECT.md, ARCHITECTURE.md, etc.)
3. **Implement following CODING_STANDARDS.md** guidelines
4. **Explain your changes** and their impact
5. **Update documentation:**
   - Update PROJECT.md if adding features
   - Update ARCHITECTURE.md if changing structure
   - Update ISSUES_TODO.md/ISSUES_RESOLVED.md as needed
6. **Suggest testing approach** for the changes

## ⚠️ Critical Reminders

### Security
- Always escape output: `esc_html()`, `esc_attr()`, `esc_url()`
- Always sanitize input: `sanitize_text_field()`, `sanitize_email()`, etc.
- Use nonces for forms: `wp_nonce_field()` and `wp_verify_nonce()`
- Check user capabilities: `current_user_can()`

### Known Critical Issues
Be aware of these critical issues when working on related code:

1. **uninstall.php crashes** - references undefined variables
2. **Autocomplete REST endpoint** - open to anonymous users (commented out permission checks)
3. **XSS vulnerabilities** - multiple templates output without escaping

See ISSUES_TODO.md for complete details.

### Accessibility
- This project prioritizes accessibility
- Follow WCAG guidelines
- Ensure keyboard navigation works
- Use semantic HTML
- Test with screen readers when possible

### WordPress Best Practices
- Use WordPress functions over PHP equivalents
- Prefix all custom functions with `dli_`
- Follow WordPress Coding Standards
- Make all strings translatable
- Enqueue scripts and styles properly

## 🔄 Documentation Update Rules

**Always update relevant files** when making code changes:

**After adding a feature:**
- ✅ Update PROJECT.md (features list)
- ✅ Update ISSUES_RESOLVED.md (if tracked)

**After fixing a bug:**
- ✅ Update ISSUES_RESOLVED.md (move from TODO)
- ✅ Update ARCHITECTURE.md (if structure changed)

**After refactoring:**
- ✅ Update ARCHITECTURE.md (if organization changed)
- ✅ Update ISSUES_RESOLVED.md (document what and why)

**After changing file structure:**
- ✅ Update ARCHITECTURE.md (directory descriptions)

## 🎯 Quick Reference

### Commands
```bash
composer lint              # Check code style
composer lint:fix          # Auto-fix code style
composer test:unit         # Run unit tests
composer test:integration  # Run integration tests
composer test:all          # Run all tests
```

### File Locations
- **Theme files:** Root directory
- **Classes/Modules:** `inc/`
- **Templates:** `template-parts/`, `page-templates/`
- **Assets:** `assets/`
- **Documentation:** `DOC/`, `AGENTS/`
- **Setup:** `SETUP/`

### Common Patterns
See ARCHITECTURE.md for details on:
- Custom post types and taxonomies
- ACF field structure
- Template hierarchy
- Module organization

## 💡 Collaboration Tips

- **Suggest next steps** after completing tasks
- **Propose refactorings** when you see opportunities
- **Ask questions** when requirements are unclear
- **Be a team player** - leave code better than you found it

## 🔍 ChatGPT-Specific Tips

### Using Code Interpreter

**For analysis tasks:**
- Read multiple files to understand patterns
- Identify code duplication or inconsistencies
- Generate statistics about code quality

**For code generation:**
- Generate complete, well-documented functions
- Include inline comments for complex logic
- Provide usage examples

### Multi-turn Conversations

**Maintain context:**
- Reference previous decisions in the conversation
- Build on earlier suggestions
- Keep track of files that need updating

**Stay organized:**
- Break large tasks into smaller steps
- Complete one step before moving to the next
- Summarize progress at key milestones

---

## For Other Projects

When adapting this file for other projects:
- Update all file paths if directory structure changes
- Verify all commands in the Quick Reference section
- Update project-specific reminders and critical issues
- Adjust custom instructions based on project needs
- Keep the workflow and documentation update rules (they're generic)
