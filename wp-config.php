<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('SCRIPT_DEBUG', true);
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);  // ghi log vào wp-content/debug.log
define('WP_DEBUG_DISPLAY', false); // không show trên trình duyệt

define('DB_NAME', 'local');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'root');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'zg)Uajh)nl[Qn9O43hdms1M7}Ft:r]6OG2s#]=_[TT^!#`Y:*qnjZXw|@1{4MqU7');
define('SECURE_AUTH_KEY', '?BDzAv&`EPEH@N[o#]c)g38p8XedC0/{h-73]PfmJ orcfIUuqfMvm`!}hjxQJ2X');
define('LOGGED_IN_KEY', ')};SUwk  hF4SPff+HMCWH`B;iF>wD:5i8Z,]b$gpjuQGXPLkAKJ7XXtx0y,2S>y');
define('NONCE_KEY', 'njQvs9c}M)BjAJuiy-X]fDY1uwhT?sZn,sHw-fqD|G]o_.)Zt2NnpYFS@=[FHD|W');
define('AUTH_SALT', 'q4S0cHJjV73q2WN~@!,|Z9JJ~ 4(ME~rD>l[,7^ZSh9qb2; 5%~m6MGg#x}Z;UMi');
define('SECURE_AUTH_SALT', 'e2ouYWS2.$bRDSFD3UH8&(;iu(GgnwV3H%ESd(5aiL#dA=lJL@~-?U5brEfWgord');
define('LOGGED_IN_SALT', 'VT6|x$;,4VvN|qZM.(Fy7B(uuwGRj1].>+P0%PWJBbCS{*oFJ?FKKJK:d]&j8Xv2');
define('NONCE_SALT', 'PF(m]%Zz:!1_|MXdHZ#YA(:seVy^j3myo2_u^Q63`2Q66*`<m%5thtCNX|__/eY7');
define('WP_CACHE_KEY_SALT', '8|EokNSLx57ZkX$Ws$z8a&sku{(.;)*[yqDX!iUfibp :plx7_>~hBvrz4)Fp5Xi');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if (!defined('WP_DEBUG')) {
	define('WP_DEBUG', false);
}

define('WP_ENVIRONMENT_TYPE', 'local');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
