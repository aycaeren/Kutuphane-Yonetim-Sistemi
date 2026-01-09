<?php
include "../config/db.php";
session_start();

$book = $db->prepare("SELECT * FROM books WHERE id=?");
$book->execute([$_GET['id']]);
$b = $book->fetch();

if($b['stock']<=0){
 die("Bu kitap şu anda ödünç verilemez. Stokta bulunmamaktadır.");
}

$db->prepare("INSERT INTO borrow_requests VALUES(NULL,?,?, 'Beklemede', CURDATE())")
->execute([$_SESSION['user']['id'],$b['id']]);

echo "Talep oluşturuldu";
