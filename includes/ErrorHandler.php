<?php
/**
 * File: includes/ErrorHandler.php
 * 
 * Purpose: Centralized error handling for CodeGaming.
 * 
 * Features:    
 *   - Handles PHP errors, exceptions, and shutdowns.
 *   - Logs errors to a file and database.
 *   - Displays user-friendly error messages in production.
 *   - Provides detailed error information in development.
 * Usage:
 *   - Automatically invoked on errors, exceptions, and shutdowns.
 *   - Can be used to log custom errors and exceptions.
 * Included Files/Dependencies:
 *  - includes/Database.php for logging errors to the database.
 *  - Uses PHP's built-in error handling functions.
 *  - Can be included in any script to handle errors consistently.
 * 
 * Author: CodeGaming Team
 * Last Updated: July 26, 2025
 */
class ErrorHandler {
    private static $instance = null;
    private $db;
    private $errors = [];
    private $isProduction = false;

    private function __construct() {
        $this->db = Database::getInstance();
        $this->isProduction = getenv('ENVIRONMENT') === 'production';
        
        // Set error reporting based on environment
        if ($this->isProduction) {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', '0');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }

        // Set custom error handler
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false; // This error code is not included in error_reporting
        }

        $error = [
            'type' => $this->getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ];

        $this->logError($error);

        if ($this->isProduction) {
            $this->displayError('An error occurred. Please try again later.');
        } else {
            $this->displayError($error);
        }

        return true;
    }

    public function handleException($exception) {
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        ];

        $this->logError($error);

        if ($this->isProduction) {
            $this->displayError('An unexpected error occurred. Please try again later.');
        } else {
            $this->displayError($error);
        }
    }

    public function handleShutdown() {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    private function getErrorType($type) {
        switch($type) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            default:
                return 'UNKNOWN';
        }
    }

    private function logError($error) {
        $logMessage = sprintf(
            "[%s] %s: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );

        // Log to error log
        error_log($logMessage);

        // Log to database if available
        try {
            $this->db->logError($error['message'], [
                'type' => $error['type'],
                'file' => $error['file'],
                'line' => $error['line']
            ]);
        } catch (Exception $e) {
            // If database logging fails, just log to error log
            error_log("Failed to log error to database: " . $e->getMessage());
        }
    }

    public function displayError($error) {
        if (headers_sent()) {
            echo $this->formatError($error);
            return;
        }

        // For AJAX requests
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $this->isProduction ? 
                    'An error occurred. Please try again later.' : 
                    $error['message']
            ]);
            return;
        }

        // For regular requests
        if ($this->isProduction) {
            header('HTTP/1.1 500 Internal Server Error');
            include 'templates/error.php';
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo $this->formatError($error);
        }
    }

    private function formatError($error) {
        if ($this->isProduction) {
            return '<div class="error-message">An error occurred. Please try again later.</div>';
        }

        $output = '<div class="error-container">';
        $output .= '<h2>Error Details</h2>';
        $output .= '<div class="error-info">';
        $output .= '<p><strong>Type:</strong> ' . htmlspecialchars($error['type']) . '</p>';
        $output .= '<p><strong>Message:</strong> ' . htmlspecialchars($error['message']) . '</p>';
        $output .= '<p><strong>File:</strong> ' . htmlspecialchars($error['file']) . '</p>';
        $output .= '<p><strong>Line:</strong> ' . htmlspecialchars($error['line']) . '</p>';
        
        if (isset($error['trace'])) {
            $output .= '<h3>Stack Trace</h3>';
            $output .= '<pre>' . htmlspecialchars(print_r($error['trace'], true)) . '</pre>';
        }
        
        $output .= '</div></div>';
        
        return $output;
    }

    public function addError($message, $context = []) {
        $this->errors[] = [
            'message' => $message,
            'context' => $context,
            'time' => date('Y-m-d H:i:s')
        ];
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }

    public function clearErrors() {
        $this->errors = [];
    }
} 
