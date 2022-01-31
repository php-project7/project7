<?php
require("../admin/config/connection.php");
$_SESSION['supertotal'] = 0;
$user_id = $_SESSION["id"];


if (isset($_POST['deleteItem'])) {
    $index = $_POST['mahdiIndex'];
    // unset($_SESSION['cart'][$index]);
    // $id = $_POST['hidden-id'];
    try {
        $_command = "DELETE FROM tempcart WHERE id = $index";
        $statement = $pdo->prepare($_command);
        $result = $statement->execute();
    } catch (PDOException $e) {
        echo "error" . $e;
    }
}

if (isset($_POST['subtractQuantity'])) {
    $mahdiQuantity = $_POST['mahdiQuantity'];
    $id = $_POST['hidden-id'];

    if ($mahdiQuantity == 1) {
        //delete from tempcart table
        $_command = "DELETE FROM tempcart WHERE id = $id";
        $statement = $pdo->prepare($_command);
        $statement->execute();
    } else {
        $mahdiQuantity--;
        $_command = "UPDATE tempcart SET quantity='$mahdiQuantity' WHERE id = $id";
        $statement = $pdo->prepare($_command);
        $statement->execute();

        //update database
    }
} elseif (isset($_POST['addQuantity'])) {
    $mahdiQuantity = $_POST['mahdiQuantity'];
    $id = $_POST['hidden-id'];

    if ($mahdiQuantity < 10) {
        //update database
        $mahdiQuantity++;
        $_command = "UPDATE tempcart SET quantity='$mahdiQuantity' WHERE id = $id";
        $statement = $pdo->prepare($_command);
        $statement->execute();
    } else {
        echo "you can't purchase more than 10";
    }
}



try {
    #new code
    $_command = "SELECT * FROM tempcart WHERE user_id='$user_id'";
    $statement = $pdo->prepare($_command);
    $statement->execute();
    $statement->setFetchMode(PDO::FETCH_ASSOC);
    $results = $statement->fetchAll();
    $superTotal = 0;
} catch (PDOException $e) {
    echo "error" . $e->getMessage();
}

function mahdiReload()
{
    echo "<script> window.location.reload();</script>";
    // echo `<meta http-equiv="Location" content="./cart.php">`;
    // echo header("Refresh:0");
}
function mahdiStopReload()
{
    echo "<script> window.stop();</script>";
}

?>

