const CONFIG = {
    API_URL: 'http://presentation-management.test/api',
    DEBUG: true
};

// Global fallback if needed (though auth.js defines its own)
window.API_URL = CONFIG.API_URL;
