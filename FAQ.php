<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>About</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="FAQ.css">
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="footer.css">
</head>
<body>
<?php
   require "nav.php";

   ?>
    <section class="FAQCover">
        <div class="FAQtitle">
            <h1>Ask Us Anything</h1>
        </div>
       <div class="FAQcontainer">
    <div class="FAQform">
        <form id="faqForm" onsubmit="sendEmail(event)">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" placeholder="Enter your email address" required><br><br>
            
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" placeholder="Type your message here..." required></textarea><br><br>
            
            <input type="submit" value="Submit">
        </form>
    </div>
    <div class="FAQimage">
        <img src="FAQ.jpg" alt="FAQ Image">
    </div>
</div>

<script>
function sendEmail(event) {
    event.preventDefault(); // Prevent the default form submission

    // Collect form data
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    // Construct the mailto link
    const mailtoLink = `mailto:aliousseily262004@gmail.com?subject=${encodeURIComponent('FAQ Form Submission')}&body=${encodeURIComponent(`
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
document.getElementById('faqForm').addEventListener('submit', sendEmail);
</script>



    </section>
    <div class="faq-container">
        <div class="FAQtitle">
        <h1>Frequently Asked Questions</h1></div>
        
        <div class="faq-item">
            <div class="faq-question">
                1. What is your return policy?
            </div>
            <div class="faq-answer">
                We offer a 30-day hassle-free return policy.  
                 Items must be unworn, unwashed, and have original tags attached. <br> Customers are responsible for return shipping costs unless the item is defective.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                2. What payment methods do you accept?
            </div>
            <div class="faq-answer">
                We accept credit cards (Visa, MasterCard),   Apple Pay, and Google Pay. <br> All transactions are secure and encrypted.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                3. How long does shipping take?
            </div>
            <div class="faq-answer">
                - Standard Shipping: 5-7 business days <br>
                - Express Shipping: 2-3 business days <br>
                - International Shipping: 7-14 business days <br>
                Shipping times may vary depending on destination and current order volume.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                4. Do you offer international shipping?
            </div>
            <div class="faq-answer">
              Unfortunately, we do not offer international shipping at this time.
            </div>
        </div>

        

        <div class="faq-item">
            <div class="faq-question">
                5. What is your exchange policy?
            </div>
            <div class="faq-answer">
                If you receive an item that doesn't fit, you can exchange it within 30 days of purchase. <br>
                 We cover shipping costs for the first exchange. Subsequent exchanges will incur shipping fees.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                6. Do you have a newsletter or loyalty program?
            </div>
            <div class="faq-answer">
              Currently, no. However, you can sign up to receive emails.<br>
                
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                7. Are your clothes sustainably produced?
            </div>
            <div class="faq-answer">
                We are committed to sustainable fashion.
                <br>
                Our clothes are made using eco-friendly materials, ethical manufacturing processes, and we continuously work to reduce our environmental footprint.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                8. How can I contact customer service?
            </div>
            <div class="faq-answer">
                Customer support is available: <br>
                - Email: aliousseily262004@gmail.com.com <br>
                - Phone: +961 81 911752 <br>
                - Live Chat: Available on our website Monday-Friday, 9am-5pm EST
            </div>
        </div>
    </div>
    <?php
   require "footer.php";

   ?>

    <script src="nav.js"></script>
    <script src="FAQ.js"></script>
    
</body>
</html>