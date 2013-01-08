<?php
require_once('../includes/config.php');
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (mysqli_connect_errno()) {
	echo "Connect failed: %s\n", mysqli_connect_error();
	exit();
}

$ret = $mysqli->query("CREATE TABLE products (
                        id int AUTO_INCREMENT PRIMARY KEY,
                        description varchar(64) NOT NULL,
                        amount float NOT NULL)");
						
if (!$ret) {
	echo "Error occured creating table: %s", $mysqli->error;
	exit();
}
else {
	echo "Table created." . "<br />";
}

$ret = $mysqli->query("CREATE TABLE orders (
                        order_id int AUTO_INCREMENT PRIMARY KEY,
                        name varchar(64) NOT NULL,
                        city varchar(64) NOT NULL,
                        state varchar(2) NOT NULL,
                        zip varchar (16) NOT NULL,						
                        cc_exp date NOT NULL,
                        cc_type varchar (64) NOT NULL,
                        cc_num varchar(16) NOT NULL,
                        total_amount float NOT NULL,
                        tax float NOT NULL)");
						
if (!$ret) {
	echo "Error occured creating table: %s", $mysqli->error;
	exit();
}
else {
	echo "Table created." . "<br />";
}

$ret = $mysqli->query("CREATE TABLE order_item (
                        order_id int NOT NULL,
                        product_id int NOT NULL,
                        amount float NOT NULL,
                        PRIMARY KEY(order_id, product_id))");
						
if (!$ret) {
	echo "Error occured creating table: %s", $mysqli->error;
	exit();
}
else {
	echo "Table created." . "<br />";
}

$ret = $mysqli->query("INSERT INTO products 
                        (description, amount) 
                       VALUES ('Shoes', 60),
                              ('Pants', 40),
                              ('T-shirt', 20),
			      ('Hat', 10)");
							  
if (!$ret) {
	echo "Error occured inserting products: %s", $mysqli->error;
	exit();
}
else {
	echo "Products inserted." . "<br />";
}

$mysqli->close();