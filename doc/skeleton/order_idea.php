<?php
  // [...]

  $wh_product= new WHProductOrder();
  $wh_product->tarif_id= 1184;
  $wh_product->domains= array();

  $dsl_product= new DSLProductOrder();
  $dsl_product->tarif_id= 1263;
  $dsl_product->has_dsl= FALSE;
  
  $order= new Order();
  $order->addItem($wh_product);
  $order->addItem($dsl_product);
  
  $order->execute();
?>

<?php
  class WHProductOrder extends ProductOrder {           // *W*eb*h*osting-Produkt
    var $domains;
  }
?>

<?php
  class DSLProductOrder extends ProductOrder {          // DSL-Produkt
    var $has_dsl;
  }
?>

<?php
  class ProductOrder {
    var $tarif_id;

    function getXMLRep() {
      return new XMLDom(/* data */);    // Repräsentation des Objekts XML
    }    
    
  }
?>

<?php
  class Order {
    var $items;
    var $customer;
    var $vermittlungsdaten;
    
    function execute() {
      $result= TRUE;
      foreach ($this->items as $item) {
        try {

          $soap= new SOAP();
          $soap->body->addChild($item->getXML());
          $soap->body->addChild($this->customer);
          $soap->body->addChild($this->vermittlungsdaten);

          // Absenden
          $answer= $soap->sendRequest();
          
        } catch (ConnectException $e) {
        
          // Kein Connect zum SOAP-Server, also nach /var/spool/order queuen
          $spool= new File('/var/spool/order/'.$this->uniqId());
          $spool->open(FILE_MODE_WRITE);
          $spool->write(serialize($soap));
          $spool->close();
        }
        
        $result= $result & ($answer->getFault() != NULL);
      }
      
      return $result;
    }
  }
?>