<?php include("../components/Navbar.php") ?>
<div class="page-wrapper">
    <main class="main">
        <div class="page-header text-center" style="background-image: url('../assets/images/page-header-bg.jpg')">
            <div class="container">
                <h1 class="page-title">Shopping Cart<span>Shop</span></h1>
            </div><!-- End .container -->
        </div><!-- End .page-header -->
        <nav aria-label="breadcrumb" class="breadcrumb-nav">
            <div class="container">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
                </ol>
            </div><!-- End .container -->
        </nav><!-- End .breadcrumb-nav -->

        <div class="page-content">
            <div class="cart">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9">
                            <?php if (!empty($results)) { ?>

                            <table class="table table-cart table-mobile">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>discount</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <!-- mahdi table -->
                                <tbody>
                                    <?php
                                        for ($i = 0; $i < count($results); $i++) {
                                            // foreach ($results as $key => $result) {
                                        ?>
                                    <tr>
                                        <td class="product-col">
                                            <div class="product">
                                                <figure class="product-media">
                                                    <!-- <a href="#"> -->
                                                    <img src="<?php echo $results[$i]['img']; ?>" alt="Product image">
                                                    <!-- </a> -->
                                                </figure>

                                                <h3 class="product-title">
                                                    <p></p>
                                                    <a><?php echo $results[$i]['name']; ?></a>
                                                </h3><!-- End .product-title -->
                                            </div><!-- End .product -->
                                        </td>
                                        <td class="price-col">$<?php echo $results[$i]['price']; ?></td>
                                        <td class="quantity-col">
                                            <div class="cart-product-quantity">
                                                <!-- <input type="number" class="form-control" value="1" min="1" max="10" step="1" data-decimals="0" required> -->
                                                <form action="./cart.php" class="form" method="POST">

                                                    <button type="submit" name="subtractQuantity"
                                                        class="form-control">-</button>
                                                    <input type="text" name="mahdiQuantity" class="form-control"
                                                        value="<?php echo $results[$i]['quantity']; ?>" readonly
                                                        style="text-align: center;">
                                                    <button type="submit" name="addQuantity"
                                                        class="form-control">+</button>

                                                    <input type="hidden" name="hidden-id"
                                                        value="<?php echo $results[$i]['id']; ?>">
                                                </form>
                                            </div><!-- End .cart-product-quantity -->
                                        </td>
                                        <td><?php echo $results[$i]['discount']; ?>%</td>
                                        <!-- <td class="total-col">$84.00</td> -->
                                        <td class="total-col">$<?php
                                                                        $price = $results[$i]['price'];
                                                                        $quantity = $results[$i]['quantity'];
                                                                        $discount = $results[$i]['discount'];
                                                                        if ($discount == 0)
                                                                            $discountAmount = 0;
                                                                        else
                                                                            $discountAmount = ($price * $quantity) /  $discount;
                                                                        $finalPrice = ($price * $quantity) - $discountAmount;
                                                                        echo $finalPrice;
                                                                        $superTotal += ($price * $quantity) - $discountAmount;
                                                                        $_SESSION['supertotal'] = $superTotal;

                                                                        try {
                                                                            #push final price into table
                                                                            $mahdi_id = $results[$i]['id'];
                                                                            $_command = "UPDATE tempcart SET final_price ='$finalPrice' WHERE id = $mahdi_id ";
                                                                            $statement = $pdo->prepare($_command);
                                                                            $result = $statement->execute();
                                                                        } catch (PDOException $e) {
                                                                            echo "error" . $e;
                                                                        }
                                                                        ?>
                                        </td>
                                        <td class="remove-col">
                                            <form action="./cart.php" method="POST">
                                                <button type="submit" name="deleteItem" class="btn-remove"
                                                    onclick="return confirm('Are you sure?')">
                                                    <i class="icon-close"></i>
                                                </button>
                                                <input type="hidden" name="mahdiIndex"
                                                    value="<?php echo $results[$i]['id']; ?>">
                                            </form>
                                            <!-- </td> -->
                                    </tr>
                                    <?php
                                            // }
                                        } #end of for loop
                                        ?>
                                </tbody>
                                <?php } else { ?>
                                <h2>Your cart is empty</h2>
                                <?php } ?>
                            </table><!-- End .table table-wishlist -->

                            <div class="cart-bottom">


                            </div><!-- End .cart-bottom -->
                        </div><!-- End .col-lg-9 -->
                        <aside class="col-lg-3">
                            <div class="summary summary-cart">
                                <h3 class="summary-title">Cart Total</h3><!-- End .summary-title -->

                                <table class="table table-summary">
                                    <tbody>
                                        <!-- <tr class="summary-subtotal">
                                                <td>Subtotal:</td>
                                                <td>$160.00</td>
                                            </tr> -->
                                        <tr class="summary-shipping">
                                            <td>Shipping:</td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr class="summary-shipping-row">
                                            <td>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="free-shipping" name="shipping"
                                                        class="custom-control-input">
                                                    <label class="custom-control-label" for="free-shipping">Free
                                                        Shipping</label>
                                                </div><!-- End .custom-control -->
                                            </td>
                                            <td>$0.00</td>
                                        </tr><!-- End .summary-shipping-row -->

                                        <!-- <tr class="summary-shipping-estimate">
                                                <td>Estimate for Your Country<br> <a href="dashboard.html">Change
                                                        address</a></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            End .summary-shipping-estimate
                                         -->


                                        <tr class="summary-total">
                                            <td>Total:</td>
                                            <!-- <td>$160.00</td> -->
                                            <td>$<?php echo $superTotal ?></td>
                                        </tr><!-- End .summary-total -->
                                    </tbody>
                                </table><!-- End .table table-summary -->

                                <a href="checkout.php" class="btn btn-outline-primary-2 btn-order btn-block">PROCEED
                                    TO CHECKOUT</a>
                            </div><!-- End .summary -->


                            <!-- <a href="category.html" class="btn btn-outline-dark-2 btn-block mb-3"><span>CONTINUE
                                        SHOPPING</span><i class="icon-refresh"></i></a> -->
                        </aside><!-- End .col-lg-3 -->
                    </div><!-- End .row -->
                </div><!-- End .container -->
            </div><!-- End .cart -->
        </div><!-- End .page-content -->
    </main><!-- End .main -->

    <?php include("../components/Footer.php") ?>
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

