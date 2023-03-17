<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// Impostazione temporanea per plugin fgm2wc (vedi https://www.fredericgilles.net/support/kb/faq.php?id=26).
define( 'WP_MEMORY_LIMIT', '1G' );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'doclabdb' );

/** MySQL database username */
define( 'DB_USER', 'admin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'admin' );

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '0gt,5pO&%R-2:d5`h= Q@E].,!Z8/}pBp@0aN#?I~sXD$}ni1~{O:i?R^K/(7SGA' );
define( 'SECURE_AUTH_KEY',  '%9+1t6c!HACRp 47(Hq1N#s3iTW=_a~Tlu(M8UpKu?q$?LtMf?B/o{Ohsm^7lkgo' );
define( 'LOGGED_IN_KEY',    '_%9,-Q$xH^kP?nz;B8n pA1uz~p hPoIs5[i>3u;IY-~S]+H#Z}OkMZ:%C=|*E&T' );
define( 'NONCE_KEY',        'h56>^8=3{cr+OufR]SGYs7-0PJtS%e(x-#|dHO{>3n~(93-=L8*wP4<co/D{fHZ|' );
define( 'AUTH_SALT',        'D5_>V^e,kEdd9l#pwQk3T)vAm.!KGkp4pA/T81-2P7nzdOK2xO7uK4$wO_yO-^M)' );
define( 'SECURE_AUTH_SALT', '/KJ#|nUP5_8[5u-Mmi~%f5sf!UVD+SI})N~%Esb8?<bSJ8iB=(n};.h!(0Y<),vN' );
define( 'LOGGED_IN_SALT',   '=}UEC_Sk+t4!v>[r7b&@bD,inRw8~zT=l4a(zM}[V3VK~vCRGF!@?{yD;8[#!&sY' );
define( 'NONCE_SALT',       'L_!(a<}@]&bs0/~98|Rx81zK%Y<k6<%[S#LRNCeV0uhJP[gGcUOiIY2YB`fui!z?' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
