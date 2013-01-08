<?php 
require_once('includes/class/FormValidator.php');
require_once('includes/bootstrap.php');

// Check if this is a valid context
if (!($_SESSION['page1'])) {
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

// Initialize some available values for the dropdown boxes
$states = array('NJ','NY','PA','CT');
$cc_types = array('Visa', 'MasterCard', 'American Express', 'Discover');
$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
$years = array('2012','2013','2014','2015','2016','2017','2018');

// Set default values for each field
$name = '';
$city = '';
$state = '---';
$zip = '';
$cc_type = '---';
$month = '---';
$year = '---';

// Validate the form if it's been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Remove any previous billing info in case we've been here before
    unset($_SESSION['billing_info']);
	
	// Supply validation rules
	$validator = new FormValidator;	
	$validator->addRule('name', 'Field is required.', 'required');
	$validator->addRule('city', 'Field is required.', 'required');
	$validator->addRule('state', 'You did not select a state.', 'in_array', $states);
	$validator->addRule('zip', 'Field is required.', 'required');
	$validator->addRule('zip', 'You did not supply a valid zip code.', 'zip');
	$validator->addRule('cc_type', 'You did not select a card type.', 'in_array', $cc_types);
	$validator->addRule('cc_num', 'Field is required.', 'required');
	$validator->addRule('cc_num', 'You did not supply a valid card number.', 'creditcard');
	$validator->addRule('cc_exp', 'You did not supply a valid date.', 'callback', 'valid_cc_exp');

	// Callback function for a combination of two form fields
	function valid_cc_exp() {
		$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
		$years = array('2012','2013','2014','2015','2016','2017','2018');
		
		if (in_array($_POST['month'], $months) && in_array($_POST['year'], $years)) {
			return true;
		}
		return false;
	}
	
	// Validate the form
	$validator->addEntries($_POST);
	$validator->validate();
	
	// Add the form entries to a session variable
    $_SESSION['billing_info'] = $validator->getEntries();

	if ($validator->foundErrors()) {
		$_SESSION['page2'] = false;
		$_SESSION['errors'] = $validator->getErrors();
        header('Location: page2.php');
        exit();
	}
	else {
		$_SESSION['page2'] = true;
		unset($_SESSION['errors']);
        header('Location: page3.php');
        exit();
	}
}

// Still more to do if the form has been submitted before
if(isset($_SESSION['page2'])) {

    // If it's been submitted, there is billing info already in the session
    $billing_info = $_SESSION['billing_info'];
    
    // If the page was submitted with errors, set the error messages
    if ($_SESSION['page2'] === false) {
        $errors = $_SESSION['errors'];
    }
	
	// Use variable variables to change the default values to the stored ones
    foreach ($billing_info as $key => $value) {
        ${$key} = $value;
    }
}
?>
<?php require('includes/view/header.php'); ?>
        <h1>Step 2: Add Your Billing Info</h1>
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
            <h2>Bill My Order To:</h2>
            <p>Note: You can test the credit card validation with - Visa: 4111111111111111</p>
            <form action="" method="post" id="page2form">
                <ol>
                    <li <?php if(isset($errors['name'])) echo "class=\"error\"";?>>
                        <label for="name">Name:</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlentities($name); ?>" />
<?php if(isset($errors['name'])) : ?>
                        <p><?php echo $errors['name']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['city'])) echo "class=\"error\"";?>>
                        <label for="city">City:</label>
                        <input type="text" name="city" id="city" value="<?php echo htmlentities($city); ?>" />
<?php if(isset($errors['city'])) : ?>
                        <p><?php echo $errors['city']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['state'])) echo "class=\"error\"";?>>
                        <label for="state">Select your state:</label>
                        <select name="state" id="state">
                            <option <?php if ($state == '---') echo "selected "; ?>value="---">State</option>
<?php foreach($states as $s) : ?>
                            <option <?php if ($state == $s) echo "selected "; ?>value="<?php echo $s; ?>"><?php echo $s; ?></option>
<?php endforeach; ?>
                        </select>
<?php if(isset($errors['state'])) : ?>
                        <p><?php echo $errors['state']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['zip'])) echo "class=\"error\"";?>>
                        <label for="zip">Zip Code:</label>
                        <input type="text" name="zip" id="zip" value="<?php echo htmlentities($zip); ?>" />
<?php if(isset($errors['zip'])) : ?>
                        <p><?php echo $errors['zip']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['cc_type'])) echo "class=\"error\"";?>>
                        <label for="cc_type">Credit Card Type:</label>
                        <select name="cc_type" id="cc_type">
                            <option <?php if ($cc_type == '---') echo "selected "; ?>value="---">Please select</option>
<?php foreach($cc_types as $c) : ?>
                            <option <?php if ($cc_type == $c) echo "selected "; ?>value="<?php echo $c; ?>"><?php echo $c; ?></option>
<?php endforeach; ?>
                        </select>
<?php if(isset($errors['cc_type'])) : ?>
                        <p><?php echo $errors['cc_type']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['cc_num'])) echo "class=\"error\"";?>>
                        <label for="cc_num">Credit Card:</label>
                        <input type="text" name="cc_num" id="cc_num" />
<?php if(isset($errors['cc_num'])) : ?>
                        <p><?php echo $errors['cc_num']; ?></p>
<?php endif; ?>
                    </li>
                    <li <?php if(isset($errors['cc_exp'])) echo "class=\"error\"";?>>
                        <fieldset>
                            <legend>Expiration Date:</legend>
                            <select name="month" id="month">
                                <option <?php if ($month == '---') echo "selected "; ?>value="---">Month</option>
<?php foreach ($months as $m) : ?>
                                <option <?php if ($month == $m) echo "selected "; ?>value="<?php echo $m; ?>"><?php echo $m; ?></option>
<?php endforeach; ?>
                            </select>
                            <select name="year" id="year">
                                <option <?php if ($year == '---') echo "selected "; ?>value="---">Year</option>
<?php foreach ($years as $y) : ?>
                                <option <?php if ($year == $y) echo "selected "; ?>value="<?php echo $y; ?>"><?php echo $y; ?></option>
<?php endforeach; ?>
                            </select>
                        </fieldset>
<?php if(isset($errors['cc_exp'])) : ?>
                        <p><?php echo $errors['cc_exp']; ?></p>
<?php endif; ?>
                    </li>
                    <li id="submitbutton">
                        <input type="submit" name="sumbitbutton" value="Place Order" />
                    </li>
                </ol>
            </form>
        </div>
<?php require('includes/view/footer.php'); ?>