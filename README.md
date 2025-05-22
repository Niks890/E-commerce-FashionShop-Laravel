# 🛍️ Laravel E-Commerce Fashion Shop System

## 📌 Project Overview
This project is a **Laravel-based E-Commerce System** designed for selling fashion products. The system includes features such as product management, order processing, cart processing, customer authentication, provider tracking, and promotional discounts. The database schema is structured for efficiency and scalability.

## 📂 Database Structure
### **1. Product Management**
| Table | Description |
|--------|-------------|
| `categories` | Stores product categories. |
| `products` | Main product table with details like name, price, brand, description, image etc. |
| `product_variants` | Stores product variations (color, size, stock). |

### **2. Orders & Transactions**
| Table | Description |
|--------|-------------|
| `orders` | Stores customer orders and payment details. |
| `order_details` | Tracks individual items in an order. |
| `comments` | Customer reviews and ratings. |

### **3. Customers & Employees**
| Table | Description |
|--------|-------------|
| `customers` | Stores registered users. |
| `employees` | Manages admin and staff accounts. |

### **4. Suppliers & Inventory**
| Table | Description |
|--------|-------------|
| `providers` | Stores provider information. |
| `inventories` | Stores product information in inventory. |
| `inventory_details` | Tracks incoming stock from providers. |

### **5. Promotions & Discounts**
| Table | Description |
|--------|-------------|
| `discounts` | Stores promotions and discount codes. |

## 🛠️ Tech Stack
- **Backend:** Laravel 10, PHP 8
- **Database:** MySQL
- **Frontend:** JQuery 3.7, Bootstrap 5
- **Authorization** Laravel Gate
- **ORM:** Eloquent
- **Payment** VNPAY, Momo, ZaloPay

## 🚀 Installation Guide
### **Step 1: Clone the Repository**
```sh
  git clone https://github.com/Niks890/E-commerce-FashionShop-Laravel.git
  cd ECommerceFashionShop
```

### **Step 2: Install Dependencies**
```sh
  composer install
```

### **Step 3: Configure Environment**
```sh
  cp .env.example .env
  php artisan key:generate
```

### **Step 4: Setup Database**
```sh
  php artisan migrate --seed
```

### **Step 5: Start Development Server**
```sh
  php artisan serve
```

## 🎯 Features
✅ Product Management
✅ User Authentication (Customers & Admins)
✅ Order & Checkout System
✅ Discount & Promotion Management
✅ Provider & Inventory Tracking
✅ Customer Reviews & Ratings
✅ REST API Support for Frontend Integration
✅ Multi-Payment Gateway (VNPAY, MoMo, ZaloPay)
✅ Role-Based Access Control with Gates
✅ ChatBot AI 


## 📜 License
This project is licensed under the [MIT License](LICENSE).

---
**Developed by [VMT]([https://github.com/Niks890/E-commerce-FashionShop-Laravel.git])** 🚀
