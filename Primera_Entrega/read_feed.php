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
    $con = new mysqli('localhost', 'root', '','', '33065');
    $con->query("CREATE DATABASE IF NOT EXISTS rss_feeds");
    $con->select_db('rss_feeds');
    $sql = "CREATE TABLE IF NOT EXISTS  news (
        date DATETIME,
        title VARCHAR(255),
        url VARCHAR(255),
        description TEXT,
        category VARCHAR(255),
        image_url VARCHAR(255) 
    )";
    $con->query($sql);
    $con->query("DELETE FROM `news`;");
    return $con;
}

function configFeed($url) {
    $feed = new SimplePie();
    $feed->set_feed_url($url);
    $feed->enable_cache(false);
    $feed->init();
    $feed->handle_content_type();
    return $feed;
}

function createHTMLTable($results) {
    $html = "";
    foreach( $results as $result) {
        $html .= '<tr>'; 
        $html .= '<td>';
        $html .= "<h2>Fecha:<span class='fecha'>" . $result['date'] . "</span></h2><br>";
        $html .= "<h1><span class='titulo'>" . $result['title'] . "</span></h1><br>";
        $html .= "<h2>URL:</h2> <a href='" . $result['url'] . "'><span class='url'>" . $result['url'] . "</span></a><br>";
        $html .= "<h2>Descripción:</h2> <span class='descripcion'>" . $result['description'] . "</span><br>";
        $html .= "<h2>Categorías:</h2> <br>";
        $html .= "<span class='categoria'>" . $result['category'] . "</span><br>";
        $html .= '</td>';
        $html .= '<td style="vertical-align: middle;">';
        if (!empty($result['image_url'])) {
            $html .= "<img src='" . $result['image_url'] . "' alt='imagen' class='newsImage'>";
        } else {
            $html .= "<img src='no_image.jpg' alt='imagen predeterminada' class='newsImage'>";
        }
        //$html .=  "<img src='" . $result['image_url'] . "' alt='imagen' class='newsImage' style='max-width: 100%; height: auto;'>";
        $html .= '</td>';
        $html .= '</tr>';

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
        $image_url = !empty($item->get_enclosure(0)) ? $con->real_escape_string($item->get_enclosure(0)->get_link()) : '';
        // $image_url = $item->get_item_tags('https://www.technologyreview.com/feed/','content')[0]['attribs']['']['url'];
        preg_match_all('/<image>(.*?)<\/image>/s', $item->get_content(), $matches);

        if (!empty($matches[1][0])) {
            preg_match('~<url>([^<]+)</url>~', $matches[1][0], $url_matches);

            if (!empty($url_matches[1])) {
                $image_url = $url_matches[1];
            }
        }
        elseif ($item->get_enclosure(0) instanceof SimplePie_Enclosure) {
            $image_url = $con->real_escape_string($item->get_enclosure(0)->get_link());
        }
        $category = '';
    
        foreach ($item->get_categories() as $cat) {
            $category .= $con->real_escape_string($cat->get_label()) . ', ';
        }
    
        $category = rtrim($category, ', ');
    
        $sql = "INSERT INTO news (date, title, url, description, category, image_url) VALUES ('$date', '$title', '$url', '$description', '$category' ,'$image_url')";
        $con->query($sql);
    }
        
}

