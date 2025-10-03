<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-post verifisering - Nubilux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .verification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .verification-body {
            padding: 2rem;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .error-icon {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="verification-card">
                    <div class="verification-header">
                        <h1>Nubilux</h1>
                        <p class="mb-0">E-post verifisering</p>
                    </div>
                    <div class="verification-body text-center">
                        <?php if ($success): ?>
                            <div class="success-icon">
                                <i class="fas fa-check-circle"></i>
                                ✅
                            </div>
                            <h3 class="text-success mb-3">Vellykket!</h3>
                            <p class="mb-4"><?= htmlspecialchars($message) ?></p>
                            <a href="index.php" class="btn btn-success btn-lg">
                                Logg inn nå
                            </a>
                        <?php else: ?>
                            <div class="error-icon">
                                <i class="fas fa-exclamation-circle"></i>
                                ❌
                            </div>
                            <h3 class="text-danger mb-3">Feil</h3>
                            <p class="mb-4"><?= htmlspecialchars($message) ?></p>
                            <div class="d-grid gap-2">
                                <a href="register.php" class="btn btn-primary">
                                    Registrer på nytt
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    Tilbake til forsiden
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>