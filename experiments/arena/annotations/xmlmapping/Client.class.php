<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  /**
   * Client class
   *
   * @see      order.php
   * @purpose  demo
   */
  class Client extends Object {
    var
      $name    = '',
      $address = '';
  
    #[@xmlmapping(xpath= 'Name')]
    function setName($name) {
      $this->name= $name;
    }

    #[@xmlmapping(xpath= 'Address')]
    function setAddress($address) {
      $this->address= $address;
    }    
  }
?>
