# CODING_STANDARDS.md

## General Guidelines

For this project act as an expert PHP and WordPress developer.

Write code in compliance with the official WordPress plugin and theme guidelines.

Explain the code you write and the inner workings of WordPress, describing standard practices as we move forward.

While developing this project, I want to learn everything needed to become an expert developer of WordPress core, themes, and plugins.

## Code Quality Priorities

Pay particular attention to the following aspects when writing code:

- **Absence of bugs:** Write defensive code and handle edge cases
- **Absence of vulnerabilities:** Maximum security, sanitize all inputs, escape all outputs
- **Responsive pages:** Mobile-first design approach
- **Page accessibility:** This point is very important - follow WCAG guidelines
- **Compliance with WordPress best practices:** Follow WordPress Coding Standards
- **Simple, readable, and modular code:** Write code that is easy to understand and maintain

## WordPress Coding Standards

### Formatting Rules

**Use tabs, not spaces**
Indentation must use tabs, not spaces.

**Comments in English**
- Place comments in English at the beginning of each file and before every function
- Comments must end with a period "."

**File Headers**
Each file should have a descriptive header comment:
```php
/**
 * Brief description of the file.
 *
 * Longer description if needed.
 *
 * @package ThemePackageName
 */
```

**Function Documentation**
Every function must have a docblock:
```php
/**
 * Brief description of what the function does.
 *
 * Longer description if needed.
 *
 * @param string $param1 Description of parameter.
 * @param int    $param2 Description of parameter.
 * @return mixed Description of return value.
 */
function my_function( $param1, $param2 ) {
	// Function body.
}
```

### Naming Conventions

Use WordPress naming conventions:

**Classes:** `Class_Name_With_Underscores`
```php
class DLI_Lab_Manager {
	// Class body.
}
```

**Files:** `file-name-with-hyphens.php`

**Variables:** `$variable_name_with_underscores`
```php
$post_id = get_the_ID();
$user_name = get_user_name();
```

**Constants:** `CONSTANT_NAME_UPPERCASE`
```php
define( 'THEME_VERSION', '1.0.0' );
```

**Functions:** `function_name_with_underscores()`
```php
function dli_get_post_main_category( $post, $taxonomy ) {
	// Function body.
}
```

### Assignment Alignment

**Align consecutive assignments** using spaces (not tabs) so the `=` signs line up:

```php
$items   = array();
$options = get_option( 'polylang' );
```

**Align related assignments** in longer blocks:
```php
$tipo_risorsa     = dli_get_post_main_category( $post, RT_TYPE_TAXONOMY );
$archive_page_obj = dli_get_page_by_post_type( TECHNICAL_RESOURCE_POST_TYPE );
$archive_page     = $archive_page_obj ? get_permalink( $archive_page_obj->ID ) : '';
```

### Security Best Practices

**Always escape output:**
```php
// For HTML content
echo esc_html( $variable );

// For attributes
echo '<div class="' . esc_attr( $class ) . '">';

// For URLs
echo '<a href="' . esc_url( $url ) . '">';

// For translation with HTML
echo wp_kses_post( $content );
```

**Always sanitize input:**
```php
// Text input
$text = sanitize_text_field( $_POST['text'] );

// Email
$email = sanitize_email( $_POST['email'] );

// URL
$url = esc_url_raw( $_POST['url'] );
```

**Use nonces for forms:**
```php
// Creating nonce
wp_nonce_field( 'my_action_name', 'my_nonce_field' );

// Verifying nonce
if ( ! wp_verify_nonce( $_POST['my_nonce_field'], 'my_action_name' ) ) {
	wp_die( 'Security check failed' );
}
```

**Check user capabilities:**
```php
if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( 'You do not have permission to perform this action.' );
}
```

### Code Organization

**Keep functions focused:** Each function should do one thing well.

**Use early returns:** Reduce nesting by returning early when conditions aren't met.

```php
function my_function( $post_id ) {
	if ( ! $post_id ) {
		return false;
	}
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return false;
	}
	
	// Main logic here.
}
```

**Avoid deep nesting:** Keep nesting to 3-4 levels maximum.

**Extract complex conditions:** Use descriptive variable names for complex conditions.

```php
$is_valid_post = $post && $post->post_status === 'publish';
$user_can_edit = current_user_can( 'edit_post', $post->ID );

if ( $is_valid_post && $user_can_edit ) {
	// Do something.
}
```

### WordPress-Specific Rules

**Use WordPress functions when available:**
```php
// Good
wp_remote_get( $url );

// Avoid
file_get_contents( $url );
```

**Prefix all custom functions and classes:**
Use a consistent prefix (e.g., `dli_` for this theme) to avoid conflicts.

**Use WordPress coding standards for SQL:**
```php
global $wpdb;
$results = $wpdb->get_results( 
	$wpdb->prepare(
		"SELECT * FROM {$wpdb->posts} WHERE post_type = %s",
		'page'
	)
);
```

**Enqueue scripts and styles properly:**
```php
function my_theme_enqueue_scripts() {
	wp_enqueue_style( 'my-style', get_template_directory_uri() . '/style.css', array(), '1.0.0' );
	wp_enqueue_script( 'my-script', get_template_directory_uri() . '/script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_scripts' );
```

### Internationalization (i18n)

**Make all strings translatable:**
```php
__( 'Text to translate', 'text-domain' );
_e( 'Text to translate and echo', 'text-domain' );
esc_html__( 'Text to translate and escape', 'text-domain' );
esc_html_e( 'Text to translate, escape and echo', 'text-domain' );
```

**Use sprintf for dynamic strings:**
```php
/* translators: %s: post title */
$message = sprintf( __( 'Post "%s" was updated', 'text-domain' ), $post_title );
```

### Testing

**Write testable code:** Keep functions small and focused.

**Add PHPUnit tests** for complex logic and utility functions.

**Run code style checks** before committing:
```bash
composer lint
```

**Auto-fix style issues** when possible:
```bash
composer lint:fix
```

