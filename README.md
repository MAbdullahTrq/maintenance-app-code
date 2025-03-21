# Maintenance Request Management Application

A Laravel-based application to handle maintenance requests submitted by guest users to property managers. Property managers can then allocate those requests to technicians for resolution.

## Features

- **User Roles**:
  - **Super Property Manager**: Oversees the entire platform and manages client accounts.
  - **Property Manager**: Handles work orders, approves requests, and assigns tasks. This user is behind a subscription paywall.
  - **Technician**: Invited by the Property Manager to complete maintenance tasks.
  - **Guest User**: Non-logged-in users who submit maintenance requests via QR code or link.

- **Maintenance Request Workflow**:
  1. **Submit a Maintenance Request** – No Login Required
     - Requesters scan a QR code or click a link to access the request page.
     - They provide location, title, description, images, and optional contact information.
     - The request is instantly sent to the Property Manager dashboard.
  
  2. **Request Review & Approval**
     - The Property Manager reviews and approves the request.
     - Assigns a due date and selects the right technician for the job.
     - The request is converted into a Work Order.
  
  3. **Task Assignment & Technician Notification**
     - The assigned technician receives a notification.
     - They can accept or decline the task.
  
  4. **Work in Progress**
     - The technician logs in and opens the Work Order.
     - They can upload photos, add notes, and provide real-time updates via comments.
  
  5. **Completion & Confirmation**
     - When the job is done, the technician marks it as complete and submits a final report.
     - The Property Manager verifies completion before officially closing the task.
     - Email notifications are sent to both the Property Manager and Requester.

- **Subscription System**:
  - Property Managers need to subscribe to use the platform.
  - Multiple subscription plans with different property and technician limits.
  - PayPal integration for payment processing.

## Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or PostgreSQL
- Node.js and NPM

### Setup Instructions

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/maintenance-app.git
   cd maintenance-app
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Create environment file**:
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Configure your database in the .env file**:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=maintenance_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Configure PayPal in the .env file**:
   ```
   PAYPAL_CLIENT_ID=your_paypal_client_id
   PAYPAL_CLIENT_SECRET=your_paypal_client_secret
   PAYPAL_SANDBOX=true
   ```

8. **Run database migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

9. **Create symbolic link for storage**:
   ```bash
   php artisan storage:link
   ```

10. **Build assets**:
    ```bash
    npm run build
    ```

11. **Start the development server**:
    ```bash
    php artisan serve
    ```

12. **Access the application**:
    Open your browser and navigate to `http://localhost:8000`

### Default Users

After running the seeders, the following users will be created:

- **Super Property Manager**:
  - Email: admin@example.com
  - Password: password

- **Property Manager**:
  - Email: manager@example.com
  - Password: password

- **Technicians**:
  - Email: john@example.com
  - Password: password
  
  - Email: jane@example.com
  - Password: password

## Usage

### For Property Managers

1. **Login** to your account.
2. **Subscribe** to a plan if you haven't already.
3. **Create properties** that you manage.
4. **Invite technicians** to join your team.
5. **Generate QR codes** for each property to allow guests to submit maintenance requests.
6. **Review and approve** maintenance requests.
7. **Assign technicians** to approved requests.
8. **Monitor progress** and verify completed tasks.

### For Technicians

1. **Accept the invitation** sent by the Property Manager.
2. **Login** to your account.
3. **View assigned tasks** on your dashboard.
4. **Update task status** and add comments as you work.
5. **Upload photos** to document your work.
6. **Mark tasks as complete** when finished.

### For Guests

1. **Scan the QR code** or click the link provided by the property manager.
2. **Fill out the maintenance request form** with details about the issue.
3. **Upload photos** if needed.
4. **Submit the request** and receive updates via email if provided.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

- [Laravel](https://laravel.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Alpine.js](https://alpinejs.dev/)
- [PayPal SDK](https://developer.paypal.com/docs/api/overview/)
- [Font Awesome](https://fontawesome.com/)
