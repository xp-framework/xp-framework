<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.sybase_ct';
  
  /**
   * Fixture for module tests
   *
   */
  class rdbms·sybase_ct·Driver extends Object {
    public static $instance;
    public $registered= FALSE;
    
    static function __static() {
      self::$instance= new self();
    }
  }
?>
