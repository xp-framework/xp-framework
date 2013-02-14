<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'rdbms2.sybase_ct';

  uses('rdbms2.Driver');
  
  /**
   * Fixture for module tests
   *
   * @see   xp://net.xp_framework.unittest.core.modules.ModuleWithStaticInitializerTest
   */
  class rdbms2·sybase_ct·Driver extends Object implements rdbms2·Driver {
    protected $version;

    /**
     * Creates a new driver instance
     *
     * @param  string version
     */
    public function __construct($version) {
      $this->version= $version;
    }

    /**
     * Creates a new connection
     *
     * @param   string dsn
     * @return  rdbms2.Connection
     */
    public function newConnection($dsn) {
      return "SybaseCtConnection<".$dsn.", ".$this->version.">";
    }
  }
?>
