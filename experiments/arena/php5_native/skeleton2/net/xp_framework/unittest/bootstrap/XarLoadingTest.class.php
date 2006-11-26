<?php
/* This class is part of the XP framework
 *
 * $Id: XarLoadingTest.class.php 8518 2006-11-20 19:31:40Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.bootstrap.SandboxSourceRunner'
  );

  /**
   * TestCase for loading classes from an XP archive (.xar).
   *
   * @see      xp://lang.archive.ArchiveClassLoader
   * @purpose  Test class loading
   */
  class XarLoadingTest extends TestCase {
  
    /**
     * Sets up test case
     *
     * @access  public
     */
    public function setUp() {
      $this->sandbox= new SandboxSourceRunner();
      
      // Include XAR into include_path
      $this->sandbox->setSetting('include_path',
        $this->sandbox->getSetting('include_path').
        PATH_SEPARATOR.
        dirname(__FILE__).'/xp-classloading-test-1.0.xar'
      );
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    public function load() {
      $this->assertEquals(0, $this->sandbox->run('
        require("lang.base.php");
        uses("net.xp_framework.unittest.bootstrap.A");
      '));
    }

    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    public function loadFromArchive() {
      $this->assertEquals(0, $this->sandbox->run('
        require("lang.base.php");
        uses("net.xp_framework.unittest.bootstrap.D");
      '));
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    public function loadFromArchiveWithArchiveDependency() {
      $this->assertEquals(0, $this->sandbox->run('
        require("lang.base.php");
        uses("net.xp_framework.unittest.bootstrap.E");
        
        // E extends D, so D must be loaded, as well
        class_exists("D") || xp::error("Class not found: D");
      '));
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    public function loadFromArchiveWithNonarchiveDependency() {
      $this->assertEquals(0, $this->sandbox->run('
        require("lang.base.php");
        uses("net.xp_framework.unittest.bootstrap.F");
      '));
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    public function registeredClassLoader() {
      $this->assertEquals(0, $this->sandbox->run('
        require("lang.base.php");
        $xpclass= &XPClass::forName("net.xp_framework.unittest.bootstrap.F");
        is("lang.archive.ArchiveClassLoader", $xpclass->getClassLoader()) || xp::error("Incorrect classloader for class loaded from archive");
        
        $xpclass= &XPClass::forName("net.xp_framework.unittest.bootstrap.C");
        !is("lang.archive.ArchiveClassLoader", $xpclass->getClassLoader()) || xp::error("Incorrect classloader for class not loaded from archive");
      '));
    }
  }
?>
