<?php
/**
 * Object to represent items in the products table
 * 
 * @category Database
 * @author Christopher Wojcik <hello@chriswojcik.net>
 */
class Product 
{
    /**
     * Unique product id
     * 
     * @var integer 
     */
    private $_id;
    
    /**
     * A short description of the product
     * 
     * @var string
     */
    private $_description;
    
    /**
     * The cost
     * @var float
     */
    private $_amount;
    
    /**
     * Instantiate and set all the values
     * 
     * @param integer $id
     * @param string $description
     * @param float $amount 
     */    
    public function __construct($id, $description, $amount) 
    {
        $this->_id = $id;
        $this->_description = $description;
        $this->_amount = $amount;
    }
    
    /**
     * Getter for the id property
     * 
     * @return integer 
     */
    public function getId() 
    {
        return $this->_id;
    }
    
    /**
     * Get the description and convert it for display
     * 
     * @return string
     */
    public function displayDescription() 
    {
        return htmlentities($this->_description);
    }
    
    /**
     * Display the amount as currency
     * 
     * @return string 
     */
    public function displayAmount() 
    {
        return number_format($this->_amount, 2, '.', ',');
    }
    
    /**
     * Getter for the amount property
     * 
     * @return float 
     */
    public function getAmount() 
    {
        return $this->_amount;
    }
    
    /**
     * Setter for the amount property
     * 
     * @param float $amount 
     */    
    public function setAmount($amount)
    {
        $this->_amount = $amount;
    }
    
    /**
     * Magic method, outputs the product as "Name ($0.00)"
     * 
     * @return string 
     */
    public function __toString()
    {
        $string = $this->_description . ' ($' . $this->displayAmount() . ')';
        return $string;
    }
}