<!-- Mobile Menu -->
<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>

        <form action="#" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Search</label>
            <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..."
                required>
            <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>

        <nav class="mobile-nav">
            <ul class="mobile-menu">
                <li class="active">
                    <a href="index.html">Home</a>

                    <ul>
                        <li><a href="index-1.html">01 - furniture store</a></li>
                        <li><a href="index-2.html">02 - furniture store</a></li>
                        <li><a href="index-3.html">03 - electronic store</a></li>
                        <li><a href="../index-4.html">04 - electronic store</a></li>
                        <li><a href="index-5.html">05 - fashion store</a></li>
                        <li><a href="index-6.html">06 - fashion store</a></li>
                        <li><a href="index-7.html">07 - fashion store</a></li>
                        <li><a href="index-8.html">08 - fashion store</a></li>
                        <li><a href="index-9.html">09 - fashion store</a></li>
                        <li><a href="index-10.html">10 - shoes store</a></li>
                        <li><a href="index-11.html">11 - furniture simple store</a></li>
                        <li><a href="index-12.html">12 - fashion simple store</a></li>
                        <li><a href="index-13.html">13 - market</a></li>
                        <li><a href="index-14.html">14 - market fullwidth</a></li>
                        <li><a href="index-15.html">15 - lookbook 1</a></li>
                        <li><a href="index-16.html">16 - lookbook 2</a></li>
                        <li><a href="index-17.html">17 - fashion store</a></li>
                        <li><a href="index-18.html">18 - fashion store (with sidebar)</a></li>
                        <li><a href="index-19.html">19 - games store</a></li>
                        <li><a href="index-20.html">20 - book store</a></li>
                        <li><a href="index-21.html">21 - sport store</a></li>
                        <li><a href="index-22.html">22 - tools store</a></li>
                        <li><a href="index-23.html">23 - fashion left navigation store</a></li>
                        <li><a href="index-24.html">24 - extreme sport store</a></li>
                    </ul>
                </li>
                <li>
                    <a href="category.html">Shop</a>
                    <ul>
                        <li><a href="category-list.php">Shop List</a></li>
                        <li><a href="category-2cols.html">Shop Grid 2 Columns</a></li>
                        <li><a href="category.html">Shop Grid 3 Columns</a></li>
                        <li><a href="category-4cols.html">Shop Grid 4 Columns</a></li>
                        <li><a href="category-boxed.html"><span>Shop Boxed No Sidebar<span
                                        class="tip tip-hot">Hot</span></span></a></li>
                        <li><a href="category-fullwidth.html">Shop Fullwidth No Sidebar</a></li>
                        <li><a href="product-category-boxed.html">Product Category Boxed</a></li>
                        <li><a href="product-category-fullwidth.html"><span>Product Category Fullwidth<span
                                        class="tip tip-new">New</span></span></a></li>
                        <li><a href="cart.html">Cart</a></li>
                        <li><a href="checkout.html">Checkout</a></li>
                        <li><a href="wishlist.html">Wishlist</a></li>
                        <li><a href="#">Lookbook</a></li>
                    </ul>
                </li>
                <li>
                    <a href="product.php" class="sf-with-ul">Product</a>
                    <ul>
                        <li><a href="product.php">Default</a></li>
                        <li><a href="product-centered.html">Centered</a></li>
                        <li><a href="product-extended.html"><span>Extended Info<span
                                        class="tip tip-new">New</span></span></a></li>
                        <li><a href="product-gallery.html">Gallery</a></li>
                        <li><a href="product-sticky.html">Sticky Info</a></li>
                        <li><a href="product-sidebar.html">Boxed With Sidebar</a></li>
                        <li><a href="product-fullwidth.html">Full Width</a></li>
                        <li><a href="product-masonry.html">Masonry Sticky Info</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#">Pages</a>
                    <ul>
                        <li>
                            <a href="about.html">About</a>
                            <<<<<<< HEAD=======>>>>>>> b9e24503f2f4e5df9788f24cbcd787a2fea64624

                                <ul>
                                    <li><a href="about.html">About 01</a></li>
                                    <li><a href="about-2.html">About 02</a></li>
                                </ul>
                        </li>
                        <li>
                            <a href="contact.html">Contact</a>

                            <ul>
                                <li><a href="contact.html">Contact 01</a></li>
                                <li><a href="contact-2.html">Contact 02</a></li>
                            </ul>
                        </li>
                        <li><a href="../login.html">Login</a></li>
                        <li><a href="faq.html">FAQs</a></li>
                        <li><a href="404.html">Error 404</a></li>
                        <li><a href="coming-soon.html">Coming Soon</a></li>
                    </ul>
                </li>
                <li>
                    <a href="blog.html">Blog</a>

                    <ul>
                        <li><a href="blog.html">Classic</a></li>
                        <li><a href="blog-listing.html">Listing</a></li>
                        <li>
                            <a href="#">Grid</a>
                            <ul>
                                <li><a href="blog-grid-2cols.html">Grid 2 columns</a></li>
                                <li><a href="blog-grid-3cols.html">Grid 3 columns</a></li>
                                <li><a href="blog-grid-4cols.html">Grid 4 columns</a></li>
                                <li><a href="blog-grid-sidebar.html">Grid sidebar</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Masonry</a>
                            <ul>
                                <li><a href="blog-masonry-2cols.html">Masonry 2 columns</a></li>
                                <li><a href="blog-masonry-3cols.html">Masonry 3 columns</a></li>
                                <li><a href="blog-masonry-4cols.html">Masonry 4 columns</a></li>
                                <li><a href="blog-masonry-sidebar.html">Masonry sidebar</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Mask</a>
                            <ul>
                                <li><a href="blog-mask-grid.html">Blog mask grid</a></li>
                                <li><a href="blog-mask-masonry.html">Blog mask masonry</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#">Single Post</a>
                            <ul>
                                <li><a href="single.html">Default with sidebar</a></li>
                                <li><a href="single-fullwidth.html">Fullwidth no sidebar</a></li>
                                <li><a href="single-fullwidth-sidebar.html">Fullwidth with sidebar</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="elements-list.html">Elements</a>
                    <ul>
                        <li><a href="elements-products.html">Products</a></li>
                        <li><a href="elements-typography.html">Typography</a></li>
                        <li><a href="elements-titles.html">Titles</a></li>
                        <li><a href="elements-banners.html">Banners</a></li>
                        <li><a href="elements-product-category.html">Product Category</a></li>
                        <li><a href="elements-video-banners.html">Video Banners</a></li>
                        <li><a href="elements-buttons.html">Buttons</a></li>
                        <li><a href="elements-accordions.html">Accordions</a></li>
                        <li><a href="elements-tabs.html">Tabs</a></li>
                        <li><a href="elements-testimonials.html">Testimonials</a></li>
                        <li><a href="elements-blog-posts.html">Blog Posts</a></li>
                        <li><a href="elements-portfolio.html">Portfolio</a></li>
                        <li><a href="elements-cta.html">Call to Action</a></li>
                        <li><a href="elements-icon-boxes.html">Icon Boxes</a></li>
                    </ul>
                </li>
            </ul>
        </nav><!-- End .mobile-nav -->

        <div class="social-icons">
            <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
            <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
        </div><!-- End .social-icons -->
    </div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->

