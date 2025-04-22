<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["image"])) {
        $targetDir      = "uploads/"; // Folder to save images
        $fileName       = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $imageFileType  = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ["jpg", "jpeg", "png"];
        if (! in_array($imageFileType, $allowedTypes)) {
            echo json_encode(["status" => "error", "message" => "Only JPG, JPEG, and PNG files are allowed."]);
            exit;
        }

        // Validate file size (2MB max)
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            echo json_encode(["status" => "error", "message" => "File size must be less than 2MB."]);
            exit;
        }

        // Upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            echo json_encode(["status" => "success", "message" => "Image uploaded successfully.", "path" => $targetFilePath]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error uploading file."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No file uploaded."]);
    }
}
