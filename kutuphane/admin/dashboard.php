<?php 
include "header.php"; 
// Artık burada tekrar session_start veya role kontrolü yapmana gerek yok, header hallediyor.
?>
<div class="admin-card">
    <p>Hoş geldiniz, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></p>
</div>
<?php include "footer.php"; ?>