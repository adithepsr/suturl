<?php
// Start the session
session_start();

// Include the database connection
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit();
}

// Check if data was received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON data received from the form
    $urlData = json_decode($_POST['urlData'], true);

    // Get the user ID from session
    $user_id = $_SESSION['user_id'];

    // Sanitize the data
    $original_url = filter_var($urlData['original'], FILTER_SANITIZE_URL);
    $short_url = filter_var($urlData['short'], FILTER_SANITIZE_STRING);
    $qrcode_image = $urlData['qrcode']; // This is the data URL of the QR Code

    // Validate the sanitized data
    if (filter_var($original_url, FILTER_VALIDATE_URL) === false) {
        echo "Invalid original URL.";
        exit();
    }

    // สร้างชื่อไฟล์สำหรับ QR Code
    $short_code = basename($short_url);
    $qrCodePath = "qrcodes/$short_code.png";

    // แปลง QR Code จาก base64 เป็นไฟล์ PNG
    list($type, $qrcode_image) = explode(';', $qrcode_image);
    list(, $qrcode_image) = explode(',', $qrcode_image);
    $qrcode_image = base64_decode($qrcode_image);
    file_put_contents($qrCodePath, $qrcode_image);

    try {
        // Prepare SQL query to insert URL data into the database
        $stmt = $pdo->prepare("INSERT INTO urls (user_id, original_url, short_url, qr_code_path) 
                               VALUES (:user_id, :original_url, :short_url, :qr_code_path)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':original_url', $original_url, PDO::PARAM_STR);
        $stmt->bindParam(':short_url', $short_url, PDO::PARAM_STR);
        $stmt->bindParam(':qr_code_path', $qrCodePath, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            echo "URL data saved successfully!";
        } else {
            echo "Failed to save URL data.";
        }
    } catch (PDOException $e) {
        // Catch any errors and display them
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No data received.";
}
?>