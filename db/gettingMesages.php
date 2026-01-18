<?php

    // funkcija ar kuru var ielikt lietotāja ziņu Message datubāzes tabulā
    function insertMessage($conn, $email, $message) {
        $addMessage = "INSERT INTO Messages (email, message) 
            VALUES
        (:email, :message)";
        $add = $conn->prepare($addMessage);
        $add->execute([
            "email" => $email,
            "message" => $message
        ]);
    }

    // Iegūst visus ierakstus no Messages tabulas
    function getAllMessages($conn) {
        $querry = 'SELECT * FROM Messages';
        $statement = $conn->query($querry);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Izdzēš konkrēto ierakstu no Messages tabulas
    function deleteMessage($conn, $id) {
        $querry = 'DELETE FROM Messages WHERE id = ?';
        $statement = $conn->prepare($querry);
        $statement->execute([$id]);
    }
?>