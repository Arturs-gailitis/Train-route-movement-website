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
?>