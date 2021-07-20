<?php

session_start();
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
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'webdev_snapd1');

/** MySQL database username */
define('DB_USER', 'webdev_snapd1');

/** MySQL database password */
define('DB_PASSWORD', '!Digitemb123');
// // ** MySQL settings - You can get this info from your web host ** //
// /** The name of the database for WordPress */
// define( 'DB_NAME', 'webdev_snapd' );

// /** MySQL database username */
// define( 'DB_USER', 'webdev_snapd' );

/** MySQL database password */
// define( 'DB_PASSWORD', '-YhCo@WY!bU4' );

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'R7d/H,*Px5H{<j%;n_lRO?cj+c!wgt3McVq#lvik)f8f).ySay1c!)>hkMrSMgY.');
define('SECURE_AUTH_KEY',  'cG;]IIx+I M/V&Ha1&,rB^F[kaK-QQX0kEk`3+r!.wDsvWkHEURISaLB]^5 yXC5');
define('LOGGED_IN_KEY',    '^@e 8gcSE]fZRT$.lyDYb3@o#FCvoY~4g1s^#KS/E<EN]@/{=yRe!w2^.^BV$K>u');
define('NONCE_KEY',        'z.L%[4OeW/T3RgG|^Q,,WT6#A{#/a>E}^1C4(3I-]eQvI}1(LemKKs^5)9nJK:c@');
define('AUTH_SALT',        'OX6Ng+:wx:$>jUoHU)dN7+|_JBE:pvmn6)14Ht8dy*jMiteE7f,:bXe/:vtiZlPk');
define('SECURE_AUTH_SALT', 'UV%jaSIK8CFL%|26^`_<iFCdza0=G1G%C_7rdTL#V>/]X&p.K=r:In_vq3#jM$Q ');
define('LOGGED_IN_SALT',   '.,76Nxhx:cry0gRHwTX(/Qp6/<T:9[h^(i rU.ZZ{0sF&+:6-;5i*cYG`cm8v?,d');
define('NONCE_SALT',       'T|]iMpQG|`(%_pM]S4)3Heq%1Y*jZ^<,:S:+i!R?8).fZ4gBz0+v9g4U>`IeuEV#');

/**#@-*/

//********************************** Api Credentials ******************************************//

define('API_URL', 'https://snapd-website.ue.r.appspot.com/v1/'); // 35.203.116.207
define('NEW_API_URL', 'https://snapd-website.ue.r.appspot.com/v1/'); // 35.203.116.207


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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', dirname(__FILE__) . '/');
}
set_time_limit(30);
define('WP_MEMORY_LIMIT', '254M');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

date_default_timezone_set('Canada/Eastern');
