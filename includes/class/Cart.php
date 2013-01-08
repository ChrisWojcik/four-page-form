<?php
/**
 * Custom cart class for use with E-Commerce applications
 * 
 * @category E-Commerce
 * @author Christopher Wojcik <hello@chriswojcik.net>
 */
class Cart 
{    
    /**
     * An array of stored products in the form id => cost
     * 
     * @var array 
     */
    private $_products = array();
    
    /**
     * The subtotal for all the products without any tax
     * @var float 
     */
    private $_subtotal = 0;
    
    /**
     * Optionally, add a sales tax rate
     * 
     * @var float 
     */
    private $_taxrate = null;
    
    /**
     * Add a product's id and cost to the cart, and increase the subtotal
     * 
     * @param integer $id a unique id, probably from a database
     * @param float $amount the cost of the product
     * @return void
     */
    public function addProduct($id, $amount) 
    {
        $this->_products[$id] = $amount;
        $this->_subtotal += $amount;
    }
    
    /**
     * Get all products from the cart
     * @return array the products
     */
    public function getProducts() 
    {
        return $this->_products;
    }
    
    /**
     * Get only the product ids
     * 
     * @return array 
     */
    public function getProdIds() 
    {
        return array_keys($this->_products);
    }
    
    /**
     * Add in a sales tax rate
     * 
     * @param float $taxrate must be between 0 and 1
     */
    public function addTaxrate($taxrate)
    {
        if ((is_numeric($taxrate)) && ($taxrate > 0) && ($taxrate < 1)) {
                $this->_taxrate = $taxrate;
        }
    }
    
    /**
     * Multiply the taxrate by the subtotal and return
     * 
     * @return float 
     */
    public function getTax()
    {
        $tax = 0;

        if (isset($this->_taxrate)) {
            $tax = $this->_taxrate * $this->_subtotal;
        }
        
        return $tax;
    }
    
    /**
     * Tax + subtotal = grand total
     * 
     * @return float 
     */
    public function getTotal() 
    {
        $tax = $this->getTax();        
        return $this->_subtotal + $tax;     
    }
}