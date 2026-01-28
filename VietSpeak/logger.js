// Production-safe logger wrapper
const DEBUG = false; // Set to true only in development

window.logger = {
    log: (...args) => {
        if (DEBUG) console.log(...args);
    },
    error: (...args) => console.error(...args), // Always log errors
    warn: (...args) => {
        if (DEBUG) console.warn(...args);
    },
    info: (...args) => {
        if (DEBUG) console.info(...args);
    }
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = logger;
}
