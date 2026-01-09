# Barber Shoppe - Barber Shop Management System

Barber Shoppe is a modern, comprehensive management system for barbers and shop owners. Built with Laravel and styled with Tailwind CSS and Flowbite, it provides a seamless experience for managing services, schedules, and bookings.

## 🚀 Key Features

- **Storefront & Booking**: Dynamic booking pages for each shop, support for custom domains.
- **Service Management**: CRUD operations for services with duration and pricing.
- **Availability Management**: Highly configurable schedule settings for barbers.
- **Appointment System**: Real-time appointment booking with multi-service selection.
- **Admin Dashboard**: Comprehensive analytics and management interface for shop owners.
- **Modern UI**: Styled with Tailwind CSS and Flowbite, featuring interactive elements and charts.

## 🛠️ Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Tailwind CSS 4.0, Flowbite, Vanilla JS
- **Database**: SQLite
- **Build Tool**: Vite
- **Charts**: ApexCharts

## 📦 Installation & Setup

To get started with Barber Shoppe, follow these steps:

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd app.barber_shoppe
   ```

2. **Run the setup script**:
   This project includes a convenient setup script that handles composer dependencies, environment configuration, key generation, migrations, and frontend assets.
   ```bash
   composer run setup
   ```

3. **Start the development server**:
   ```bash
   composer run dev
   ```
   This will start the Laravel server, queue listener, and Vite dev server simultaneously.

## 📄 License

The Barber Shoppe management system is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
