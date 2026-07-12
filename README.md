# Ecom Shop

A full-featured e-commerce application built with **Laravel 13**, **Inertia.js v3**, **Vue 3**, and **Tailwind CSS v4**. The project includes a customer-facing storefront and a comprehensive admin panel with analytics, order management, coupon management, and multiple payment gateway integrations (Cash on Delivery, SSLCommerz, and Stripe).

---

## Table of Contents

- [Setup Instructions](#setup-instructions)
- [Default Accounts](#default-accounts)
- [UI Changes — Admin Panel](#ui-changes--admin-panel)
- [UI Changes — Storefront](#ui-changes--storefront)
- [Stripe Integration Notes](#stripe-integration-notes)
- [Coupon Schema Explanation](#coupon-schema-explanation)
- [Additional Features](#additional-features)
- [Application Workflow](#application-workflow)

---

## Setup Instructions

### Prerequisites

- PHP 8.4+
- Composer
- Node.js 20+ & npm
- MySQL 8+

### Quick Start

```bash

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=b9_ecom_shop
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. (Optional) Configure Stripe keys in .env
#    STRIPE_KEY=pk_test_...
#    STRIPE_SECRET=sk_test_...
#    STRIPE_WEBHOOK_SECRET=whsec_...
#    STRIPE_CURRENCY=usd

# 6. Run database migrations and seeders
php artisan migrate --force
php artisan db:seed

# 7. Install frontend dependencies
npm install

# 8. Build frontend assets
npm run build

# 9. Start the development server
composer run dev
# This runs the Laravel server, queue worker, and Vite dev server concurrently
```

Alternatively, use the one-liner setup script:

```bash
composer run setup
```

---

## Default Accounts

All seeded accounts use the password: **`password`**

| Role     | Email                  | Name           |
| -------- | ---------------------- | -------------- |
| Admin    | `admin@shopease.test`  | ShopEase Admin |
| Customer | `test@example.com`     | Test User      |
| Customer | `rina@example.com`     | Rina A.        |
| Customer | `karim@example.com`    | Karim H.       |
| Customer | `sadia@example.com`    | Sadia R.       |

---

## UI Changes — Admin Panel

### 1. Analytics Dashboard (`/dashboard`)
- **Overview cards**: Total revenue, total orders, average order value, pending orders, total customers, new customers this month, total/active/out-of-stock products, total wishlists.
- **Revenue chart**: 30-day daily revenue and order count.
- **Breakdown charts**: Orders by status, payment status breakdown, payment method breakdown (COD / SSLCommerz / Stripe).
- **Top products & categories**: Best-selling products and most-populated categories.
- **Recent orders**: Quick-view table of the latest 8 orders.

### 2. Coupon Management (CRUD) — `/admin/coupons`
- **Index page**: Sortable table displaying code, discount type/value, min order amount, usage count vs. limit, expiry date, and active/inactive status. Actions include Edit and Delete (with confirmation dialog).
- **Create page** (`/admin/coupons/create`): Form to create a new coupon with fields for code, discount type (flat/percentage), discount value, minimum order amount, usage limit, expiry date, and active toggle.
- **Edit page** (`/admin/coupons/{id}/edit`): Pre-populated form for updating existing coupons.
- **Delete**: Confirmation dialog before removing a coupon.

### 3. Order Detail Page — `/admin/orders/{id}`
- **Gradient header bar** with order number, status badges, and Print/Save PDF buttons.
- **Fulfillment stepper**: Visual progress indicator (Pending → Processing → Shipped → Delivered) with color-coded steps.
- **Cancelled banner**: Red alert banner shown when an order is cancelled.
- **Line items table**: Product name, quantity, unit price, and line total with styled subtotal/delivery/total rows.
- **Status timeline**: Chronological log of all status changes with icons, timestamps, notes, and changed-by info.
- **Customer card**: Name, phone, email, and account type (registered/guest).
- **Shipping address card**: Full address with optional delivery notes.
- **Payment summary card**: Method (COD/SSLCommerz/Stripe), payment status badge, subtotal, delivery charge, and total.
- **Update order form**: Dropdowns to change fulfillment status and payment status with optional note.
- **Printable invoice**: Hidden on screen, displayed via `window.print()`. Features a branded gradient header, meta row (order date, payment method, statuses), Bill To / Ship To sections, styled items table, and a branded footer.

### 4. Order Index — `/admin/orders`
- Search by order number, customer name, phone, or email.
- Filter by order status and payment status.
- Stripe added as a recognized payment method label.

---

## UI Changes — Storefront

### 1. Checkout Page (`/checkout`)
- **Shipping details form**: Full name, phone, email, district (dropdown), area, full address, and optional order notes.
- **Payment method selection**: Three radio options with descriptions:
  - **Cash on Delivery** — Pay the delivery agent in cash.
  - **Pay Online (SSLCommerz)** — bKash, Nagad, cards, mobile banking.
  - **Pay Online (Stripe)** — Cards, Apple Pay, and other Stripe-supported methods.
- **Coupon input section** (in Order Summary sidebar):
  - Text input to enter a coupon code with an "Apply" button.
  - Success state: Green chip showing the applied coupon code with a "Remove" button.
  - Error state: Red validation message below the input.
  - Discount line item displayed in the summary when a coupon is applied.
- **Order summary sidebar**: Cart items with quantity controls, subtotal, discount (if coupon applied), delivery charge (Inside/Outside Dhaka), and grand total.
- **Submit button**: "Place Order" for COD, "Proceed to Payment" for SSLCommerz/Stripe.

### 2. Payment Result Pages
- **Payment Success** (`/orders/payment/success`): Shows full order details including items, coupon discount, delivery charge, and totals.
- **Payment Failed** (`/orders/payment/failed`): Error state with order summary.
- **Payment Cancelled** (`/orders/payment/cancelled`): Cancellation state with order summary.

### 3. Order Success Page (`/orders/success`)
- Displays order confirmation with order number, total, and payment label.
- Fallback UI when no order is found.

---

## Stripe Integration Notes

### Architecture

The Stripe integration uses **Stripe Checkout Sessions** (server-side redirect flow):

1. Customer selects "Pay Online (Stripe)" at checkout and submits the form.
2. `CheckoutController@store` calls `OrderService::placeStripeOrder()` which creates the order, applies any coupon, and creates order items.
3. `StripeCheckoutService::initiate()` creates a `Payment` record with gateway `stripe` and status `initiated`, then calls Stripe's API to create a Checkout Session.
4. The customer is redirected to Stripe's hosted checkout page via `Inertia::location()`.
5. On successful payment, Stripe redirects back to the success URL.
6. **Webhook handling**: `StripeWebhookController` listens for `checkout.session.completed` events, verifies the signature, finds the order by `order_number` in the session metadata, and calls `markAsPaid()`.

### Environment Configuration

```env
STRIPE_KEY=pk_test_...          # Publishable key (not used server-side currently)
STRIPE_SECRET=sk_test_...       # Secret key for API calls
STRIPE_WEBHOOK_SECRET=whsec_... # Webhook signing secret
STRIPE_CURRENCY=usd             # Default currency (lowercase)
```

### Test Card Details

When using Stripe in **test mode**, use these test card numbers:

| Card Number          | Description             |
| -------------------- | ----------------------- |
| `4242 4242 4242 4242` | Successful payment      |
| `4000 0000 0000 3220` | 3D Secure authentication |
| `4000 0000 0000 9995` | Declined payment        |

- **Expiry**: Any future date (e.g., `12/34`)
- **CVC**: Any 3 digits (e.g., `123`)
- **ZIP**: Any 5 digits (e.g., `12345`)

### Webhook Setup (Local Development)

For local development, use the [Stripe CLI](https://stripe.com/docs/stripe-cli) to forward webhooks:

```bash
stripe listen --forward-to localhost:8000/payments/stripe/webhook
```

Copy the webhook signing secret (`whsec_...`) from the CLI output and set it as `STRIPE_WEBHOOK_SECRET` in `.env`.

### Test Environment

In the test environment (`APP_ENV=testing`), the `StripeCheckoutService` bypasses the actual Stripe API and returns a fake gateway URL (`https://checkout.stripe.com/c/pay/test-session`), allowing checkout flow tests to run without Stripe credentials.

---

## Coupon Schema Explanation

### `coupons` Table

| Column             | Type                        | Description                                                                 |
| ------------------ | --------------------------- | --------------------------------------------------------------------------- |
| `id`               | `bigint` (PK)               | Auto-incrementing primary key                                               |
| `code`             | `varchar(50)` UNIQUE        | The coupon code (stored uppercase, e.g., `SAVE10`, `FLAT100`)               |
| `discount_type`    | `enum('flat','percentage')` | Whether the discount is a fixed amount or a percentage of the subtotal      |
| `discount_value`   | `decimal(10,2)`             | The discount amount (e.g., `100.00` for ৳100 flat, or `10.00` for 10%)      |
| `min_order_amount` | `decimal(12,2)` default `0` | Minimum cart subtotal required to use this coupon                            |
| `usage_limit`      | `unsigned int` nullable     | Maximum number of times this coupon can be used (`null` = unlimited)         |
| `times_used`       | `unsigned int` default `0`  | Counter tracking how many times the coupon has been redeemed                 |
| `expires_at`       | `date` nullable             | Expiry date (`null` = never expires)                                        |
| `is_active`        | `boolean` default `true`    | Whether the coupon is currently active and available for use                 |
| `created_at`       | `timestamp`                 | Record creation timestamp                                                   |
| `updated_at`       | `timestamp`                 | Record last-update timestamp                                                |

### Coupon Fields Added to `orders` Table

| Column            | Type                        | Description                                                      |
| ----------------- | --------------------------- | ---------------------------------------------------------------- |
| `coupon_id`       | `foreignId` nullable        | References `coupons.id` (set to `null` on coupon deletion)       |
| `coupon_code`     | `varchar(50)` nullable      | Snapshot of the coupon code at the time of order placement        |
| `discount_amount` | `decimal(12,2)` default `0` | The calculated discount amount applied to this order              |

### Validation Logic

The `Coupon` model's `isValid(float $subtotal)` method checks:
1. **Active**: `is_active` must be `true`.
2. **Not expired**: `expires_at` must be `null` or in the future.
3. **Usage available**: `times_used` must be less than `usage_limit` (or `usage_limit` is `null`).
4. **Minimum met**: Subtotal must be ≥ `min_order_amount`.

### Discount Calculation

- **Percentage**: `subtotal × discount_value / 100` (rounded to 2 decimal places).
- **Flat**: `min(discount_value, subtotal)` — the discount never exceeds the subtotal.

### Coupon Apply Flow (Storefront)

1. User enters a coupon code in the checkout summary sidebar.
2. Frontend sends a `POST /coupon/apply` request with `{ code, subtotal }`.
3. `CouponService::validate()` looks up the coupon, checks validity, and calculates the discount.
4. If valid: returns `{ valid: true, coupon_code, discount_type, discount_value, discount_amount }`.
5. If invalid: returns `{ valid: false, message }` with HTTP 422.
6. On form submission, the `coupon_code` is sent along with the checkout payload.
7. `OrderService::placeOrder()` re-validates the coupon server-side, applies the discount to the total, stores `coupon_id`, `coupon_code`, and `discount_amount` on the order, and increments `times_used`.

---

## Additional Features

### Multiple Payment Gateways
- **Cash on Delivery (COD)**: Default payment method; order status starts as `pending`.
- **SSLCommerz**: Bangladesh-specific payment gateway supporting bKash, Nagad, cards, and mobile banking. Sandbox mode available.
- **Stripe**: International payment gateway with Checkout Sessions. Webhook-based payment confirmation.

### Admin Order Management
- Full order lifecycle management: Pending → Processing → Shipped → Delivered (or Cancelled).
- Payment status tracking: Pending → Paid / Failed / Cancelled.
- Status history timeline with notes and audit trail (who changed what, when).
- Printable/PDF invoice with branded design, item breakdown, and totals.

### Product Management
- CRUD for products with rich text descriptions (TipTap editor).
- Multiple product images.
- Stock status management (in stock / out of stock).
- Category assignment.

### Category Management
- CRUD for product categories with images.

### Cart System
- Session-based cart (works for both guests and authenticated users).
- Add, update quantity, remove items, and clear cart.

### Wishlist
- Authenticated users can save products to a wishlist.
- Admin can view all wishlists.

### Reviews
- Product review system.

### Hero Slides
- Configurable homepage hero slider.

### Analytics Dashboard
- Revenue tracking with 30-day chart.
- Order status distribution.
- Payment method and status breakdowns.
- Top products and categories.
- Customer statistics.
- Month-over-month comparison with percentage change indicators.

### Authentication
- Laravel Fortify-powered authentication.
- Registration, login, password reset, email verification.
- Two-factor authentication support.
- Passkey support (`@laravel/passkeys`).
- Role-based access control (admin/customer).

### Testing
- Comprehensive Pest test suite covering:
  - Coupon model validation, service layer, storefront API, and admin CRUD.
  - Stripe checkout flow, webhook handling, and payment result pages.
  - SSLCommerz checkout flow.
  - Cart, wishlist, and checkout integration.
  - Admin dashboard, orders, products, categories, and wishlists.
  - Role-based access control.
  - Shop homepage, product listing, and product detail pages.

---

## Application Workflow

### Customer Shopping Flow

```
Home Page → Shop/Browse → Product Detail → Add to Cart → Cart Page
  → Checkout (fill shipping + select payment + apply coupon)
    → COD: Order placed → Order Success page
    → SSLCommerz: Redirect to SSLCommerz → Callback → Success/Failed/Cancelled
    → Stripe: Redirect to Stripe Checkout → Success/Failed/Cancelled
```

### Admin Management Flow

```
Login (admin@shopease.test / password)
  → Dashboard (analytics overview)
  → Products (CRUD with images & rich text)
  → Categories (CRUD with images)
  → Orders (list → detail → update status → print invoice)
  → Coupons (create → edit → delete)
  → Wishlists (view customer wishlists)
```

### Coupon Application Flow

```
Checkout Page → Enter coupon code → Click "Apply"
  → POST /coupon/apply { code, subtotal }
  → Server validates: active? not expired? usage available? min order met?
  → Valid: Show green chip + discount line in summary
  → Invalid: Show red error message
  → On order submit: coupon re-validated server-side, discount applied, usage incremented
```

### Stripe Checkout Flow

```
Checkout → Select "Stripe" → Submit form
  → Server creates Order + Payment record (status: initiated)
  → Server creates Stripe Checkout Session via API
  → Customer redirected to Stripe's hosted checkout page
  → Customer pays with test card (4242 4242 4242 4242)
  → Stripe redirects to success URL
  → Webhook: checkout.session.completed → markAsPaid()
  → Payment Success page shown with full order details
```

### Invoice (Print/PDF)

```
Admin → Orders → View Order → Click "Print" or "Save PDF"
  → Browser print dialog opens
  → Printable invoice layout shown:
     - Branded gradient header (B9-Ecom)
     - Order meta (date, payment method, statuses)
     - Bill To / Ship To sections
     - Items table with subtotal, delivery, and grand total
     - Branded footer
```

---

## Tech Stack

| Layer        | Technology                                      |
| ------------ | ----------------------------------------------- |
| Backend      | Laravel 13, PHP 8.4                             |
| Frontend     | Vue 3, Inertia.js v3, Tailwind CSS v4           |
| Database     | MySQL 8                                         |
| Auth         | Laravel Fortify, Passkeys                       |
| Payments     | Stripe (stripe-php), SSLCommerz                 |
| Testing      | Pest v4                                         |
| Code Quality | Laravel Pint, Larastan (PHPStan), ESLint        |
| Editor       | TipTap (rich text for product descriptions)     |
| UI Kit       | Reka UI, Lucide Icons, Vue Sonner (toasts)      |
| Build        | Vite 8, Laravel Vite Plugin                     |
| Routing      | Laravel Wayfinder (type-safe client-side routes) |

---

## Running Tests

```bash
# Run all tests
php artisan test --compact

# Run specific test files
php artisan test --compact --filter=CouponTest
php artisan test --compact --filter=ShopStripeCheckoutTest

# Run PHP linting
vendor/bin/pint --dirty --format agent

# Run static analysis
./vendor/bin/phpstan analyse

# Run frontend checks
npm run lint:check
npm run format:check
npm run types:check
```

---