<!-- Sign in / Register Modal -->
<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="icon-close"></i></span>
                </button>

                <div class="form-box">
                    <div class="form-tab">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab"
                                    aria-controls="signin" aria-selected="true">Sign In</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab"
                                    aria-controls="register" aria-selected="false">Register</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="tab-content-5">
                            <div class="tab-pane fade show active" id="signin" role="tabpanel"
                                aria-labelledby="signin-tab">
                                <form action="#">
                                    <div class="form-group">
                                        <label for="singin-email">Username or email address *</label>
                                        <input type="text" class="form-control" id="singin-email" name="singin-email"
                                            required>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="singin-password">Password *</label>
                                        <input type="password" class="form-control" id="singin-password"
                                            name="singin-password" required>
                                    </div><!-- End .form-group -->

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>LOG IN</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="signin-remember">
                                            <label class="custom-control-label" for="signin-remember">Remember
                                                Me</label>
                                        </div><!-- End .custom-checkbox -->

                                        <a href="#" class="forgot-link">Forgot Your Password?</a>
                                    </div><!-- End .form-footer -->
                                </form>
                                <div class="form-choice">
                                    <p class="text-center">or sign in with</p>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <a href="#" class="btn btn-login btn-g">
                                                <i class="icon-google"></i>
                                                Login With Google
                                            </a>
                                        </div><!-- End .col-6 -->
                                        <div class="col-sm-6">
                                            <a href="#" class="btn btn-login btn-f">
                                                <i class="icon-facebook-f"></i>
                                                Login With Facebook
                                            </a>
                                        </div><!-- End .col-6 -->
                                    </div><!-- End .row -->
                                </div><!-- End .form-choice -->
                            </div><!-- .End .tab-pane -->
                            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                <form action="#">
                                    <div class="form-group">
                                        <label for="register-email">Your email address *</label>
                                        <input type="email" class="form-control" id="register-email"
                                            name="register-email" required>
                                    </div><!-- End .form-group -->

                                    <div class="form-group">
                                        <label for="register-password">Password *</label>
                                        <input type="password" class="form-control" id="register-password"
                                            name="register-password" required>
                                    </div><!-- End .form-group -->

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SIGN UP</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="register-policy"
                                                required>
                                            <label class="custom-control-label" for="register-policy">I agree to the
                                                <a href="#">privacy policy</a> *</label>
                                        </div><!-- End .custom-checkbox -->
                                    </div><!-- End .form-footer -->
                                </form>
                                <div class="form-choice">
                                    <p class="text-center">or sign in with</p>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <a href="#" class="btn btn-login btn-g">
                                                <i class="icon-google"></i>
                                                Login With Google
                                            </a>
                                        </div><!-- End .col-6 -->
                                        <div class="col-sm-6">
                                            <a href="#" class="btn btn-login  btn-f">
                                                <i class="icon-facebook-f"></i>
                                                Login With Facebook
                                            </a>
                                        </div><!-- End .col-6 -->
                                    </div><!-- End .row -->
                                </div><!-- End .form-choice -->
                            </div><!-- .End .tab-pane -->
                        </div><!-- End .tab-content -->
                    </div><!-- End .form-tab -->
                </div><!-- End .form-box -->
            </div><!-- End .modal-body -->
        </div><!-- End .modal-content -->
    </div><!-- End .modal-dialog -->
</div><!-- End .modal -->

<!-- Plugins JS File -->
<script src="../assets/js/jquery.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery.hoverIntent.min.js"></script>
<script src="../assets/js/jquery.waypoints.min.js"></script>
<script src="../assets/js/superfish.min.js"></script>
<script src="../assets/js/owl.carousel.min.js"></script>
<script src="../assets/js/bootstrap-input-spinner.js"></script>
<!-- Main JS File -->
<script src="../assets/js/main.js"></script>
</body>


<!-- molla/cart.html  22 Nov 2019 09:55:06 GMT -->

</html>