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
     * Run XP script and check exitcode
     *
     * @access  public
     * @param   int code expected exitcode
     * @param   string source sourcecode to execute
     */
    function assertExitCode($code, $source) {
      try(); {
        $p= &new Process($_SERVER['_']);
        $s= &$p->getInputStream();
        $s->write('<?php '.$source.'?>');
        
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
  }
?>
