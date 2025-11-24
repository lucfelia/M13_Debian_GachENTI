<?php
session_start();

require("template.php");

$title = "Cartas";
$id = "cards";

openHTML($title, $id);
writeHeader();

require_once("db_config.php");
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_db);
if (!$conn) {
    $datos = "<p>Error DB: no se pudo conectar a la base de datos</p>";
    writeMain($datos);
    closeHTML();
    exit;
}

$query = <<<EOD
SELECT c.id_card,
       ct.id_card_template,
       ct.card AS card_name,
       ct.description,
       ct.image,
       t.type AS card_type,
       r.rarity AS card_rarity,
       COALESCE(c.price, ct.price, ct.initial_price, 0) AS effective_price
FROM cards c
JOIN card_templates ct ON c.id_card_template = ct.id_card_template
LEFT JOIN card_types t ON ct.id_card_type = t.id_card_type
LEFT JOIN card_rarities r ON ct.id_card_rarity = r.id_card_rarity
ORDER BY effective_price DESC, c.id_card DESC
EOD;

$res = mysqli_query($conn, $query);
if (!$res) {
    $cards_html = "<p>Error al cargar cartas.</p>";
} else {
    $cards_html = "<section><h2>Cartas públicas</h2><ul class='cards-list'>";

    while ($row = mysqli_fetch_assoc($res)) {
        $id_card = intval($row["id_card"]);
        $name = htmlspecialchars($row["card_name"]);
        $price = floatval($row["effective_price"]);
        $desc = nl2br(htmlspecialchars($row["description"] ?? ""));
        $ctype = htmlspecialchars($row["card_type"] ?? "");
        $crarity = htmlspecialchars($row["card_rarity"] ?? "");
        $img = htmlspecialchars($row["image"] ?? "");

        $cards_html .= "<li><article>
            <h3>{$name} (ID carta: {$id_card})</h3>
            <figure><img src='imgs/{$img}' alt='{$name}'/></figure>
            <p><strong>Tipo:</strong> {$ctype} — <strong>Rareza:</strong> {$crarity}</p>
            <p><strong>Precio:</strong> {$price} €</p>
            <p>{$desc}</p>
        </article></li>";
    }
    $cards_html .= "</ul></section>";
}

writeMain($cards_html);
mysqli_close($conn);

closeHTML();
?>