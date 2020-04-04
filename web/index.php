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
	$app->get('/bridgelog/{limit}', function($limit) use ($app) {
		$result = array();

		$time_start = microtime(true);

		$sql = "SELECT * FROM bridge_log LIMIT $limit";
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
        $execution_time = $time_end - $time_start;

		return $app->json(array('Bridge_Log' => $result, "time" => $execution_time));
	});

	// SEARCH DATA
	$app->get('/searchsilex/{cari}', function($cari) use ($app) {
		$time_start = microtime(true);

		$sql = "SELECT * FROM bridge_log WHERE msisdn='$cari'";
		$stmt = $app['db']->query($sql);
		$result = $stmt->fetchAll();

		$time_end = microtime(true);
        $execution_time = $time_end - $time_start;

		return $app->json(array('Bridge_Log' => $result, "time" => $execution_time));
	});

	// UPDATE DATA
	$app->post('/updatesilex', function(Silex\Application $app,Request $request) {
		$time_start = microtime(true);

		$jmlupdate = (int)$request->get('jumlahupdate');
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

		$lastid = $id;
		$maxulang = $lastid-$jmlupdate;

		for ($i=$lastid; $i > $maxulang; $i--) {
			$sql = "UPDATE bridge_log SET msisdn='$msisdn', called='$called', lat='$lat', lng='$lat', area='$area', ts='$ts', tenant='$tenant' WHERE id='$i'";

			$stmt = $app['db']->query($sql);
		}

		$time_end = microtime(true);
		$execution_time = $time_end - $time_start;

		if ($stmt) {
			return $app->json(array('message' => 'Update Succes', 'time' => $execution_time));
		} else {
			return $app->json(array('message' => 'Update Failed', 'time' => $execution_time));
		}

	});

	// INSERT DATA
	$app->post('/insertsilex', function(Silex\Application $app,Request $request) {

		$time_start = microtime(true);

		$msisdn = $request->get('msisdn');
		$called = $request->get('called');
		$lat = $request->get('lat');
		$lng = $request->get('lng');
		$area = $request->get('area');
		$ts = $request->get('ts');
		$tenant = $request->get('tenant');
		$jmlinput = (int)$request->get('jumlahinsert');

		$sql = "SELECT MAX(id) as id FROM bridge_log";
		$sqlid = $app['db']->query($sql);

		while ($row=$sqlid->fetch()) {
			$id = $row['id'];
		}

		$lastid = $id+1;
		$maxulang = $lastid+$jmlinput;

		for ($i=$lastid; $i < $maxulang; $i++) {
			$sqlinsert = "INSERT INTO bridge_log VALUES ('$i', '$msisdn', '$called', '$lat', '$lng', '$area', '$ts', '$tenant')";
			$stmt = $app['db']->query($sqlinsert);
		}

		$time_end = microtime(true);
		$execution_time = $time_end - $time_start;

		if ($stmt) {
			return $app->json(array('message' => 'Update Succes', 'time' => $execution_time));
		} else {
			return $app->json(array('message' => 'Update Failed', 'time' => $execution_time));
		}
		// return $app->json(array('message' => $id, 'lastid' => $lastid));

	});

	// DELETE DATA
	$app->get('/deletesilex/{jmldel}', function($jmldel) use ($app) {

		$time_start = microtime(true);
		$sql = "SELECT MAX(id) as id FROM bridge_log";
		$sqlid = $app['db']->query($sql);

		while ($row=$sqlid->fetch()) {
			$id = $row['id'];
		}
		
		$jml = (int)$jmldel;

		$lastid = $id;
		$maxulang = $lastid-$jml;

		for ($i=$lastid; $i > $maxulang; $i--) {
			$sql = "DELETE FROM bridge_log WHERE id='$i'";
			$stmt = $app['db']->query($sql);
		}

		$time_end = microtime(true);
		$execution_time = $time_end - $time_start;

		if ($stmt) {
			return $app->json(array('message' => 'Delete Succes', 'time' => $execution_time));
		} else {
			return $app->json(array('message' => 'Delete Failed', 'time' => $execution_time));
		}
	});

	$app->get('/hello/{name}', function($name) use ($app) {
		return 'Hello '.$app->escape($name);
	});

	$app->run();

	// php -S localhost:7070 -t web web/index.php
?>