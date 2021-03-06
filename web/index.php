<?php
namespace Leafcutter;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set("America/Denver");

//initialize configuration
$config = new Config\Config();
$config['directories.base'] = realpath(__DIR__.'/..');
$config->readDir(__DIR__ . '/../config/', null, true);
$config->readFile(__DIR__ . '/../config/env.yaml', null, true);

//initialize logger
$logger = new Logger('leafcutter');
if ($config['debug_log']) {
    $logger->pushHandler(
        new StreamHandler(__DIR__ . '/../logs/debug.log', Logger::DEBUG)
    );
}
$logger->pushHandler(
    new StreamHandler(__DIR__ . '/../logs/error.log', Logger::ERROR)
);

//initialize URL site and context
URLFactory::beginSite($config['site.url']);
URLFactory::beginContext(); //calling without argument sets context to site

//normalize URL
URLFactory::normalizeCurrent();

//initialize CMS context
Leafcutter::beginContext($config, $logger);
$leafcutter = Leafcutter::get();
$leafcutter->content()->addDirectory($config['site.content_dir']);

//build response from URL
$response = $leafcutter->buildResponse(URLFactory::current());

//render response
$response->renderHeaders();
$response->renderContent();
