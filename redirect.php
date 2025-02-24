<?php
include 'db_connection.php';

if (isset($_GET['code']) && !empty($_GET['code'])) {
    $short_code = htmlspecialchars($_GET['code'], ENT_QUOTES, 'UTF-8');

    try {
        // ค้นหา URL ต้นฉบับจากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT original_url, views FROM urls WHERE short_url = :short_code");
        $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
        $stmt->execute();
        $url = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($url && filter_var($url['original_url'], FILTER_VALIDATE_URL)) {
            // Increment the view count
            $stmt = $pdo->prepare("UPDATE urls SET views = views + 1 WHERE short_url = :short_code");
            $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
            $stmt->execute();

            // ทำการเปลี่ยนเส้นทางไปยัง URL ต้นฉบับ
            header("Location: " . $url['original_url']);
            exit();
        } else {
            // หากไม่พบลิงก์ย่อหรือ URL ไม่ถูกต้อง
            http_response_code(404);
            echo "<h1>ไม่พบลิงก์ที่คุณขอ</h1>";
        }
    } catch (PDOException $e) {
        // จัดการข้อผิดพลาดของฐานข้อมูล
        http_response_code(500);
        echo "<h1>เกิดข้อผิดพลาดในการเข้าถึงฐานข้อมูล</h1>";
        error_log("Database error: " . $e->getMessage());
    }
} else {
    // หากไม่มีรหัสลิงก์ย่อ
    echo "<h1>กรุณาระบุรหัสลิงก์ย่อ</h1>";
}
?>