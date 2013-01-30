<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms.sybase_ct';
  
  /**
   * Fixture for module tests
   *
   * @see   xp://net.xp_framework.unittest.core.modules.ModuleWithStaticInitializerTest
   */
  class rdbms·sybase_ct·Driver extends Object {
    public static $instance;
    public $registered= NULL;
    
    static function __static() {
      self::$instance= new self();
    }
  }
?>
