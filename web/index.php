<?php 
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	require_once __DIR__.'/../vendor/autoload.php';

	$app = new Silex\Application();

	// Benchmark
	function startTimer() {
		global $starttime;
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
	}
	
	
	function endTimer() {
		global $starttime;
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round(($endtime - $starttime), 4);
		return $totaltime;
	}

	function memory() {
        return round(memory_get_usage()/1048576,2);
	}
	
	function get_cpu_usage() {
        $cmd = "wmic cpu get loadpercentage";
        exec($cmd, $output, $value);

        if ($value==0) {
            return $output[1];
        } else {
            return "Cannot Get CPU Usage!";
        }
    }
	

	$app->register(New Silex\Provider\DoctrineServiceProvider(), array(
		'db.options' => array(
			'driver' => 'mysqli',
			'host' => 'localhost',
			'dbname' =>'spgdt_ncc',
			'user' => 'root',
			'password' => '',
		),
	));

	// SELECT DATA 
	$app->get('/selectsilex/{limit}', function($limit) use ($app) {
		startTimer();
		$result = array();

		for ($i=1; $i <= $limit; $i++) { 
			$sql = "SELECT * FROM bridge_log WHERE id='$i'";
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
		}

		return $app->json(array(
			'result'=>'succes',
			'Bridge_Log'=>$result,
			'request'=>$i,
			'time'=>endTimer()." Second",
			'memory'=>memory().' MB',
			'cpu'=>get_cpu_usage()."%"
		));
	});

	// SEARCH DATA
	$app->get('/searchsilex/{cari}', function($cari) use ($app) {
		startTimer();

		$sql = "SELECT * FROM bridge_log WHERE msisdn='$cari'";
		$stmt = $app['db']->query($sql);
		$result = $stmt->fetchAll();

		return $app->json(
			array(
				'result'=>'succes',
				'Bridge_Log'=>$result,
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			)
		);
	});

	// UPDATE DATA
	$app->put('/updatesilex', function(Silex\Application $app,Request $request) {
		startTimer();

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

		if ($stmt) {
			return $app->json(array(
				'result'=>'succes',
				'request'=>$i,
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		} else {
			return $app->json(array(
				'result'=>'failed',
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		}

	});

	// INSERT DATA
	$app->post('/insertsilex', function(Silex\Application $app,Request $request) {

		startTimer();

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

		if ($stmt) {
			return $app->json(array(
				'result'=>'succes',
				'request'=>$i,
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		} else {
			return $app->json(array(
				'result'=>'failed',
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		}

	});

	// DELETE DATA
	$app->delete('/deletesilex/{jmldel}', function($jmldel) use ($app) {

		startTimer();

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

		if ($stmt) {
			return $app->json(array(
				'result'=>'succes',
				'request'=>$i,
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		} else {
			return $app->json(array(
				'result'=>'failed',
				'time'=>endTimer()." Second",
				'memory'=>memory().' MB',
				'cpu'=>get_cpu_usage()."%"
			));
		}
	});

	$app->get('/hello/{name}', function($name) use ($app) {
		return 'Hello '.$app->escape($name);
	});

	$app->run();

	// php -S localhost:7070 -t web web/index.php
?>