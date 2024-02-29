<?php
$conn = new mysqli('localhost', 'root', '', 'rss_feeds', '33065');

if (!$conn) {
    echo "No se pudo realizar la conexión PHP - MySQL";
}

$sql = "SELECT date,  title, url,  description, category, image_url FROM news ORDER BY date DESC";

$result = $conn->query($sql);
$html = '';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>'; 
        $html .= '<td>';
        $html .= "<h2>Fecha:<span class='fecha'>" . $row['date'] . "</span></h2><br>";
        $html .= "<h1><span class='titulo'>" . $row['title'] . "</span></h1><br>";
        $html .= "<h2>URL:</h2> <a href='" . $row['url'] . "'><span class='url'>" . $row['url'] . "</span></a><br>";
        $html .= "<h2>Descripción:</h2> <span class='descripcion'>" . $row['description'] . "</span><br>";
        $html .= "<h2>Categorías:</h2> <br>";
        $html .= "<span class='categoria'>" . $row['category'] . "</span><br>";
        $html .= '</td>';
        $html .= '<td style="vertical-align: middle;">';
        if (!empty($row['image_url'])) {
            $html .= "<img src='" . $row['image_url'] . "' alt='imagen' class='newsImage'>";
        } else {
            $html .= "<img src='no_image.jpg' alt='imagen predeterminada' class='newsImage'>";
        }
        //$html .=  "<img src='" . $row['image_url'] . "' alt='imagen' class='newsImage' style='max-width: 100%; height: auto;'>";
        $html .= '</td>';
        $html .= '</tr>';
    }
    echo $html; 
} else {
    echo "0 resultados";
}

$conn->close();