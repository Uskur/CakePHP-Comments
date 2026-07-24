<?php
declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Locator\TableLocator;

$pluginRoot = dirname(__DIR__);
$autoload = $pluginRoot . '/vendor/autoload.php';
if (!is_file($autoload)) {
    $autoload = dirname($pluginRoot, 2) . '/autoload.php';
}
if (!is_file($autoload)) {
    throw new RuntimeException('Unable to locate Composer autoload.php');
}

$loader = require $autoload;
$loader->addPsr4('Kareylo\\Comments\\Test\\', $pluginRoot . '/tests/');

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', $pluginRoot);
define('APP_DIR', 'App');
define('APP', ROOT . DS . 'tests' . DS . APP_DIR . DS);
define('CONFIG', ROOT . DS . 'tests' . DS . 'config' . DS);
define('WWW_ROOT', ROOT . DS . 'webroot' . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('TMP', sys_get_temp_dir() . DS . 'cakephp-comments-tests' . DS);
define('LOGS', TMP . 'logs' . DS);
define('CACHE', TMP . 'cache' . DS);

foreach ([TMP, LOGS, CACHE, CACHE . 'models' . DS, CACHE . 'persistent' . DS, CACHE . 'views' . DS] as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        throw new RuntimeException(sprintf('Unable to create test directory "%s"', $directory));
    }
}

require dirname($autoload) . '/cakephp/cakephp/config/bootstrap.php';

Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'defaultLocale' => 'en_US',
    'defaultTimezone' => 'UTC',
    'paths' => [
        'plugins' => [dirname(ROOT) . DS],
        'templates' => [ROOT . DS . 'templates' . DS],
        'locales' => [ROOT . DS . 'resources' . DS . 'locales' . DS],
    ],
]);
Configure::write('debug', true);
Configure::write('Session', ['defaults' => 'php']);

Cache::setConfig([
    'default' => [
        'className' => 'File',
        'path' => CACHE,
    ],
    '_cake_translations_' => [
        'className' => 'File',
        'path' => CACHE . 'persistent' . DS,
        'prefix' => 'cake_translations_',
    ],
    '_cake_model_' => [
        'className' => 'File',
        'path' => CACHE . 'models' . DS,
        'prefix' => 'cake_model_',
    ],
]);

$databaseUrl = getenv('DB_URL') ?: getenv('db_dsn') ?: 'sqlite:///:memory:';
ConnectionManager::setConfig('test', [
    'url' => $databaseUrl,
    'timezone' => 'UTC',
]);

FactoryLocator::add('Table', new TableLocator());
