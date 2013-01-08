<?php
// Require the class files
require_once('includes/config.php');
require_once('includes/class/Database.php');
require_once('includes/class/ApplicationDB.php');
require_once('includes/class/Product.php');
require_once('includes/class/Cart.php');

$db = new ApplicationDB;
session_start();