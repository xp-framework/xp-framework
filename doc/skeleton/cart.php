<?php
  /* 
   * Dokumentiert Warenkorb/Product
   * 
   * $Id$
   */
   
  require_once('../../skeleton/lang.base.php');
  import('de.schlund.util.WebhostingProduct');
  import('de.schlund.util.Cart');
  
  define('CART_STOR',   '/tmp/cart-'.getenv('USER'));
  
  $fd= @fopen(CART_STOR, 'r');
  if ($fd) {
    $cart= unserialize(fread($fd, filesize(CART_STOR)));
    fclose($fd);
  } else {
    $cart= new Cart();
  }
  
  $action= isset($argv[1]) ? $argv[1]: 'view';
  switch ($action) {
    case 'add':
      $cart->addItem(new WebhostingProduct(array(
        'tarif_id'  => 1186,
        'name'      => 'webadresse',
        'feeSetup'  => 9.60,
        'feeMonthly'=> 0.69
      )));
      echo "+OK product added\n";
      break;
      
    case 'get':
      $no= isset($argv[2]) ? intval($argv[2]) : 0;
      echo "+OK item #{$no}\n";
      var_dump($cart->getItem($no));
      break;
      
    case 'del':
      $no= isset($argv[2]) ? intval($argv[2]) : 0;
      $cart->removeItem($no);
      echo "+OK item #{$no} deleted\n";
      break;
    default:
      var_dump($cart);
  }
  
  $fd= fopen(CART_STOR, 'w');
  fwrite($fd, serialize($cart));
  fclose($fd);
?>
