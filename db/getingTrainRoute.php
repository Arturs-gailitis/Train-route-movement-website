<?php

    // iegūst apstāšanās vietu no datubāzes
    function getStops($connection,$stopName) {
        $querry = 'SELECT * FROM Stops WHERE name = ?';
        $statement = $connection->prepare($querry);
        $statement->execute([$stopName]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // iegūst apstāšanās un izbraukšanas laiku no datubāzes
    function getStopTime($connection, $stopID) {
        $querry = 'SELECT * FROM Stop_Times WHERE stop_id = ?';
        $statement = $connection->prepare($querry);
        $statement->execute([$stopID]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst Trip no datubāzes
    function getTrips($connection, $tripID) {
        $querry = 'SELECT * FROM Trips WHERE trip_id = ?';
        $statement = $connection->prepare($querry);
        $statement->execute([$tripID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // iegūst vilciena maršrutu no datubāzes
    function getRoutes($connection, $routeID) {
        $querry = 'SELECT * FROM Routes WHERE route_id = ?';
        $statement = $connection->prepare($querry);
        $statement->execute([$routeID]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // iegūst maršruta kalendāru no datubāzes
    function getCalendar($connection, $serviceID, $day) {
        $statuss = false;
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        for ($i = 0; $i < count($days); $i++) {
            if ($day == $days[$i]) {
                $statuss = true;
            }
        }
        if ($statuss == true) {
            $querry = 'SELECT * FROM Calendar WHERE service_id = ? AND '. $day .' = 1';
            $statement = $connection->prepare($querry);
            $statement->execute([$serviceID]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        }
    }

    // organizē maršruta masīvu pēc atiešana laika
    function organiseArray($array) {
        usort($array, function($a, $b) {
            return strtotime($a['startTime']) - strtotime($b['startTime']);
        });

        return $array;
    }
?>