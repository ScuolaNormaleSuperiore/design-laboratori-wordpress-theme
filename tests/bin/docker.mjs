/**
 * Shared check of the Node scripts of the test tools: Docker must answer before wp-env is used.
 *
 * wp-env trusts the state it saved, so with Docker Desktop stopped it can report the environment as started and fail
 * only at the next command, with an error that does not name Docker. This check runs first and stops with a clear
 * message.
 *
 * @package Design_Laboratori_Italia
 */

import { spawnSync } from 'node:child_process';

/**
 * Stops the script when the Docker daemon does not answer.
 */
export function requireDocker() {
	const result = spawnSync( 'docker', [ 'info', '--format', '{{.ServerVersion}}' ], { encoding: 'utf8' } );

	// `error` is set when the command could not start (Docker not installed).
	if ( result.error || 0 !== result.status ) {
		console.error( 'Docker does not answer: start Docker Desktop (or the Docker daemon) and run the command again.' );
		process.exit( 1 );
	}
}
