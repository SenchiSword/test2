<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
redirectIfLoggedIn();

$pageTitle = "Inscription - Dofus Dungeon Pass";
$bodyClass = "auth-page";
include __DIR__ . '/includes/header.php';
?>

<div class="auth-container">
  <div class="auth-card">
    <h1>Inscription</h1>
    <form class="auth-form" method="POST">
      <div class="form-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required class="form-control">
      </div>
      
      <div class="form-group">
        <label for="email">Adresse email</label>
        <input type="email" id="email" name="email" required class="form-control">
      </div>
      
      <div class="form-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required class="form-control">
      </div>
      
      <div class="form-group">
        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" id="password_confirm" name="password_confirm" required class="form-control">
      </div>
      
      <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
    </form>
    
    <div class="auth-footer">
      <p>Déjà un compte? <a href="/login.php">Se connecter</a></p>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>