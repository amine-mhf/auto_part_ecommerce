# Pièces Auto

A web application for browsing and purchasing automotive spare parts, developed with PHP and MySQL using the XAMPP environment.

## About the Project

**Pièces Auto** is an e-commerce web application designed for selling automotive spare parts online.

The application provides two main interfaces:

* A customer interface for browsing products, managing a shopping cart, placing orders, and viewing order history.
* An administration interface for managing products, orders, and users.

The project was developed locally using **XAMPP**, with **Apache** as the web server and **MySQL** as the database management system.

## Features

### Customer

* Browse the product catalog
* Filter products
* View product details
* Add products to the shopping cart
* Buy products directly
* Place orders
* Simulated checkout and payment process — no real transactions
* User registration and authentication
* Customer profile management
* Order history
* Contact page with map
* Responsive navigation

### Administration

* Dashboard with statistics
* Product management (CRUD)
* Order management
* User management
* Dedicated administration interface

## Technologies Used

* **PHP** — Server-side application logic
* **MySQL** — Database management
* **HTML5** — Application structure
* **CSS3** — Styling and responsive design
* **JavaScript** — Client-side interactions
* **AJAX** — Asynchronous cart operations
* **PDO** — Database connection and queries
* **XAMPP** — Local development environment
* **Apache** — Web server
* **phpMyAdmin** — Database administration

## Project Structure

```text
pieces_auto/
│
├── admin/
│   ├── index.php
│   ├── gerer_produits.php
│   ├── gerer_commandes.php
│   └── gerer_utilisateurs.php
│
├── database/
│   └── pieces_auto.sql
│
├── includes/
│   ├── config.php
│   ├── header.php
│   ├── footer.php
│   ├── admin_header.php
│   └── admin_footer.php
│
├── ressources/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   │
│   ├── js/
│   │   ├── main.js
│   │   └── admin.js
│   │
│   └── images/
│
├── screenshots/
│   ├── client-deconnecte/
│   ├── client-connecte/
│   └── admin/
│
├── index.php
├── produits.php
├── produit_detail.php
├── panier.php
├── commande.php
├── mes_commandes.php
├── connexion.php
├── inscription.php
├── profil.php
├── contact.php
├── ajax_panier.php
└── acheter_maintenant.php
```

## Screenshots

### Client — Not Connected

The public interface available before user authentication.

### Client — Connected

The customer interface available after authentication, including the shopping cart, profile and order management.

### Administration

The administration interface used to manage products, orders and users.

## Environment

The project was developed and tested locally using:

* **XAMPP**
* **Apache**
* **MySQL**
* **phpMyAdmin**

## Future Improvements

* Integration of a real payment gateway
* Product reviews and ratings
* Order tracking
* Email notifications
* Additional security improvements
* More advanced dashboard statistics
