<?php
    // Temporairement, renvoyer un exemple statique pour vérifier la structure JSON
    $example_data = array(
        'success' => true,
        'events' => array(
            array('title' => 'Événement 1', 'start' => '2023-11-22T12:00:00', 'end' => '2023-11-22T14:00:00'),
            array('title' => 'Événement 2', 'start' => '2023-11-23T10:00:00', 'end' => '2023-11-23T12:00:00'),
        )
    );
    $json_data = json_encode($example_data, JSON_PRETTY_PRINT);
    echo $json_data;
?>