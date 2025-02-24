</div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="float-right d-none d-sm-block">
    <!-- เพิ่มส่วนแสดงเวลาโหลดหน้า -->
    <div id="loadTime" style="text-align: center; margin-top: 10px; font-size: 14px;"></div>
    </div>
     สงวนลิขสิทธิ์ &copy; <?php echo date('Y')+543 ?><a href=" "> CEIT มหาวิทยาลัยเทคโนโลยีสุรนารี</a>.</strong> All rights reserved.
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="assets/plugins/datatables/jquery.dataTables.js"></script>
<script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="assets/plugins/bootstrap-tagsinput/tagsinput.js?v=1"></script>
<!-- Select2 -->
<script src="assets/plugins/select2/js/select2.full.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.min.js"></script>


<script>
  $(document).ready(function () {
    //$('.sidebar-menu').tree();
    //$('.select2').select2();
    //Initialize Select2 Elements
    $('.select2').select2({
      theme: 'bootstrap4'
    })
  })
</script>

<script>
  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>

<!-- เพิ่มสคริปต์สำหรับวัดเวลาโหลดหน้า -->
<script>
  /// เริ่มต้นวัดเวลาตอนหน้าเริ่มโหลด
  var startTime = performance.now();

// เมื่อหน้าโหลดเสร็จสมบูรณ์
window.onload = function() {
  var endTime = performance.now();
  var loadTime = (endTime - startTime) / 1000; // แปลงมิลลิวินาทีเป็นวินาที

  // แสดงผลเวลาโหลดหน้าใน console
  console.log("หน้าเว็บโหลดเสร็จในเวลา: " + loadTime.toFixed(2) + " วินาที");

  // แสดงผลเวลาโหลดหน้าใน footer
  var loadTimeElement = document.getElementById('loadTime');
  if (loadTimeElement) {
    loadTimeElement.innerHTML = "หน้าเว็บโหลดเสร็จในเวลา: " + loadTime.toFixed(2) + " วินาที";
  }
};
</script>