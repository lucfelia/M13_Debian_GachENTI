<?php
session_start();

if (!session_id() || !isset($_SESSION['id_user'])){
    header("Location: index.php");
    exit();
}

$id_user = intval($_SESSION['id_user']);

require_once("db_config.php");
require("template.php");

$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_db);
if (!$conn){
    openHTML("", "portada");
    writeHeader();
    writeMain("<article><p>Error DB: no se pudo conectar a la base de datos</p></article>");
    closeHTML();
    exit();
}

$query_user = "SELECT * FROM users WHERE id_user=".$id_user;
$result_user = mysqli_query($conn, $query_user);
if (!$result_user || mysqli_num_rows($result_user) != 1){
    header("Location: index.php");
    exit();
}

$user = mysqli_fetch_assoc($result_user);

openHTML("", "portada");
writeHeader();

$datos = <<<EOD
<article>
    <h2>Dashboard</h2>
    <menu>
        <li><a href="dashboard.php">Perfil</a></li>
        <li><a href="dashboard_cards.php"><strong>Cartas</strong></a></li>
    </menu>
EOD;

if ($user["id_user"] == 1){

    // ROOT: ve todas las cartas
    $datos .= <<<EOD

    <section>
        <h3>Cartas (todas)</h3>
EOD;

    $query_cards = <<<EOD
SELECT cards.id_card, card_templates.card, cards.price, card_templates.image
FROM cards
LEFT JOIN card_templates ON cards.id_card_template=card_templates.id_card_template
ORDER BY cards.id_card DESC
EOD;

} else {

    $datos .= <<<EOD

    <section>
        <h3>Mis cartas</h3>
EOD;

    $query_cards = <<<EOD
SELECT cards.id_card, card_templates.card, cards.price, card_templates.image
FROM cards
LEFT JOIN card_templates ON cards.id_card_template=card_templates.id_card_template
INNER JOIN user_cards ON cards.id_card=user_cards.id_card
WHERE user_cards.id_user={$id_user}
ORDER BY cards.id_card DESC
EOD;
}

$result_cards = mysqli_query($conn, $query_cards);

if($result_cards){
    while($card = mysqli_fetch_assoc($result_cards)){
        $card_name = htmlspecialchars($card["card"]);
        $card_price = isset($card["price"]) ? floatval($card["price"]) : 0;
        $card_img = htmlspecialchars($card["image"]);
        $card_id = intval($card["id_card"]);

        $datos .= <<<EOD
        <article>
            <h4>{$card_name} (ID: {$card_id})</h4>
            <figure>
                <img src="imgs/{$card_img}" />
            </figure>
            <p><strong>Precio:</strong> {$card_price} €</p>
        </article>
EOD;
    }
} else {
    $datos .= "<p>No se han podido cargar las cartas.</p>";
}

$datos .= <<<EOD
    </section>
EOD;

if ($user["id_user"] == 1){

    // Solo ROOT puede añadir templates/cartas
    $query = "SELECT id_card_type,type FROM card_types";
    $result = mysqli_query($conn, $query);
    if (!$result){
        die("ERROR: No hay tipos de cartas.");
    }

    $options_cards_types = "";
    while ($card_type = mysqli_fetch_assoc($result)){
        $options_cards_types .= <<<EOD
    <option value="{$card_type["id_card_type"]}">
        {$card_type["type"]}
    </option>
EOD;
    }

    $query = "SELECT id_card_rarity,rarity FROM card_rarities";
    $result = mysqli_query($conn, $query);
    if (!$result){
        die("ERROR: No hay rarezas.");
    }

    $options_cards_rarities = "";
    while ($card_rarity = mysqli_fetch_assoc($result)){
        $options_cards_rarities .= <<<EOD
    <option value="{$card_rarity["id_card_rarity"]}">
        {$card_rarity["rarity"]}
    </option>
EOD;
    }

    $datos .= <<<EOD
<form method="POST" action="dashboard_card_check.php">
<h2>Añade una carta:</h2>
    <p><label for="card_name">Nombre: </label><input type="text" name="name" id="card_name"></input></p>

    <p><label for="card_type">Tipo: </label>
    <select name="card_type" id="card_type">
        {$options_cards_types}
    </select></p>

    <p><label for="card_price">Precio: </label><input type="number" name="price" id="card_price"></input></p>

    <p><label for="card_image">Imagen: </label><input type="text" name="image" id="card_image"></input></p>

    <p><label for="card_rarity">Rareza: </label>
    <select name="card_rarity" id="card_rarity">
        {$options_cards_rarities}
    </select></p>

    <p><input type="submit" value="Crear carta" /></p>
</form>
EOD;

} else {

    $datos .= <<<EOD
<form method="POST" action="get_lucky.php">
    <p><input type="submit" value="Voy a tener suerte!" /></p>
</form>
EOD;
}

$datos .= <<<EOD
</article>
EOD;

writeMain($datos);

mysqli_close($conn);
closeHTML();
?>