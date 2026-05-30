<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Contact Us – Job Tracker Support</title>

  <meta name="description" content="Contact us for support, feedback, or questions about the Job Application Tracker. Get help managing your job applications, interviews, and career progress.">

  <meta name="keywords" content="job tracker contact, job application support, interview tracker help, career tracking app support, job management tool contact">

  <meta name="author" content="Job Tracker">

  <!-- CSS -->
  <link rel="stylesheet" href="Style/contact-us-style.css">

  <!-- Icons -->
  <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Navbar Include -->
  <?php include "navBar.html"; ?>

  <!-- Contact Section -->
  <section>
    <div class="contact-container">

      <!-- Header -->
      <div class="contact-header">
        <h1>Get in Touch</h1>
        <p>We’d love to hear from you! Have questions, feedback, or just want to say hi? Send us a message below or reach out using the details on the right.</p>
      </div>

      <!-- Form -->
      <div class="contact-form">
        <h2>Send a Message</h2>
        <form action="contact-handler.php" method="post">
          <div class="form-group">
            <i class='bx bxs-user'></i>
            <input type="text" class="txtName" id="name" name="name" placeholder="Enter your name" required>
          </div>
          <div class="form-group">
            <i class='bx bxs-envelope'></i>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
          </div>
          <div class="form-group">
            <i class='bx bxs-message-detail'></i>
            <textarea id="message" name="message" placeholder="Write your message" required></textarea>
          </div>
          <button type="submit" class="submit-btn">Send Message</button>

          <?php if (isset($_SESSION['errorMsg'])): ?>
            <p style="color:red; text-align:center;">
              <?= htmlspecialchars($_SESSION['errorMsg']) ?>
            </p>
            <?php unset($_SESSION['errorMsg']); ?>
          <?php endif; ?>
        </form>
      </div>

      <!-- Info Section -->
      <div class="contact-info">
        <h2>Contact Information</h2>
        <p>We are here to assist you with any queries about XpenStore.  
        Reach out to us through the details below — our team will respond promptly.</p>
        
        <div class="info-item">
          <i class='bx bxs-map'></i>
          <a href="https://www.google.com/maps/place/Sirat+Mohalla,+Datta+Colony,+Kolhapur,+Maharashtra+416008" target="_blank" class="text">Kolhapur, Maharashtra, India, Pin Code : 416012</a>
        </div>
        <div class="info-item">
          <i class='bx bxs-phone'></i>
          <a href="tel:+918600646475" class="text">+91 8600646475</a>
        </div>
        <div class="info-item">
          <i class='bx bxs-envelope'></i>
          <a href="mailto:kitcollegework119@gmail.com" class="text">kitcollegework119@gmail.com</a>
        </div>
        <div class="info-item">
          <i class='bx bxs-time-five'></i>
          <span>Mon - Fri: 9:00 AM - 6:00 PM</span>
        </div>
        
        <!-- Social Icons -->
        <div class="social-icons">
          <a href="#" class="facebook"><i class='bx bxl-facebook'></i></a>
          <a href="#" class="instagram"><i class='bx bxl-instagram'></i></a>
          <a href="mailto:kitcollegework119@gmail.com" class="google"><i class='bx bxl-google'></i></a>
        </div>
      </div>

    </div>
  </section>
  
  <?php include "footer.html"; ?>
</body>
</html>
