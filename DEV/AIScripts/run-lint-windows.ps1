param(
	[string]$Target = ''
)

$ErrorActionPreference = 'Stop'

$php = 'C:\Users\Claudio\AppData\Roaming\Local\lightning-services\php-8.2.27+1\bin\win64\php.exe'
$composer = 'C:\ProgramData\ComposerSetup\bin\composer.phar'
$projectDir = 'C:\Users\Claudio\Local Sites\alm-site\app\public\wp-content\plugins\asset-lending-manager'

if (-not (Test-Path $php)) {
	throw "PHP executable not found: $php"
}

if (-not (Test-Path $composer)) {
	throw "Composer not found: $composer"
}

if (-not (Test-Path $projectDir)) {
	throw "Project directory not found: $projectDir"
}

$args = @(
	'-n',
	'-d', 'extension_dir=C:\Users\Claudio\AppData\Roaming\Local\lightning-services\php-8.2.27+1\bin\win64\ext',
	'-d', 'extension=php_openssl.dll',
	$composer,
	'lint'
)

if ($Target -and $Target.Trim().Length -gt 0) {
	$args += '--'
	$args += $Target.Trim()
}

$process = Start-Process -FilePath $php -ArgumentList $args -WorkingDirectory $projectDir -NoNewWindow -Wait -PassThru
exit $process.ExitCode
