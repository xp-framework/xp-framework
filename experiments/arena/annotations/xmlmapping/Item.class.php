<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  /**
   * Item class
   *
   * @see      order.php
   * @purpose  demo
   */
  class Item extends Object {
    var
      $reference    = '',
      $description  = '',
      $quantity     = 0,
      $unitPrice    = 0.0;
  
    #[@xmlmapping(xpath= '@reference')]
    function setReference($reference) {
      $this->reference= $reference;
    }
    
    #[@xmlmapping(xpath= 'Description')]
    function setDescription($description) {
      $this->description= $description;
    }

    #[@xmlmapping(xpath= 'Quantity')]
    function setQuantity($quantity) {
      $this->quantity= (int)$quantity;
    }

    #[@xmlmapping(xpath= 'UnitPrice')]
    function setUnitPrice($unitPrice) {
      $this->unitPrice= (float)$unitPrice;
    }
  }
?>
