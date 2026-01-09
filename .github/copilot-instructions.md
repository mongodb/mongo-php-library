# GitHub Copilot Instructions

## Code Review Guidelines

### Review Focus
- **Do not repeat or paraphrase the PR description**
- Only highlight changes or notable information that was omitted from the description
- Focus on code quality, potential issues, and improvements not mentioned in the PR

### PHP Version and Type Requirements
- The minimum PHP version supported is in `./composer.json`. Code must support **this version and newer**
- Use **full type declarations** wherever possible
- Leverage PHP 8.1+ features when appropriate (enums, readonly properties, intersection types, etc.)
- Use **PHPDoc blocks** to supplement native types where PHP limitations exist:
  - Generics: `@template T`, `@param Collection<T>`, `@return T`
  - Union types for complex scenarios
  - Array shapes: `@param array{name: string, age: int}`
  - More specific types than native PHP allows

### Code Quality Standards
- Ensure all public methods have complete type hints
- Private/protected methods should also be fully typed
- Use strict scalar type declarations (`declare(strict_types=1)`)
- Prefer constructor property promotion when applicable
- Use named arguments for better readability in complex method calls to functions inside this package

### MongoDB Library Specific
- Check for proper error handling patterns
- Ensure BSON type handling is correct
- Verify connection pool and client usage follows best practices
- Look for potential performance implications in database operations
- Ensure proper use of type maps and codecs

### What to Flag in Reviews
- Missing type declarations that could be added
- Opportunities to use newer PHP features
- Code that might not work with the minimum PHP version requirement
- Missing PHPDoc for complex types or generics
- Inconsistent coding standards
- Potential performance or security issues not mentioned in PR description
- Breaking changes that weren't highlighted
- Documentation that needs updating due to code changes

### What NOT to Comment On
- Changes that are clearly explained in the PR description
- Style preferences that are already covered by existing linting tools
- Minor formatting issues that automated tools should catch
