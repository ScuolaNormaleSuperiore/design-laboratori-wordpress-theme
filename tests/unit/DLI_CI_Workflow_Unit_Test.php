<?php
/**
 * Contract test: the GitHub workflows run on a fixed runner and on actions pinned to a commit.
 *
 * A tag or a branch can be moved to other code, and "ubuntu-latest" changes when GitHub moves the
 * label: either way a run can change without a change in this repository. Pinned SHAs with the full
 * version in the comment also let Dependabot and the reader see which release is in use.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Pins and runners of .github/workflows/*.yml.
 */
class DLI_CI_Workflow_Unit_Test extends TestCase {

	/**
	 * Runner of every job.
	 */
	const RUNNER = 'ubuntu-24.04';

	/**
	 * Lines of every workflow, keyed by file name.
	 *
	 * @return array<string, string[]>
	 */
	private function workflows() {
		$files = glob( DLI_TEST_THEME_DIR . '/.github/workflows/*.yml' );
		$this->assertNotEmpty( $files, 'No workflow found in .github/workflows' );
		$workflows = array();
		foreach ( $files as $file ) {
			$content = file_get_contents( $file );
			$this->assertNotFalse( $content, "Cannot read {$file}" );
			$workflows[ basename( $file ) ] = explode( "\n", str_replace( "\r\n", "\n", $content ) );
		}
		return $workflows;
	}

	/**
	 * The external actions used by the workflows, one entry per "uses:" line.
	 *
	 * @return array<int, array{where: string, action: string, ref: string, comment: string}>
	 */
	private function uses() {
		$uses = array();
		foreach ( $this->workflows() as $name => $lines ) {
			foreach ( $lines as $index => $line ) {
				if ( ! preg_match( '/^\s*(?:-\s+)?uses:\s*([^\s#]+)\s*(?:#\s*(.*))?$/', $line, $matches ) ) {
					continue;
				}
				if ( 0 === strpos( $matches[1], './' ) ) {
					continue;
				}
				$parts  = explode( '@', $matches[1], 2 );
				$uses[] = array(
					'where'   => $name . ':' . ( $index + 1 ),
					'action'  => $parts[0],
					'ref'     => isset( $parts[1] ) ? $parts[1] : '',
					'comment' => isset( $matches[2] ) ? trim( $matches[2] ) : '',
				);
			}
		}
		$this->assertNotEmpty( $uses, 'No "uses:" found in the workflows' );
		return $uses;
	}

	/**
	 * Every action is pinned to a full commit SHA, with the full version in the comment.
	 */
	public function test_every_action_is_pinned_to_a_sha_with_its_full_version() {
		$wrong = array();
		foreach ( $this->uses() as $use ) {
			if ( ! preg_match( '/^[0-9a-f]{40}$/', $use['ref'] ) ) {
				$wrong[] = "{$use['where']} {$use['action']}@{$use['ref']}: not a 40-character SHA";
			}
			if ( ! preg_match( '/^v?\d+\.\d+\.\d+$/', $use['comment'] ) ) {
				$wrong[] = "{$use['where']} {$use['action']}: comment \"{$use['comment']}\" is not a full version (such as v1.2.3)";
			}
		}
		$this->assertSame( array(), $wrong, "Actions not pinned as agreed:\n" . implode( "\n", $wrong ) );
	}

	/**
	 * The same action is pinned to the same commit in every workflow.
	 */
	public function test_each_action_has_one_sha_in_all_workflows() {
		$shas = array();
		foreach ( $this->uses() as $use ) {
			$shas[ $use['action'] ][ $use['ref'] . ' ' . $use['comment'] ][] = $use['where'];
		}
		$mixed = array();
		foreach ( $shas as $action => $refs ) {
			if ( count( $refs ) > 1 ) {
				$list = array();
				foreach ( $refs as $ref => $where ) {
					$list[] = $ref . ' (' . implode( ', ', $where ) . ')';
				}
				$mixed[] = $action . ': ' . implode( '; ', $list );
			}
		}
		$this->assertSame( array(), $mixed, "Actions pinned to different commits:\n" . implode( "\n", $mixed ) );
	}

