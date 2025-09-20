# HungerHub 🍕

A comprehensive online food ordering system built with PHP, MySQL, and Bootstrap. HungerHub provides a complete solution for restaurants to manage their menu, orders, and customers through an intuitive web interface.

## ✨ Features

### Customer Features

- **User Registration & Authentication** - Secure account creation and login
- **Browse Menu** - Filter by categories (Veg/Non-Veg, Pizza, Biryani, etc.)
- **Shopping Cart** - Add items, view cart, and manage quantities
- **Order Checkout** - Simple and secure order placement
- **Order History** - Track past orders with real-time status updates
- **User Profile Management** - Update personal information
- **Contact Form** - Get in touch with restaurant support

### Admin Features

- **Admin Dashboard** - Real-time statistics and overview
- **Menu Management** - Add, edit, delete menu items with image uploads
- **Order Management** - View and update order status (Pending → Preparing → Ready → Delivered)
- **Customer Management** - View customer details and order history
- **Message Management** - Handle customer inquiries from contact form
- **Real-time Analytics** - Track orders, revenue, and customer metrics

### Order Status Tracking

- **Pending** - Order received and awaiting confirmation
- **Confirmed** - Order confirmed by restaurant
- **Preparing** - Food is being prepared
- **Ready** - Order ready for pickup/delivery
- **Out for Delivery** - Order on the way to customer
- **Delivered** - Order successfully delivered
- **Cancelled** - Order cancelled

## 🛠️ Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: Vanilla JS with AOS animations
- **Icons**: Font Awesome 6
- **Server**: Apache (XAMPP recommended)

## 📋 Prerequisites

- XAMPP (Apache + MySQL + PHP) or similar LAMP stack
- Web browser (Chrome, Firefox, Safari, Edge)
- Text editor or IDE (VS Code recommended)

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/HungerHub.git
cd HungerHub
```

### 2. Setup XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/)
2. Start Apache and MySQL services from XAMPP Control Panel
3. Copy the project folder to `C:\xampp\htdocs\HungerHub`

### 3. Database Setup

1. Open [phpMyAdmin](http://localhost/phpmyadmin)
2. Create a new database named `hungerhub`
3. Import the database schema:
   - Use the existing `hungerhub.sql` file for sample data
   - OR run `database_updates.sql` for enhanced features

### 4. Database Configuration

Update database connection in `db.php`:

```php
$host = "localhost";
$user = "root";        // Your MySQL username
$pass = "";            // Your MySQL password
$db = "hungerhub";     // Database name
```

### 5. Folder Permissions

Ensure the `uploads/` directory has write permissions for image uploads.

### 6. Access the Application

- **Customer Interface**: `http://localhost/HungerHub/`
- **Admin Interface**: `http://localhost/HungerHub/admin/`

## 📁 Project Structure

```
HungerHub/
├── admin/                  # Admin panel files
│   ├── admin_dashboard.php # Admin dashboard with real statistics
│   ├── admin_login.php     # Admin authentication
│   ├── admin_register.php  # Admin registration
│   ├── menu_items.php      # Menu management
│   ├── orders.php          # Order management with status updates
│   ├── customers.php       # Customer management
│   ├── messages.php        # Contact form messages
│   └── ...
├── user/                   # User authentication files
│   ├── login.php
│   ├── register.php
│   ├── profile.php
│   └── logout.php
├── css/
│   └── style.css          # Custom styles
├── images/                # Static images (logos, banners)
├── uploads/               # Uploaded images (food items, profiles)
├── index.php              # Homepage
├── menu.php               # Menu browsing page
├── cart.php               # Shopping cart
├── checkout.php           # Order checkout
├── order_history.php      # Customer order history
├── contact_process.php    # Contact form handler
├── db.php                 # Database configuration
├── hungerhub.sql          # Database structure and sample data
├── database_updates.sql   # Enhanced database features
└── README.md              # This file
```

## 👥 Default Accounts

### Admin Accounts (from existing data)

- **Email**: aayush.kr.gope@gmail.com
- **Name**: Aayush Kumar

### Sample Customer

- **Email**: chhoturahul944@gmail.com
- **Name**: Rahul Kumar

_Note: Check the SQL file for password hashes or create new accounts_

## 🔄 Recent Updates

### ✅ Enhanced Features Added

1. **Order Status Management** - Complete order tracking system
2. **Real Admin Dashboard** - Live statistics instead of hardcoded numbers
3. **Customer Management** - Admin can view customer details and order history
4. **Order History** - Customers can track their orders
5. **Contact Form Processing** - Message handling system
6. **Improved UI/UX** - Better navigation and status indicators

### 🗄️ Database Enhancements

- Added `status` column to orders table with enum values
- Added `estimated_delivery` and `admin_notes` columns
- Created `messages` table for contact form submissions
- Enhanced foreign key relationships

## 🎨 Customization

### Adding New Menu Categories

1. Update the category arrays in `admin/add_menu_item.php` and `menu.php`
2. Add corresponding filter options

### Styling

- Modify `css/style.css` for custom styling
- Update Bootstrap classes in HTML files
- Customize color schemes in the CSS variables

### Adding Payment Gateway

1. Integrate payment APIs in `checkout.php`
2. Add payment status to orders table
3. Update order flow to handle payment confirmation

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session-based authentication
- File upload validation

## 🚀 Future Enhancements

- [ ] Payment gateway integration (Razorpay, PayPal)
- [ ] Email notifications for order updates
- [ ] SMS notifications
- [ ] Rating and review system
- [ ] Discount/coupon system
- [ ] Multi-restaurant support
- [ ] Mobile app (React Native/Flutter)
- [ ] Real-time notifications
- [ ] Advanced analytics and reporting

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📞 Support

For support or questions:

- Create an issue on GitHub
- Contact through the application's contact form
- Email: support@hungerhub.com

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Bootstrap team for the amazing CSS framework
- Font Awesome for beautiful icons
- AOS library for smooth animations
- PHP community for excellent documentation

---

**HungerHub** - Delivering delicious food experiences through technology! 🍴✨
