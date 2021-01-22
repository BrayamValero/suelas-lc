<?php
// Set Default Timezone
date_default_timezone_set('America/Bogota');
// Set Date Language -> Unix
setlocale(LC_TIME, 'es_ES.UTF-8');
// Set Date Language -> Windows
setlocale(LC_TIME, '');

// Archivo de configuración y conexión a la base de datos
if(strtoupper(substr(PHP_OS, 0 ,3)) == 'WIN'){
    define('DB', array(
        'dsn' => 'mysql:host=localhost;dbname=SUELAS;charset=utf8',
        'user' => 'root',
        'pass' => 'root'
    ));
} else{
    define('DB', array(
        'dsn' => 'mysql:host=localhost;dbname=sabbcizw_suelas;charset=utf8',
        'user' => 'sabbcizw_admin',
        'pass' => 'Suelaslc2020*'
    ));
}

function db_connect(){
    $dsn = DB['dsn'];
    $user = DB['user'];
    $pass = DB['pass'];
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
    
    try {
        $conn = new PDO($dsn, $user, $pass, $options);
        // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $error) {
        echo '<h1>Error al conectarse a la base de datos</h1><br>';
        echo '<p>Error: <mark>' . $error->getMessage() . '</mark></p>';
        die();
    }
}

function db_query($sql, $data = array()){
    try {
        $conn = db_connect();
        $mysql = $conn->prepare($sql);
        $mysql->execute($data);
        $result = $mysql->fetchAll(PDO::FETCH_ASSOC);
        $conn = null;
        return $result;
    } catch (PDOException $e) {
        echo '<p>Error: <mark>' . $e->getMessage() . '</mark></p>';
        die();
    }
}

// db_insert => Insertar un elemento a la base de datos.
function db_insert($sql, $data = array()){
    try {
        $conn = db_connect();
        $result = $conn->prepare($sql)->execute($data);
        $conn = null;
        return $result;
    } catch (PDOException $error) {
        echo $sql . "<br>" . $error->getMessage(); 
        die();
    }
}

// Chequear los inputs.
function test_input($data) {
    if(!empty($data)){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}