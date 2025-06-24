# Upload Configuration Guide - Preventing 413 Errors

This guide helps resolve **413 Request Entity Too Large** errors when uploading images.

## 🚨 Current Issue
Users are experiencing 413 errors when uploading property images, even with client-side compression.

## ✅ Solutions Implemented

### 1. **Client-Side Image Optimization**
- Images automatically resized to max 600px
- Aggressive JPEG compression (target: under 500KB per image)
- Multiple compression attempts if file still too large
- Real-time size feedback to users

### 2. **Server-Side Configuration**

#### **Apache (.htaccess)**
The `.htaccess` file includes:
```apache
php_value upload_max_filesize 10M
php_value post_max_size 12M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
php_value max_file_uploads 20
```

#### **Nginx Configuration**
Add to your nginx configuration:
```nginx
client_max_body_size 10M;
client_body_timeout 300s;
client_header_timeout 300s;
```

#### **PHP Configuration (php.ini)**
```ini
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
max_file_uploads = 20
```

### 3. **Laravel Configuration**
In `config/app.php`:
```php
'upload_max_filesize' => '10M',
'post_max_size' => '12M',
```

## 🔧 **Troubleshooting Steps**

### Step 1: Check Current Limits
Add this to a PHP file to check current settings:
```php
<?php
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
?>
```

### Step 2: Server-Specific Solutions

#### **Shared Hosting (cPanel)**
1. Go to cPanel → Select PHP Version
2. Click "Switch To PHP Options"
3. Increase:
   - `upload_max_filesize` → 10M
   - `post_max_size` → 12M
   - `max_execution_time` → 300

#### **VPS/Dedicated Server**
1. Edit `/etc/php/8.3/apache2/php.ini` (adjust PHP version)
2. Restart Apache: `sudo systemctl restart apache2`

#### **Cloudflare (if used)**
- Check Cloudflare's upload limits (100MB for Pro plans)
- Temporarily disable Cloudflare for testing

### Step 3: Test Upload Limits
Create a test file:
```php
<?php
$maxUpload = ini_get('upload_max_filesize');
$maxPost = ini_get('post_max_size');
$memoryLimit = ini_get('memory_limit');

echo "Max upload size: " . $maxUpload . "\n";
echo "Max post size: " . $maxPost . "\n";
echo "Memory limit: " . $memoryLimit . "\n";

// Convert to bytes for comparison
function parseSize($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    return round($size);
}

$maxUploadBytes = parseSize($maxUpload);
$maxPostBytes = parseSize($maxPost);

echo "Max upload bytes: " . $maxUploadBytes . "\n";
echo "Max post bytes: " . $maxPostBytes . "\n";

echo "Effective upload limit: " . min($maxUploadBytes, $maxPostBytes) . " bytes\n";
?>
```

## 📱 **Client-Side Features**

### **Visual Feedback**
- Shows original file size vs optimized size
- Button changes color based on file size:
  - 🟢 Green: Under 1MB (safe)
  - 🟠 Orange: 1MB+ (might be slow)
  - 🔴 Red: Would show for very large files

### **Compression Process**
1. Image resized to max 600px (maintaining aspect ratio)
2. Converted to JPEG for better compression
3. Multiple compression attempts (quality: 70% → 60% → 50% → 40% → 10%)
4. Target size: Under 500KB per image

## 🛠 **Additional Debugging**

### Check Server Response
Use browser developer tools:
1. F12 → Network tab
2. Try uploading an image
3. Look for 413 response
4. Check response headers for server info

### Server Logs
Check error logs:
- Apache: `/var/log/apache2/error.log`
- Nginx: `/var/log/nginx/error.log`
- PHP: Check `error_log` location in php.ini

## 🔄 **Quick Fix Priorities**

1. **Immediate**: Client-side compression is working
2. **Server**: Increase `post_max_size` to 12M minimum
3. **Proxy**: Check if Cloudflare/CDN has upload limits
4. **Code**: Server-side image processing as backup

## 🆘 **Emergency Workaround**

If nothing else works, you can temporarily:
1. Disable image uploads
2. Use external image hosting (Cloudinary, AWS S3)
3. Process images asynchronously via queue

## ✅ **Success Indicators**

When working correctly:
- Images show compressed size before upload
- Submit button turns green for small files
- No 413 errors in browser console
- Property/request creation succeeds

---

**Note**: The most common cause is `post_max_size` being smaller than `upload_max_filesize`. Always ensure `post_max_size` is larger than `upload_max_filesize`. 