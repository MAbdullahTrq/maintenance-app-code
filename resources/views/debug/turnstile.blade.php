<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnstile Debug - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; border-radius: 5px; }
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
        .info { color: blue; }
        .warning { color: orange; font-weight: bold; }
        #debug-log { background: #000; color: #0f0; padding: 15px; font-family: monospace; min-height: 300px; border-radius: 5px; overflow-y: auto; max-height: 400px; }
        .config-table { width: 100%; border-collapse: collapse; }
        .config-table th, .config-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .config-table th { background: #f2f2f2; }
        .widget-container { border: 2px dashed #ccc; padding: 20px; min-height: 100px; background: #fff; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 Turnstile CAPTCHA Debug Page</h1>
    <p><strong>Generated at:</strong> {{ now()->format('Y-m-d H:i:s T') }}</p>
    
    <div class="debug-section">
        <h2>📋 Environment Configuration</h2>
        <table class="config-table">
            <tr>
                <th>Setting</th>
                <th>Value</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>App Environment</td>
                <td>{{ config('app.env') }}</td>
                <td>{{ config('app.env') === 'production' ? '🟡 Production' : '🟢 Development' }}</td>
            </tr>
            <tr>
                <td>App Debug</td>
                <td>{{ config('app.debug') ? 'true' : 'false' }}</td>
                <td>{{ config('app.debug') ? '🟢 Enabled' : '🔴 Disabled' }}</td>
            </tr>
            <tr>
                <td>Turnstile Site Key</td>
                <td>{{ config('services.turnstile.site_key') ? substr(config('services.turnstile.site_key'), 0, 20) . '...' : 'NOT SET' }}</td>
                <td>{{ config('services.turnstile.site_key') ? '🟢 Configured' : '🔴 Missing' }}</td>
            </tr>
            <tr>
                <td>Turnstile Secret Key</td>
                <td>{{ config('services.turnstile.secret_key') ? substr(config('services.turnstile.secret_key'), 0, 20) . '...' : 'NOT SET' }}</td>
                <td>{{ config('services.turnstile.secret_key') ? '🟢 Configured' : '🔴 Missing' }}</td>
            </tr>
            <tr>
                <td>Current Domain</td>
                <td>{{ request()->getHost() }}</td>
                <td>{{ request()->isSecure() ? '🔒 HTTPS' : '⚠️ HTTP' }}</td>
            </tr>
            <tr>
                <td>Current URL</td>
                <td>{{ request()->url() }}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>User Agent</td>
                <td id="user-agent">Loading...</td>
                <td>-</td>
            </tr>
        </table>
    </div>

    <div class="debug-section">
        <h2>🌐 Network & Script Loading</h2>
        <div id="script-status">🔄 Checking script loading...</div>
        <div id="network-info"></div>
    </div>

    <div class="debug-section">
        <h2>🔧 Turnstile Widget Test</h2>
        <form id="test-form">
            <div class="widget-container" id="turnstile-widget-container">
                <div id="turnstile-widget"></div>
                <div id="widget-placeholder" style="color: #666; text-align: center; padding: 20px;">
                    Widget will appear here...
                </div>
            </div>
            <br>
            <button type="submit">🧪 Test Form Submit</button>
            <button type="button" id="reset-widget">🔄 Reset Widget</button>
            <button type="button" id="manual-render">⚙️ Manual Render</button>
        </form>
        <div id="form-status"></div>
    </div>

    <div class="debug-section">
        <h2>📊 Real-time Debug Log</h2>
        <button type="button" id="clear-log">🗑️ Clear Log</button>
        <div id="debug-log"></div>
    </div>

    <div class="debug-section">
        <h2>🔍 Additional Tests</h2>
        <button type="button" id="test-api-endpoint">📡 Test API Endpoint</button>
        <button type="button" id="check-csp">🛡️ Check CSP Headers</button>
        <button type="button" id="test-cors">🌐 Test CORS</button>
        <div id="additional-tests"></div>
    </div>

    <script>
        const debugLog = document.getElementById('debug-log');
        let widgetId = null;
        
        function log(message, type = 'info') {
            const timestamp = new Date().toISOString();
            const colors = {
                'error': '#f00',
                'success': '#0f0', 
                'warning': '#ff0',
                'info': '#fff'
            };
            const color = colors[type] || '#fff';
            
            debugLog.innerHTML += `<div style="color: ${color}">[${timestamp}] ${message}</div>`;
            debugLog.scrollTop = debugLog.scrollHeight;
            console.log(`[TURNSTILE DEBUG] ${message}`);
        }

        function updateStatus(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const icons = {
                'error': '❌',
                'success': '✅',
                'warning': '⚠️',
                'info': 'ℹ️'
            };
            element.innerHTML = `${icons[type]} ${message}`;
            element.className = type;
        }

        // Initialize debugging
        log('🚀 Debug script started');
        document.getElementById('user-agent').textContent = navigator.userAgent;

        // Configuration validation
        const siteKey = '{{ config("services.turnstile.site_key") }}';
        const secretKey = '{{ config("services.turnstile.secret_key") }}';
        
        if (!siteKey) {
            log('❌ TURNSTILE_SITE_KEY not configured in environment', 'error');
        } else {
            log(`✅ Site key configured: ${siteKey.substring(0, 10)}...`, 'success');
        }

        // Track script loading
        let scriptLoaded = false;
        let loadStartTime = Date.now();
        
        window.onTurnstileLoad = function() {
            const loadTime = Date.now() - loadStartTime;
            scriptLoaded = true;
            log(`✅ Turnstile script loaded successfully in ${loadTime}ms`, 'success');
            updateStatus('script-status', `Script loaded in ${loadTime}ms`, 'success');
            
            // Hide placeholder and render widget
            document.getElementById('widget-placeholder').style.display = 'none';
            renderWidget();
        };

        function renderWidget() {
            try {
                log('🎯 Attempting to render Turnstile widget...');
                
                if (typeof turnstile === 'undefined') {
                    throw new Error('Turnstile object not available');
                }

                const config = {
                    sitekey: siteKey || '1x00000000000000000000AA', // Cloudflare test key as fallback
                    size: 'flexible', // Use responsive size for testing
                    callback: function(token) {
                        log(`✅ Token received: ${token.substring(0, 20)}...`, 'success');
                        updateStatus('form-status', 'CAPTCHA solved successfully', 'success');
                        document.getElementById('widget-placeholder').style.display = 'none';
                    },
                    'error-callback': function(error) {
                        log(`❌ Turnstile error: ${error}`, 'error');
                        updateStatus('form-status', `CAPTCHA error: ${error}`, 'error');
                    },
                    'expired-callback': function() {
                        log('⏰ Turnstile token expired', 'warning');
                        updateStatus('form-status', 'CAPTCHA expired, please solve again', 'warning');
                    },
                    'timeout-callback': function() {
                        log('⏱️ Turnstile timeout', 'warning');
                        updateStatus('form-status', 'CAPTCHA timed out', 'warning');
                    }
                };

                log('📝 Widget config: ' + JSON.stringify(config, null, 2));
                
                widgetId = turnstile.render('#turnstile-widget', config);
                log(`✅ Widget rendered successfully. Widget ID: ${widgetId}`, 'success');
                
            } catch (error) {
                log(`❌ Error rendering widget: ${error.message}`, 'error');
                updateStatus('form-status', `Render error: ${error.message}`, 'error');
                console.error('Turnstile render error:', error);
            }
        }

        // Timeout check
        setTimeout(() => {
            if (!scriptLoaded) {
                log('❌ Turnstile script failed to load within 10 seconds', 'error');
                updateStatus('script-status', 'Script failed to load (timeout)', 'error');
                
                // Network diagnostics
                log('🔍 Running network diagnostics...', 'info');
                
                fetch('https://challenges.cloudflare.com/turnstile/v0/api.js')
                    .then(response => {
                        log(`📡 Direct script fetch status: ${response.status}`, response.ok ? 'success' : 'error');
                    })
                    .catch(error => {
                        log(`📡 Direct script fetch failed: ${error.message}`, 'error');
                    });
            }
        }, 10000);

        // Form submission test
        document.getElementById('test-form').addEventListener('submit', function(e) {
            e.preventDefault();
            log('📤 Form submitted', 'info');
            
            if (typeof turnstile !== 'undefined') {
                const response = turnstile.getResponse(widgetId);
                if (response) {
                    log(`📋 Turnstile response: ${response.substring(0, 50)}...`, 'success');
                    
                    // Test server-side validation
                    fetch('{{ route('register') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            'cf-turnstile-response': response,
                            test: true
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        log('📊 Server validation response: ' + JSON.stringify(data), 'info');
                    })
                    .catch(error => {
                        log('📊 Server validation error: ' + error.message, 'error');
                    });
                } else {
                    log('❌ No Turnstile response found', 'error');
                }
            } else {
                log('❌ Turnstile object not available', 'error');
            }
        });

        // Additional controls
        document.getElementById('reset-widget').addEventListener('click', function() {
            if (typeof turnstile !== 'undefined' && widgetId !== null) {
                turnstile.reset(widgetId);
                log('🔄 Widget reset', 'info');
            }
        });

        document.getElementById('manual-render').addEventListener('click', function() {
            if (scriptLoaded) {
                renderWidget();
            } else {
                log('❌ Cannot render: script not loaded', 'error');
            }
        });

        document.getElementById('clear-log').addEventListener('click', function() {
            debugLog.innerHTML = '';
            log('🗑️ Log cleared');
        });

        // Additional tests
        document.getElementById('test-api-endpoint').addEventListener('click', function() {
            log('📡 Testing Turnstile API endpoint...', 'info');
            fetch('https://challenges.cloudflare.com/turnstile/v0/siteverify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'secret=test&response=test'
            })
            .then(response => {
                log(`📡 API endpoint status: ${response.status} ${response.statusText}`, response.ok ? 'success' : 'error');
                return response.json();
            })
            .then(data => {
                log('📡 API response: ' + JSON.stringify(data), 'info');
            })
            .catch(error => {
                log(`📡 API endpoint error: ${error.message}`, 'error');
            });
        });

        // Global error monitoring
        window.addEventListener('error', function(e) {
            log(`🚨 Global error: ${e.message} at ${e.filename}:${e.lineno}:${e.colno}`, 'error');
        });

        window.addEventListener('unhandledrejection', function(e) {
            log(`🚨 Unhandled promise rejection: ${e.reason}`, 'error');
        });

        // CSP and CORS checks
        document.getElementById('check-csp').addEventListener('click', function() {
            log('🛡️ Checking CSP headers...', 'info');
            const metaCsp = document.querySelector('meta[http-equiv="Content-Security-Policy"]');
            if (metaCsp) {
                log('🛡️ CSP meta tag found: ' + metaCsp.content, 'info');
            } else {
                log('🛡️ No CSP meta tag found', 'info');
            }
        });

        log('⏳ Waiting for Turnstile script to load...');
    </script>

    <!-- Load Turnstile with debugging parameters -->
    <script 
        src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad&render=explicit" 
        async 
        defer
        onerror="log('❌ Script tag onerror triggered', 'error')"
        onload="log('📜 Script tag onload triggered', 'info')"
    ></script>
</body>
</html> 