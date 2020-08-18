<?php
// Set Default Timezone
date_default_timezone_set('America/Bogota');
// Set Date Language -> Unix
setlocale(LC_TIME, 'es_ES.UTF-8');
// Set Date Language -> Windows
setlocale(LC_TIME, '');

// Archivo de configuración y conexión a la base de datos
define('DB', array(
    'dsn' => 'mysql:host=localhost;dbname=SUELAS;charset=utf8',
    'user' => 'root',
    'pass' => ''
));

function db_connect(){
    $dsn = DB['dsn'];
    $user = DB['user'];
    $pass = DB['pass'];
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

    try {
        $db = new PDO($dsn, $user, $pass, $options);
        // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo '<h1>Error al conectarse a la base de datos</h1><br />';

        echo '<p>Error: <mark>' . $e->getMessage() . '</mark></p>';
        die();
    }
}

function db_query($sql, $data = array()){
    try {
        $db = db_connect();
        $mysql = $db->prepare($sql);
        $mysql->execute($data);
    } catch (PDOException $e) {
        echo '<p>Error: <mark>' . $e->getMessage() . '</mark></p>';
        die();
        // return $mysql->errorInfo();
    }

    $result = $mysql->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    return $result;
}

// Global Function which is going to be used in every Database Manipulation Script.
function test_input($data) {

    // If GET or POST data is not empty, apply trim(), stripslashes(), and htmlspecialchars().
    if(!empty($data)){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}
