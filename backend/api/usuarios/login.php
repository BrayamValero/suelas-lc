<?php
session_start();
require_once '../db.php';
$action = strtoupper($_GET['action']);

function generateStrongPassword($length = 6, $add_dashes = false, $available_sets = 'luds'){
	$sets = array();
	if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
	if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
	if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
	if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%&*?';

	$all = '';
	$password = '';
	foreach($sets as $set)
	{
		$password .= $set[array_rand(str_split($set))];
		$all .= $set;
	}

	$all = str_split($all);
	for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

	$password = str_shuffle($password);

	if(!$add_dashes)
		return $password;

	$dash_len = floor(sqrt($length));
	$dash_str = '';
	while(strlen($password) > $dash_len)
	{
		$dash_str .= substr($password, 0, $dash_len) . '-';
		$password = substr($password, $dash_len);
	}
	$dash_str .= $password;
	return $dash_str;
}

switch ($action) {

    case "LOGIN":

        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        
        // Si el usuario y contraseña no se encuentran vacios.
        if( isset($username) && isset($password) ){

            $sql = "SELECT * FROM USUARIOS WHERE CORREO = ?";
            $user = db_query($sql, array($username));

            // Si el correo coincide.
            if(!empty($user)){

                // Si la contraseña coincide.
                if( password_verify($password, $user[0]['CONTRASENA']) ){

                    $_SESSION['ID'] = $user[0]['ID'];
                    $_SESSION['NOMBRE'] = $user[0]['NOMBRE'];
                    $_SESSION['CORREO'] = $user[0]['CORREO'];
                    $_SESSION['ROL'] = $user[0]['CARGO'];
                    $_SESSION['CREADO'] = time();
                    $_SESSION['ULTIMA_ACTIVIDAD'] = time();
        
                    $sql = "UPDATE USUARIOS SET SESSION_ID = ? WHERE ID = ?;";
                    $data = array(session_id(), $user[0]['ID']);
                    db_query($sql, $data);

                    echo "SUCCESS";
                    
                } else {

                    echo "ERROR";

                }

            } else {

                echo "ERROR";

            }

        }

        break;

    case "REGISTER":
        
        $nombre = test_input($_POST['nombre']);
        $telefono = test_input($_POST['telefono']);
        $cedula = test_input($_POST['cedula']);
        $cargo = test_input($_POST['cargo']);
        $correo = test_input($_POST['correo']);
        $password = test_input($_POST['contrasena']);

        if(isset($nombre, $telefono, $cedula, $cargo, $correo, $password)){

            $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
            $data = array($nombre, $cedula, $correo, $telefono, password_hash($password, PASSWORD_DEFAULT), $cargo);
            db_query($sql, $data);
    
            header("Location: ../../../usuarios.php");

        }

        break;

    case "UNLOGIN":

        session_unset();
        session_destroy();

        if (isset($_GET['inactivity']) && $_GET['inactivity'] == 'true') {
            header("Location: ../../../login.php?inactivity=true");
        } else {
            header("Location: ../../../login.php");
        }

    case "RECOVER":

        $toEmail = test_input($_POST['correo']);
        
        // Si el usuario y contraseña no se encuentran vacios.
        if(isset($toEmail)){

            $sql = "SELECT * FROM USUARIOS WHERE CORREO = ?";
            $user = db_query($sql, array($toEmail));

            // Si el correo coincide.
            if(!empty($user)){

                // Genera una contraseña nueva.
                $contraseña = generateStrongPassword();
                $password_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                
                // Se actualiza la nueva contraseña.
                $sql = "UPDATE USUARIOS SET CONTRASENA = ? WHERE CORREO = ?;";
                db_query($sql, array($password_hash, $toEmail));

                $nombre = $user[0]['NOMBRE'];

                // We validate if the email passed is valid (Server Side Validation)
                if (filter_var($toEmail, FILTER_VALIDATE_EMAIL) === false) {
                    // Failed
                    echo "ERROR";
                } else {
                    
                    // Subject & body
                    $subject = 'Suelas LC | Restablecimiento de contraseña.';
                    $body = "Saludos {$nombre}, aquí está tu nueva contraseña, con ella podrás acceder a la aplicación web. La clave es: {$contraseña}
                    ";

                    // Email Headers
                    $headers =  "MIME-Version: 1.0" . "\r\n"; 
                    $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
                    $headers .= "From: Suelas LC <contacto@suelaslc.com>" . "\r\n";

                    echo $headers;

                    if (mail($toEmail, $subject, $body, $headers)) {
                        // Email Sent
                        echo "SUCCESS";
                    } else {
                        // Email Not Sent
                        echo "FAILED";
                    }
                    
                }                    

            } else {

                echo "ERROR";

            }

        }

        break;

}