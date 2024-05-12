<?php

//header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//header("Pragma: no-cache");

$cache_time = 15; // Tiempo en segundos que la página estará en caché  
$cache_filename = "cache_feeds.txt"; // archivo de caché  
$cache_created = (file_exists($cache_filename)) ? filemtime($cache_filename) : 0;  
  
if ((time() - $cache_created) < $cache_time) {  
  readfile($cache_filename);  
  die();  
}
ob_start();
$conn = new mysqli('localhost', 'root', '', 'rss_feeds', '33065');

if (!$conn) {
    echo "No se pudo realizar la conexión PHP - MySQL";
}

$sql = "SELECT date,  title, url,  description, category, image_url FROM news ORDER BY date DESC";
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
file_put_contents($cache_filename,  ob_get_contents());  
$conn->close();
ob_end_flush();  



