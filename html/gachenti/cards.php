<?php

require("template.php");

openHTML("Listado de cartas", "cards");

writeHeader();

$query = <<<EOD
SELECT cards.id_card, card_templates.card, cards.price FROM cards LEFT JOIN card_templates ON cards.id_card_template = card_templates.id_card_template;
EOD;

$conn = mysqli_connect("localhost", "enti", "enti", "gachenti_db");
if (!$conn) {
	die("Error DB 1: Error en la conexión");
}

$result = mysqli_query($conn, $query);
if (!$result) {
	die("Error DB 2: Error al realizar la petición");
}

if (mysqli_num_rows($result) <= 0) {
	die("Error 1: No hay cartas");
}

$contenido = "<section>\n";
$contenido .= "\t<h2>Listado de cartas</h2>\n";
$contenido .= "\t<ol>\n";

while ($card = mysqli_fetch_assoc($result)) {
	$contenido .= "\t\t<li><strong>CardID:</strong> {$card["id_card"]}; ";
	$contenido .= "<strong>Card:</strong> {$card["card"]}; ";
	$contenido .= "<strong>Price:</strong> {$card["price"]}</li>\n";
}

$contenido .= "\t</ol>\n";
$contenido .= "</section>\n";

writeMain($contenido);

closeHTML();

?>
