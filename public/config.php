<?
// Prepare app
$app = new \Slim\Slim(array(
    'debug' => true,
    'templates.path' => '../templates',
));

// Create monolog logger and store logger in container as singleton 
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

$config=array(
    // 'db_host'=>'localhost',
    'db_name'=>'rss',
    'db_user'=>'rss',
    'db_pass'=>'rss_pass',
);

$app->container->singleton('db_pdo', function () {
    global $config;
    return new PDO("mysql:dbname=".$config['db_name'].(isset($config['db_host'])&&strlen($config['db_host'])>0?";host=".$config['db_host']:''), $config['db_user'], $config['db_pass']);
});
$app->container->singleton('db', function () use ($app) {
    return new NotORM($app->db_pdo);
});
// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());
include('funct.inc.php');
?>