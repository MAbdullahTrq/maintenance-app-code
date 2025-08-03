# Twilio SMS Setup Guide

This guide will help you set up Twilio SMS functionality for the MaintainXtra application.

## Prerequisites

1. A Twilio account (sign up at [twilio.com](https://www.twilio.com))
2. A Twilio phone number for sending SMS messages
3. Your Twilio Account SID and Auth Token

## Configuration

### 1. Environment Variables

Add the following variables to your `.env` file:

```env
# Twilio Configuration
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_FROM_NUMBER=+1234567890
TWILIO_SMS_ENABLED=true
TWILIO_WEBHOOK_URL=https://your-domain.com/twilio/webhook
```

### 2. Get Your Twilio Credentials

1. Log in to your Twilio Console
2. Go to the Dashboard
3. Copy your Account SID and Auth Token
4. Go to Phone Numbers → Manage → Active numbers
5. Purchase or use an existing phone number for sending SMS

### 3. Phone Number Format

The system expects phone numbers in international format:
- ✅ `+1234567890` (US number)
- ✅ `+447911123456` (UK number)
- ❌ `1234567890` (missing country code)
- ❌ `(123) 456-7890` (formatted number)

## Testing

### Test SMS Command

Use the artisan command to test SMS functionality:

```bash
php artisan sms:test +1234567890
```

Replace `+1234567890` with your actual phone number.

### Test Technician Assignment

1. Create a maintenance request
2. Assign it to a technician with a valid phone number
3. The technician should receive both email and SMS notifications

## SMS Message Format

When a technician is assigned to a maintenance request, they will receive an SMS with the following format:

```
🔧 New Maintenance Request Assigned

Property: [Property Name]
Title: [Request Title]
Priority: [Priority Level]
Due Date: [Due Date]

Please review and take appropriate action.

MaintainXtra
```

## Troubleshooting

### Common Issues

1. **"Invalid phone number format"**
   - Ensure phone numbers are in international format with country code
   - Example: `+1234567890` for US numbers

2. **"SMS sending is disabled"**
   - Set `TWILIO_SMS_ENABLED=true` in your `.env` file

3. **"Failed to send SMS"**
   - Check your Twilio credentials
   - Verify your Twilio account has sufficient credits
   - Check the Laravel logs for detailed error messages

### Logs

SMS-related logs are written to the Laravel log file:
- Success: `storage/logs/laravel.log`
- Errors: Check for "Failed to send SMS" messages

### Disabling SMS

To disable SMS functionality temporarily:
```env
TWILIO_SMS_ENABLED=false
```

## Security Considerations

1. **Environment Variables**: Never commit your Twilio credentials to version control
2. **Phone Number Validation**: The system validates phone numbers before sending
3. **Error Handling**: Failed SMS attempts are logged but don't break the application
4. **Rate Limiting**: Consider implementing rate limiting for SMS sending

## Cost Considerations

- Twilio charges per SMS sent
- International SMS rates vary by country
- Consider implementing SMS limits or user preferences
- Monitor your Twilio usage in the Twilio Console

## Support

For Twilio-specific issues:
- [Twilio Documentation](https://www.twilio.com/docs)
- [Twilio Support](https://www.twilio.com/help)

For application-specific issues:
- Check the Laravel logs
- Review the SMS service implementation
- Test with the provided artisan command 