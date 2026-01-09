<?php
include "header.php";
include "../config/db.php";

$user_id = $_SESSION['user']['id'];

// --- ÖDÜNÇ TALEBİ GÖNDERME ---
if(isset($_GET['id'])){
    $book_id = $_GET['id'];
    
    // Aktif talebi var mı kontrol et (Beklemede veya Onaylandı durumundakiler)
    $check = $db->prepare("SELECT * FROM borrow_requests WHERE user_id = ? AND book_id = ? AND status IN ('Beklemede', 'Onaylandı')");
    $check->execute([$user_id, $book_id]);
    
    if($check->rowCount() == 0){
        // Stok kontrolü (Sütun adınızın 'stock' olduğunu varsayıyoruz)
        $bookCheck = $db->prepare("SELECT stock FROM books WHERE id = ?"); 
        $bookCheck->execute([$book_id]);
        $book = $bookCheck->fetch();

        if($book && ($book['stock'] ?? 0) > 0) {
            $insert = $db->prepare("INSERT INTO borrow_requests (user_id, book_id, status, request_date) VALUES (?, ?, 'Beklemede', NOW())");
            $insert->execute([$user_id, $book_id]);
            $msg = "Ödünç talebiniz başarıyla oluşturuldu.";
            $msg_type = "success";
        } else {
            $msg = "Hata: Kitap stokta bulunmuyor.";
            $msg_type = "error";
        }
    } else {
        $msg = "Bu kitap için zaten aktif bir işleminiz bulunuyor.";
        $msg_type = "error";
    }
}

// --- GEÇMİŞ LİSTESİ ---
$requests = $db->prepare("
  SELECT b.title, b.author, br.status, br.request_date, br.return_date
  FROM borrow_requests br
  JOIN books b ON b.id = br.book_id
  WHERE br.user_id = ?
  ORDER BY br.request_date DESC
");
$requests->execute([$user_id]);
$data = $requests->fetchAll();
?>

<div class="admin-card">
  <h3>📖 Ödünç Taleplerim ve Geçmişim</h3>

  <?php if(isset($msg)): ?>
    <div style="padding:15px; margin-bottom:20px; border-radius:5px; border:1px solid; 
                background:<?= $msg_type == 'success' ? '#d4edda' : '#f8d7da' ?>; 
                color:<?= $msg_type == 'success' ? '#155724' : '#721c24' ?>; 
                border-color:<?= $msg_type == 'success' ? '#c3e6cb' : '#f5c6cb' ?>;">
        <?= $msg ?>
    </div>
  <?php endif; ?>

  <table class="admin-table">
    <thead>
        <tr>
          <th>Kitap Bilgisi</th>
          <th>Talep Tarihi</th>
          <th>İade Tarihi</th>
          <th>Durum</th>
          <th>Not / Gecikme</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($data as $r): 
        // Zaman Dönüşümleri ve Null Kontrolleri
        $request_time = strtotime($r['request_date'] ?? 'now');
        $return_timestamp = !empty($r['return_date']) ? strtotime($r['return_date']) : null;
        
        // Gecikme Kontrolü: İade tarihi dolmuşsa ve kitap hala kullanıcıdaysa (Onaylandı durumundaysa)
        $is_late = ($return_timestamp !== null && $return_timestamp < time() && $r['status'] == 'Onaylandı');
    ?>
    <tr>
      <td>
        <strong><?= htmlspecialchars($r['title']) ?></strong><br>
        <small style="color:#666;"><?= htmlspecialchars($r['author']) ?></small>
      </td>
      <td><?= date("d.m.Y H:i", $request_time) ?></td>
      
      <td>
          <?php if($return_timestamp): ?>
              <?= date("d.m.Y", $return_timestamp) ?>
          <?php else: ?>
              <span style="color:#999; font-style:italic;">Henüz Belirlenmedi</span>
          <?php endif; ?>
      </td>

      <td>
        <?php 
            $colors = [
                'Beklemede' => '#f39c12', // Turuncu
                'Onaylandı' => '#3498db', // Mavi
                'İade Edildi' => '#2ecc71', // Yeşil
                'Gecikmiş' => '#e74c3c', // Kırmızı
                'Reddedildi' => '#c0392b'  // Koyu Kırmızı
            ];
            $color = $colors[$r['status']] ?? '#7f8c8d';
        ?>
        <span style="background:<?= $color ?>; color:white; padding:4px 10px; border-radius:4px; font-size:11px; font-weight:bold; display:inline-block; min-width:80px; text-align:center;">
            <?= mb_strtoupper($r['status'], 'UTF-8') ?>
        </span>
      </td>

      <td>
        <?php if($is_late): ?>
            <span style="color:#e74c3c; font-weight:bold; background:#fdf2f2; padding:3px 8px; border-radius:3px; border:1px solid #f9d6d6;">⚠️ GECİKTİ!</span>
        <?php elseif($r['status'] == 'Onaylandı' && $return_timestamp): ?>
        <?php elseif($r['status'] == 'Beklemede'): ?>
            <span style="color:#f39c12; font-size:12px;">Onay Bekliyor</span>
        <?php else: ?>
            <span style="color:#999;">-</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div style="margin-top:30px; text-align:left;">
    <a href="dashboard.php" style="text-decoration:none; background:#34495e; color:white; padding:10px 20px; border-radius:5px; font-weight:bold; font-size:14px;">← Panele Geri Dön</a>
  </div>
</div>

<?php include "footer.php"; ?>