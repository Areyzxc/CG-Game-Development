/**
 * API Helper - Handles all API requests with CSRF token
 */

class APIHelper {
    static getCSRFToken() {
        // Get token from meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (!metaTag) {
            console.error('CSRF token meta tag not found');
            return '';
        }
        const token = metaTag.getAttribute('content');
        if (!token) {
            console.error('CSRF token is empty');
        } else {
            console.log('Retrieved CSRF token:', token.substring(0, 8) + '...');
        }
        return token || '';
    }

    static async fetchWithAuth(url, options = {}) {
        // Ensure we have a valid URL
        if (!url) {
            console.error('No URL provided for API request');
            return { error: 'No URL provided' };
        }

        // Set default headers
        const csrfToken = this.getCSRFToken();
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };

        // Log the request for debugging
        console.log(`API Request: ${options.method || 'GET'} ${url}`, { 
            headers: { ...headers, 'X-CSRF-Token': '***' + (csrfToken ? csrfToken.slice(-4) : '') },
            body: options.body ? JSON.parse(options.body) : null
        });

        // Merge headers
        options.headers = { ...headers, ...(options.headers || {}) };

        try {
            const response = await fetch(url, options);
            
            // Log the response for debugging
            console.log(`API Response: ${response.status} ${response.statusText}`, response);

            // Handle 401 Unauthorized
            if (response.status === 401) {
                console.warn('Authentication required, redirecting to login');
                window.location.href = 'login.php?redirect=' + encodeURIComponent(window.location.pathname);
                return { error: 'Authentication required' };
            }

            // Handle 403 Forbidden (CSRF token mismatch)
            if (response.status === 403) {
                console.error('CSRF token validation failed. Please refresh the page.');
                // Try to get more details from the response
                let errorData = {};
                try {
                    errorData = await response.json();
                } catch (e) {
                    console.error('Failed to parse error response:', e);
                }
                
                // Show a user-friendly message
                alert('Your session has expired. Please refresh the page and try again.');
                window.location.reload();
                return { 
                    error: 'CSRF token validation failed',
                    details: errorData 
                };
            }

            // Parse and return JSON response
            try {
                const data = await response.json();
                if (!response.ok) {
                    console.error('API Error:', data);
                }
                return data;
            } catch (e) {
                console.error('Failed to parse JSON response:', e);
                return { 
                    error: 'Invalid response from server',
                    status: response.status
                };
            }
        } catch (error) {
            console.error('API request failed:', error);
            return { 
                error: 'Network error: ' + error.message,
                code: error.code
            };
        }
    }

    // Example usage:
    // const data = await APIHelper.fetchWithAuth('api/check-first-visit.php');
    // const result = await APIHelper.fetchWithAuth('api/some-endpoint.php', { method: 'POST', body: JSON.stringify({ key: 'value' }) });
}

// Add to global scope if needed
window.APIHelper = APIHelper;