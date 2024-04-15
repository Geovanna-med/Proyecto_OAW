<?php
$conn = new mysqli('localhost', 'root', '', 'rss_feeds', '33065');

if (!$conn) {
    echo "No se pudo realizar la conexión PHP - MySQL";
}

$searchString = $_GET['searchString']; // Obtener el término de búsqueda

$sql = "SELECT * FROM news WHERE title LIKE '%$searchString%' ORDER BY date DESC";
$result = $conn->query($sql);
$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $newsItem = array(
            'date' => $row['date'],
            'title' => $row['title'],
            'url' => $row['url'],
            'description' => $row['description'],
            'category' => $row['category'],
            'image_url' => $row['image_url']
        );
        $data[] = $newsItem;
    }
} else {
    $data['message'] = '0 resultados';
}
$jsonData = json_encode($data);
header('Content-Type: application/json');
echo $jsonData;
$conn->close();

