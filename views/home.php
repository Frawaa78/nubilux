<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nubilux - Hjem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <h1 class="card-title text-primary">Nubilux</h1>
                        <p class="card-text">Velkommen til Nubilux - Din smarte husstand assistent</p>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-success">
                                Velkommen tilbake, <?= $_SESSION['user_name'] ?>!
                            </div>
                            <a href="dashboard.php" class="btn btn-primary">Gå til Dashboard</a>
                            <a href="logout.php" class="btn btn-outline-secondary">Logg ut</a>
                        <?php else: ?>
                            <p class="text-muted">Logg inn eller registrer deg for å komme i gang</p>
                            <div class="mt-4">
                                <a href="login.php" class="btn btn-primary">Logg inn</a>
                                <a href="register.php" class="btn btn-outline-primary">Registrer deg</a>
                            </div>
                        <?php endif; ?>
                        
                        <hr class="mt-4">
                        <small class="text-muted">
                            PHP <?= PHP_VERSION ?> | <?= date('Y-m-d H:i:s') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>