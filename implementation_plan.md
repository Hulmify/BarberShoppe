# Barber Shop Management System - Implementation Plan

## 1. Project Initialization
- [ ] Clean up existing Next.js artifacts.
- [ ] Install Laravel via Composer.
- [ ] Configure SQLite database.
- [ ] Set up basic layout with Vanilla CSS.

## 2. Database Schema (Schema Design)
- **Users**: Authentication (Barbers/Shop Owners).
- **Shops**: Profile, Slug, Custom Domain (CNAME).
- **Services**: Name, Duration, Price, Shop ID.
- **Availabilities**: Schedule configuration.
- **Bookings**: Appointment details, Status.
- **BookingItems**: Pivot for multiple services per booking.

## 3. Core Features - Admin/Barber
- [ ] Authentication (Registration/Login).
- [ ] Shop Setup (Name, Domain settings).
- [ ] Service Management (CRUD).
- [ ] Schedule/Availability Management.
- [ ] Appointment Management (View/Cancel/Reschedule).

## 4. Core Features - Customer (Public)
- [ ] Dynamic Booking Page (resolved via Slug or Custom Domain).
- [ ] Service Selection (Multi-select, Real-time Price/Time calc).
- [ ] Time Slot Selection (Availability logic).
- [ ] Appointment Confirmation.

## 5. Technical Implementation
- **Custom Domains**: Middleware to handle requests from `book.custom.com` mapping to `shops` table.
- **Styling**: Modern UI with Tailwind CSS and Flowbite (replaced Vanilla CSS plan).
- **Interactivity**: Vanilla JS for booking flow validation and state.

## 6. Review & Refine
- [ ] Aesthetics polishing.
- [ ] Functional testing.
