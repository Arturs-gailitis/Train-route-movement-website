<?php

try {
    require_once __DIR__.'/routes.php';
    require_once __DIR__.'/trips.php';
    require_once __DIR__.'/stops.php';
    require_once __DIR__.'/stopTimes.php';
    require_once __DIR__.'/calendar.php';

} catch (Exception $e) {
    die('Kļūda apstrādājot datus: ' . $e->getMessage());
}

?>