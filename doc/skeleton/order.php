<?php
  /* 
   * Dokumentiert Bestellung
   * 
   * $Id$
   */
   
  require('../../skeleton/lang.base.php');
  import('de.schlund.util.order.Cart');
  import('de.schlund.util.order.OrderPerson');
  import('de.schlund.util.order.OrderTarif');
  
  $order= new Cart();  
  $order->addItem(new OrderTarif(array(
    'tarif_id'          => 1216,
    'domains'           => array('thekid.de')
  )));
  $order->customer= new OrderPerson(array(
    'anrede'	        => 'Herr',
    'vorname'	        => 'Timm',
    'nachname'	        => 'Friebe',
    'firma'	        => NULL,
    'strasse'	        => 'Essenweinstr. 3',
    'lcode'	        => 'D',
    'plz'	        => '76131',
    'ort'	        => 'Karlsruhe',
    'phone'	        => '0721-6649575',
    'fax'	        => NULL,
    'email'	        => 'thekid@rst.de',
    'titel'	        => 'Kaiser',
    'strassezusatz'	=> NULL
  ));
  
  try(); {
    $order->execute();
  } if ($e= catch(E_ANY_EXCEPTION)) {
    exit("Exception $e->type: '$e->message'\n");
  }
?>
