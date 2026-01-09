<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include "../config/db.php";

if(!isset($_SESSION['user']) || strtolower($_SESSION['user']['role']) != "staff"){
    die("Yetkisiz erişim!");
}

if(isset($_GET['id']) && isset($_GET['s'])){
    $id = $_GET['id'];
    $new_status = $_GET['s'];
    
    // Mevcut durumu kontrol et (Onaylıdan Redde geçiş için kritik)
    $stmt = $db->prepare("SELECT status FROM borrow_requests WHERE id = ?");
    $stmt->execute([$id]);
    $current_status = $stmt->fetchColumn();

    $allowed = ['Beklemede', 'Onaylandı', 'Reddedildi', 'İade Talebi', 'İade Edildi'];
    
    if(in_array($new_status, $allowed)){
        
        // DURUM: ONAYLANDI (Kitabı Ver)
        if($new_status == 'Onaylandı' && $current_status != 'Onaylandı'){
            $db->prepare("UPDATE borrow_requests SET status=?, return_date = DATE_ADD(NOW(), INTERVAL 15 DAY) WHERE id=?")
               ->execute([$new_status, $id]);
            
            // Stok düş
            $db->prepare("UPDATE books b JOIN borrow_requests br ON b.id = br.book_id SET b.stock = b.stock - 1 WHERE br.id = ?")
               ->execute([$id]);
        } 
        // DURUM: REDDEDİLDİ (Onaylanmış bir kitabı iptal ediyorsa stoğu geri ekle)
        elseif($new_status == 'Reddedildi'){
            if($current_status == 'Onaylandı'){
                // Kitap zaten verilmişti, stoğu geri al
                $db->prepare("UPDATE books b JOIN borrow_requests br ON b.id = br.book_id SET b.stock = b.stock + 1 WHERE br.id = ?")
                   ->execute([$id]);
            }
            $db->prepare("UPDATE borrow_requests SET status=?, return_date = NULL WHERE id=?")->execute([$new_status, $id]);
        }
        // DURUM: İADE EDİLDİ
        elseif($new_status == 'İade Edildi'){
            $db->prepare("UPDATE borrow_requests SET status=? WHERE id=?")->execute([$new_status, $id]);
            $db->prepare("UPDATE books b JOIN borrow_requests br ON b.id = br.book_id SET b.stock = b.stock + 1 WHERE br.id = ?")
               ->execute([$id]);
        }
        else {
            $db->prepare("UPDATE borrow_requests SET status=? WHERE id=?")->execute([$new_status, $id]);
        }
    }
}
header("Location: dashboard.php");
exit;