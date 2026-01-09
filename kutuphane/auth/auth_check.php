<?php
session_start();

if(!isset($_SESSION['user'])){
  echo "
  <div style='
    margin:50px auto;
    width:400px;
    background:white;
    padding:20px;
    text-align:center;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
  '>
    <h3>Uyarı</h3>
    <p>Bu sayfayı görüntülemek için giriş yapmalısınız.</p>
    <a href='../auth/login.php'>Giriş Yap</a>
  </div>
  ";
  exit;
}
