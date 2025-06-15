# Technician Welcome Email System

## Overview

When a manager adds a new technician to the system, the technician automatically receives a welcome email with a one-time verification link. This link allows them to set their own password and activate their account.

## How It Works

### 1. Technician Creation
- Manager creates a technician via mobile interface (`/m/at/create`) or web interface (`/technicians/create`)
- System creates user account with temporary password
- Verification token is generated (valid for 24 hours)
- Welcome email is sent to technician

### 2. Welcome Email
- **Subject**: "Welcome to MaintainXtra - Verify Your Account"
- **Content**: Personalized welcome message with account details
- **Action**: "Verify Your Account" button linking to password reset page
- **Template**: `resources/views/emails/technician/welcome.blade.php`

### 3. Account Verification
- Technician clicks verification link
- Redirected to password reset page with special verification context
- Sets their own password
- Account is activated and verification token is cleared
- Redirected to login page with success message

## Technical Implementation

### Database Changes
- Added `verification_token` (string, nullable) to users table
- Added `verification_token_expires_at` (timestamp, nullable) to users table

### New Classes
- `App\Mail\TechnicianWelcomeMail` - Email class for welcome messages
- Migration: `add_verification_token_to_users_table`

### Modified Classes
- `App\Models\User` - Added verification token methods
- `App\Http\Controllers\Auth\ResetPasswordController` - Added verification token handling
- `App\Http\Controllers\Mobile\TechnicianController` - Added welcome email sending
- `App\Http\Controllers\TechnicianController` - Added welcome email sending

### User Model Methods
```php
// Generate a 24-hour verification token
$token = $user->generateVerificationToken();

// Check if token is valid
$isValid = $user->isValidVerificationToken($token);

// Clear verification token after use
$user->clearVerificationToken();
```

### Email Configuration
Make sure your `.env` file has proper SMTP settings:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.smtp2go.com
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="MaintainXtra"
```

## Security Features

1. **Token Expiry**: Verification tokens expire after 24 hours
2. **One-Time Use**: Tokens are cleared after successful password reset
3. **Email Validation**: Only sent to verified email addresses
4. **Secure Generation**: Uses Laravel's secure random string generation

## Error Handling

- If email sending fails, user creation still succeeds
- Manager receives appropriate feedback about email status
- Expired or invalid tokens show clear error messages
- Fallback: Manager can manually provide login details if email fails

## Testing

Run the test suite to verify functionality:
```bash
php artisan test --filter=TechnicianWelcomeEmailTest
```

## Troubleshooting

### Email Not Received
1. Check SMTP configuration in `.env`
2. Verify email address is correct
3. Check spam/junk folders
4. Test email configuration: `php artisan tinker` then `Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });`

### Verification Link Not Working
1. Check if token has expired (24 hours)
2. Verify URL is complete and not truncated
3. Check database for verification token presence
4. Ensure routes are properly configured

### Database Issues
1. Run migrations: `php artisan migrate`
2. Check if verification token columns exist in users table
3. Verify user has proper role assignments 