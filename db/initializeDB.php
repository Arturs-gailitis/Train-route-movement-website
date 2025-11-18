<?php

// izveido savienojumu ar datubāzi
function getConnection($databasePath) {
    try {
        $db = new PDO("sqlite:".$databasePath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "Nedevās savienoties ar datubāzi: " . $e->getMessage();
    }
}

?>