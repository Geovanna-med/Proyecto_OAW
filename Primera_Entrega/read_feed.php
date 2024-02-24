<?php

require_once('simplepie-1.8.0/autoloader.php');
error_reporting(0);
// Verificar si se proporciona la URL del feed en el parámetro GET
if (isset($_GET['urls'])) {
    // Obtener la URL del feed del parámetro GET
    $urls = $_GET['urls'];
    $html = "";
    foreach ($urls as $feedUrl) {
        $feed = new SimplePie();
        $feed->set_feed_url($feedUrl);
        $feed->enable_cache(false);
        $feed->init();
        $feed->handle_content_type();
       
        foreach ($feed->get_items() as $item) {
            
            if($item != null){
                $html .= '<tr>'; 
                $html .= '<td>' . $item->get_date('j-F-Y g:i a') . '</td>';
                $html .= '<td>' . $item->get_title() . '</td>';
                $html .= '<td><a href="' . $item->get_permalink() . '"> '. $item->get_permalink() .'</td>';
                $html .= '<td>' . $item->get_description() . '</td>';
                //categories
                //$html .= '<td>';
                $categories = $item->get_categories();
                usort($categories, function($a, $b) {
                    return strcasecmp($a->get_label(), $b->get_label());
                });

                $html .= '<td>';
                foreach ($categories as $category) {
                    $html .= "<p>" . $category->get_label() . "<p>";
                }
                $html .= '</td>';
                $html .= '</tr>';
                /*
                $categories = array();
                foreach ($item->get_categories() as $category) {
                    $categories[] = "<p>" . $category->get_label() . "<p>";
                }
                $html .= implode('', $categories);
                $html .= '</td>';
                $html .= '</tr>';
                */
            }
        }
    }
    echo $html;
} else {
    echo "No se proporcionó la URL del feed.";
}
