<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glemt passord - Nubilux</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background-color: #2C2D32;
            padding: 20px 0;
            text-align: center;
        }
        
        .logo {
            height: 40px;
            width: auto;
        }
        
        .forgot-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 20px;
        }
        
        .forgot-form {
            width: 100%;
            max-width: 400px;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 32px;
            color: #2C2D32;
            font-size: 24px;
            font-weight: 600;
        }
        
        .form-description {
            text-align: center;
            margin-bottom: 32px;
            color: #6B7280;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #2C2D32;
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            transition: border-color 0.2s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2C2D32;
        }
        
        .btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 16px;
        }
        
        .btn-primary {
            background-color: #2C2D32;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1f2025;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: #2C2D32;
            border: 2px solid #2C2D32;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-secondary:hover {
            background-color: #2C2D32;
            color: white;
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 24px;
        }
        
        .back-to-login a {
            color: #6B7280;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-to-login a:hover {
            color: #2C2D32;
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        .success-message {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        
        /* Mobile optimizations */
        @media (max-width: 480px) {
            .forgot-container {
                padding: 20px 16px;
            }
            
            .header {
                padding: 16px 0;
            }
            
            .logo {
                height: 36px;
            }
            
            .form-title {
                font-size: 20px;
                margin-bottom: 24px;
            }
            
            .form-description {
                font-size: 14px;
                margin-bottom: 24px;
            }
            
            .form-input {
                padding: 14px;
            }
            
            .btn {
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/shared/images/logo.svg" alt="Nubilux" class="logo">
    </div>
    
    <div class="forgot-container">
        <div class="forgot-form">
            <h1 class="form-title">Glemt passord?</h1>
            <p class="form-description">
                Skriv inn e-postadressen din, så sender vi deg en lenke for å tilbakestille passordet.
            </p>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <?= htmlspecialchars($error) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
                <a href="index.php" class="btn btn-secondary">
                    Tilbake til innlogging
                </a>
            <?php else: ?>
                <form method="POST" action="forgot-password.php">
                    <div class="form-group">
                        <label for="email" class="form-label">E-postadresse</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            required 
                            autocomplete="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Send tilbakestillingslenke
                    </button>
                    
                    <a href="index.php" class="btn btn-secondary">
                        Tilbake til innlogging
                    </a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>