	/**
	 * Every Docker image started by a workflow step is pinned to a digest, with its version tag.
	 */
	public function test_every_docker_image_is_pinned_to_a_digest() {
		$wrong = array();
		foreach ( $this->workflows() as $name => $lines ) {
			foreach ( $lines as $index => $line ) {
				if ( false === strpos( $line, 'docker run' ) ) {
					continue;
				}
				if ( ! preg_match( '/\s[a-z0-9._\/-]+:v?\d+\.\d+\.\d+@sha256:[0-9a-f]{64}(\s|$)/', $line ) ) {
					$wrong[] = $name . ':' . ( $index + 1 ) . ' ' . trim( $line );
				}
			}
		}
		$this->assertSame( array(), $wrong, "Docker images not pinned as image:1.2.3@sha256:<digest>:\n" . implode( "\n", $wrong ) );
	}

	/**
	 * The CI validates publiccode.yml with the action of Developers Italia.
	 */
	public function test_the_ci_validates_publiccode_yml() {
		$actions = array_column( $this->uses(), 'action', 'where' );
		$found   = array_filter(
			$actions,
			function ( $action, $where ) {
				return 'italia/publiccode-parser-action' === $action && 0 === strpos( $where, 'ci.yml:' );
			},
			ARRAY_FILTER_USE_BOTH
		);
		$this->assertNotEmpty( $found, 'ci.yml does not validate publiccode.yml with italia/publiccode-parser-action.' );
	}

	/**
	 * The links of publiccode.yml can be reached from the CI.
	 *
	 * The publiccode job opens every URL of the file, and github.com answers 503 to the GitHub
	 * runners on its HTML pages (/blob/, /tree/): such a link fails the job although it works in a
	 * browser. Files of the repository are linked through raw.githubusercontent.com or the
	 * documentation site instead.
	 */
	public function test_publiccode_yml_does_not_link_github_html_pages() {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/publiccode.yml' );
		$this->assertNotFalse( $content, 'Cannot read publiccode.yml' );
		preg_match_all( '#https://github\.com/[^\s\'"]+/(?:blob|tree)/[^\s\'"]+#', $content, $matches );
		$this->assertSame( array(), $matches[0], "publiccode.yml links github.com pages that the CI cannot reach:\n" . implode( "\n", $matches[0] ) );
	}

	/**
	 * The CI lints the workflows with the official actionlint image.
	 */
	public function test_the_ci_lints_the_workflows_with_actionlint() {
		$ci = implode( "\n", $this->workflows()['ci.yml'] );
		$this->assertMatchesRegularExpression( '/docker run\b[^\n]*\srhysd\/actionlint:\d+\.\d+\.\d+@sha256:[0-9a-f]{64}/', $ci, 'ci.yml does not run actionlint from the official image pinned to a digest.' );
	}

	/**
	 * Every job runs on the fixed runner.
	 */
	public function test_every_job_runs_on_the_fixed_runner() {
		$wrong = array();
		$found = 0;
		foreach ( $this->workflows() as $name => $lines ) {
			foreach ( $lines as $index => $line ) {
				if ( ! preg_match( '/^\s*runs-on:\s*(.+?)\s*$/', $line, $matches ) ) {
					continue;
				}
				++$found;
				if ( self::RUNNER !== $matches[1] ) {
					$wrong[] = $name . ':' . ( $index + 1 ) . ' runs-on: ' . $matches[1];
				}
			}
		}
		$this->assertGreaterThan( 0, $found, 'No "runs-on:" found in the workflows' );
		$this->assertSame( array(), $wrong, 'Jobs not on ' . self::RUNNER . ":\n" . implode( "\n", $wrong ) );
	}
}
