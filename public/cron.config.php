<?
$config=array(
    // 'db_host'=>'localhost',
    'db_name'=>'rss',
    'db_user'=>'rss',
    'db_pass'=>'rss_pass',
    'email'=>'example@example.com',
    'email.from'=>'',
    'email.server'=>'',
    'email.port'=>'',
    'email.user'=>'',
    'email.pass'=>'',
);
$pdo=new PDO("mysql:dbname=".$config['db_name'].(isset($config['db_host'])&&strlen($config['db_host'])>0?";host=".$config['db_host']:''), $config['db_user'], $config['db_pass']);
$db=new NotORM($pdo);

$log = new \Monolog\Logger('slim-skeleton');
$log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
include('funct.inc.php');