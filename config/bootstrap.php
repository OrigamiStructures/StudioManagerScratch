<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require __DIR__ . '/paths.php';

// Use composer to load the autoloader.
require ROOT . DS . 'vendor' . DS . 'autoload.php';

/**
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

// You can remove this if you are confident you have intl installed.
if (!extension_loaded('intl')) {
    trigger_error('You must enable the intl extension to use CakePHP.', E_USER_ERROR);
}

use Cake\Cache\Cache;
use Cake\Console\ConsoleErrorHandler;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorHandler;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\Network\Request;
use Cake\Routing\DispatcherFactory;
use Cake\Utility\Inflector;
use Cake\Utility\Security;

/**
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (\Exception $e) {
    die($e->getMessage() . "\n");
}

// Load an environment local configuration file.
// You can use a file like app_local.php to provide local overrides to your
// shared configuration.
//Configure::load('app_local', 'default');

// When debug = false the metadata cache should last
// for a very very long time, as we don't want
// to refresh the cache while users are doing requests.
if (!Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+1 years');
    Configure::write('Cache._cake_core_.duration', '+1 years');
}

/**
 * Set server timezone to UTC. You can change it to another timezone of your
 * choice but using UTC makes time calculations / conversions easier.
 */
date_default_timezone_set('UTC');

/**
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/**
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', 'en_US');

/**
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

// Include the CLI bootstrap overrides.
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/**
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 *
 * If you define fullBaseUrl in your config file you can remove this.
 */
if (!Configure::read('App.fullBaseUrl')) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        Configure::write('App.fullBaseUrl', 'http' . $s . '://' . $httpHost);
    }
    unset($httpHost, $s);
}

Cache::config(Configure::consume('Cache'));
ConnectionManager::config(Configure::consume('Datasources'));
Email::configTransport(Configure::consume('EmailTransport'));
Email::config(Configure::consume('Email'));
Log::config(Configure::consume('Log'));
Security::salt(Configure::consume('Security.salt'));

/**
 * The default crypto extension in 3.0 is OpenSSL.
 * If you are migrating from 2.x uncomment this code to
 * use a more compatible Mcrypt based implementation
 */
// Security::engine(new \Cake\Utility\Crypto\Mcrypt());

/**
 * Setup detectors for mobile and tablet.
 */
Request::addDetector('mobile', function ($request) {
    $detector = new \Detection\MobileDetect();
    return $detector->isMobile();
});
Request::addDetector('tablet', function ($request) {
    $detector = new \Detection\MobileDetect();
    return $detector->isTablet();
});

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 *
 * Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
 * Inflector::rules('irregular', ['red' => 'redlings']);
 * Inflector::rules('uninflected', ['dontinflectme']);
 * Inflector::rules('transliteration', ['/å/' => 'aa']);
 */

/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on Plugin to use more
 * advanced ways of loading plugins
 *
 * Plugin::loadAll(); // Loads all plugins at once
 * Plugin::load('Migrations'); //Loads a single plugin named Migrations
 *
 */

Plugin::load('Migrations');
Plugin::load('CakeDC/Users', ['routes' => true, 'bootstrap' => true]);
Plugin::load('Proffer');

// Only try to load DebugKit in development mode
// Debug Kit should not be installed on a production system
if (Configure::read('debug')) {
    Plugin::load('DebugKit', ['bootstrap' => true]);
	Plugin::load('OSDebug', ['bootstrap' => true, 'routes' => true]);
}

/**
 * Connect middleware/dispatcher filters.
 */
DispatcherFactory::add('Asset');
DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');

/**
 * Enable default locale format parsing.
 * This is needed for matching the auto-localized string output of Time() class when parsing dates.
 */
Type::build('date')->useLocaleParser();
Type::build('datetime')->useLocaleParser();

Configure::write('Users.config', ['users']);


/**
 * Constants
 */

//Member Types for Member/Group
define('MEMBER_TYPE_INSTITUTION', 'Institution');
define('MEMBER_TYPE_PERSON', 'Person');
define('MEMBER_TYPE_USER', 'User');
define('MEMBER_TYPE_CATEGORY', 'Category');

// <editor-fold defaultstate="collapsed" desc="SYSTEM STATES, ARTWORK_REVIEW etc.">

// These are used in Lib/StateMap.php to map Controller actions
//Member State
define('MEMBER_CREATE', 1);
define('MEMBER_REVIEW', 2);
define('MEMBER_REFINE', 4);
define('MEMBER_SAVE', 8);

// Artwork State
define('ARTWORK_CREATE', 1);
define('ARTWORK_REVIEW', 2);
define('ARTWORK_REFINE', 4);
define('ARTWORK_SAVE', 8);
define('ARTWORK_CREATE_UNIQUE', 3);

//Disposition State
define('DISPOSITION_CREATE', 1);
define('DISPOSITION_REVIEW', 2);
define('DISPOSITION_REFINE', 4); 

