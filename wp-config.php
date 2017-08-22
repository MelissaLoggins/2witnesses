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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', '2witnesses');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'D<:HS!P3UGE9mx6Lb/AOy$k;k+NYaK,zpKJ117~6tUzE.,/~M]+84xi2^3to(dyK');
define('SECURE_AUTH_KEY',  'i,*_E&/ 5g%l|?U~+FjL$8x(1jC07fi8)IJ7;G@ulL u_7v|&#HHoqpx)$B( :15');
define('LOGGED_IN_KEY',    'FHmpEk.p<LrWG/Yrzma.q9te/Ki)>]%xFTu+tMc7!oxOyjsH_DN9B~*;#tklkkqC');
define('NONCE_KEY',        'VllfvK%5kX_@q}s3df[@JB]d*h3C%XiO$j,EQ?E!V9XXz0b=vr|`~v-t~pa[)0@1');
define('AUTH_SALT',        '?_e4*R:KAX|U<vtb:A$-#XO+C}:Hy:~Cm{mI@(Jk$nN9Sf)TU!KHJVGn<bkzq1tz');
define('SECURE_AUTH_SALT', 'L:lUh8yV ImqDa31c3vP9xd?E rk*Q{j ]NJ&E.H+fQo^f7G$LxH>*LqcELy|[$m');
define('LOGGED_IN_SALT',   '#A5>Xdn!csPwKI50_:%<b/ xO1ro?M}[4[v/bGRnZ=M`,>^Nlmg#5hyTfdf``xOW');
define('NONCE_SALT',       '<_yU~`N6w+69Rl{o:eIehq5Pn^R,b(I)T5+=<%TXf4$f~wa9eA(VH(kh%UlCHXo*');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
