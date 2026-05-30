

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Tracker - Manage Your Applications</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style><?php @include __DIR__ . "/Style/about-us-Style.css"; ?></style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</head>

<body>

<?php @include "navBar.html"; ?>

<!-- HERO -->
<section class="hero-section">
    <div class="hero-container">

        <div class="hero-text">
            <span class="tagline">Job Tracker</span>
            <h1>Track Your <span>Job Applications</span> Smarter</h1>

            <p>
                Stay organized in your job search. Track applications, interviews, and offers —
                all in one place. Never miss an opportunity again.
            </p>

            <div class="hero-buttons">
                <a href="user/dashboard.php" class="btn-primary">Dashboard</a>
                <a href="#headSection" class="btn-primary">Explore</a>
            </div>
        </div>

        <div class="hero-image">
            <lottie-player 
                src="img/XlLgen2ilk.json"  
                background="transparent"  
                speed="1"  
                style="width: 100%; max-width: 450px;"  
                loop  
                autoplay>
            </lottie-player>
        </div>

    </div>
</section>

<!-- WHY SECTION -->
<div class="section" id="headSection">
    <div class="text">
        <h2>Why Job Tracker?</h2>
        <p>
            Job hunting can be stressful. This tracker helps you manage all your job applications,
            track status (Applied, Interview, Offer, Rejected), and stay organized without confusion.
        </p>
    </div>
    <div class="image">
        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=800&q=80" alt="Planning and Tracking">
    </div>
</div>

<!-- BUILDER -->
<div class="section">
    <div class="text">
        <h2>Built for Job Seekers</h2>
        <p>
            This platform is designed for students and professionals who want to track their job applications efficiently.
        </p>
        <p>
            Keep everything organized — from applications to interviews — without messy spreadsheets.
        </p>
    </div>
    <div class="image">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f" alt="Job Seekers">
    </div>
</div>

<!-- FEATURES -->
<div class="features">
    <h2>What We Offer</h2>

    <div class="feature-list">

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-briefcase"></i>
                    <p>Track Applications</p>
                </div>
                <div class="feature-back">
                    <p>Save and manage all job applications in one place.</p>
                </div>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-chart-line"></i>
                    <p>Application Status</p>
                </div>
                <div class="feature-back">
                    <p>Track status: Applied, Interview, Offer, Rejected.</p>
                </div>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-calendar-check"></i>
                    <p>Interview Scheduling</p>
                </div>
                <div class="feature-back">
                    <p>Keep track of interview dates and deadlines.</p>
                </div>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-building"></i>
                    <p>Company Details</p>
                </div>
                <div class="feature-back">
                    <p>Store company info, HR details, and notes.</p>
                </div>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-file-alt"></i>
                    <p>Resume Tracking</p>
                </div>
                <div class="feature-back">
                    <p>Track which resume you sent to which company.</p>
                </div>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-inner">
                <div class="feature-front">
                    <i class="fas fa-bolt"></i>
                    <p>Quick Add</p>
                </div>
                <div class="feature-back">
                    <p>Add new job applications in seconds.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- SIMPLE SECTION -->
<div class="section">
    <div class="text">
        <h2>Simple & Powerful</h2>
        <p>
            Start tracking your job applications for free and stay focused on getting hired faster.
        </p>
    </div>
    <div class="image">
        <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80" alt="Simple Workflow">
    </div>
</div>

<!-- SECURITY -->
<div class="section">
    <div class="text">
        <h2>Your Data is Safe</h2>
        <p>
            Your job data is private and securely stored. We never share your information.
        </p>
    </div>
    <div class="image">
        <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80" alt="Data Security">
    </div>
</div>

<!-- CTA -->
<div class="cta">
    <h2>Start Tracking Your Job Applications</h2>
    <a href="user/dashboard.php">Go to Dashboard</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const featureItems = document.querySelectorAll('.feature-item');

  featureItems.forEach(item => {
    let clickCount = 0;

    item.addEventListener('click', () => {
      item.classList.remove('flip-left', 'flip-right');

      clickCount++;

      if (clickCount === 1) {
        item.classList.add('flip-right');
      } else if (clickCount === 2) {
        item.classList.add('flip-left');

        setTimeout(() => {
          const inner = item.querySelector('.feature-inner');

          inner.style.transition = 'none';
          item.classList.remove('flip-left', 'flip-right');

          void inner.offsetWidth;

          inner.style.transition = '';

          clickCount = 0;
        }, 500);
      }
    });
  });
});
</script>

<?php @include "footer.html"; ?>

</body>
</html>