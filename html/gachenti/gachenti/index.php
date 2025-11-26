<?php
session_start();

require("db_config.php");
require("template.php");

$title = "GachENTI!";
$id = "portada";

openHTML($title, $id);
writeHeader();

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_db);
if (!$conn) {
    $datos = "<p>Error DB: no se pudo conectar a la base de datos</p>";
    writeMain($datos);
    closeHTML();
    exit;
}


$queryMostExpensive = "
SELECT 
    cards.id_card,
    CASE 
        WHEN cards.price IS NULL OR cards.price <= 0 THEN 1 
        ELSE cards.price 
    END AS price,
    card_templates.card AS card_name,
    card_templates.image AS card_image
FROM cards
LEFT JOIN card_templates 
    ON cards.id_card_template = card_templates.id_card_template
ORDER BY price DESC
LIMIT 1
";

$resMost = mysqli_query($conn, $queryMostExpensive);
$mostCard = $resMost ? mysqli_fetch_assoc($resMost) : null;

$queryLast = "
SELECT 
    cards.id_card,
    CASE 
        WHEN cards.price IS NULL OR cards.price <= 0 THEN 1 
        ELSE cards.price 
    END AS price,
    card_templates.card AS card_name,
    card_templates.image AS card_image
FROM cards
LEFT JOIN card_templates 
    ON cards.id_card_template = card_templates.id_card_template
ORDER BY cards.id_card DESC
LIMIT 1
";

$resLast = mysqli_query($conn, $queryLast);
$lastCard = $resLast ? mysqli_fetch_assoc($resLast) : null;

$datos = "<article>";

$datos .= "<section>";
$datos .= "<h2>La carta más cara hasta ahora</h2>";

if ($mostCard) {
    $name = htmlspecialchars($mostCard["card_name"]);
    $price = (float)$mostCard["price"];
    $img = htmlspecialchars($mostCard["card_image"]);

    $datos .= "<article>
        <h3>{$name}</h3>
        <p><strong>Precio:</strong> {$price} €</p>";

    if (!empty($img)) {
        $datos .= "<figure>
            <img src=\"imgs/{$img}\" alt=\"{$name}\" class=\"card_img\" />
        </figure>";
    }

    $datos .= "</article>";
} else {
    $datos .= "<p>Todavía no hay cartas en el sistema.</p>";
}

$datos .= "</section>";

$datos .= "<section>";
$datos .= "<h2>La última carta generada</h2>";

if ($lastCard) {
    $name = htmlspecialchars($lastCard["card_name"]);
    $price = (float)$lastCard["price"];
    $img = htmlspecialchars($lastCard["card_image"]);

    $datos .= "<article>
        <h3>{$name}</h3>
        <p><strong>Precio:</strong> {$price} €</p>";

    if (!empty($img)) {
        $datos .= "<figure>
            <img src=\"imgs/{$img}\" alt=\"{$name}\" class=\"card_img\" />
        </figure>";
    }

    $datos .= "</article>";
} else {
    $datos .= "<p>Todavía no hay cartas generadas.</p>";
}

$datos .= "</section>";

$datos .= "</article>";

writeMain($datos);

mysqli_close($conn);
closeHTML();
?>
