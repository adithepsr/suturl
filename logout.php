<?php
// เริ่มเซสชัน
session_start();

// ลบข้อมูลในเซสชันทั้งหมด
session_unset();

// ทำลายเซสชัน
session_destroy();

// การใช้ SweetAlert2 เพื่อแสดงการแจ้งเตือน
echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>ออกจากระบบ</title>
    <!-- SweetAlert2 CDN -->
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <!-- Google Fonts - Noto Sans Thai -->
    <link href='https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&display=swap' rel='stylesheet'>
    <style>
        body {
            font-family: 'Noto Sans Thai', sans-serif;
        }
    </style>
</head>
<body>
    <script>
        Swal.fire({
            icon: 'success',  // You can change the icon to 'success', 'error', 'warning', etc.
            title: 'ออกจากระบบสำเร็จ',
            text: 'คุณได้ทำการออกจากระบบเรียบร้อยแล้ว',
            imageAlt: 'Custom image',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            // หลังจากแสดง SweetAlert2 แล้วจะเปลี่ยนเส้นทางไปที่ login.php
            window.location.href = 'login.php';
        });
    </script>
</body>
</html>";
exit();
?>