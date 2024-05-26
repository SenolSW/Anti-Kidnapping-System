<?php include("Feedback.php"); ?>
<?php include("Header.php"); ?>
 
<!----------------Contact Section------------------->
     <div class="contact-section">
        <div class="contact-info">
             <div><i class="fa fa-map-marker"></i>Old Royal Naval College, Park Row, London</div>
             <div><i class="fa fa-envelope-open-o"></i>sw9955i@gre.ac.uk</div>
             <div><i class="fa fa-phone"></i>+44 20 8331 8000</div>
        </div>
         <div class="contact-form">
             <h2>Contact Us</h2>
              <form class="contact" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
               <input type="text" name="name" class="text-box" placeholder="Your Name" required>
               <input type="email" name="email" class="text-box" placeholder="Your Email" required>
               <textarea name="feedback" rows="5" placeholder="Your Message" required></textarea>
               <input type="submit" name="btnFeedback" class="send-btn" value="Send">
               </form>
          </div>
     </div>

<?php include("Footer.php"); ?>