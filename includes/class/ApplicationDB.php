<?php
/**
 * Container class for database access functions
 * 
 * @category Database
 * @author Christopher Wojcik <hello@chriswojcik.net>
 */
class ApplicationDB extends Database
{
    /**
     * Runs the parent constructor to establish a database connection
     * 
     * @return void
     * @see classDatabase.php
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Returns all products from the database as Product objects
     * 
     * @return array an array of Product objects
     * @see classProduct.php
     */
    public function getAllProducts()
    {
        $query = "SELECT id, description, amount
                  FROM products";        
        /**
         * Container array for the products 
         */
        $products = array();
        
        /**
         * Products are returned from the db as an associative array, loop through
         * and instantiate each row as a new Product object 
         */
        foreach ($this->get($query) as $row) {
            
            $product = new Product ($row['id'], 
                                    $row['description'], 
                                    $row['amount']);            
            $products[] = $product;
        }
        
        return $products;
    }
    
    /**
     * Returns a single product from the database
     * 
     * @param integer $id the product id
     * @return \Product
     * @see classProduct.php 
     */
    public function getProductById($id)
    {
        $query = "SELECT id, description, amount
                  FROM products
                  WHERE id = ?";
        
        $rows = $this->get($query, array($id));
        $row = $rows[0];
        
        return new Product($row['id'], 
                           $row['description'], 
                           $row['amount']);
    }
    
    /**
     * Return all products which are in an array of ids
     * 
     * @param array $ids an array containing the ids to search for
     * @return array an array of Product objects
     * @see classProduct.php
     */
    public function getProductsById($ids)
    {
        /**
         * Typecast each id as an integer 
         */
        foreach ($ids as $id) {
            $id = (int)$id;
        }
        
        /**
         * Turn the array into a comma-delimited string 
         */
        $ids = implode(",", $ids);
        
        /**
         * Get the products 
         */
        $query = "SELECT id, description, amount
                  FROM products
                  WHERE id in ($ids)";
        
        $products = array();
        
        foreach ($this->get($query) as $row) {
            
            $product = new Product ($row['id'], 
                                    $row['description'], 
                                    $row['amount']);            
            $products[] = $product;
        }
        
        return $products;
    }
    
    /**
     * Inserts a product into the database
     * 
     * @param string $desc a short description of the product
     * @param float $amount the product's cost
     * @return void
     */
    public function insertProduct($desc, $amount)
    {
        $query = "INSERT INTO products
                    (description, amount)
                  VALUES
                    (?, ?)";
        
        $this->execute($query, array($desc, $amount));
    }
    
    /**
     * Inserts a customer order into the database
     * 
     * @param string $name the customer name
     * @param string $city customer city
     * @param string $state customer state
     * @param string $zip customer zipcode
     * @param string $cc_exp the expiration date in string format
     * @param string $cc_type e.g. 'Visa'
     * @param string $cc_num the credit card number
     * @param string $total_amount the total amount of the order, including tax
     * @param string $tax the amount added as tax
     * @return void
     */
    public function insertOrder($name, $city, $state, $zip, 
                                $cc_exp, $cc_type, $cc_num, 
                                $total_amount, $tax)
    {
        $query = "INSERT INTO orders
                    (name, city, state, zip, 
                     cc_exp, cc_type, cc_num, 
                     total_amount, tax)
                  VALUES 
                        (?, ?, ?, ?, 
                        STR_TO_DATE(?, '%m/%d/%Y'), 
                        ?, ?, ?, ?)";
        
        $order_details = func_get_args();        
        $this->execute($query, $order_details);
    }
    
    /**
     * Perform an insert into the orders-products junction table
     * 
     * @param integer $order_id
     * @param integer $prod_id
     * @param float $amount
     * @return void
     */
    public function insertOrderItem($order_id, $prod_id, $amount)
    {
        $query = "INSERT INTO order_item
                    (order_id, product_id, amount)
                  VALUES
                    (?, ?, ?)";
        
        $this->execute($query, array($order_id, $prod_id, $amount));
    }
}