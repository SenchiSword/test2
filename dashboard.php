<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header('Location: /login.php');
    exit;
}

$pageTitle = "Tableau de bord Passeur";
$bodyClass = "admin-dashboard";

include '../includes/header.php';

// Récupérer les statistiques
$stmt = $pdo->query("SELECT COUNT(*) as total_requests FROM requests");
$totalRequests = $stmt->fetch()['total_requests'];

$stmt = $pdo->query("SELECT COUNT(*) as pending_requests FROM requests WHERE status = 'en attente'");
$pendingRequests = $stmt->fetch()['pending_requests'];

$stmt = $pdo->query("SELECT COUNT(*) as completed_requests FROM requests WHERE status = 'terminé'");
$completedRequests = $stmt->fetch()['completed_requests'];

// Dernières demandes
$stmt = $pdo->query("SELECT r.*, u.username as client_name, d.name as dungeon_name 
                     FROM requests r 
                     JOIN users u ON r.client_id = u.id 
                     JOIN dungeons d ON r.dungeon_id = d.id 
                     ORDER BY r.created_at DESC LIMIT 5");
$recentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container animate-on-load">
    <h1 class="animate__animated animate__fadeInDown">Tableau de bord Passeur</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Demandes totales</h3>
            <p class="big-number"><?php echo $totalRequests; ?></p>
        </div>
        <div class="stat-card">
            <h3>En attente</h3>
            <p class="big-number"><?php echo $pendingRequests; ?></p>
        </div>
        <div class="stat-card">
            <h3>Terminées</h3>
            <p class="big-number"><?php echo $completedRequests; ?></p>
        </div>
    </div>
    
    <div class="table-container mt-4">
        <h2>Dernières demandes</h2>
        <table>
            <thead>
                <tr>
                    <th>Créé le</th>
                    <th>Client</th>
                    <th>Donjon</th>
                    <th>Statut</th>
                    <th>Prix</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recentRequests as $request): ?>
                <tr>
                    <td><?php echo date('d/m/Y H:i', strtotime($request['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($request['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['dungeon_name']); ?></td>
                    <td class="status-<?php echo str_replace(' ', '-', $request['status']); ?>">
                        <?php echo ucfirst($request['status']); ?>
                    </td>
                    <td><?php echo number_format($request['total_price'], 2); ?> kamas</td>
                    <td>
                        <a href="requests.php?action=view&id=<?php echo $request['id']; ?>" class="btn btn-sm">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>