<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logg inn - Nubilux</title>
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
            padding: 24px 0; /* 20% økning fra 20px til 24px */
            text-align: center;
            transition: padding 0.3s ease;
        }
        
        .logo {
            height: 40px;
            width: auto;
        }
        
        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 20px;
            transition: padding-top 0.3s ease;
        }
        
        .login-form {
            width: 100%;
            max-width: 400px;
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
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #2C2D32;
            color: white;
            border: 1px solid #2C2D32;
        }
        
        .btn-primary:hover {
            background-color: #1f2025;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: transparent;
            color: #2C2D32;
            border: 1px solid #2C2D32;
            box-sizing: border-box;
        }
        
        .btn-secondary:hover {
            background-color: #2C2D32;
            color: white;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 24px;
        }
        
        .forgot-password a {
            color: #6B7280;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password a:hover {
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
        @media (max-width: 768px) {
            .login-container {
                padding: 20px 16px;
            }
            
            .header {
                padding: 19.2px 0; /* 20% økning fra 16px til 19.2px */
            }
            
            /* Mobile keyboard optimization - kun på mobil */
            .header.mobile-focused {
                padding: 19.2px 0 !important; /* BEHOLDER samme padding som normalt */
            }
            
            .login-container.mobile-focused {
                padding-top: 10px !important; /* 50% reduksjon fra 20px til 10px */
            }
            
            .logo {
                height: 36px;
            }
            
            .form-input {
                padding: 14px;
            }
            
            .btn {
                padding: 14px;
            }
        }
        
        /* Prevent zoom on iOS */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            select,
            textarea,
            input[type="text"],
            input[type="password"],
            input[type="email"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/shared/images/logo.svg" alt="Nubilux" class="logo">
    </div>
    
    <div class="login-container">
        <form class="login-form" method="POST" action="index.php">
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <?= htmlspecialchars($error) ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['success_message']) ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
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
            
            <div class="form-group">
                <label for="password" class="form-label">Passord</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    required 
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary">
                Logg inn
            </button>
            
            <a href="register.php" class="btn btn-secondary">
                Registrer
            </a>
            
            <div class="forgot-password">
                <a href="forgot-password.php">Glemt passord?</a>
            </div>
        </form>
    </div>
    
    <script>
        // Forbedret mobile deteksjon for nye enheter som iPhone 17 Pro
        function isMobileDevice() {
            // Sjekk for touch capability og skjermstørrelse
            const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            const isSmallScreen = window.innerWidth <= 768; // Økt fra 480px for bedre støtte
            
            return isTouchDevice && isSmallScreen;
        }
        
        // Funksjon for å håndtere mobile focus events
        function handleMobileFocus() {
            const isMobile = isMobileDevice();
            
            if (!isMobile) {
                return;
            }
            
            const header = document.querySelector('.header');
            const loginContainer = document.querySelector('.login-container');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Focus event - kun på mobil
            function addMobileFocus() {
                header.classList.add('mobile-focused');
                loginContainer.classList.add('mobile-focused');
            }
            
            // Blur event - kun på mobil
            function removeMobileFocus() {
                // Sjekk om noen av input-feltene fortsatt har focus
                setTimeout(() => {
                    if (document.activeElement !== emailInput && document.activeElement !== passwordInput) {
                        header.classList.remove('mobile-focused');
                        loginContainer.classList.remove('mobile-focused');
                    }
                }, 100);
            }
            
            // Legg til event listeners
            if (emailInput) {
                emailInput.addEventListener('focus', addMobileFocus);
                emailInput.addEventListener('blur', removeMobileFocus);
            }
            
            if (passwordInput) {
                passwordInput.addEventListener('focus', addMobileFocus);
                passwordInput.addEventListener('blur', removeMobileFocus);
            }
            
            // Alternativ: Legg til visual feedback for testing
            // Fjernet rød ramme etter testing
        }
        
        // Kjør når siden lastes
        document.addEventListener('DOMContentLoaded', function() {
            handleMobileFocus();
        });
        
        // Kjør på window resize for å håndtere orientering endringer
        window.addEventListener('resize', function() {
            setTimeout(handleMobileFocus, 100);
        });
    </script>
</body>
</html>