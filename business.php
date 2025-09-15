<?php
require 'db.php';

// Handle filters
$nameFilter = $_GET['business_name'] ?? '';
$addressFilter = $_GET['business_address'] ?? '';

$sql = "SELECT * FROM businesses WHERE status = 1";
$params = [];

if (!empty($nameFilter)) {
    $sql .= " AND business_name LIKE ?";
    $params[] = "%$nameFilter%";
}
if (!empty($addressFilter)) {
    $sql .= " AND business_address LIKE ?";
    $params[] = "%$addressFilter%";
}

$sql .= " ORDER BY id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$businesses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKGST-Verified Businesses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="/images/foundation_icon.png" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">

    <style>
        .business-section {
            margin-top: 95px;

            padding: 20px;

        }



        .container {
            margin-top: 30px;
        }




        /* responsive design from here onwards */




        /* for mobile-nav */
        @media screen and (max-width: 1070px) {
            #navbar {
                display: none;
            }

            .nav-toggle,
            .mobile-nav,
            .nav-overlay {
                display: block;
            }

            .table-responsive table,
            .table-responsive thead,
            .table-responsive tbody,
            .table-responsive th,
            .table-responsive td,
            .table-responsive tr {
                display: block;
                width: 100%;
            }

            .table-responsive thead {
                display: none;
                /* hide header */
            }

            .table-responsive tr {
                margin-bottom: 15px;
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 10px;
                background: #fff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .table-responsive td {
                text-align: left;
                padding: 8px 10px;
                border: none;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                flex-direction: column;
            }

            .table-responsive td::before {
                content: attr(data-label);
                font-weight: bold;
                flex: 1;
                color: #333;
            }

            .table-responsive td:last-child {
                justify-content: flex-start;
            }

            ul {
                padding-left: 1rem;
            }
        }
    </style>


</head>

