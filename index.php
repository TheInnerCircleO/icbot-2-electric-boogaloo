<?php

use App\Bootstrap;
use App\Commands;
use BotMan\BotMan\BotMan;
use DI\Bridge\Slim\Bridge;
use DI\Container;
use Dotenv\Dotenv;
use Slim\Psr7\Response;

require __DIR__ . '/vendor/autoload.php';

// Initialize environment variable handler
Dotenv::createImmutable(__DIR__)->load();

// Initialize the container
$container = new Container();
$container->set('app.config', require __DIR__ . '/config/app.php');

// Configure the application componentes
$container->call(Bootstrap\BotManProvider::class);

// Register bot commands
$container->call(function (BotMan $botman) {
    $botman->hears('ping', function (BotMan $botman) {
        $botman->reply('pong');
    });

    $botman->hears('eightball {question}', Commands\Eightball::class);
    $botman->hears('roll ([0-9]+)d([0-9]+)', Commands\Roll::class);

    $botman->fallback(Commands\Fallback::class);
});

// Create the application
$app = Bridge::create($container);

// Register web routes
$app->post('/', function (BotMan $botman, Response $response) {
    $botman->listen();

    return $response;
});

// Enagage!
$app->run();
