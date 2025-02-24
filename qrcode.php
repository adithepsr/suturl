<?php
if (isset($_GET['code'])) {
    require 'vendor/autoload.php'; // โหลดไลบรารี QR Code
    $code = $_GET['code'];
    $url = 'https://go.sut.ac.th/' . $code;

    // กำหนดขนาดของ QR Code
    $size = 1000;

    // สร้าง QR Code
    \QRcode::png($url, false, QR_ECLEVEL_H, 10, 2);

    // ปรับขนาด QR Code เป็น 1000x1000 พิกเซล
    $qrImage = imagecreatefromstring(ob_get_clean());
    $resizedImage = imagescale($qrImage, $size, $size);

    // ส่งภาพ QR Code ไปยังเบราว์เซอร์
    header('Content-Type: image/png');
    imagepng($resizedImage);
    imagedestroy($qrImage);
    imagedestroy($resizedImage);
}
?>
