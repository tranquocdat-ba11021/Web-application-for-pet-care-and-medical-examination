    <!-- <script src="../js/owl.carousel.min.js"></script> -->
    <!-- <script src="../js/app.js"></script> -->
    <script src="/ckeditor/ckeditor.js"></script>
    <!-- Thêm các file JS tại đây -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    
    <script>
    CKEDITOR.replace('content', {
      height: 500,

      // Configure your file manager integration. This example uses CKFinder 3 for PHP.
      filebrowserBrowseUrl: '/ckfinder/ckfinder.html',
      filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?type=Images',
      filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
      filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
    });
  </script>

    </body>

    </html>