<?php 
require_once('includes/bootstrap.php');

// Check if this is a valid context
if (!($_SESSION['page2'])) {
    header('Location: index.php');
    exit();
}

// If the form was submitted, redirect
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['page3'] = true;
    header('Location: page4.php');
    exit();
}

// We can get started
$cart = $_SESSION['cart'];
$tax = $cart->getTax();
$total = $cart->getTotal();

// Empty array to hold the selected products
$products = array();

// Get each product in the cart from the database
foreach ($cart->getProducts() as $id => $amount) {
    $product = $db->getProductById($id);
    
    // Re-set the amount to what's stored in session
    $product->setAmount($amount);
    
    $products[] = $product;
}

// Extract the billing info
$billing_info = $_SESSION['billing_info'];

foreach ($billing_info as $key => $value) {
    ${$key} = $value;
}

// Redact all but the last 4 digits of the credit card number with "*" for display
$hidden_chars = substr($cc_num, 0, -4);
$show_chars = substr($cc_num, -4);
$redacted = '';
for ($i = 1; $i <= strlen($hidden_chars); $i++) {
    $redacted .= '*';
}
$cc_num = $redacted . $show_chars;


?>
<?php require('includes/view/header.php'); ?>
        <h1>Step 3: Review Your Order</h1>
        <div id="cart">
            <h2>Shopping Cart</h2>
            <table>
<?php foreach ($products as $product) : ?>
                <tr>
                    <th>Product ID: <?php echo $product->getId(); ?></th>
                    <td><?php echo $product->displayDescription(); ?></td>
                    <td class="number">$<?php echo $product->displayAmount(); ?></td>
                </tr>
<?php endforeach; ?>
                <tr>
                    <th colspan="2">Sales tax (8%):</th>
                    <td class="number">$<?php echo number_format($tax, 2); ?></td>
                </tr>
                <tr>
                    <th colspan="2">Total:</th>
                    <td class="number">$<?php echo number_format($total,2); ?></td>
                </tr>
            </table>
            <a href="index.php">&laquo; Edit Cart</a>
        </div>
        <div id="info">
            <h2>Billing Info:</h2>
            <table>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlentities($name); ?></td>
                </tr>
                <tr>
                    <th>City:</th>
                    <td><?php echo htmlentities($city); ?></td>
                </tr>
                <tr>
                    <th>State:</th>
                    <td><?php echo htmlentities($state); ?></td>
                </tr>
                <tr>
                    <th>Zip Code:</th>
                    <td><?php echo htmlentities($zip); ?></td>
                </tr>
                <tr>
                    <th>Credit Card:</th>
                    <td><?php echo htmlentities($cc_type . ': ' . $cc_num); ?></td>
                </tr>
                <tr>
                    <th>Expiration Date:</th>
                    <td><?php echo htmlentities($month . ' / ' . $year); ?></td>
                </tr>
            </table>
            <a href="page2.php">&laquo; Edit Billing Info</a>
            <form action="" method="post" id="page3form">
                <ol>
                    <li id="submitbutton">
                        <input type="submit" name="sumbitbutton" value="Confirm Your Order" />
                    </li>
                </ol>
            </form>
        </div>
<?php require('includes/view/footer.php'); ?>