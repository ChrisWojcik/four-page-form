<?php 
require_once('includes/bootstrap.php');

// Check if this is a valid context
if (!($_SESSION['page3'])) {
    header('Location: index.php');
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

// Extract the billing info to store in the database
$billing_info = $_SESSION['billing_info'];

foreach ($billing_info as $key => $value) {
    ${$key} = $value;
}

// The expiration date will be inserted as a single date field
$cc_exp = $month . '/01/' . $year;

// Add the order to the database
$db->insertOrder($name, $city, $state, $zip, 
                 $cc_exp, $cc_type, $cc_num, 
                 $total, $tax);

$order_id = $db->getLastInsertId();

// Make an entry in the order_item table for each product bought with this order
foreach ($products as $product) {
    $db->insertOrderItem($order_id, $product->getId(), $product->getAmount());
}

// Redact all but the last 4 digits of the credit card number with "*" for display
$hidden_chars = substr($cc_num, 0, -4);
$show_chars = substr($cc_num, -4);
$redacted = '';
for ($i = 1; $i <= strlen($hidden_chars); $i++) {
    $redacted .= '*';
}
$cc_num = $redacted . $show_chars;

// We extracted all the data, so destroy the session
$_SESSION = array();
session_destroy();

?>
<?php require('includes/view/header.php'); ?>
        <h1>Thank You For Your Order!</h1>
        <div id="confirmed">
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
            <a href="index.php">&laquo; Start a New Order</a>
        </div>
<?php require('includes/view/footer.php'); ?>