// Piece State
define('PIECE_RENUMBER', 5);
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="EDITION TYPES">
define('EDITION_UNIQUE', 'Unique');
define('EDITION_RIGHTS', 'Rights');
define('EDITION_LIMITED', 'Limited Edition');
define('EDITION_OPEN', 'Open Edition');
define('PORTFOLIO_LIMITED', 'Limited Portfolio');
define('PORTFOLIO_OPEN', 'Open Portfolio');
define('PUBLICATION_LIMITED', 'Limited Publication');
define('PUBLICATION_OPEN', 'Open Publication');
// </editor-fold>

// Serves as a boolean argument in method call(s)
define('NUMBERED_PIECES', 1);
define('OPEN_PIECES', 0);

// These will need to be change to something meaningful
// For now, we can act as admins even though we're users
define('ADMIN_SYSTEM', 'user'); // 'admin'
define('ADMIN_ARTIST', 'artist_admin');

//System Constants
// Serves as a boolean argument in method call(s)
define('SYSTEM_VOID_REFERER'	, TRUE);
define('SYSTEM_CONSUME_REFERER' , FALSE);

//AssignemtTrait Constants
// boolean argument to control the kind of return value from a method
define('PIECE_ENTITY_RETURN'	, FALSE);
define('PIECE_COLLECTION_RETURN', TRUE);

// <editor-fold defaultstate="collapsed" desc="PIECE FILTERS">

// NOTES ON ADDING TO THIS SECTION
// PieceTableHelper::_map needs matching entry to identify the
// filter strategy callable or sort strategy callable
define('PIECE_FILTER_LOAN_FOR_RANGE', 'for_loan_in_range');
define('PIECE_FILTER_FOR_SALE_ON_DATE', 'for_sale_on_date');
define('PIECE_FILTER_COLLECTED', 'collected');
define('PIECE_FILTER_NOT_COLLECTED', 'not_collected');
define('PIECE_FILTER_ASSIGNED', 'assigned');
define('PIECE_FILTER_UNASSIGNED', 'not_assigned');
define('PIECE_FILTER_FLUID', 'fluid');
define('PIECE_FILTER_RIGHTS', 'rights');
define('PIECE_FILTER_NONE', 'none');
define('PIECE_SORT_NONE', 'none');
// </editor-fold>

// <editor-fold defaultstate="collapsed" desc="DISPOSITION TYPES">
define('DISPOSITION_TRANSFER', 'transfer');
define('DISPOSITION_LOAN', 'loan');
define('DISPOSITION_STORE', 'storage');
define('DISPOSITION_UNAVAILABLE', 'unavailable');
//define('DISPOSITION_REVIEW'				, 'review');


define('DISPOSITION_TRANSFER_SALE', 'Sale');
define('DISPOSITION_TRANSFER_SUBSCRIPTION', 'Subscription');
define('DISPOSITION_TRANSFER_DONATION', 'Donation');
define('DISPOSITION_TRANSFER_GIFT', 'Gift');
define('DISPOSITION_TRANSFER_RIGHTS', 'Published');
//
define('DISPOSITION_LOAN_SHOW', 'Show');
define('DISPOSITION_LOAN_CONSIGNMENT', 'Consignment');
define('DISPOSITION_LOAN_PRIVATE', 'Loan');
define('DISPOSITION_LOAN_RENTAL', 'Rental');
define('DISPOSITION_LOAN_RIGHTS', 'Licensed');
define('DISPOSITION_REVIEW_CONTACT', 'Contact'); 
//
define('DISPOSITION_UNAVAILABLE_LOST', 'Lost');
define('DISPOSITION_UNAVAILABLE_DAMAGED', 'Damaged');
define('DISPOSITION_UNAVAILABLE_STOLEN', 'Stolen');
define('DISPOSITION_NFS', 'Not For Sale');
//
define('DISPOSITION_STORE_STORAGE', 'Storage');
// </editor-fold>


define('PIECE_SPLIT_RETURN_NEW', 'new');
define('PIECE_SPLIT_RETURN_BOTH', 'both');

// <editor-fold defaultstate="collapsed" desc="FONT ICONS">
define('ICON_REVIEW', 'fi eye');
define('ICON_REFINE', 'fi pencil');
define('ICON_REMOVE', 'fi trash');
define('ICON_WRENCH', 'fi wrench');
define('ICON_COG', 'fi cog');

define('ICON_MEMBER_TYPE_INSTITUTION', 'fi-results-demographics');
define('ICON_MEMBER_TYPE_PERSON', 'fi-torsos-female-male');
define('ICON_MEMBER_TYPE_USER', 'fi-torsos-all');
define('ICON_MEMBER_TYPE_CATEGORY', 'fi-results'); 
// </editor-fold>

define('REJECTION_RECORD', TRUE);
define('REJECTION_DONT_RECORD', FALSE);

define('CLEAR', TRUE);

