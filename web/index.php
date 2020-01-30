<?php 
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	require_once __DIR__.'/../vendor/autoload.php';

	$app = new Silex\Application();

	$app->register(New Silex\Provider\DoctrineServiceProvider(), array(
		'db.options' => array(
			'driver' => 'mysqli',
			'host' => 'localhost',
			'dbname' =>'spgdt_ncc',
			'user' => 'root',
			'password' => '',
		),
	));

	$app->get('/authtenant', function (Silex\Application $app, Request $request) {
		$result = array();

		$sql = "SELECT * FROM auth_tenant ";
		$stmt = $app['db']->query($sql);

		while ($row=$stmt->fetch()) {
			$result[] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'whitelist' => $row['whitelist'],
				'key_public' => $row['key_public'],
				'key_private' => $row['key_private'],
			);
		}

		return $app->json(array('Auth_Tenant' => $result));
	});

	$app->get('/bridgelog', function (Silex\Application $app, Request $request) {
		$result = array();

		$sql = "SELECT * FROM bridge_log LIMIT 100000";
		$stmt = $app['db']->query($sql);

		while ($row=$stmt->fetch()) {
			$result[] = array(
				'id' => $row['id'],
				'msisdn' => $row['msisdn'],
				'called' => $row['called'],
				'lat' => $row['lat'],
				'lng' => $row['lng'],
				'area' => $row['area'],
				'ts' => $row['ts'],
				'tenant' => $row['tenant'],
			);
		}

		return $app->json(array('Bridge_Log' => $result));
	});

	$app->get('/hello/{name}', function($name) use ($app) {
		return 'Hello '.$app->escape($name);
	});

	$app->run();

	// php -S localhost:7070 -t web web/index.php
?>