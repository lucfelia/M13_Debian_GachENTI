<?php

if (
	!isset($_POST["username"]) ||
	!isset($_POST["password"]) ||
	!isset($_POST["password2"]) ||
	!isset($_POST["birthyear"]) ||
	!isset($_POST["email"])
) {
	die("ERROR 1: Formulario no enviado.");
}

$username = $_POST["username"];
$password = $_POST["password"];
$password2 = $_POST["password2"];
$birthyear = intval($_POST["birthyear"]);
$email = $_POST["email"];

if (strlen($username) < 3 || strlen($username) > 16){
	die("ERROR 2: Nombre de usuario incorrecto.");
}

if (strlen($password) < 4){
	die("ERROR 3: Contraseña demasiado corta.");
}

if ($password !== $password2){
	die("ERROR 4: Las contraseñas no coinciden.");
}

$yearNow = intval(date("Y"));
if ($yearNow - $birthyear < 18){
	die("ERROR 5: Debes ser mayor de edad.");
}

$username_safe = addslashes($username);
if($username_safe != $username){
	die("ERROR 6: Caracteres inválidos en el usuario.");
}

$email_safe = addslashes($email);
if($email_safe != $email){
	die("ERROR 7: Caracteres inválidos en email.");
}

$password_hash = md5($password);

$conn = mysqli_connect("localhost", "enti", "enti", "gachenti");
if(!$conn){
	die("ERROR DB 1: Error en la conexión.");
}

$query = <<<EOD
INSERT INTO users (username, password, email, birthyear)
VALUES ('{$username_safe}', '{$password_hash}', '{$email_safe}', {$birthyear})
EOD;

$result = mysqli_query($conn, $query);
if(!$result){
	die("ERROR DB 2: Error al insertar.");
}

$new_id = mysqli_insert_id($conn);

echo "Registro completado correctamente. ID asignado: " . $new_id;

?>