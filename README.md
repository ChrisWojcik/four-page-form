# four-page-form

A class project of mine: a four-page e-commerce-type application. I hope this code 
sample is of use to someone else as well.

## Project Description
This project demonstrates the "POST-REDIRECT-GET" technique. The assignment 
was to create a four page e-commerce app with the following stages:

1. Select your products and add them to your cart.
2. Add your billing info.
3. Re-display the order information and allow confirmation.
4. Confirmation message.

Form inputs are validated at each stage. No errors or warnings should occur from 
pressing the back button in the browser or refreshing the page.

## The Database
The configuration settings for the database are found in `includes/config.php`. After 
creating the database and adding your configuration settings, you can run the script 
`create_tables\create_tables.php` to set up the following database tables:

1. products (the available products)
2. orders (initially empty, entry created for each submitted order)
3. order_item (initially empty, entry created for each order line item)

## OOP
The project also includes some object oriented programming, using classes to represent 
the shopping cart and products. Also included are a FormValidation class and a database 
wrapper class for mysqli to make it easier to retrieve records as objects. `includes/class/ApplicationDB` 
extends `includes/class/Database.php` with several helper methods specific to the project.


