<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
redirectIfLoggedIn();

$pageTitle = "Connexion - Dofus Dungeon Pass";
$bodyClass = "auth-page";
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
  <div class="auth-card">
    <h1>Connexion</h1>
    <form class="auth-form" method="POST">
      <div class="form-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required class="form-control">
      </div>
      
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required class="form-control">
      </div>
      
      <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
    </form>
    
    <div class="auth-footer">
      <p>Pas encore de compte? <a href="/register.php">S'inscrire</a></p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>