# 🛡️ Cloudflare Turnstile Setup Guide

## 🌟 Why Cloudflare Turnstile?

Cloudflare Turnstile is a free, privacy-focused alternative to Google reCAPTCHA with several advantages:

- ✅ **Completely Free** - No usage limits or costs
- ✅ **Better Privacy** - No user tracking or data collection
- ✅ **Often Invisible** - Many users won't see any challenge
- ✅ **Better Performance** - Faster loading than reCAPTCHA
- ✅ **Better UX** - Less intrusive user experience

## 🚀 Setup Instructions

### Step 1: Get Cloudflare Turnstile Keys

1. **Go to Cloudflare Dashboard**
   - Visit: https://dash.cloudflare.com/
   - Log in to your Cloudflare account (create one if needed)

2. **Navigate to Turnstile**
   - In the left sidebar, click **"Turnstile"**
   - Click **"Add Site"**

3. **Configure Your Site**
   - **Site name**: Enter a name for your site (e.g., "My Laravel App")
   - **Domain**: Add your domain(s):
     - For development: `localhost`
     - For production: `yourdomain.com`
   - **Widget mode**: Choose **"Managed"** (recommended)
   - Click **"Create"**

4. **Get Your Keys**
   - **Site Key**: Copy the Site Key (public key)
   - **Secret Key**: Copy the Secret Key (private key)

### Step 2: Configure Environment Variables

Add these lines to your `.env` file:

```env
# Cloudflare Turnstile Configuration
TURNSTILE_SITE_KEY=your_site_key_here
TURNSTILE_SECRET_KEY=your_secret_key_here
```

### Step 3: Clear Cache

```bash
php artisan config:cache
php artisan route:cache
```

### Step 4: Test Your Setup

1. **Visit your registration page**
2. **Fill out the form**
3. **Complete the Turnstile challenge** (if one appears)
4. **Submit the form**

## 🎛️ Configuration Options

### Widget Appearance

You can customize the Turnstile widget appearance:

```html
<div class="cf-turnstile" 
     data-sitekey="YOUR_SITE_KEY"
     data-theme="light"          <!-- light, dark, auto -->
     data-size="normal"          <!-- normal, compact -->
     data-language="en">         <!-- Language code -->
</div>
```

### Environment-Specific Setup

#### Development Environment
```env
TURNSTILE_SITE_KEY=1x00000000000000000000AA
TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA
```
*Note: These are dummy keys for testing - always passes*

#### Production Environment
```env
TURNSTILE_SITE_KEY=0x4AAAAAAABkMYinukNVWwtf
TURNSTILE_SECRET_KEY=0x4AAAAAAABkMYiojzF-AgVPq9pqP0hZqKT
```
*Note: Replace with your actual production keys*

## 🔧 Advanced Configuration

### Custom Error Messages

You can customize error messages in the validation rule:

```php
// In app/Rules/TurnstileRule.php
$fail('Custom error message here.');
```

### Disable in Development

To disable Turnstile in development, leave the keys empty:

```env
TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=
```

### Multiple Domains

Add multiple domains in Cloudflare dashboard:
- `localhost` (development)
- `yourdomain.com` (production)
- `staging.yourdomain.com` (staging)

## 🛠️ Troubleshooting

### Common Issues

1. **Widget not loading**
   - Check if the site key is correct
   - Verify domain is added to Cloudflare
   - Check console for JavaScript errors

2. **Validation failing**
   - Verify secret key is correct
   - Check server logs for error details
   - Ensure HTTPS in production

3. **"Invalid domain" error**
   - Add your domain to Cloudflare Turnstile settings
   - Check that the domain matches exactly

### Debug Mode

Enable debug logging by adding to your `.env`:

```env
LOG_LEVEL=debug
```

Check logs at `storage/logs/laravel.log` for Turnstile validation details.

## 📈 Benefits Over reCAPTCHA

| Feature | Turnstile | reCAPTCHA |
|---------|-----------|-----------|
| **Cost** | Free | Free (with limits) |
| **Privacy** | No tracking | Tracks users |
| **Performance** | Faster | Slower |
| **UX** | Often invisible | More intrusive |
| **Setup** | Simple | Complex |

## 🔒 Security

Turnstile provides excellent bot protection while being more privacy-friendly than reCAPTCHA:

- **No user tracking** - Doesn't collect personal data
- **No cookies** - Works without tracking cookies
- **EU compliant** - GDPR friendly
- **Fast verification** - Usually completes in milliseconds

## 🎯 Production Checklist

Before going live:

- [ ] Replace dummy keys with production keys
- [ ] Add production domain to Cloudflare
- [ ] Test form submission
- [ ] Enable HTTPS
- [ ] Check error handling
- [ ] Monitor logs for issues

## 🆘 Support

- **Cloudflare Turnstile Docs**: https://developers.cloudflare.com/turnstile/
- **Community Forum**: https://community.cloudflare.com/
- **Status Page**: https://www.cloudflarestatus.com/ 