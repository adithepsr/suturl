<?php
// ตรวจสอบว่า session เริ่มต้นแล้วหรือยัง
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // เริ่ม session ถ้ายังไม่เริ่ม
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>URL Shortener มหาวิทยาลัยเทคโนโลยีสุรนารี </title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/dist/img/SUT_LOGO.png">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <!-- Google Font: Noto Sans Thai -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;700&display=swap" rel="stylesheet">

  <link href="assets/plugins/bootstrap-tagsinput/tagsinput.css?v=11" rel="stylesheet" type="text/css">

  <!-- ckeditor -->
  <script src="assets/adminlte/bower_components/ckeditor/ckeditor.js"></script>

  <style>
    body {
      font-family: 'Noto Sans Thai', sans-serif;
    }
  </style>
</head>