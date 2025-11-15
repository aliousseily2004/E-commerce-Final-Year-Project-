<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact US</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="contact.css">
    
    <link rel="stylesheet" href="footer.css">
</head>
<body>
<?php
   require "nav.php";

   ?>
    <section class="contact-cover">

    <img src="contact-cover.jpeg" alt="">
    <div class="contact-container">
        <h1>Contact AOSTORE Support</h1>
        
        <div class="contact-infos">
            <div class="contact-details">
                <div class="info-block">
                    <h3>Call to Us</h3>
                    <p>We're available 24/7, 7 days a week</p>
                    <p>+961 81 911752</p>
                </div>
                
                <div class="info-block">
                    <h3>Write to Us</h3>
                    <p>Fill out our form and we will contact you within 24 hours.</p>
                    <p class="email-contact">Email: aliousseily262004@gmail.com</p>
                </div>
                
                <div class="info-block">
                    <h3>Headquarter Hours</h3>
                    <p>Monday – Friday: 9:00-20:00</p>
                    <p>Saturday: 11:00 – 15:00</p>
                    <p>Hay Al Abyad, Haret Hreik, Beirut, Lebanon</p>
                </div>
            </div>
            
           <div class="contact-form">
    <form id="contactForm" onsubmit="sendEmail(event)">
        <div class="form-row">
            <div class="form-group half-width">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name" required>
            </div>
                
            <div class="form-group half-width">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
        </div>
                
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="Enter message subject" required>
        </div>
                
        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Type your message here..." required></textarea>
        </div>
                
        <button type="submit" class="submit-btn">Send Message</button>
    </form>
</div>

<script>
function sendEmail(event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect form data
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;

    // Construct the mailto link
    const mailtoLink = `mailto:aliousseily262004@gmail.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(`
Name: ${name}
Email: ${email}

Message:
${message}`)}`;

    // Open the default email client
    window.location.href = mailtoLink;

    // Optional: Reset the form after sending
    event.target.reset();
}

// Attach the event listener to the form
document.getElementById('contactForm').addEventListener('submit', sendEmail);
</script>



     </section>
     <section class="location">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d985.1310205685087!2d35.51673064854376!3d33.849131869654364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2slb!4v1734340694330!5m2!1sen!2slb" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>


     </section>
     <?php
   require "footer.php";

   ?>
    <script src="nav.js"></script>
    
</body>
</html>