<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - CodeGaming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dc3545;
        }
        .error-title {
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .error-actions {
            margin-top: 2rem;
        }
        .error-actions .btn {
            margin: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1 class="error-title">Oops! Something went wrong</h1>
            <p class="error-message">
                We apologize, but an error occurred while processing your request.
                Our team has been notified and we're working to fix the issue.
            </p>
            <div class="error-actions">
                <a href="/" class="btn btn-primary">Return to Home</a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">Go Back</a>
            </div>
            <?php if (!getenv('ENVIRONMENT') === 'production'): ?>
            <div class="mt-4 text-start">
                <small class="text-muted">
                    Error Reference: <?php echo uniqid('ERR-'); ?><br>
                    Time: <?php echo date('Y-m-d H:i:s'); ?>
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 
