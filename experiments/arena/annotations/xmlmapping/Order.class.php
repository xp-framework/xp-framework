<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  /**
   * Order class
   *
   * @see      order.php
   * @purpose  demo
   */
  class Order extends Object {
    var
      $reference  = '',
      $client     = NULL,
      $items      = array();
  
    #[@xmlmapping(xpath= '@reference')]
    function setReference($reference) {
      $this->reference= $reference;
    }
    
    #[@xmlmapping(xpath= 'Client', class= 'Client')]
    function setClient(&$client) {
      $this->client= &$client;
    }

    #[@xmlmapping(xpath= 'Item', class= 'Item')]
    function addItem(&$item) {
      $this->items[]= &$item;
    }
  }
?>
