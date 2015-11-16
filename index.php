<?php

session_start();
$apiURL = "/components/";
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

function getConnection() {
    $DBHost = "YOUR.ORACLE.SERVER"; //Database Host URL or IP Address
    $DBOraclePort = "1525"; //DB Oracle Port
    $DBName = "OracleSID"; //if MySQL use Database Name, if Oracle use Oracle System ID (SID)
    //Connection String
    //$connectionDB = "mysql:host={$DBHost};dbname={$DBName}";
    $connectionDB = "oci:dbname=(DESCRIPTION=(ADDRESS=(HOST={$DBHost})(PROTOCOL=tcp)(PORT={$DBOraclePort}))(CONNECT_DATA=(SID={$DBName})))";
    $DBUser = "username";
    $DBPswd = "password";
    $dbh = new PDO($connectionDB, $DBUser, $DBPswd);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $dbh;
}

$app->post($apiURL . 'users', function() {
		$request = \Slim\Slim::getInstance()->request();
		$jsonArray = array();
		try {
			$db = getConnection();
			$stmt = $db->prepare("SELECT * FROM USERS");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_OBJ);
			if (count($result) >= 1) {
				foreach ($result as $result) {
					$jsonArray[] = $result;
				}
			}
			echo json_encode(array('data' => $jsonArray));
		} catch (PDOException $e) {
			echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
		}
	}
);

$app->get($apiURL . 'users/:id', function($id) {
		$sql = "SELECT * FROM USERS WHERE USER_ID = :idUser";
		$jsonArray = array();
		try {
			$db = getConnection();
			$stmt = $db->prepare($sql);
			$stmt->bindParam(":idUser", $id);
			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if (count($result) >= 1) {
				$jsonArray[] = $result;
			}
			echo json_encode(array('data' => $jsonArray));
		} catch (PDOException $e) {
			echo json_encode(array('success' => false, 'msg' => $e->getMessage()));
		}
	}
);

$app->run();
