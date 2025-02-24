<?php
session_start();

// ตรวจสอบว่าผู้ใช้งานล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลบทบาทของผู้ใช้งานจากฐานข้อมูล
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบบทบาท
if ($user['role'] !== 'admin') {
    header("Location: index.php"); // ส่งกลับไปหน้า index ถ้าไม่ใช่ admin
    exit();
}

// ฟังก์ชันสำหรับอัพโหลดข้อมูลผู้ใช้งานจากไฟล์ Excel/CSV
if (isset($_POST['upload_users'])) {
    if ($_FILES['user_file']['error'] == UPLOAD_ERR_OK) {
        require 'vendor/autoload.php'; // โหลด PhpSpreadsheet

        $file = $_FILES['user_file']['tmp_name'];
        // ...existing code...
    } else {
        // Handle file upload error
        echo "Error uploading file.";
    }
}
?>