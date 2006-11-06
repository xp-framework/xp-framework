<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Process',
    'util.profiling.unittest.TestCase'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class BootstrapTest extends TestCase {

    /**
     * Sets up the tests
     *
     * @access  public
     */
    function setUp() {
      if (!isset($_SERVER['_'])) return throw(new PrerequisitesNotMetError(
        'This test can only be run in a non-web environment'
      ));
    }
  
    /**
     * Retrieve PHP binary path. Converts the path to a proper
     * windows path in case the PHP binary has been invoked through
     * a cygwin path.
     *
     * @access  protected
     * @return  string
     */
    function _binary() {
      static $binary= NULL;
      
      if (!$binary) {
        $binary= preg_replace('#^/cygdrive/(\w)/#', '$1:/', $_SERVER['_']);
      }
      return $binary;
    }

    /**
     * Run XP script and check exitcode
     *
     * @access  public
     * @param   int code expected exitcode
     * @param   string source sourcecode to execute
     */
    function assertExitCode($code, $source) {
      try(); {
        $p= &new Process($this->_binary().' -dinclude_path='.ini_get("include_path"));
        $p->in->write('<?php '.$source.'?>');
        $p->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      $this->assertEquals($code, $p->exitValue());
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
