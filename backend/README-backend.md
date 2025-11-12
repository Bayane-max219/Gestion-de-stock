# Inventory Management System - Backend

## Setup Instructions

1. Clone the repository
2. Navigate to the backend directory
3. Copy `.env.example` to `.env` and configure your database settings
4. Install dependencies:
   ```bash
   composer install
   ```
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Run migrations and seeders:
   ```bash
   php artisan migrate:fresh --seed
   ```
7. Start the development server:
   ```bash
   php artisan serve
   ```

## Demo Credentials

The following users are created by the seeder:

### Administrator
- Email: admin@example.com
- Password: admin123
- Access: Full system access, all stores

### Sales Agent
- Email: sales@example.com
- Password: sales123
- Access: Sales operations, Main store only

### Warehouse Manager
- Email: warehouse@example.com
- Password: warehouse123
- Access: Stock management, all stores

## API Documentation

The API documentation is available at `/api/documentation` when running the development server.

## Development Notes

- The system uses Laravel Sanctum for API authentication
- Store-based access control is implemented through middleware
- Automatic sequential invoice numbering per store
- Full audit logging for all operations
- PDF generation for invoices and reports

## Testing

Run the test suite with:
```bash
php artisan test
```

## License

This project is proprietary and confidential.