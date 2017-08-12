<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../../vendor/autoload.php';

$applicationConfig = new \Api\ApplicationConfig(
	[
		'rabbitmq' => [
			'host' => getenv('RABBIT_HOST'),
			'port' => (int) getenv('RABBIT_PORT'),
			'user' => getenv('RABBIT_LOGIN'),
			'password' => getenv('RABBIT_PASSWORD'),
			'channel' => getenv('RABBIT_CHANNEL'),
		],
		'mysql' => [
			'host' => getenv('MYSQL_HOST'),
			'port' => (int) getenv('MYSQL_PORT'),
			'user' => getenv('MYSQL_LOGIN'),
			'password' => getenv('MYSQL_PASSWORD'),
			'database' => getenv('MYSQL_DATABASE'),
		],
		'name' => [
			'minLength' => (int) getenv('NAME_MIN_LENGTH'),
			'maxLength' => (int) getenv('NAME_MAX_LENGTH')
		]
	]
);
$applicationBuilder = new \Api\ApplicationBuilder();

$applicationBuilder->build($applicationConfig)->run();
