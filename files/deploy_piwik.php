<?php


define('PIWIK_INCLUDE_PATH', '/var/www/piwik/');
define('PIWIK_USER_PATH',    '/var/www/piwik/');

include('core/Loader.php');
include('libs/upgradephp/upgrade.php');

include("plugins/Installation/Controller.php");
#include("plugins/Installation/ServerFilesGenerator.php");

use Piwik\DbHelper;
use Piwik\Db\Adapter;
use Piwik\Db;
use Piwik\Plugin\ControllerAdmin;
use Piwik\Plugins\Installation;
use Piwik\Config;
use Piwik\Common;

$db_host = '127.0.0.1';
$db_user = $argv[1];
$db_pass = $argv[2];
$db_name = $argv[3];
$db_adapter = 'PDO\MYSQL';
$db_port = '3306';

$dbInfos = array(
    'host'          => $db_host,
    'username'      => $db_user,
    'password'      => $db_pass,
    'dbname'        => $db_name,
    'tables_prefix' => '',
    'adapter'       => $db_adapter,
    'port'          => $db_port,
    'schema'        => 'Mysql',
    'type'          => 'InnoDB',
);

$c = new Piwik\Plugins\Installation\Controller();

try {
    @Db::createDatabaseObject($dbInfos);
} catch (Zend_Db_Adapter_Exception $e) {
    $db = Adapter::factory($db_adapter, $dbInfos, $connect = false);

    // database not found, we try to create  it
    if ($db->isErrNo($e, '1049')) {
        $dbInfosConnectOnly = $dbInfos;
        $dbInfosConnectOnly['dbname'] = null;
        @Db::createDatabaseObject($dbInfosConnectOnly);
        @DbHelper::createDatabase($dbInfos['dbname']);

        // select the newly created database
        @Db::createDatabaseObject($dbInfos);
    }
}

DbHelper::createTables();
DbHelper::createAnonymousUser();


use Piwik\Plugins\UsersManager\API as APIUsersManager;
use Piwik\Piwik;


Piwik::setUserHasSuperUserAccess();
$login = 'root';

$api = APIUsersManager::getInstance();
$api->addUser($login, 'rootpw','root@localhost.com');
Piwik::setUserHasSuperUserAccess();
$api->setSuperUserAccess($login, true);

$config = Config::getInstance();
$config->General['salt'] = Common::generateUniqId();
$config->database = $dbInfos;
$config->forceSave();
