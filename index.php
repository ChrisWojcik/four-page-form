<?php 
require_once('includes/bootstrap.php');

// Get all available products for display
$products = $db->getAllProducts();

// The checked off products array starts empty
$selected_products = array();

// Check if we're here as the result of a form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['selected_products'])) {
        
        // Get all products from the database matching the selected ids
        $selected_products = $db->getProductsById($_POST['selected_products']);
        
        // The cart starts empty
        $cart = new Cart();
        
        // Add each product to the cart
        foreach ($selected_products as $product) {
            $cart->addProduct($product->getId(), $product->getAmount());
        }
        
        // Set a sales tax rate
        $cart->addTaxrate(0.08);

        // Add the cart to the session
        $_SESSION['cart'] = $cart;
        
        // Ok to go to page2
        $_SESSION['page1'] = true;
        header('Location: page2.php');
        exit();
    }
    else {
        // No products were selected, Page 1 did not submit properly
        $_SESSION['page1'] = false;
        
        // Remove the cart from the session if it exists
        unset($_SESSION['cart']);
        
        // Redirect back to the same page
        header('Location: index.php');
        exit();
    }
}

// Still more to do if the form has been submitted before
if (isset($_SESSION['page1'])) {
    
    // We may have products in the cart already
    if (isset($_SESSION['cart'])) {
        $selected_products = $_SESSION['cart']->getProdIds();
    }
    
    // If the form was submitted with an error, set the message
    if ($_SESSION['page1'] === false) {
        $errors['cart'] = 'You did not select any products.';
    }
}
?>
<?php require('includes/view/header.php'); ?>
        <h1>Step 1: Choose your products</h1>
        <div id="page1">
            <form action="" method="post" id="page1form">
                <ol>
                    <li id="products" <?php if(isset($errors['cart'])) echo "class=\"error\""; ?>>
                        <fieldset>
                            <legend>Available Products:</legend>
                            <ol>
<?php foreach ($products as $product) :?>
                                <li>
                                    <label for="product-<?php echo $product->getID(); ?>">
                                        <input type="checkbox" name="selected_products[]" value="<?php echo $product->getId(); ?>" id="product-<?php echo $product->getId(); ?>" <?php if (in_array($product->getId(), $selected_products)) echo "checked"; ?>/>
                                        <?php echo $product; ?>
                                    </label>
                                </li>
<?php endforeach; ?>
                            </ol>
                        </fieldset>
<?php if(isset($errors['cart'])) : ?>
                        <p><?php echo $errors['cart']; ?></p>
<?php endif; ?>
                    </li>
                    <li id="submitbutton">
                        <input type="submit" name="sumbitbutton" value="Submit" />
                    </li>
                </ol>
            </form>
        </div>
<?php require('includes/view/footer.php'); ?>