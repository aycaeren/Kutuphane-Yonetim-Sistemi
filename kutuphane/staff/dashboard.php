<?php
include "header.php";
include "../config/db.php";

if(!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) != 'staff'){
    die("Yetkisiz erişim!");
}

$today = date('Y-m-d');
$filter = $_GET['filter'] ?? 'all';

// --- İSTATİSTİK SORGULARI ---
// Bugün Onaylananlar
$daily_given = $db->query("SELECT COUNT(*) FROM borrow_requests WHERE status='Onaylandı' AND DATE(request_date)='$today'")->fetchColumn();
// Bugün İade Edilenler
$daily_returned = $db->query("SELECT COUNT(*) FROM borrow_requests WHERE status='İade Edildi' AND DATE(request_date)='$today'")->fetchColumn();

// --- LİSTE SORGULARI ---
// 1. Yeni Talepler (Beklemede olanlar)
$new_requests = $db->query("SELECT br.*, u.name as s_name, b.title FROM borrow_requests br 
                             JOIN users u ON u.id = br.user_id JOIN books b ON b.id = br.book_id 
                             WHERE br.status IN ('Beklemede', 'İade Talebi') ORDER BY br.request_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// 2. Geciken Kitaplar
$late_books = $db->query("SELECT br.*, u.name as s_name, b.title, b.shelf FROM borrow_requests br 
                           JOIN users u ON u.id = br.user_id JOIN books b ON b.id = br.book_id 
                           WHERE br.status = 'Onaylandı' AND br.return_date < NOW() ORDER BY br.return_date ASC")->fetchAll(PDO::FETCH_ASSOC);

// 3. Aktif Ödünçler (Onaylanmış ama henüz dönmemiş olanlar - Durum değiştirmek için)
$active_requests = $db->query("SELECT br.*, u.name as s_name, b.title FROM borrow_requests br 
                               JOIN users u ON u.id = br.user_id JOIN books b ON b.id = br.book_id 
                               WHERE br.status = 'Onaylandı' ORDER BY br.request_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// 4. Geçmiş Arşiv (Biten işlemler)
$past_requests = $db->query("SELECT br.*, u.name as s_name, b.title FROM borrow_requests br 
                             JOIN users u ON u.id = br.user_id JOIN books b ON b.id = br.book_id 
                             WHERE br.status IN ('İade Edildi', 'Reddedildi') ORDER BY br.request_date DESC LIMIT 15")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-card">
    <div style="display:flex; gap:20px; margin-bottom:20px;">
        <div style="background:#e3f2fd; padding:15px; border-radius:8px; flex:1; text-align:center; border:1px solid #2196f3;">
            <strong style="color:#0d47a1;">Bugün Verilen: <?= $daily_given ?></strong>
        </div>
        <div style="background:#e8f5e9; padding:15px; border-radius:8px; flex:1; text-align:center; border:1px solid #4caf50;">
            <strong style="color:#1b5e20;">Bugün İade Alınan: <?= $daily_returned ?></strong>
        </div>
    </div>

    <div style="display: flex; gap: 10px; margin-bottom: 25px;">
        <a href="dashboard.php?filter=all" style="padding: 10px 20px; text-decoration: none; border-radius: 5px; background: <?= $filter == 'all' ? '#3498db' : '#eee' ?>; color: <?= $filter == 'all' ? 'white' : '#333' ?>; font-weight: bold;">Tüm Talepler</a>
        
        <a href="dashboard.php?filter=late" style="padding: 10px 20px; text-decoration: none; border-radius: 5px; background: <?= $filter == 'late' ? '#e74c3c' : '#eee' ?>; color: <?= $filter == 'late' ? 'white' : '#333' ?>; font-weight: bold; position: relative;">
            ⚠️ Geciken Kitaplar 
            <?php if(count($late_books) > 0): ?>
                <span style="position: absolute; top: -5px; right: -5px; background: black; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px;"><?= count($late_books) ?></span>
            <?php endif; ?>
        </a>
    </div>

    <?php if($filter == 'late'): ?>
        <div style="border: 2px solid #e74c3c; padding: 15px; border-radius: 10px;">
            <h3 style="color: #e74c3c;">Geciken Kitaplar Listesi</h3>
            <?php if ($late_books): ?>
                <table class="admin-table">
                    <tr style="background: #fdf2f2;"><th>Öğrenci</th><th>Kitap</th><th>Raf</th><th>Son Tarih</th><th>İşlem</th></tr>
                    <?php foreach($late_books as $lb): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($lb['s_name']) ?></strong></td>
                        <td><?= htmlspecialchars($lb['title']) ?></td>
                        <td><?= htmlspecialchars($lb['shelf'] ?? '-') ?></td>
                        <td style="color: red; font-weight: bold;"><?= date('d.m.Y', strtotime($lb['return_date'])) ?></td>
                        <td><a href="update_status.php?id=<?= $lb['id'] ?>&s=İade Edildi" style="color:red; font-weight:bold;">İade Al</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Şu an geciken kitap bulunmuyor.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div style="margin-bottom: 30px;">
            <h3>🔔 Yeni Gelen Talepler</h3>
            <table class="admin-table">
                <tr style="background: #fff9c4;"><th>Öğrenci</th><th>Kitap</th><th>İşlem</th></tr>
                <?php foreach($new_requests as $nr): ?>
                <tr>
                    <td><?= htmlspecialchars($nr['s_name']) ?></td>
                    <td><?= htmlspecialchars($nr['title']) ?></td>
                    <td>
                        <a href="update_status.php?id=<?= $nr['id'] ?>&s=Onaylandı" style="color:green; font-weight:bold;">Onayla</a> |
                        <a href="update_status.php?id=<?= $nr['id'] ?>&s=Reddedildi" style="color:red; font-weight:bold;">Reddet</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="margin-bottom: 30px; border-top: 1px solid #ddd; padding-top: 10px;">
            <h3>📖 Aktif Ödünçler (İade Al / Reddet)</h3>
            <table class="admin-table">
                <tr style="background: #f4f4f4;"><th>Öğrenci</th><th>Kitap</th><th>Durum</th><th>İşlem</th></tr>
                <?php foreach($active_requests as $ar): ?>
                <tr>
                    <td><?= htmlspecialchars($ar['s_name']) ?></td>
                    <td><?= htmlspecialchars($ar['title']) ?></td>
                    <td style="color: blue; font-weight: bold;">ONAYLANDI</td>
                    <td>
                        <a href="update_status.php?id=<?= $ar['id'] ?>&s=İade Edildi" style="color:green; font-weight:bold;">İade Al</a> |
                        <a href="update_status.php?id=<?= $ar['id'] ?>&s=Reddedildi" 
                           onclick="return confirm('Bu onaylanmış işlemi REDDETMEK istediğinize emin misiniz? Kitap stoğu geri eklenecektir.')" 
                           style="color:red; font-size: 12px;">Hatalı Onay (Reddet)</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div style="opacity: 0.7;">
            <h3>📜 Geçmiş İşlemler</h3>
            <table class="admin-table" style="font-size: 12px;">
                <tr><th>Öğrenci</th><th>Kitap</th><th>Durum</th><th>Tarih</th></tr>
                <?php foreach($past_requests as $pr): ?>
                <tr>
                    <td><?= htmlspecialchars($pr['s_name']) ?></td>
                    <td><?= htmlspecialchars($pr['title']) ?></td>
                    <td style="color: <?= $pr['status'] == 'Reddedildi' ? 'red' : 'green' ?>; font-weight:bold;"><?= $pr['status'] ?></td>
                    <td><?= date('d.m.Y', strtotime($pr['request_date'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>