<body class="bg-light">
    <header>
        <nav id="navbar">
            <img src="images/logo.png" alt="logo" />
            <div class="nav_button">
                <li><a href="index.html" class="nav-link">ABOUT</a></li>
                <li><a href="ourwork.html" class="nav-link">OUR WORK</a></li>
                <li><a href="campaings.html" class="nav-link">CAMPAINGS</a></li>
                <li><a href="business.php" class="nav-link">BUSINESS</a></li>
                <li><a href="getinvolved.html" class="nav-link">GET INVOLVED</a></li>
                <li><a href="contact.html" class="nav-link">CONTACT US</a></li>
                <li><button onclick="window.location.href='donate.html'">DONATE</button></li>
                <li><button onclick="window.location.href='login.php'">Login</button></li>

            </div>
        </nav>

        <!-- moblie nav from here onward -->
        <!-- Hamburger Toggle Icon -->
        <div id="hamburger" class="nav-toggle" onclick="openMobileNav()">☰</div>

        <!-- Overlay (clicking closes mobile nav) -->
        <div id="navOverlay" class="nav-overlay" onclick="closeMobileNav()"></div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileNav" class="mobile-nav">
            <div class="close-btn" onclick="closeMobileNav()">×</div>
            <img src="images/logo.png" alt="Logo" class="logo" />
            <ul>
                <li><a href="index.html" onclick="closeMobileNav()">ABOUT</a></li>
                <li><a href="ourwork.html" onclick="closeMobileNav()">OUR WORK</a></li>
                <li><a href="campaings.html" onclick="closeMobileNav()">CAMPAIGNS</a></li>
                <li><a href="business.php" class="nav-link">BUSINESS</a></li>
                <li><a href="getinvolved.html" onclick="closeMobileNav()">GET INVOLVED</a></li>
                <li><a href="contact.html" onclick="closeMobileNav()">CONTACT US</a></li>
                <div>
                    <a href="donate.html" class="donate-btn" onclick="closeMobileNav()">DONATE</a>

                </div>
                <div>
                    <a href="login.php" class="donate-btn" onclick="closeMobileNav()">Login</a>

                </div>



            </ul>
        </div>

    </header>

    <div class="container business-section">
        <h2 class="mb-4">Verified Businesses</h2>

        <!-- Filter Form -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="business_name" class="form-control"
                    placeholder="Filter by Name" value="<?= htmlspecialchars($nameFilter) ?>">
            </div>
            <div class="col-md-4">
                <input type="text" name="business_address" class="form-control"
                    placeholder="Filter by Address" value="<?= htmlspecialchars($addressFilter) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="business.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <!-- Business Table -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Business Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Details</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($businesses): ?>
                        <?php foreach ($businesses as $b): ?>
                            <tr>
                                <td data-label="ID"><?= $b['id'] ?></td>
                                <td data-label="Business Name"><?= htmlspecialchars($b['business_name']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($b['business_email']) ?></td>
                                <td data-label="Phone"><?= htmlspecialchars($b['business_contact']) ?></td>
                                <td data-label="Address"><?= htmlspecialchars($b['business_address']) ?></td>
                                <td data-label="Details"><?= htmlspecialchars($b['business_details']) ?></td>
                                <td data-label="Image">
                                    <?php if (!empty($b['business_pic'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($b['business_pic']) ?>"
                                            width="60" height="60" class="rounded"
                                            style="cursor:pointer;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#imageModal"
                                            onclick="showImage('uploads/<?= htmlspecialchars($b['business_pic']) ?>')">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No businesses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center">
                    <img id="modalImage" class="rounded shadow" style="max-width:100%; max-height:90vh;">
                </div>
            </div>
        </div>
    </div>


    <footer class="footer">
        <div class="footer-top">
            <div class="footer-message">
                <img src="https://static.wixstatic.com/media/2cdbfc_4493008c02e5438f9213540c254bd076~mv2.png/v1/fill/w_44,h_25,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/13%2C3%20(5).png"
                    alt="Heart Icon">
                <p>All our efforts are made possible only because of your support.</p>
            </div>
            <div class="footer-message">
                <img src="https://static.wixstatic.com/media/2cdbfc_f9080a11c6eb4d41929e85c098f91d53~mv2.png/v1/fill/w_44,h_25,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/13%2C3%20(9).png"
                    alt="Tax Icon">
                <p>Your donations are tax exempted under 80G of the Indian Income Tax Act.</p>
            </div>
            <div class="footer-message">
                <img src="https://static.wixstatic.com/media/2cdbfc_6aa6d0167b084267b90e78b554d6b5b9~mv2.png/v1/fill/w_44,h_25,al_c,q_85,usm_0.66_1.00_0.01,enc_avif,quality_auto/13%2C3%20(8).png"
                    alt="Secure Icon">
                <p>Your donation transactions are completely safe and secure.</p>
            </div>
        </div>


        <div class="footer-main">
            <div class="footer-left">
                <h3>SKGST NGO</h3>
                <p><strong>Email:</strong> connect@marpu.org</p>
                <p><strong>Phone:</strong> 79997801001</p>
                <p><strong>Charity ID:</strong> AAGTM4991A</p>
                <img src="images/logo.png" alt="Logo" class="footer-logo">
            </div>

            <div class="footer-right">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html">About SKGST NGO</a></li>
                    <li><a href="ourwork.html">Our Work</a></li>
                    <li><a href="campaings.html">Campaings</a></li>
                    <li><a href="getinvolved.html">Get Involved</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="social-icons">
            <a href="http://google.com"><i class="fab fa-instagram"></i></a>
            <a href="http://google.com"><i class="fab fa-facebook-f"></i></a>
            <a href="http://google.com"><i class="fab fa-twitter"></i></a>
            <a href="http://google.com"><i class="fab fa-linkedin-in"></i></a>
            <a href="http://google.com"><i class="fab fa-youtube"></i></a>
        </div>

        <div class="footer-bottom">
            <p style="margin-bottom: 0px;">© 2020 by SKGST_NGO | <a href="TCRp.html">Terms, Conditions & Refund Policy</a> | <a
                    href="Privacy.html">Privacy
                    Policy</a></p>
        </div>
    </footer>


    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showImage(src) {
            document.getElementById('modalImage').src = src;
        }


        // scrolling effect for nav bar

        let lastScrollTop = 0;
        const nav = document.querySelector("nav");
        const section1 = document.querySelector(".business-section");

        window.addEventListener("scroll", () => {
            const section1Bottom = section1.getBoundingClientRect().bottom;
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            // Trigger effect after scrolling past half of section1
            if (section1Bottom <= window.innerHeight / 2) {
                if (currentScroll > lastScrollTop) {
                    // Scrolling down
                    // nav.style.top = "-100px";
                    nav.style.top = "-400px";
                } else {
                    // Scrolling up
                    nav.style.top = "0";
                }
            } else {
                // Always show nav while in first half of section1
                nav.style.top = "0";
            }

            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
        });

        // for auto adjusting margin-top for section1 according to nav height
        function adjustSectionMargin() {
            const navbar = document.getElementById("navbar");
            const section = document.querySelector(".business-section");

            if (!navbar || !section) return;

            if (window.innerWidth >= 1070) {
                const navHeight = navbar.offsetHeight;
                section.style.marginTop = navHeight + "px";
            } else {
                // disable completely by resetting to default
                section.style.marginTop = "";
            }
        }

        window.addEventListener("load", adjustSectionMargin);
        window.addEventListener("resize", adjustSectionMargin);
    </script>
</body>

</html>