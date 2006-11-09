<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.bootstrap.SandboxSourceRunner'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class BootstrapTest extends TestCase {
    var
      $sandbox    = NULL;
      
    /**
     * Sets up the tests
     *
     * @access  public
     */
    function setUp() {
      if (!isset($_SERVER['_'])) return throw(new PrerequisitesNotMetError(
        'This test can only be run in a non-web environment'
      ));
      
      $this->sandbox= &new SandboxSourceRunner();
    }
  
    /**
     * Run XP script and check exitcode
     *
     * @access  public
     * @param   int code expected exitcode
     * @param   string source sourcecode to execute
     */
    function assertExitCode($code, $source) {
      $this->assertEquals($code, $this->sandbox->run($source));
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function emptyBootstrap() {
      $this->assertExitcode(0, 'require("lang.base.php");');
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function useOfExistingFile() {
      $this->assertExitcode(0, 'require("lang.base.php"); uses("'.$this->getClassName().'");');
    }    
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function useOfNonexistingFile() {
      $this->assertExitcode(255, 'require("lang.base.php"); uses("does.not.exist");');
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function recursiveInclusion() {
      $this->assertExitcode(123, '
        require("lang.base.php"); 
        uses("net.xp_framework.unittest.bootstrap.A");
        
        exit(123);
      ');
    }
    
    /**
     * Test
     *
     * @access  public
     */
    #[@test]
    function recursiveInclusionWithTicks() {
      $this->assertExitcode(123, '
        declare(ticks=1);
        require("lang.base.php");
        uses("net.xp_framework.unittest.bootstrap.A");
        
        exit(123);
      ');
    }
    
    
  }
?>
