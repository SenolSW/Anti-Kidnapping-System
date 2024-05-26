<link rel="stylesheet" href="stylesheet.css" type="text/css" />
<!-------------footer------------>
 <div class="footer">
 <div class="footer-links">
 <div class="row">
 <div class="footer-col-1">
     <h3>Download Our App</h3>
     <p>Download App for Android and IOS mobile phone</p>
     <div class="app-logo">
     <img src="img/play-store.png" />
     <img src="img/app-store.png" />
     </div>
 </div>
 <div class="footer-col-2">
     <img src="img/Logo.png" />
     <p>A peace of mind designed for modern life</p>
 </div>
 <div class="footer-col-3">
     <h3>Useful Links</h3>
     <ul>
     <li>Home</li>
     <li>Products</li> 
     <li>About Us</li>
     <li>Contact Us</li>
	 <li>Sign In/Up</li>
     </ul>
 </div>
 <div class="footer-col-4">
     <h3>Follow Us</h3>
     <ul>
     <li>Facebook</li>
     <li>Twitter</li>
     <li>Instagram</li>
     <li>YouTube</li>
     </ul>
 </div>
 </div>
  <hr />
  <p class="copyright">® 2023 SalvationJewellers, Inc. All rights reserved. The SalvationJewellers name, logos, and related marks are trademarks of SalvationJewellers, Inc.</p>
 </div>
 </div>
<script>
// When the user scrolls the page, execute myFunction
window.onscroll = function() {myFunction()};

// Get the navbar
var nav_bar = document.getElementById("nav_bar");

// Get the offset position of the navbar
var sticky = nav_bar.offsetTop;

// Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
function myFunction() {
  if (window.pageYOffset >= sticky) {
    nav_bar.classList.add("sticky")
  } else {
    nav_bar.classList.remove("sticky");
  }
}
</script>
<script src="js/script.js"></script>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript" src="vendor/bootstrap-select/bootstrap-select.min.js"></script>

    <script type="text/javascript" src="vendor/datepicker/bootstrap-datepicker.js"></script>
</body>
</html>