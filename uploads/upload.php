<?php
// Permitir peticiones desde tu app móvil o web
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$targetDir = "uploads/";

// Verificar si se envió un archivo
if (isset($_FILES["file"])) {
    $fileName = basename($_FILES["file"]["name"]);
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    
    // Generar un nombre único para evitar que se sobrescriban
    $uniqueName = uniqid("file_", true) . "." . $fileExtension;
    $targetFilePath = $targetDir . $uniqueName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Construir la URL completa que usarás en tu app y base de datos
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $publicUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/" . $targetFilePath;

        echo json_encode([
            "status" => "success",
            "url" => $publicUrl
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No se pudo mover el archivo al servidor."
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No se encontró ningún archivo en la petición."
    ]);
}
?>