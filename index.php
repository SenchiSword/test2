<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = "Dofus Dungeon Pass - Accueil";
$bodyClass = "home-page";
include __DIR__ . '/includes/header.php';
?>

<main class="home-container">
  <section class="hero-section">
    <div class="container">
      <h1>Dofus Dungeon Pass</h1>
      <p class="subtitle">Trouvez des passeurs pour vos donjons et succès Dofus</p>
      <div class="cta-buttons">
        <a href="/register.php" class="btn btn-primary">Commencer</a>
        <a href="/login.php" class="btn btn-secondary">Connexion</a>
      </div>
    </div>
  </section>

  <section class="features-section">
    <div class="container">
      <h2>Pourquoi choisir notre service?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <h3>Passeurs expérimentés</h3>
          <p>Nos guides ont une grande expérience des donjons et vous accompagnent efficacement.</p>
        </div>
        <div class="feature-card">
          <h3>Prix compétitifs</h3>
          <p>Tarifs transparents avec différentes options selon vos besoins.</p>
        </div>
        <div class="feature-card">
          <h3>Suivi en temps réel</h3>
          <p>Suivez l'avancement de votre demande directement sur votre tableau de bord.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>