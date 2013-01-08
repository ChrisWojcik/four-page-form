<?php
/**
 * Basic form validation class allows you to set custom validation rules
 * 
 * @category Forms
 * @author Christopher Wojcik <hello@chriswojcik.net>
 */
class FormValidator
{
	/**
     * An array of errors in the form of fieldname => error msg 
	 *
     * @var array
     */
	private $_errors = array();
	
	/**
	* An array of fields in the form of fieldname => value
	*
	* @var array
	*/
	private $_fields = array();
	
	/**
	* An array of rule objects
	*
	* @var ValidationRule
	*/
	private $_rules = array();
	
	/**
	* Instantiate a rule object based on the given parameters
	*
	* @param string $fieldname
	* @param string $error_msg
	* @param string $ruletype
	* @param mixed $criteria
	* @return void
	*/
	public function addRule($fieldname, $error_msg, $ruletype, $criteria = NULL)
	{
		$this->_rules[] = new ValidationRule($fieldname, $error_msg, $ruletype, $criteria);
	}
	
	/**
	* Sanitize and add each form entry provided
	*
	* @param array $fields
	* @return void
	*/
	public function addEntries($fields) 
	{
		if (is_array($fields)) {
			foreach ($fields as $name => $value) {
				$this->_fields[$name] = $this->sanitize($value);
			}
		}
	}
	
	/**
	* Get all of the supplied field entries (now sanitized)
	*
	* @return array
	*/
	public function getEntries()
	{
		return $this->_fields;
	}
	
	/**
	* Wrapper function for checking each rule
	*
	* @return void
	*/
	public function validate()
	{
		foreach ($this->_rules as $rule) {
			$this->_checkRule($rule);
		}
	}
	
	/**
	* If the form has been validated, check if any errors were found
	*
	* @return boolean
	*/
	public function foundErrors()
	{
		if (count($this->_errors)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	* Return an array of error messages
	*
	* @return array
	*/
	public function getErrors()
	{
		return $this->_errors;
	}
	
	/**
	* Clean up all form entries
	*
	* @param string $text
	* @return string
	*/
	public function sanitize($text) {
		$text = trim($text);
		
		if (get_magic_quotes_gpc()) {
			$text = stripslashes($text);
		}
		return $text;
	}
	
	/**
	* Check if the value is valid zip code
	*
	* @param string $zip
	* @return boolean
	*/
	public function valid_zip($zip) 
	{
		/**
		 * 5 digits followed by optional dash and 4 more digits
		 */
		if(preg_match("/^[0-9]{5}(-[0-9]{4})?$/",$zip)) {
			return true;
		}
		return false;
	}
	
	/**
	* Check if the value is a valid credit card number
	*
	* @param string $number
	* @return boolean
	*/
	public function valid_cc_num($number) 
	{    
		/**
		 * Supported cardtypes: Visa, Mastercard, American Express, Discover
		 */
		if (!preg_match("/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13})$$/", $number)) {
			return false;
		}
		
		/**
		 * Take the string of digits and convert it to an array 
		 */
		$digits = str_split($number);
		
		/**
		 * Pop off the last digits to use as the check digit
		 */
		$check_dig = array_pop($digits);
		
		/**
		 * Reverse the order so we can move from right to left 
		 */
		$digits = array_reverse($digits);
		
		/**
		 * Starting with the first digit, double every other one then sum its digits
		 */
		for ($i = 0, $j = count($digits); $i < $j; $i += 2) {
			$digits[$i] *= 2;
			$digits[$i] = array_sum(str_split($digits[$i]));
		}
		
		/**
		 * The sum of all the new digits + the check must be evenly divisible by 10
		 */
		$sum = array_sum($digits) + $check_dig;
		return ($sum % 10) == 0;
	}
	
	/**
	* Check a the value associated with the rule's field against the ruletype
	*
	* @param ValidationRule
	* @return void
	*/
	private function _checkRule(ValidationRule $rule)
	{
		if (isset($this->_errors[$rule->fieldname])) {
			return;
		}
		$fieldvalid = false;
		
		switch ($rule->ruletype) {			
			case "required":
				if (!is_array($this->_fields[$rule->fieldname])) {
					if (isset($this->_fields[$rule->fieldname]) && strlen($this->_fields[$rule->fieldname]) > 0) {
						$fieldvalid = true;
					}
				}
				else {
					if (!empty($this->_fields[$rule->fieldname])) {
						$fieldvalid = true;
					}
				}
				break;
			case "in_array":
				if (is_array($rule->criteria) && in_array($this->_fields[$rule->fieldname], $rule->criteria)) {
					$fieldvalid = true;
				}
			case "zip":
				if ($this->valid_zip($this->_fields[$rule->fieldname])) {
					$fieldvalid = true;
				}
				break;
			case "creditcard":
				if ($this->valid_cc_num($this->_fields[$rule->fieldname])) {
					$fieldvalid = true;
				}
				break;
			case "callback":
				if(call_user_func($rule->criteria)) {
					$fieldvalid = true;
				}
		}
		
		if (!$fieldvalid) {
			$this->_errors[$rule->fieldname] = $rule->error_msg;
		}
	}
}

class ValidationRule
{
	private $_fieldname;
	private $_error_msg;
	private $_ruletype;
	private $_criteria;
	
	public function __construct($fieldname, $error_msg, $ruletype, $criteria = NULL)
	{
		$this->_fieldname = $fieldname;
		$this->_error_msg = $error_msg;
		$this->_ruletype = $ruletype;
		$this->_criteria = $criteria;
	}
	
	public function __get($name)
	{
		$property = "_" . $name;
		
		if (isset($this->$property)) {
			return $this->$property;
		}
		else {
			return false;
		}
	}
}