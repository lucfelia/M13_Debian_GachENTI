<?php

session_start();

require("template.php");

openHTML();

writeHeader();

$datos = <<<EOD
<section>
	<h2>Formulario de Login</h2>
	<form method="POST" action="login_check.php">
		<p><label for="login_username">Usuario </label><input type="text" name="username" id="login_username" /></p>
		<p><label for="login_password">Contraseña </label><input type="password" name="password" id="login_password" /></p>
		<p><input type="submit" value="Login" /></p>
	</form>
</section>

<section>
	<h2>Registro de nuevo usuario</h2>
	<form method="POST" action="register_check.php">
		<p><label for="reg_username">Usuario </label><input type="text" name="username" id="reg_username" /></p>
		<p><label for="reg_password">Contraseña </label><input type="password" name="password" id="reg_password" /></p>
		<p><label for="reg_password2">Repite contraseña </label><input type="password" name="password2" id="reg_password2" /></p>
		<p><label for="reg_birthdate">Nacimiento </label><input type="date" name="birthdate" id="reg_birthdate" /></p>
		<p><label for="reg_email">Email </label><input type="email" name="email" id="reg_email" /></p>
		<p><input type="submit" value="Registrar" /></p>
	</form>
</section>
EOD;

writeMain($datos);

closeHTML();

?>
