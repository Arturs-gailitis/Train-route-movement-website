<?php

// izveido lietotāja ierakstu
function createUser($conn, $username, $email, $password) {
    $querry = "INSERT INTO Users (username, email, rights, password) 
    VALUES 
    (:username, :email, :rights, :password)";
    $temp = $conn->prepare($querry);

    $temp->execute([
        "username" => $username,
        "email" => $email,
        "rights" => "lietotajs",
        "password" => $password,
    ]);
}

// pārbauda vai lietotājs eksistē ar konkrētu parametru
function checkUserByParam($conn, $param, $paramName) {
    $querry = "SELECT * FROM USERS WHERE " . $paramName . " = ?";
    $statment = $conn->prepare($querry);
    $statment->execute([$param]);
    return $statment->fetch(PDO::FETCH_ASSOC);
}

// iegūst lietotāja ierakstu skatoties pēc lietotājvārda vai epasta
function getUser($conn, $param) {
    $querry = "SELECT * FROM USERS WHERE username = ? OR email = ?";
    $statment = $conn->prepare($querry);
    $statment->execute([$param, $param]);
    return $statment->fetch(PDO::FETCH_ASSOC);
}

// iegūst visus datus no Users tabulas
function getAllUsers($conn) {
    $querry = 'SELECT * FROM Users';
    $statement = $conn->query($querry);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Izmaina konkrētajam lietotājam tiesības
function changeRights($conn, $id, $rights) {
    $querry = 'UPDATE Users SET rights = ? WHERE id = ?';
    $statment = $conn->prepare($querry);
    $statment->execute([$rights, $id]);
}

// Izdzēš konkrēto ierakstu no Users tabulas
function deleteUser($conn, $id) {
    $querry = 'DELETE FROM Users WHERE id = ?';
    $statement = $conn->prepare($querry);
    $statement->execute([$id]);
}
?>