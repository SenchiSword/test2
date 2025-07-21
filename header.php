<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= $bodyClass ?>">
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="/"><img src="/assets/images/dofus-logo.png" alt="Dofus Dungeon Pass"></a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="/<?= isAdmin() ? 'admin/dashboard.php' : 'client/dashboard.php' ?>">Tableau de bord</a></li>
                        <li><a href="/logout.php">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="/login.php">Connexion</a></li>
                        <li><a href="/register.php">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="content-wrapper">