<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isAdmin()) {
    header('Location: /login.php');
    exit;
}

$pageTitle = "Gestion des Donjons";
$bodyClass = "admin-dungeons";

// Ajouter un donjon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dungeon'])) {
    $name = $_POST['name'];
    $basePrice = $_POST['base_price'];
    $description = $_POST['description'];
    
    // Gestion de l'image
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetPath = "../assets/images/dungeon-icons/" . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
    }
    
    $stmt = $pdo->prepare("INSERT INTO dungeons (name, base_price, image, description) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $basePrice, $imageName, $description]);
    
    header('Location: dungeons.php?success=1');
    exit;
}

// Récupérer tous les donjons
$stmt = $pdo->query("SELECT * FROM dungeons ORDER BY name");
$dungeons = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="container animate-on-load">
    <h1>Gestion des Donjons</h1>
    
    <div class="grid-2-col">
        <div>
            <h2>Ajouter un donjon</h2>
            <form method="POST" enctype="multipart/form-data" class="dungeon-form">
                <div class="form-group">
                    <label for="name">Nom du donjon</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="base_price">Prix de base (kamas)</label>
                    <input type="number" id="base_price" name="base_price" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Image (optionnelle)</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>
                
                <button type="submit" name="add_dungeon" class="btn">Ajouter le donjon</button>
            </form>
        </div>
        
        <div>
            <h2>Liste des donjons</h2>
            <div class="dungeons-list">
                <?php foreach($dungeons as $dungeon): ?>
                <div class="dungeon-card">
                    <div class="dungeon-header">
                        <?php if($dungeon['image']): ?>
                        <img src="/assets/images/dungeon-icons/<?php echo htmlspecialchars($dungeon['image']); ?>" alt="<?php echo htmlspecialchars($dungeon['name']); ?>" class="dungeon-icon">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($dungeon['name']); ?></h3>
                    </div>
                    <p class="dungeon-price">Prix de base: <?php echo number_format($dungeon['base_price'], 2); ?> kamas</p>
                    <p><?php echo htmlspecialchars($dungeon['description']); ?></p>
                    <div class="dungeon-actions">
                        <a href="achievements.php?dungeon_id=<?php echo $dungeon['id']; ?>" class="btn btn-sm">Gérer les succès</a>
                        <a href="#" class="btn btn-sm btn-secondary">Modifier</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>