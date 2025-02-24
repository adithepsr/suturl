<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        // ลบข้อมูลเฉพาะของผู้ใช้ที่เข้าสู่ระบบ
        $stmt = $pdo->prepare("DELETE FROM urls WHERE id = :id AND user_id = :user_id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'URL ถูกลบแล้ว']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage()]);
    }
}

if (isset($_POST['deleteAll']) && $_POST['deleteAll'] == true) {
    try {
        // ลบข้อมูลทั้งหมดเฉพาะของผู้ใช้ที่เข้าสู่ระบบ
        $stmt = $pdo->prepare("DELETE FROM urls WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'ข้อมูลทั้งหมดถูกลบแล้ว']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูลทั้งหมด: ' . $e->getMessage()]);
    }
}
?>