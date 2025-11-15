<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="about.css">
    <link rel="stylesheet" href="footer.css">
</head>

    <body>
     
    
        <?php
        require "nav.php"
        ?>
        <div class="parts">
            <div class="about-part1">
                <div class="who">
                    <h2>WHO WE ARE?</h2>
                    <h1>Your Ultimate Fashion Destination</h1>
                    <p>Founded with a passion for style and quality, our online clothing store is dedicated to bringing the latest fashion trends directly to your wardrobe.</p>
                    
                    <div>
                        <div class="Mission">
                            <h4>Our Mission</h4>
                            <p>We aim to provide fashion-forward individuals with high-quality, trendy, and affordable clothing that helps you express your unique style.</p>
                        </div>
                        
                        <div class="Commitment">
                            <h4>Our Commitment</h4>
                            <p>We carefully curate our collections to offer the perfect blend of comfort, style, and current fashion trends across men's, women's, and kids' categories.</p>
                        </div>
                    </div>
    
                    <div class="quality">
                        <div>
                            <h3>20+</h3>
                            <p>Products</p>
                        </div>
                        <div>
                            <h3>100+</h3>
                            <p>Happy Customers</p>
                        </div>
                        <div>
                            <h3>100%</h3>
                            <p>Satisfaction Guarantee</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="about-part2">
                <div class="fashion-image">
                    <img src="fashion-collection.jpg" alt="Fashion Collection">
                </div>
            </div>
        </div>
        
        <section class="why-choose-section">
            <h2 class="text-center">Why Choose Us?</h2>
            <div class="why-choose-us">
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <h3>Quality Assured</h3>
                    <p>Premium quality fabrics and meticulous craftsmanship in every garment.</p>
                </div>
                
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Customer Support</h3>
                    <p>Our dedicated support team is always ready to assist you.</p>
                </div>
                
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Free Shipping</h3>
                    <p>Enjoy free shipping on orders over $50 across our entire collection.</p>
                </div>
                
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3>Easy Returns</h3>
                    <p>Hassle-free 30-day return policy for all our products.</p>
                </div>
                
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Best Price Guarantee</h3>
                    <p>Competitive pricing and regular exclusive discounts for our customers.</p>
                </div>
                
                <div class="choose-item">
                    <div class="icons">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Exclusive Collections</h3>
                    <p>Discover unique pieces that you won't find anywhere else.</p>
                </div>
            </div>
        </section>
        
        <section>
            <div class="text-center">
                <h2>Our Team</h2>
            </div>
            <section class="our-team-section">
               
                <div class="team-members">
                    <div class="team-member">
                        <div class="team-member-image">
                            <img src="ceo.jpg" alt="CEO">
                            <div class="social-links">
                                <a href="#" class="fab fa-linkedin"></a>
                                <a href="#" class="fab fa-twitter"></a>
                            </div>
                        </div>
                        <div class="team-member-info">
                            <h4>Michael Chen</h4>
                            <p>Chief Executive Officer (CEO)</p>
                        </div>
                    </div>
            
                    <div class="team-member">
                        <div class="team-member-image">
                            <img src="director.jpg" alt="Creative Director">
                            <div class="social-links">
                                <a href="#" class="fab fa-linkedin"></a>
                                <a href="#" class="fab fa-instagram"></a>
                            </div>
                        </div>
                        <div class="team-member-info">
                            <h4>Oscar Thompson</h4>
                            <p>Head of Marketing
                            </p>
                        </div>
                    </div>
            
                    <div class="team-member">
                        <div class="team-member-image">
                            <img src="fashion-designer.jpg" alt="Lead Designer">
                            <div class="social-links">
                                <a href="#" class="fab fa-linkedin"></a>
                                <a href="#" class="fab fa-instagram"></a>
                            </div>
                        </div>
                        <div class="team-member-info">
                            <h4>Nathan Ramirez</h4>
                            <p>Customer Service Specialist</p>
                        </div>
                    </div>
            
                    <div class="team-member">
                        <div class="team-member-image">
                            <img src="manager.jpg" alt="Customer Experience">
                            <div class="social-links">
                                <a href="#" class="fab fa-linkedin"></a>
                                <a href="#" class="fab fa-twitter"></a>
                            </div>
                        </div>
                        <div class="team-member-info">
                            <h4>David Kim</h4>
                            <p>Product Designer</p>
                        </div>
                    </div>
                </div>
            </section>
            <div class="container testimonial-section">
                <div class="section-header text-center mb-5">
                    <h2 class="testimonial-title">What Our Clients Say?</h2>
                    <p class="text-muted">Hear from our satisfied customers about their shopping experience</p>
                </div>
            <div class="container testimonial-section">
                
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="customer-card">
                                        <div class="customer-info">
                                            <img src="customer1.jpg" alt="Customer 1" class="customer-image">
                                            <h5 class="mb-0">John Doe</h5>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                        </div>
                                        <p>The selection of clothing was impressive, with a variety of styles that catered to different tastes. I found the website easy to navigate, which made my shopping experience enjoyable.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="customer-card">
                                        <div class="customer-info">
                                            <img src="customer2.jpg" alt="Customer 2" class="customer-image">
                                            <h5 class="mb-0">Jane Smith</h5>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                        </div>
                                        <p>My order arrived promptly, and the quality of the items exceeded my expectations.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="customer-card">
                                        <div class="customer-info">
                                            <img src="customer3.jpg" alt="Customer 3" class="customer-image">
                                            <h5 class="mb-0">Emily Brown</h5>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                        <p>I had a positive experience with the customer support team on the clothes website. When I encountered a minor issue during the checkout process, I reached out for assistance, and I was pleasantly surprised by how quickly they responded.</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="customer-card">
                                        <div class="customer-info">
                                            <img src="customer4.jpg" alt="Customer 4" class="customer-image">
                                            <h5 class="mb-0">Mike Johnson</h5>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                        </div>
                                        <p>Most products I purchased were of good quality, matching their descriptions.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="custom-navigation">
                        <button class="custom-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <i class="fas fa-chevron-left"></i> 
                        </button>
                        <button class="custom-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <i class="fas fa-chevron-right"></i> 
                        </button>
                    </div>
                </div>
            </div>
           <?php
           require "footer.php";

           ?>
        
            
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    

    <script src="nav.js"></script>
</body>
</html>