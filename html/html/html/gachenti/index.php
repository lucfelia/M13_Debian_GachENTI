<?php

session_start();

require("template.php");
require_once("db_config.php");

openHTML();

writeHeader();

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_db);
if (!$conn) {
    $datos = "<article><p>Error DB: no se pudo conectar a la base de datos</p></article>";
    writeMain($datos);
    closeHTML();
    exit;
}

$query_most_expensive = <<<EOD
SELECT c.id_card,
       ct.card AS card_name,
       ct.image,
       COALESCE(c.price, ct.price, ct.initial_price, 0) AS effective_price
FROM cards c
JOIN card_templates ct ON c.id_card_template = ct.id_card_template
ORDER BY effective_price DESC, c.id_card DESC
LIMIT 1
EOD;

$res_expensive = mysqli_query($conn, $query_most_expensive);
$most_expensive = $res_expensive ? mysqli_fetch_assoc($res_expensive) : null;

$query_latest = <<<EOD
SELECT c.id_card,
       ct.card AS card_name,
       ct.image,
       COALESCE(c.price, ct.price, ct.initial_price, 0) AS effective_price
FROM cards c
JOIN card_templates ct ON c.id_card_template = ct.id_card_template
ORDER BY c.id_card DESC
LIMIT 1
EOD;

$res_latest = mysqli_query($conn, $query_latest);
$latest_card = $res_latest ? mysqli_fetch_assoc($res_latest) : null;

$datos = "<article>";

$datos .= "<section><h2>La carta más cara</h2>";
if ($most_expensive){
    $name = htmlspecialchars($most_expensive["card_name"]);
    $price = floatval($most_expensive["effective_price"]);
    $img = htmlspecialchars($most_expensive["image"]);
    $id_card = intval($most_expensive["id_card"]);
    $datos .= <<<EOD
    <article>
        <h3>{$name} (ID: {$id_card})</h3>
        <figure><img src="imgs/{$img}" alt="{$name}"/></figure>
        <p><strong>Precio:</strong> {$price} €</p>
    </article>
EOD;
} else {
    $datos .= "<p>Aún no hay cartas generadas.</p>";
}
$datos .= "</section>";

$datos .= "<section><h2>La última carta que ha salido</h2>";
if ($latest_card){
    $name = htmlspecialchars($latest_card["card_name"]);
    $price = floatval($latest_card["effective_price"]);
    $img = htmlspecialchars($latest_card["image"]);
    $id_card = intval($latest_card["id_card"]);
    $datos .= <<<EOD
    <article>
        <h3>{$name} (ID: {$id_card})</h3>
        <figure><img src="imgs/{$img}" alt="{$name}"/></figure>
        <p><strong>Precio:</strong> {$price} €</p>
    </article>
EOD;
} else {
    $datos .= "<p>Aún no hay cartas generadas.</p>";
}
$datos .= "</section>";

$datos .= "</article>";

writeMain($datos);

mysqli_close($conn);
closeHTML();

?>