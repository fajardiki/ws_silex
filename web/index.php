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


	// SELECT DATA 
	$app->get('/bridgelog', function (Silex\Application $app, Request $request) {
		$result = array();

		$time_start = microtime(true);

		$sql = "SELECT * FROM bridge_log LIMIT 1000";
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

		$time_end = microtime(true);

        $execution_time = round(($time_end - $time_start), 3);

        $result = number_format($execution_time,3);

		return $app->json(array('Bridge_Log' => $result, "Time" => $execution_time));
	});

	// SEARCH DATA
	$app->get('/searchsilex/{msisdn}', function($msisdn) use ($app) {
		$sql = "SELECT * FROM bridge_log WHERE msisdn='$msisdn'";
		$stmt = $app['db']->query($sql);
		$result = $stmt->fetchAll();

		return $app->json(array('Bridge_Log' => $result));
	});

	// UPDATE DATA
	$app->post('/updatesilex', function(Silex\Application $app,Request $request) {
		$id = $request->get('id');

		$msisdn = $request->get('msisdn');
		$called = $request->get('called');
		$lat = $request->get('lat');
		$lng = $request->get('lng');
		$area = $request->get('area');
		$ts = $request->get('ts');
		$tenant = $request->get('tenant');


		$sql = "UPDATE bridge_log SET msisdn='$msisdn', called='$called', lat='$lat', lng='$lat', area='$area', ts='$ts', tenant='$tenant' WHERE id='$id'";

		$stmt = $app['db']->query($sql);

		if ($stmt) {
			return $app->json(array('message' => 'Update Succes'));
		} else {
			return $app->json(array('message' => 'Update Failed'));
		}

	});

	// INSERT DATA
	$app->post('/insertsilex', function(Silex\Application $app,Request $request) {

		$msisdn = $request->get('msisdn');
		$called = $request->get('called');
		$lat = $request->get('lat');
		$lng = $request->get('lng');
		$area = $request->get('area');
		$ts = $request->get('ts');
		$tenant = $request->get('tenant');

		$sql = "SELECT MAX(id) as id FROM bridge_log";
		$sqlid = $app['db']->query($sql);

		while ($row=$sqlid->fetch()) {
			$id = $row['id'];
		}

		$lastid = $id+1;


		$sqlinsert = "INSERT INTO bridge_log VALUES ('$lastid', '$msisdn', '$called', '$lat', '$lng', '$area', '$ts', '$tenant')";

		$stmt = $app['db']->query($sqlinsert);

		if ($stmt) {
			return $app->json(array('message' => 'Update Succes'));
		} else {
			return $app->json(array('message' => 'Update Failed'));
		}
		// return $app->json(array('message' => $id, 'lastid' => $lastid));

	});

	// DELETE DATA
	$app->get('/deletesilex/{id}', function($id) use ($app) {
		$sql = "DELETE FROM bridge_log WHERE id='$id'";
		$stmt = $app['db']->query($sql);

		if ($stmt) {
			return $app->json(array('message' => 'Delete Succes'));
		} else {
			return $app->json(array('message' => 'Delete Failed'));
		}
	});

	$app->get('/hello/{name}', function($name) use ($app) {
		return 'Hello '.$app->escape($name);
	});

	$app->run();

	// php -S localhost:7070 -t web web/index.php
?>