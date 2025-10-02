<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nubilux</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3>Dashboard</h3>
                        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logg ut</a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <strong>Velkommen, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>!</strong>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Din profil</h5>
                                <p><strong>Navn:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                <p><strong>E-post:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <?php if ($user['phone']): ?>
                                <p><strong>Telefon:</strong> <?= htmlspecialchars($user['phone']) ?></p>
                                <?php endif; ?>
                                <p><strong>Registrert:</strong> <?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></p>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Kommende funksjoner</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">🏠 Hushold administrasjon</li>
                                    <li class="list-group-item">⚡ Energi sporing</li>
                                    <li class="list-group-item">🚗 Transport planlegging</li>
                                    <li class="list-group-item">📅 Aktivitets kalender</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">Tilbake til hjem</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>