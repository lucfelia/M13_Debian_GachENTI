<?php

require("template.php");

openHTML("Listado de cartas", "cards");

writeHeader();

$contenido = <<<EOD
<section>
	<h2>Listado de cartas</h2>
	<p>Aquí irán las cartas chachis obtenidas de la base de datos.</p>
</section>
EOD;

writeMain($contenido);

closeHTML();

?>