<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp_kanabis');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '1`ql`uj7s_e5z_]|5`$4m!bt@/+Nz4m1Xn2{g2jiO3X<AF#3e4 c5h[2-?#(GS$8');
define('SECURE_AUTH_KEY',  '>+=<k= QMh%]-LB>}!?Wt*N.s36K(._.AaS$6+^+O:oJtSt%Kj-z,twhiKZ4[-X*');
define('LOGGED_IN_KEY',    '-k<sG]qB^g_676O$dH`)z~?}poD!D.XzDm)$sauh^j-B%[[u@ANH4Zuj|}&KAfbc');
define('NONCE_KEY',        '2WKr9WIP@(T|w_M^Pf>eq!$f8;&}a=`dHYhr51HBR/MJ9>+$R#iBtteEKx?b|E&Z');
define('AUTH_SALT',        '|+[Y+}dIYk2e-F@mKPSkM9<CW#iyyQ|S7.>UHE+O7DZtAyR1CW$e_7EgiJTo]?aV');
define('SECURE_AUTH_SALT', '.%da#(!-1{]>P2*P6/G=n*HYERoasC*}oTsoVHm}8E`XI(r0kg| rQkdy$bw-^`h');
define('LOGGED_IN_SALT',   '7z8}>#AZ-.h,r^^s(a|tB8!yFxPW2H.9h|dv+[4n^:9Hx9-|+(4Ap>=b/Q/=#,}r');
define('NONCE_SALT',       '=8}4n)$~hCVKFMY4%(z5dO|<,#_[+ l7/w1AMsX=+uVSwbsP2+A|SgidF2q{y-)$');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'kan_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
