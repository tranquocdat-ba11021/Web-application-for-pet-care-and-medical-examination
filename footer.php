<?php
require('./connection.php');

// Fetch settings from the database
$sql = "SELECT `key`, `value` FROM `settings` WHERE `key` IN ('navbar_email', 'navbar_phone', 'navbar_facebook', 'navbar_instagram', 'navbar_twitter')";
$result = $con->query($sql);

$settings = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $settings[$row['key']] = $row['value'];
  }
}

// Set variables for settings
$navbar_email = isset($settings['navbar_email']) ? $settings['navbar_email'] : '';
$navbar_phone = isset($settings['navbar_phone']) ? $settings['navbar_phone'] : '';
$navbar_facebook = isset($settings['navbar_facebook']) ? $settings['navbar_facebook'] : '#';
$navbar_instagram = isset($settings['navbar_instagram']) ? $settings['navbar_instagram'] : '#';
$navbar_twitter = isset($settings['navbar_twitter']) ? $settings['navbar_twitter'] : '#';
?>


<footer id="footer" class="footer">

  <div class="footer-content position-relative">
    <div class="container">
      <div class="row">

        <div class="col-lg-4 col-md-6">
          <div class="footer-info">
            <h3>UpConstruction</h3>
            <p>
              A108 Adam Street <br>
              NY 535022, USA<br><br>
              <strong>Phone:</strong> <?php echo $navbar_phone; ?><br>
              <strong>Email:</strong> <?php echo $navbar_email; ?><br>
            </p>
            <a href="<?php echo $navbar_facebook; ?>"><i class='bx bxl-facebook'></i></a>
            <a href="<?php echo $navbar_instagram; ?>"><i class='bx bxl-instagram'></i></a>
            <a href="<?php echo $navbar_twitter; ?>"><i class='bx bxl-twitter'></i></a>
          </div>
        </div><!-- End footer info column-->

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div><!-- End footer links column-->

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul>
        </div><!-- End footer links column-->

        <div class="col-lg-4 col-md-6">
          <!-- <h4>Our Location</h4> -->
          <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=YOUR_MAP_EMBED_URL" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
        </div><!-- End footer map column-->

      </div>
    </div>
  </div>

  <div class="footer-legal text-center position-relative">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong><span>UpConstruction</span></strong>. All Rights Reserved
      </div>
      <div class="credits">
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> Distributed by <a href="https://themewagon.com">ThemeWagon</a>
      </div>
    </div>
  </div>

</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- <script src="./assets/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="./js/owl.carousel.min.js"></script>
<script src="./js/app.js"></script>

<style>
  @media (min-width: 1400px) {

    .container,
    .container-lg,
    .container-md,
    .container-sm,
    .container-xl,
    .container-xxl {
      max-width: 1140px;
    }
  }
</style>

<script>
  function toggleContent() {
    const section = document.querySelector('.section.about-surgery');
    const moreContent = section.querySelector('.more-content');
    const showMoreBtn = section.querySelector('.show-more-btn');

    moreContent.style.display = moreContent.style.display === 'none' ? 'block' : 'none';
    showMoreBtn.textContent = moreContent.style.display === 'none' ? 'Xem thêm' : 'Ẩn bớt';
  }
</script>
<script>
  // Function để xem trước ảnh khi người dùng chọn ảnh mới
  function previewImage(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function(e) {
        $('.img-thumbnail').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }
</script>


</body>

</html>