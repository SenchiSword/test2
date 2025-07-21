<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || isAdmin()) {
    header('Location: /login.php');
    exit;
}

$pageTitle = "Demande de Passage";
$bodyClass = "client-request";

// Récupérer tous les donjons avec leurs succès
$stmt = $pdo->query("SELECT d.*, a.id as achievement_id, a.name as achievement_name, a.price as achievement_price 
                     FROM dungeons d 
                     LEFT JOIN achievements a ON d.id = a.dungeon_id 
                     ORDER BY d.name, a.name");
$dungeonsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organiser les données par donjon
$dungeons = [];
foreach ($dungeonsData as $row) {
    $dungeonId = $row['id'];
    
    if (!isset($dungeons[$dungeonId])) {
        $dungeons[$dungeonId] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'base_price' => $row['base_price'],
            'image' => $row['image'],
            'description' => $row['description'],
            'achievements' => []
        ];
    }
    
    if ($row['achievement_id']) {
        $dungeons[$dungeonId]['achievements'][] = [
            'id' => $row['achievement_id'],
            'name' => $row['achievement_name'],
            'price' => $row['achievement_price']
        ];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $dungeonId = $_POST['dungeon_id'];
    $achievements = isset($_POST['achievements']) ? $_POST['achievements'] : [];
    
    // Calculer le prix total
    $stmt = $pdo->prepare("SELECT base_price FROM dungeons WHERE id = ?");
    $stmt->execute([$dungeonId]);
    $dungeon = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $totalPrice = $dungeon['base_price'];
    
    if (!empty($achievements)) {
        $placeholders = implode(',', array_fill(0, count($achievements), '?'));
        $stmt = $pdo->prepare("SELECT SUM(price) as total FROM achievements WHERE id IN ($placeholders)");
        $stmt->execute($achievements);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalPrice += $result['total'];
    }
    
    // Créer la demande
    $stmt = $pdo->prepare("INSERT INTO requests (client_id, dungeon_id, total_price) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $dungeonId, $totalPrice]);
    $requestId = $pdo->lastInsertId();
    
    // Ajouter les succès à la demande
    if (!empty($achievements)) {
        foreach ($achievements as $achievementId) {
            $stmt = $pdo->prepare("INSERT INTO request_achievements (request_id, achievement_id) VALUES (?, ?)");
            $stmt->execute([$requestId, $achievementId]);
        }
    }
    
    header('Location: my-requests.php?success=1');
    exit;
}

include '../includes/header.php';
?>

<div class="container animate-on-load">
    <h1>Demande de Passage</h1>
    
    <form method="POST" id="request-form">
        <div class="form-group">
            <label for="dungeon_id">Sélectionnez un donjon</label>
            <select id="dungeon_id" name="dungeon_id" class="form-control" required>
                <option value="">-- Choisissez un donjon --</option>
                <?php foreach($dungeons as $dungeon): ?>
                <option value="<?php echo $dungeon['id']; ?>" data-base-price="<?php echo $dungeon['base_price']; ?>">
                    <?php echo htmlspecialchars($dungeon['name']); ?> (Base: <?php echo number_format($dungeon['base_price'], 2); ?> kamas)
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div id="dungeon-info" style="display: none;">
            <div class="dungeon-card">
                <div class="dungeon-header">
                    <img id="dungeon-image" src="" alt="" class="dungeon-icon" style="max-width: 100px; display: none;">
                    <h3 id="dungeon-name"></h3>
                </div>
                <p id="dungeon-description"></p>
                <p>Prix de base: <span id="base-price"></span> kamas</p>
            </div>
            
            <div class="form-group">
                <label>Sélectionnez les succès (optionnel)</label>
                <div id="achievements-list" class="achievements-grid"></div>
            </div>
        </div>
        
        <div id="total-price-container" style="display: none; margin: 20px 0;">
            <h3>Prix total: <span id="total-price">0.00</span> kamas</h3>
        </div>
        
        <button type="submit" name="submit_request" class="btn" id="submit-btn" disabled>Envoyer la demande</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dungeonSelect = document.getElementById('dungeon_id');
    const dungeonInfo = document.getElementById('dungeon-info');
    const achievementsList = document.getElementById('achievements-list');
    const totalPriceContainer = document.getElementById('total-price-container');
    const totalPriceElement = document.getElementById('total-price');
    const submitBtn = document.getElementById('submit-btn');
    
    const dungeons = <?php echo json_encode($dungeons); ?>;
    
    dungeonSelect.addEventListener('change', function() {
        const dungeonId = this.value;
        
        if (!dungeonId) {
            dungeonInfo.style.display = 'none';
            totalPriceContainer.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        
        const dungeon = dungeons[dungeonId];
        
        // Afficher les infos du donjon
        document.getElementById('dungeon-name').textContent = dungeon.name;
        document.getElementById('dungeon-description').textContent = dungeon.description || 'Aucune description disponible.';
        document.getElementById('base-price').textContent = dungeon.base_price.toFixed(2);
        
        if (dungeon.image) {
            const imgElement = document.getElementById('dungeon-image');
            imgElement.src = '/assets/images/dungeon-icons/' + dungeon.image;
            imgElement.style.display = 'block';
        } else {
            document.getElementById('dungeon-image').style.display = 'none';
        }
        
        // Afficher les succès
        achievementsList.innerHTML = '';
        
        if (dungeon.achievements.length > 0) {
            dungeon.achievements.forEach(achievement => {
                const achievementDiv = document.createElement('div');
                achievementDiv.className = 'achievement-item';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'achievements[]';
                checkbox.value = achievement.id;
                checkbox.id = 'achievement-' + achievement.id;
                checkbox.dataset.price = achievement.price;
                checkbox.addEventListener('change', updateTotalPrice);
                
                const label = document.createElement('label');
                label.htmlFor = 'achievement-' + achievement.id;
                label.innerHTML = `${achievement.name} (+${achievement.price.toFixed(2)} kamas)`;
                
                achievementDiv.appendChild(checkbox);
                achievementDiv.appendChild(label);
                achievementsList.appendChild(achievementDiv);
            });
        } else {
            achievementsList.innerHTML = '<p>Aucun succès disponible pour ce donjon.</p>';
        }
        
        dungeonInfo.style.display = 'block';
        totalPriceContainer.style.display = 'block';
        submitBtn.disabled = false;
        
        updateTotalPrice();
    });
    
    function updateTotalPrice() {
        const basePrice = parseFloat(dungeonSelect.options[dungeonSelect.selectedIndex].dataset.basePrice) || 0;
        let achievementsPrice = 0;
        
        document.querySelectorAll('#achievements-list input[type="checkbox"]:checked').forEach(checkbox => {
            achievementsPrice += parseFloat(checkbox.dataset.price);
        });
        
        const total = basePrice + achievementsPrice;
        totalPriceElement.textContent = total.toFixed(2);
    }
});
</script>

<?php include '../includes/footer.php'; ?>