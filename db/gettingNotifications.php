<?php
    // Iegūst visus ierakstus no Notifications tabulas
    function getAllNotifications($conn) {
        $querry = 'SELECT * FROM Notifications';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // funkcija ar kuru var ielikt paziņojumu Notifications datubāzes tabulā
    function insertNotification($conn, $title, $image, $text, $titleEng, $textEng) {
        $addNotification = "INSERT INTO Notifications (title, image, info, title_Eng, info_Eng) 
            VALUES
        (:title, :image, :info, :title_Eng, :info_Eng)";
        $add = $conn->prepare($addNotification);
        $add->execute([
            "title" => $title,
            "image" => $image,
            "info" => $text,
            "title_Eng" => $titleEng,
            "info_Eng" => $textEng
        ]);
    }

    // Izdzēš konkrēto ierakstu no Notifications tabulas
    function deleteNotification($conn, $id) {
        $querry = 'DELETE FROM Notifications WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }

    // Iegūst konkrētu ierakstu Notifications tabulā pēc tās id
    function getSpecificNotification($conn, $id) {
        $querry = 'SELECT * FROM Notifications WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Atjaunina konkrēto ierakstu Notifications tabulā
    function updateNotification($conn, $id, $title, $image, $text, $titleEng, $textEng) {
        $querry = 'UPDATE Notifications
            SET
                title = ?,
                image = ?,
                info = ?,
                title_Eng = ?,
                info_Eng = ?
            WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$title, $image, $text, $titleEng, $textEng, $id]);
    }

    // iegūst paziņojumus kur tituls vai teksts asociējās ar atslēgvārdu 
    function searchByKeyword($conn, $key) {
        $querry = 'SELECT * FROM Notifications WHERE title LIKE ? OR info LIKE ? OR title_Eng LIKE ? OR info_Eng LIKE ?';
        $statement = $conn->prepare($querry);
        $statement->execute(["%" . $key . "%", "%" . $key . "%" , "%" . $key . "%", "%" . $key . "%"]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
?>