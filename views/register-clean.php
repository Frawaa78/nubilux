<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrer deg - Nubilux</title>
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
        
        .register-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 20px;
            transition: padding-top 0.3s ease;
        }
        
        .register-form {
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
        
        .login-link {
            text-align: center;
            margin-top: 24px;
        }
        
        .login-link a {
            color: #6B7280;
            text-decoration: none;
            font-size: 14px;
        }
        
        .login-link a:hover {
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
        
        .error-message ul {
            margin: 0;
            padding-left: 20px;
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
        
        .form-row {
            display: flex;
            gap: 12px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .register-container {
                padding: 20px 16px;
            }
            
            .header {
                padding: 19.2px 0; /* 20% økning fra 16px til 19.2px */
            }
            
            /* Mobile keyboard optimization - kun på mobil */
            .header.mobile-focused {
                padding: 19.2px 0 !important; /* BEHOLDER samme padding som normalt */
            }
            
            .register-container.mobile-focused {
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
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
        
        /* Prevent zoom on iOS */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            select,
            textarea,
            input[type="text"],
            input[type="password"],
            input[type="email"],
            input[type="tel"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="/shared/images/logo.svg" alt="Nubilux" class="logo">
    </div>
    
    <div class="register-container">
        <form class="register-form" method="POST" action="register.php">
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">
                    <strong>Registrering vellykket!</strong><br>
                    <?= isset($successMessage) ? htmlspecialchars($successMessage) : 'Du kan nå logge inn.' ?>
                </div>
                <a href="index.php" class="btn btn-primary">
                    Gå til innlogging
                </a>
            <?php else: ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">Fornavn</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            class="form-input" 
                            required 
                            value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">Etternavn</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            class="form-input" 
                            required 
                            value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                        >
                    </div>
                </div>
                
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
                    <label for="phone" class="form-label">Telefonnummer (valgfritt)</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-input" 
                        autocomplete="tel"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
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
                        autocomplete="new-password"
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Bekreft passord</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        required 
                        autocomplete="new-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Registrer deg
                </button>
            <?php endif; ?>
            
            <div class="login-link">
                <a href="index.php">Har du allerede en konto? Logg inn</a>
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
            const registerContainer = document.querySelector('.register-container');
            const inputs = document.querySelectorAll('.form-input');
            
            // Focus event - kun på mobil
            function addMobileFocus() {
                header.classList.add('mobile-focused');
                registerContainer.classList.add('mobile-focused');
            }
            
            // Blur event - kun på mobil
            function removeMobileFocus() {
                // Sjekk om noen av input-feltene fortsatt har focus
                setTimeout(() => {
                    const activeElement = document.activeElement;
                    const isInputFocused = Array.from(inputs).includes(activeElement);
                    
                    if (!isInputFocused) {
                        header.classList.remove('mobile-focused');
                        registerContainer.classList.remove('mobile-focused');
                    }
                }, 100);
            }
            
            // Legg til event listeners for alle input-felt
            inputs.forEach(input => {
                input.addEventListener('focus', addMobileFocus);
                input.addEventListener('blur', removeMobileFocus);
            });
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