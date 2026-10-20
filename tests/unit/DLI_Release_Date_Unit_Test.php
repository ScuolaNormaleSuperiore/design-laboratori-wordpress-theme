<?php
/**
 * Contract test: the release date of publiccode.yml is the date of the latest
 * CHANGELOG.md entry.
 *
 * @package Design_Laboratori_Italia
 */

use PHPUnit\Framework\TestCase;

/**
 * Release date consistency.
 */
class DLI_Release_Date_Unit_Test extends TestCase {

	/**
	 * The release date declared in publiccode.yml.
	 *
	 * @return string Date as YYYY-MM-DD.
	 */
	private function publiccode_release_date() {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/publiccode.yml' );
		$this->assertNotFalse( $content, 'Cannot read publiccode.yml' );
		$this->assertSame( 1, preg_match( '/^releaseDate:\s*[\'"]?(\d{4}-\d{2}-\d{2})/m', $content, $matches ), 'releaseDate not found in publiccode.yml' );
		return $matches[1];
	}

	/**
	 * The date and the version of the latest CHANGELOG.md entry.
	 *
	 * @return array{version:string,date:string}
	 */
	private function latest_changelog_entry() {
		$content = file_get_contents( DLI_TEST_THEME_DIR . '/CHANGELOG.md' );
		$this->assertNotFalse( $content, 'Cannot read CHANGELOG.md' );
		$this->assertSame(
			1,
			preg_match( '/^##\s*\[(\d+\.\d+\.\d+)\]\s*-\s*(\d{4}-\d{2}-\d{2})/m', $content, $matches ),
			'No dated version entry found in CHANGELOG.md'
		);
		return array(
			'version' => $matches[1],
			'date'    => $matches[2],
		);
	}

	/**
	 * Both dates are valid calendar dates.
	 */
	public function test_dates_are_valid() {
		$release   = $this->publiccode_release_date();
		$changelog = $this->latest_changelog_entry()['date'];
		foreach ( array( $release, $changelog ) as $date ) {
			$parsed = DateTime::createFromFormat( 'Y-m-d', $date );
			$this->assertInstanceOf( DateTime::class, $parsed, "Invalid date {$date}" );
			$this->assertSame( $date, $parsed->format( 'Y-m-d' ), "Invalid date {$date}" );
		}
	}

	/**
	 * The release date of publiccode.yml equals the date of the latest changelog entry.
	 */
	public function test_release_date_equals_latest_changelog_date() {
		$entry = $this->latest_changelog_entry();
		$this->assertSame(
			$entry['date'],
			$this->publiccode_release_date(),
			"releaseDate in publiccode.yml must equal the date of the latest CHANGELOG.md entry ({$entry['version']})"
		);
	}
}
