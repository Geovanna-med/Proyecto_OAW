<?php

require_once('simplepie-1.8.0/autoloader.php');
error_reporting(0);
// Verificar si se proporciona la URL del feed en el parámetro GET
if (isset($_GET['urls'])) {
    // Obtener la URL del feed del parámetro GET
    $urls = $_GET['urls'];
    $con = configDatabase();
    foreach ($urls as $feedUrl) {
        $feed = configFeed($feedUrl);
        insertItemsToDB($feed, $con);
    }
    $sql = "SELECT * FROM `news`;";
    $results = $con->query($sql);
    $html = createHTMLTable($results);
    echo $html;
} else {
    echo "No se proporcionó la URL del feed.";
}

function configDatabase() {
    $con = new mysqli('localhost', 'root', '');
    $con->query("CREATE DATABASE IF NOT EXISTS rss_feed");
    $con->select_db('rss_feed');
    $sql = "CREATE TABLE IF NOT EXISTS  news (
        date DATETIME,
        title VARCHAR(255),
        url VARCHAR(255),
        description TEXT,
        category VARCHAR(255)
    )";
    $con->query($sql);
    $con->query("DELETE FROM `news`;");
    return $con;
}

function configFeed($url) {
    $feed = new SimplePie();
    $feed->set_feed_url($url);
    $feed->enable_cache(false);
    $feed->strip_attributes(array('img'));
    $feed->init();
    $feed->handle_content_type();
    return $feed;
}

function createHTMLTable($results) {
    $html = "";
    foreach( $results as $result) {
        $html .= '<tr> <td>'. $result['date']. '</td>';
        $html .= '<td>'. $result['title']. '</td>';
        $html .= '<td><a href="'. $result['url']. '" target="_blank">'. $result['url']. '</a></td>';
        $html .= '<td>'. $result['description']. '</td>';
        $html .= '<td>'. $result['category']. '</td> </tr>';
    }
    return $html;
}

function insertItemsToDB($feed, $con) {
    foreach ($feed->get_items() as $item) {
        $date = $con->real_escape_string($item->get_date('Y-m-d H:i:s'));
        $title = $con->real_escape_string($item->get_title());
        $url = $con->real_escape_string($item->get_permalink());
        $description = $con->real_escape_string($item->get_description());
        $description = str_replace(array("\r", "\n"), ' ', $description);
        
        $category = '';
    
        foreach ($item->get_categories() as $cat) {
            $category .= $con->real_escape_string($cat->get_label()) . ', ';
        }
    
        $category = rtrim($category, ', ');
    
        $sql = "INSERT INTO news (date, title, url, description, category) VALUES ('$date', '$title', '$url', '$description', '$category')";
        $con->query($sql);
    }
}