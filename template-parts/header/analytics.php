<?php
	$analytics_code = dli_get_option( 'analytics_code', 'setup' );
	if ( $analytics_code && trim( $analytics_code ) !== '' ) {
		echo $analytics_code;
	}
