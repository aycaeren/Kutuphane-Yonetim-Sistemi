<?php
include "header.php"; //
include "../config/db.php";

// KULLANICI SİLME İŞLEMİ
if(isset($_GET['delete_user'])){
    $user_id = $_GET['delete_user'];
    
    // Güvenlik: Admin kendini silemesin
    if($user_id != $_SESSION['user']['id']){
        // Veritabanı bütünlüğü için önce talepleri siler
        $db->prepare("DELETE FROM borrow_requests WHERE user_id = ?")->execute([$user_id]);
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        echo "<script>alert('Kullanıcı ve tüm kayıtları başarıyla silindi'); window.location='users_manage.php';</script>";
    } else {
        echo "<script>alert('Kendi hesabınızı silemezsiniz!');</script>";
    }
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Arama Sorgusu - Türkçe Karakter ve Büyük/Küçük Harf Duyarsız (Case-Insensitive)
if(!empty($search)){
    // COLLATE utf8mb4_turkish_ci: Harf eşleşmelerini Türkçe kurallarına göre yapar (i-İ, ş-Ş vb.)
    $stmt = $db->prepare("SELECT * FROM users 
                          WHERE (name COLLATE utf8mb4_turkish_ci LIKE ? 
                          OR email COLLATE utf8mb4_turkish_ci LIKE ?) 
                          AND id != ? 
                          ORDER BY role DESC, name ASC");
    $stmt->execute(["%$search%", "%$search%", $_SESSION['user']['id']]);
    $users = $stmt->fetchAll();
} else {
    // Normal Listeleme
    $users = $db->query("SELECT * FROM users ORDER BY role DESC, name ASC")->fetchAll();
}
?>

<div class="admin-card">
    <h3 style="color:white; background:#2c3e50; padding:15px; border-radius:5px;">👤 Kullanıcı / Öğrenci Yönetimi</h3>
    
    <form method="GET" style="margin: 20px 0; display: flex; gap: 10px;">
        <input type="text" name="search" placeholder="İsim veya e-posta ara " 
               value="<?= htmlspecialchars($search) ?>" 
               style="padding:10px; flex:1; border:1px solid #ddd; border-radius:5px;">
        <button type="submit" style="background:#34495e; color:white; border:none; padding:10px 25px; border-radius:5px; cursor:pointer; font-weight:bold;">
            Kullanıcı Ara
        </button>
        <?php if(!empty($search)): ?> 
            <a href="users_manage.php" style="background:#bdc3c7; color:white; padding:10px; border-radius:5px; text-decoration:none; display:flex; align-items:center;">
                Temizle
            </a> 
        <?php endif; ?>
    </form>

    <table class="admin-table">
        <thead>
            <tr style="background:#f8f9fa;">
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($users) > 0): ?>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['name']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span style="padding:4px 8px; border-radius:4px; font-size:11px; font-weight:bold; background:<?= $u['role']=='staff' ? '#d1ecf1' : ($u['role']=='admin' ? '#f8d7da' : '#eee') ?>;">
                            <?= strtoupper($u['role']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if($u['id'] != $_SESSION['user']['id']): ?>
                            <a href="set_role.php?id=<?= $u['id'] ?>&role=staff" 
                               style="background:#2980b9; color:white; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:12px; font-weight:bold;">
                               Personel Yap
                            </a>
                            
                            <a href="set_role.php?id=<?= $u['id'] ?>&role=student" 
                               style="background:#7f8c8d; color:white; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:12px; font-weight:bold;">
                               Öğrenci Yap
                            </a>
                            
                            <a href="?delete_user=<?= $u['id'] ?>" 
                               onclick="return confirm('Bu kullanıcıyı ve tüm geçmişini silmek istediğinize emin misiniz?')" 
                               style="background:#c0392b; color:white; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:12px; font-weight:bold; margin-left:10px;">
                               Kullanıcıyı Sil
                            </a>
                        <?php else: ?>
                            <span style="color:#95a5a6; font-style:italic;">Siz (Admin)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px; color:#7f8c8d;">
                        Aranan kriterlere uygun kullanıcı bulunamadı.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "footer.php"; // ?>