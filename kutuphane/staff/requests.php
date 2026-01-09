<?php
include "header.php";
include "../config/db.php";

// Ödünç alma işlemi (Kitaplar sayfasından bir ID ile gelindiyse)
if(isset($_GET['id'])){
    $book_id = $_GET['id'];
    
    // Daha önce aynı kitap için talep açılmış mı kontrol et
    $check = $db->prepare("SELECT * FROM borrow_requests WHERE user_id = ? AND book_id = ? AND status = 'Beklemede'");
    $check->execute([$_SESSION['user']['id'], $book_id]);
    
    if($check->rowCount() == 0){
        // Talep yoksa yeni talep oluştur
        $insert = $db->prepare("INSERT INTO borrow_requests (user_id, book_id, status, request_date) VALUES (?, ?, 'Beklemede', NOW())");
        $insert->execute([$_SESSION['user']['id'], $book_id]);
        $msg = "Ödünç talebiniz başarıyla oluşturuldu.";
    } else {
        $msg = "Bu kitap için zaten bekleyen bir talebiniz mevcut.";
    }
}

// Giriş yapan öğrencinin ödünç aldığı/talep ettiği tüm kitapları listeleme
$requests = $db->prepare("
  SELECT b.title, b.author, b.year, br.status, br.request_date
  FROM borrow_requests br
  JOIN books b ON b.id = br.book_id
  WHERE br.user_id = ?
  ORDER BY br.request_date DESC
");
$requests->execute([$_SESSION['user']['id']]);
$data = $requests->fetchAll();
?>

<div class="admin-card">
  <h3>📖 Ödünç Taleplerim ve Geçmişim</h3>

  <?php if(isset($msg)): ?>
    <div style="background: #e1f5fe; color: #01579b; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3e5fc;">
        <?= $msg ?>
    </div>
  <?php endif; ?>

  <?php if(count($data) == 0): ?>
    <p>Henüz herhangi bir ödünç talebiniz bulunmamaktadır.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Kitap Adı</th>
          <th>Yazar</th>
          <th>Yıl</th>
          <th>Durum</th>
          <th>Talep Tarihi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($data as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['author']) ?></td>
            <td><?= $r['year'] ?></td>
            <td>
              <span class="status-badge <?= $r['status'] == 'Beklemede' ? 'yellow' : ($r['status'] == 'Onaylandı' ? 'green' : 'red') ?>">
                <?= htmlspecialchars($r['status']) ?>
              </span>
            </td>
            <td><?= date('d.m.Y H:i', strtotime($r['request_date'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div style="margin-top:20px;">
    <a href="dashboard.php" class="btn-link">← Panele Dön</a>
  </div>
</div>

<?php include "footer.php"; ?>