<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
authenticate();

if (isAdmin()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$pageTitle = "Mes Demandes";
$bodyClass = "client-requests";

// Récupérer les demandes de l'utilisateur
$stmt = $pdo->prepare("SELECT r.*, d.name as dungeon_name, d.image as dungeon_image 
                      FROM requests r 
                      JOIN dungeons d ON r.dungeon_id = d.id 
                      WHERE r.client_id = ? 
                      ORDER BY r.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les succès pour chaque demande
foreach ($requests as &$request) {
    $stmt = $pdo->prepare("SELECT a.name FROM request_achievements ra 
                          JOIN achievements a ON ra.achievement_id = a.id 
                          WHERE ra.request_id = ?");
    $stmt->execute([$request['id']]);
    $request['achievements'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
unset($request);

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Mes Demandes de Passage</h1>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Votre demande a été créée avec succès!</div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
    <div class="no-requests">
        <p>Vous n'avez aucune demande pour le moment.</p>
        <a href="request.php" class="btn">Créer une demande</a>
    </div>
    <?php else: ?>
    <div class="requests-grid">
        <?php foreach ($requests as $request): ?>
        <div class="request-card">
            <div class="request-header">
                <?php if ($request['dungeon_image']): ?>
                <img src="/assets/images/dungeon-icons/<?= htmlspecialchars($request['dungeon_image']) ?>" 
                     alt="<?= htmlspecialchars($request['dungeon_name']) ?>" class="dungeon-icon">
                <?php endif; ?>
                <h3><?= htmlspecialchars($request['dungeon_name']) ?></h3>
            </div>
            
            <div class="request-details">
                <p><strong>Statut:</strong> 
                    <span class="status-<?= str_replace(' ', '-', $request['status']) ?>">
                        <?= ucfirst($request['status']) ?>
                    </span>
                </p>
                
                <p><strong>Créée le:</strong> <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></p>
                
                <p><strong>Prix total:</strong> <?= number_format($request['total_price'], 2) ?> kamas</p>
                
                <?php if (!empty($request['achievements'])): ?>
                <div class="request-achievements">
                    <strong>Succès:</strong>
                    <ul>
                        <?php foreach ($request['achievements'] as $achievement): ?>
                        <li><?= htmlspecialchars($achievement) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if ($request['status'] === 'en attente'): ?>
                <form method="POST" action="cancel-request.php">
                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>