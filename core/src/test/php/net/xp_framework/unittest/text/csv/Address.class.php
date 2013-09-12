<?php namespace net\xp_framework\unittest\text\csv;



/**
 * Address value object
 *
 */
class Address extends \lang\Object {
  public 
    $name   = '', 
    $city   = '',
    $zip    = '';
  
  /**
   * Constructor
   *
   * @param   string name
   * @param   string city
   * @param   string zip
   */
  public function __construct($name= '', $city= '', $zip= '') {
    $this->name= $name;
    $this->city= $city;
    $this->zip= $zip;
  }
  
  /**
   * Returns whether another object is equal to this address
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self && 
      $cmp->name === $this->name &&
      $cmp->city === $this->city &&
      $cmp->zip === $this->zip
    );
  }
}
