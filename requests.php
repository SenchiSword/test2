<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
authenticate();

if (!isAdmin()) {
    header('Location: /login.php');
    exit;
}

$pageTitle = "Gestion des Demandes";
$bodyClass = "admin-requests";

// Changement de statut
if (isset($_GET['action']) && isset($_GET['id'])) {
    $validStatuses = ['en attente', 'en cours', 'terminé', 'annulé'];
    
    if ($_GET['action'] === 'update_status' && isset($_POST['new_status']) && in_array($_POST['new_status'], $validStatuses)) {
        $stmt = $pdo->prepare("UPDATE requests SET status = ?, passeur_id = ? WHERE id = ?");
        $stmt->execute([
            $_POST['new_status'],
            $_SESSION['user_id'],
            $_GET['id']
        ]);
        header('Location: requests.php?updated=1');
        exit;
    }
}

// Récupérer toutes les demandes
$stmt = $pdo->query("SELECT r.*, u.username as client_name, d.name as dungeon_name 
                     FROM requests r 
                     JOIN users u ON r.client_id = u.id 
                     JOIN dungeons d ON r.dungeon_id = d.id 
                     ORDER BY r.created_at DESC");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Gestion des Demandes</h1>

    <?php if (isset($_GET['updated'])): ?>
    <div class="alert alert-success">Statut mis à jour avec succès!</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Créé le</th>
                    <th>Client</th>
                    <th>Donjon</th>
                    <th>Succès</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= $request['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></td>
                    <td><?= htmlspecialchars($request['client_name']) ?></td>
                    <td><?= htmlspecialchars($request['dungeon_name']) ?></td>
                    <td>
                        <?php 
                        $stmt = $pdo->prepare("SELECT a.name FROM request_achievements ra 
                                              JOIN achievements a ON ra.achievement_id = a.id 
                                              WHERE ra.request_id = ?");
                        $stmt->execute([$request['id']]);
                        $achievements = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        echo $achievements ? implode(', ', $achievements) : 'Aucun';
                        ?>
                    </td>
                    <td><?= number_format($request['total_price'], 2) ?> kamas</td>
                    <td class="status-<?= str_replace(' ', '-', $request['status']) ?>">
                        <?= ucfirst($request['status']) ?>
                    </td>
                    <td>
                        <a href="requests.php?action=view&id=<?= $request['id'] ?>" class="btn btn-sm">Voir</a>
                        <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#statusModal<?= $request['id'] ?>">
                            Modifier statut
                        </button>
                    </td>
                </tr>

                <!-- Modal pour changement de statut -->
                <div class="modal" id="statusModal<?= $request['id'] ?>">
                    <div class="modal-content">
                        <h3>Changer le statut</h3>
                        <form method="POST" action="requests.php?action=update_status&id=<?= $request['id'] ?>">
                            <select name="new_status" class="form-control">
                                <option value="en attente" <?= $request['status'] === 'en attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="en cours" <?= $request['status'] === 'en cours' ? 'selected' : '' ?>>En cours</option>
                                <option value="terminé" <?= $request['status'] === 'terminé' ? 'selected' : '' ?>>Terminé</option>
                                <option value="annulé" <?= $request['status'] === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                            </select>
                            <button type="submit" class="btn">Enregistrer</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>