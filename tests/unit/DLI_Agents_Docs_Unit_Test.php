<?php
/**
 * Contract test: the documents cited by AGENTS/AI_BOOTSTRAP.md exist.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Consistency of the AI agent documentation.
 */
class DLI_Agents_Docs_Unit_Test extends TestCase {

	/**
	 * Local files that are ignored by Git and may be missing on a clone.
	 *
	 * @var string[]
	 */
	private const LOCAL_ONLY_FILES = array(
		'AGENTS/ISSUES_TODO.md',
		'AGENTS/ISSUES_RESOLVED.md',
		'AGENTS/CODE_REVIEW_PROGRESS.md',
	);

	/**
	 * The entry points and the bootstrap documents must be in the repository.
	 */
	public function test_entry_points_and_bootstrap_documents_exist() {
		$required = array(
			'AGENTS.md',
			'CLAUDE.md',
			'AGENTS/AI_BOOTSTRAP.md',
			'AGENTS/PROJECT.md',
			'AGENTS/ARCHITECTURE.md',
			'AGENTS/AI_BEHAVIOR.md',
			'AGENTS/CODING_STANDARDS.md',
			'AGENTS/GIT_WORKFLOW.md',
			'AGENTS/CODE_REVIEW.md',
		);
		foreach ( $required as $file ) {
			$this->assertFileExists( DLI_TEST_THEME_DIR . '/' . $file, "{$file} is missing" );
		}
	}

	/**
	 * Every AGENTS/*.md file named with its full path in AI_BOOTSTRAP.md exists,
	 * except the local files that Git ignores.
	 */
	public function test_documents_cited_in_the_bootstrap_exist() {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/AGENTS/AI_BOOTSTRAP.md' );
		$this->assertNotFalse( $content, 'Cannot read AGENTS/AI_BOOTSTRAP.md' );

		$this->assertGreaterThan( 0, preg_match_all( '/`(AGENTS\/[A-Za-z0-9_]+\.md)`/', $content, $matches ), 'No document cited in AI_BOOTSTRAP.md' );

		$cited = array_diff( array_unique( $matches[1] ), self::LOCAL_ONLY_FILES );
		foreach ( $cited as $file ) {
			$this->assertFileExists( DLI_TEST_THEME_DIR . '/' . $file, "{$file} is cited in AGENTS/AI_BOOTSTRAP.md but does not exist" );
		}
	}
}
