<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => '%s?version=%s',
    'assets.named_packages' => array(
        'css' => array('version' => 'css2', 'base_path' => '/css'),
        'images' => array('base_path' => '/images'),
    ),
));

$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['swiftmailer.use_spool'] = true;

$app['swiftmailer.options'] = array(
    'host' => 'smtp.gmail.com',
    'port' => '465',
    'username' => 'casamientomeliyfranco@gmail.com',
    'password' => 'meliyfranco',
    'encryption' => 'ssl',
    'auth_mode' => 'login'
);

$app->get('/', function () use ($app) {
    return $app['twig']->render('homepage.html.twig');
});

$app->post('/confirmar', function(Request $request) use ($app) {
    $nombre = $request->request->get('name');
    $mail = $request->request->get('email');
    $obs = $request->request->get('message');

    $obs = sprintf('%s <%s> confirmó asistencia. Observaciones: %s', $nombre, $mail, $obs ?: 'N/C');

    $mensaje = \Swift_Message::newInstance()
        ->setSubject('CONFIRMACION INVITACION')
        ->setFrom(['casamientomeliyfranco@gmail.com' => 'Casamiento Meli & Franco'])
        ->setTo([$mail => $nombre])
        ->setCC(['melisa284@hotmail.com' => 'Melisa Roldan', 'casamientomeliyfranco@gmail.com'])
        ->setBody($obs);

    $r = $app['mailer']->send($mensaje);

    return new Response('Gracias por confirmar la asistencia!');
});

$app->run();
