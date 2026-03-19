<?php
require_once 'includes/config.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        
        if ($stmt->execute()) {
            $contact_success = "Message sent successfully! We'll get back to you soon.";
        } else {
            $contact_error = "Failed to send message. Please try again.";
        }
    } else {
        $contact_error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zardari Bachelor Hostel - Best Accommodation</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: #333 !important;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1555854877-bab0e564b8d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .hero-content {
            animation: fadeInUp 1s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Features */
        .features {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .feature-box:hover {
            transform: translateY(-10px);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }
        
        /* Rooms Section */
        .rooms {
            padding: 80px 0;
        }
        
        .room-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .room-card:hover {
            transform: translateY(-10px);
        }
        
        .room-img {
            height: 200px;
            object-fit: cover;
        }
        
        .room-price {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
        }
        
        /* Mess Menu */
        .mess-menu {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .menu-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .menu-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px;
        }
        
        .menu-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        /* Rules */
        .rules {
            padding: 80px 0;
        }
        
        .rule-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .rule-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        /* Contact */
        .contact {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }
        
        /* Footer */
        footer {
            background: #1a1a2e;
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer-link {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: white;
        }
        
        .social-icons a {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            transition: background 0.3s;
        }
        
        .social-icons a:hover {
            background: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building"></i> Royal Hostel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rooms">Rooms</a></li>
                    <li class="nav-item"><a class="nav-link" href="#mess">Mess Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#rules">Rules</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-3">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 hero-content">
                    <h1 class="display-3 fw-bold mb-4">Zardari Bachelor Hostel </h1>
                    <p class="lead mb-4">Experience comfortable and affordable accommodation with modern amenities. Your home away from home.</p>
                    <div class="d-flex gap-3">
                        <a href="#rooms" class="btn btn-primary btn-lg">
                            <i class="fas fa-bed"></i> View Rooms
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2 class="fw-bold">Why Choose Us?</h2>
                    <p class="text-muted">We provide the best hostel experience for students</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <h4>Free WiFi</h4>
                        <p class="text-muted">High-speed internet connectivity available 24/7 for all residents.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h4>Healthy Mess</h4>
                        <p class="text-muted">Nutritious and delicious meals served three times a day.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>24/7 Security</h4>
                        <p class="text-muted">Round-the-clock security with CCTV surveillance.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-parking"></i>
                        </div>
                        <h4>Free Parking</h4>
                        <p class="text-muted">Secure parking space for bikes and cars available.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-cleaning"></i>
                        </div>
                        <h4>Daily Cleaning</h4>
                        <p class="text-muted">Regular cleaning services for all common areas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>24/7 Support</h4>
                        <p class="text-muted">Dedicated warden available for any assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="rooms">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2 class="fw-bold">Our Rooms</h2>
                    <p class="text-muted">Choose from a variety of room options to suit your needs</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card room-card">
                        <img src="https://images.unsplash.com/photo-1630699144867-37acec97df5a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Single Room" class="room-img card-img-top">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Single Room</h5>
                                <span class="room-price">₹8,000/mo</span>
                            </div>
                            <p class="text-muted">Private room with attached bathroom, study table, and wardrobe.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>1 Bed</li>
                                <li><i class="fas fa-check text-success me-2"></i>Attached Bathroom</li>
                                <li><i class="fas fa-check text-success me-2"></i>Study Table</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card room-card">
                        <img src="https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Double Room" class="room-img card-img-top">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Double Room</h5>
                                <span class="room-price">₹6,000/mo</span>
                            </div>
                            <p class="text-muted">Spacious room for two with modern amenities and storage.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>2 Beds</li>
                                <li><i class="fas fa-check text-success me-2"></i>Shared Bathroom</li>
                                <li><i class="fas fa-check text-success me-2"></i>Study Tables</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card room-card">
                        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Triple Room" class="room-img card-img-top">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0">Triple Room</h5>
                                <span class="room-price">₹4,500/mo</span>
                            </div>
                            <p class="text-muted">Economical option for three with all basic facilities.</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>3 Beds</li>
                                <li><i class="fas fa-check text-success me-2"></i>Shared Bathroom</li>
                                <li><i class="fas fa-check text-success me-2"></i>Storage Space</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mess Menu Section -->
    <section id="mess" class="mess-menu">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2 class="fw-bold">Weekly Mess Menu</h2>
                    <p class="text-muted">Healthy and delicious meals prepared daily</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="menu-table">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Breakfast</th>
                                    <th>Lunch</th>
                                    <th>Dinner</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Monday</strong></td>
                                    <td>Puri, Sabzi, Tea</td>
                                    <td>Rice, Dal, Roti, Salad</td>
                                    <td>Roti, Chicken, Rice</td>
                                </tr>
                                <tr>
                                    <td><strong>Tuesday</strong></td>
                                    <td>Paratha, Curd, Tea</td>
                                    <td>Rice, Dal Makhani, Roti</td>
                                    <td>Roti, Paneer, Rice</td>
                                </tr>
                                <tr>
                                    <td><strong>Wednesday</strong></td>
                                    <td>Idli, Sambar, Tea</td>
                                    <td>Rice, Dal, Roti, Pickle</td>
                                    <td>Roti, Fish, Rice</td>
                                </tr>
                                <tr>
                                    <td><strong>Thursday</strong></td>
                                    <td>Omelette, Bread, Tea</td>
                                    <td>Rice, Dal, Roti, Curd</td>
                                    <td>Roti, Veg, Rice, Soup</td>
                                </tr>
                                <tr>
                                    <td><strong>Friday</strong></td>
                                    <td>Poori, Chana, Tea</td>
                                    <td>Rice, Dal, Roti, Papad</td>
                                    <td>Roti, Biryani, Salad</td>
                                </tr>
                                <tr>
                                    <td><strong>Saturday</strong></td>
                                    <td>Dosa, Chutney, Tea</td>
                                    <td>Rice, Dal, Roti, Curry</td>
                                    <td>Roti, Chicken, Rice</td>
                                </tr>
                                <tr>
                                    <td><strong>Sunday</strong></td>
                                    <td>Upma, Fruits, Tea</td>
                                    <td>Biryani, Raita, Papad</td>
                                    <td>Roti, Paneer, Rice, Sweet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rules Section -->
    <section id="rules" class="rules">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2 class="fw-bold">Rules & Regulations</h2>
                    <p class="text-muted">Please follow these rules for a peaceful stay</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <h5>Quiet Hours</h5>
                            <p class="mb-0 text-muted">Maintain silence from 10 PM to 6 AM</p>
                        </div>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-user-friends"></i></div>
                        <div>
                            <h5>Guest Policy</h5>
                            <p class="mb-0 text-muted">No guests allowed inside the hostel premises</p>
                        </div>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-smoking-ban"></i></div>
                        <div>
                            <h5>No Smoking</h5>
                            <p class="mb-0 text-muted">Smoking is strictly prohibited inside the hostel</p>
                        </div>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-bolt"></i></div>
                        <div>
                            <h5>Electricity</h5>
                            <p class="mb-0 text-muted">Use electrical appliances responsibly</p>
                        </div>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div>
                            <h5>Fee Payment</h5>
                            <p class="mb-0 text-muted">Pay monthly fees before the 5th of each month</p>
                        </div>
                    </div>
                    <div class="rule-item">
                        <div class="rule-icon"><i class="fas fa-id-card"></i></div>
                        <div>
                            <h5>ID Card</h5>
                            <p class="mb-0 text-muted">Carry your ID card at all times inside the hostel</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2 class="fw-bold">Contact Us</h2>
                    <p class="text-muted">Have questions? Get in touch with us</p>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form">
                        <?php if (isset($contact_success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $contact_success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($contact_error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $contact_error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter your name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" placeholder="Enter phone number">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject *</label>
                                    <input type="text" class="form-control" name="subject" placeholder="Enter subject" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message *</label>
                                    <textarea class="form-control" name="message" rows="5" placeholder="Enter your message" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="contact_submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h4><i class="fas fa-building"></i> Royal Hostel</h4>
                    <p class="text-muted">Providing comfortable and affordable accommodation for students since 2010.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="footer-link">Home</a></li>
                        <li><a href="#rooms" class="footer-link">Rooms</a></li>
                        <li><a href="#mess" class="footer-link">Mess Menu</a></li>
                        <li><a href="#rules" class="footer-link">Rules</a></li>
                        <li><a href="#contact" class="footer-link">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5>Contact Info</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Hostel Road, City</li>
                        <li><i class="fas fa-phone me-2"></i>+91 9876543210</li>
                        <li><i class="fas fa-envelope me-2"></i>info@royalhostel.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">&copy; 2024 Royal Hostel. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
