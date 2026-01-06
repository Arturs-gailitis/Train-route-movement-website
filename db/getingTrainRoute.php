<?php

    // iegūst apstāšanās vietu no datubāzes pēc tās nosaukuma
    function getStops($connection,$stopName) {
        $querry = 'SELECT * FROM Stops WHERE name = ?';
        $statement = $connection->prepare($querry);
        $statement->execute([$stopName]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // iegūst apstāšanās un izbraukšanas laiku no datubāzes izmantojot stop_id
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

    // iegūst apstāšanās un izbraukšanas laiku no datubāzes izmantojot trip_id
    function getingStopTime($conn, $trip, $order) {

        if ($order == true) {
            $querry = "SELECT * FROM Stop_Times WHERE trip_id = ? ORDER BY stop_sequence";
        } else {
            $querry = "SELECT * FROM Stop_Times WHERE trip_id = ? ORDER BY stop_sequence DESC";
        }

        $statement = $conn->prepare($querry);
        $statement->execute([$trip['trip_id']]);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst apstāšanās vietu no datubāzes izmantojot stop_id
    function getingStations($conn, $stops) {
        $querry = "SELECT * FROM Stops WHERE stop_id = ?";
        $statement = $conn->prepare($querry);
        $statement->execute([$stops['stop_id']]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // iegūst visus datus no Calendar tabulas
    function getAllCalendar($conn) {
        $querry = 'SELECT * FROM Calendar';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst visus datus no Routes tabulas
    function getAllRoutes($conn) {
        $querry = 'SELECT * FROM Routes';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst visus datus no Stop_times tabulas
    function getAllStopTimes($conn) {
        $querry = 'SELECT * FROM Stop_Times';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst visus datus no Stops tabulas
    function getAllStops($conn) {
        $querry = 'SELECT * FROM Stops';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // iegūst visus datus no Trips tabulas
    function getAllTrips($conn) {
        $querry = 'SELECT * FROM Trips';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
?>