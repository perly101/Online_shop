# Absolute Essential Trading - Laravel Application

This is a converted Laravel version of the Absolute Essential Trading online ordering and pick-up system.

## Features

- Customer shopping interface with product catalog
- Shopping cart functionality
- Order management system
- Admin dashboard for managing orders
- Inventory management
- Analytics and reporting
- QR code order verification
- User authentication and authorization

## Installation & Setup

### Prerequisites
- PHP >= 7.3
- Composer
- MySQL or any Laravel-supported database

### Quick Start Guide

1. **Configure Database**
   
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absolute_trading
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```

3. **Seed Database with Products**
   ```bash
   php artisan db:seed --class=ProductSeeder
   ```

4. **Create Admin User**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   User::create([
       'name' => 'Admin',
       'email' => 'admin@absolutetrading.com',
       'password' => Hash::make('password'),
       'role' => 'admin',
       'phone' => '0929 222 4308'
   ]);
   exit
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

6. **Access the Application**
   - Main Site: http://localhost:8000
   - Customer Login: http://localhost:8000/login
   - Admin Login: http://localhost:8000/admin/login

## Default Credentials

### Admin
- Email: admin@absolutetrading.com
- Password: password

## Project Structure

### Models
- `User` - Customer and admin users
- `Product` - Product catalog
- `ProductVariant` - Product flavors/variants
- `Order` - Customer orders
- `OrderItem` - Individual items in orders

### Controllers
- `HomeController` - Landing and public pages
- `AuthController` - Authentication
- `ShopController` - Product catalog
- `CartController` - Shopping cart operations
- `OrderController` - Order management
- `ProfileController` - User profile
- `Admin/DashboardController` - Order management
- `Admin/InventoryController` - Product management
- `Admin/AnalyticsController` - Business analytics

### Routes

#### Public
- `/` - Homepage
- `/user-type` - User type selection
- `/login` - Customer login
- `/signup` - Customer registration
- `/admin/login` - Admin login

#### Customer (Authenticated)
- `/shop` - Product catalog
- `/cart` - Shopping cart
- `/orders` - Order history
- `/profile` - User profile

#### Admin (Requires admin role)
- `/admin/dashboard` - Order management
- `/admin/inventory` - Product management
- `/admin/analytics` - Business analytics

## Database Schema

- **users**: id, name, email, password, role, phone
- **products**: id, name, price, image, description, stock
- **product_variants**: id, product_id, flavor, image
- **orders**: id, order_number, user_id, total_amount, status, pickup_qr_code
- **order_items**: id, order_id, product_id, product_name, price, quantity, flavor

## Frontend Assets

All CSS, JS, and images migrated to:
- `public/css/`
- `public/js/`
- `public/images/`
- `public/data/`

## Technologies

- Laravel 8.x
- Blade templating
- Eloquent ORM
- Session-based authentication
- Role-based access control (admin middleware)

## Features Implemented

### Customer
- Product browsing with search
- Product details and variants
- Shopping cart
- Order placement
- Order history
- Profile management

### Admin
- Order management
- QR code verification
- Inventory management
- Analytics dashboard
- Product CRUD operations
