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

    // Iegūst konkrētu ierakstu Calendar tabulā pēc tās id
    function getCalendarByID($conn, $id) {
        $querry = 'SELECT * FROM Calendar WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Iegūst konkrētu ierakstu Routes tabulā pēc tās id
    function getRoutesByID($conn, $id) {
        $querry = 'SELECT * FROM Routes WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Iegūst konkrētu ierakstu Stops tabulā pēc tās id
    function getStopsByID($conn, $id) {
        $querry = 'SELECT * FROM Stops WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Iegūst konkrētu ierakstu Stop_Times tabulā pēc tās id
    function getStopTimesByID($conn, $id) {
        $querry = 'SELECT * FROM Stop_Times WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Iegūst konkrētu ierakstu Trips tabulā pēc tās id
    function getTripsByID($conn, $id) {
        $querry = 'SELECT * FROM Trips WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Atjaunina konkrēto ierakstu Calendar tabulā
    function updateCalendar($conn, $id, $serviceId, $mon, $tue, $wed, $thu, $fri, $sat, $sun, $start, $end) {
        $querry = 'UPDATE Calendar 
            SET
                service_id = ?,
                monday = ?,
                tuesday = ?,
                wednesday = ?,
                thursday = ?,
                friday = ?,
                saturday = ?,
                sunday = ?,
                start_date = ?,
                end_date = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$serviceId, $mon, $tue, $wed, $thu, $fri, $sat, $sun, $start, $end, $id]);
    }

    // Atjaunina konkrēto ierakstu Routes tabulā
    function updateRoutes($conn, $id, $routeId, $agency, $name, $type, $color, $text) {
        $querry = 'UPDATE Routes 
            SET
                route_id = ?,
                agency = ?,
                name = ?,
                type = ?,
                color = ?,
                text_color = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$routeId, $agency, $name, $type, $color, $text, $id]);
    }

    // Atjaunina konkrēto ierakstu Stops tabulā
    function updateStations($conn, $id, $stopId, $name, $lat, $long) {
        $querry = 'UPDATE Stops
            SET
                stop_id = ?,
                name = ?,
                latitude = ?,
                longitude = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$stopId, $name, $lat, $long, $id]);
    }

    // Atjaunina konkrēto ierakstu Stop_Times tabulā
    function updateStopTimes($conn, $id, $tripId, $arr, $dep, $stopId, $seq) {
        $querry = 'UPDATE Stop_Times 
            SET
                trip_id = ?,
                arrival_time = ?,
                departure_time = ?,
                stop_id = ?,
                stop_sequence = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$tripId, $arr, $dep, $stopId, $seq, $id]);
    }

    // Atjaunina konkrēto ierakstu Trips tabulā
    function updateTrips($conn, $id, $routeId, $serviceId, $tripId, $headsign) {
        $querry = 'UPDATE Trips
            SET
                route_id = ?,
                service_id = ?,
                trip_id = ?,
                headsign = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$routeId, $serviceId, $tripId, $headsign, $id]);
    }

    // Izdzēš konkrēto ierakstu no Calendar tabulas 
    function deleteCalendar($conn, $id) {
        $querry = 'DELETE FROM Calendar WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Izdzēš konkrēto ierakstu no Routes tabulas
    function deleteRoute($conn, $id) {
        $querry = 'DELETE FROM Routes WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Izdzēš konkrēto ierakstu no Stop_Times tabulas
    function deleteStopTime($conn, $id) {
        $querry = 'DELETE FROM Stop_Times WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Izdzēš konkrēto ierakstu no Stops tabulas
    function deleteStop($conn, $id) {
        $querry = 'DELETE FROM Stops WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Izdzēš konkrēto ierakstu no Trips tabulas
    function deleteTrip($conn, $id) {
        $querry = 'DELETE FROM Trips WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Pārbauda vai konkrētais ieraksts eksistē ar konkrētu id variantu
    function usedId($conn, $table, $id, $idName) {
        $querry = 'SELECT * FROM '. $table .' WHERE ' . $idName . ' = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result != false) {
            return false;
        } else {
            return true;
        }
    }

    // Izveido ierakstu Calendar tabulā
    function createCalendar($conn, $serviceId, $mon, $tue, $wed, $thu, $fri, $sat, $sun, $start, $end) {
        $querry = 'INSERT INTO Calendar (service_id, monday, tuesday, wednesday, thursday, friday, saturday, 
            sunday, start_date, end_date)
            VALUES
        (:service_id, :monday, :tuesday, :wednesday, :thursday, :friday, :saturday, :sunday, :start_date, :end_date)';
        $statement = $conn->prepare($querry);

        $statement->execute([
            ':service_id' => $serviceId,
            ':monday' => $mon,
            ':tuesday' => $tue,
            ':wednesday' => $wed,
            ':thursday' => $thu,
            ':friday' => $fri,
            ':saturday' => $sat,
            ':sunday' => $sun,
            ':start_date' => $start,
            ':end_date' => $end
        ]);
    }

    // Izveido ierakstu Routes tabulā
    function createRoute($conn, $routeId, $agency, $name, $type, $color, $textColor) {
        $querry = 'INSERT INTO Routes (route_id, agency, name, type, color, text_color)
        VALUES (:route_id, :agency, :name, :type, :color, :text_color)';
        $statement = $conn->prepare($querry);

        $statement->execute([
            'route_id' => $routeId,
            'agency' => $agency,
            'name' => $name,
            'type' => $type,
            'color' => $color,
            'text_color' => $textColor
        ]);
    }

    // Izveido ierakstu Stops tabulā
    function createStops($conn, $stopId, $name, $lat, $long) {
        $querry = 'INSERT INTO Stops (stop_id, name, latitude, longitude) 
        VALUES (:stop_id, :name, :latitude, :longitude)';
        $statement = $conn->prepare($querry);

        $statement->execute([
            'stop_id' => $stopId,
            'name' => $name,
            'latitude' => $lat,
            'longitude' => $long,
        ]);
    }

    // Izveido ierakstu Stop_times tabulā
    function createStopTimes($conn, $tripId, $arrival, $departure, $stopId, $sequence) {
        $querry = 'INSERT INTO Stop_Times (trip_id, arrival_time, departure_time, stop_id, stop_sequence) 
        VALUES (:trip_id, :arrival_time, :departure_time, :stop_id, :stop_sequence)';
        $statement = $conn->prepare($querry);

        $statement->execute([
            'trip_id' => $tripId,
            'arrival_time' => $arrival,
            'departure_time' => $departure,
            'stop_id' => $stopId,
            'stop_sequence' => $sequence,
        ]);
    }

    // Izveido ierakstu Trip tabulā
    function createTrip($conn, $routeId, $serviceId, $tripId, $headsign) {
        $querry = 'INSERT INTO Trips (route_id, service_id, trip_id, headsign) 
        VALUES (:route_id, :service_id, :trip_id, :headsign)';
        $statement = $conn->prepare($querry);

        $statement->execute([
            'route_id' => $routeId,
            'service_id' => $serviceId,
            'trip_id' => $tripId,
            'headsign' => $headsign,
        ]);
    }
?>