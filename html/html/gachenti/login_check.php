<?php

if (!isset($_POST["username"]) || !isset($_POST["password"])){
	die("ERROR 1: Formulario no enviado.");
}

if (strlen($_POST["username"]) < 3 || strlen($_POST["username"]) > 16){
	die("ERROR 2: Nombre de usuario de tamaño incorrecto.");
}

if (strlen($_POST["password"]) < 4){
	die("ERROR 3: Contraseña muy breve.");
}

$username = addslashes($_POST["username"]);

if($username != $_POST["username"]){
	die("ERROR 4: Caracteres incorrectos en el usuario.");
}

$password = addslashes($_POST["password"]);

if($password != $_POST["password"]){
	die("ERROR 5: Caracteres incorrectos en la contraseña.");
}

$password = md5($password);

$query = <<<EOD
SELECT id_user
FROM users
WHERE
	username='{$username}'
	AND password='{$password}'
EOD;

$conn = mysqli_connect("localhost", "enti", "enti", "gachenti");
if(!$conn){
	die("ERROR DB 1: Error en la conexión.");
}

$result = mysqli_query($conn, $query);
if(!$result){
	die("ERROR DB 2: Error al realizar la petición.");
}

if (mysqli_num_rows($result) != 1){
	die("ERROR 6: Usuario o password erróneos.");
}

$user = mysqli_fetch_assoc($result);

echo $user["id_user"];

?>
