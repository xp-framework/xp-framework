<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.ast.StringNode',
    'xp.compiler.checks.Checks',
    'xp.compiler.types.MethodScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.Checks
   */
  class ChecksTest extends TestCase {
    protected static $check;
    protected $fixture= NULL;
    protected $scope= NULL;
    protected $messages= array();

    static function __static() {
      self::$check= newinstance('xp.compiler.check.Check', array(), '{
        public function node() { 
          return XPClass::forName("xp.compiler.ast.StringNode"); 
        }

        public function defer() { 
          return FALSE; 
        }

        public function verify(xp·compiler·ast·Node $in, Scope $scope) {
          return array("C100", "Test");
        }
      }');
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new Checks();
      $this->scope= new MethodScope();
    }
    
    /**
     * Callback for warnings
     *
     * @param   string code
     * @param   string message
     */
    public function warn($code, $message) {
      $this->messages[]= array('warning', $code, $message);
    }

    /**
     * Callback for errors
     *
     * @param   string code
     * @param   string message
     */
    public function error($code, $message) {
      $this->messages[]= array('error', $code, $message);
    }

    /**
     * Test verify()
     *
     */
    #[@test]
    public function withoutCheck() {
      $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(array(), $this->messages);
    }

    /**
     * Test add() and verify()
     *
     */
    #[@test]
    public function withErrorCheck() {
      $this->fixture->add(self::$check, TRUE);
      $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(array(array('error', 'C100', 'Test')), $this->messages);
    }

    /**
     * Test add() and verify()
     *
     */
    #[@test]
    public function withWarningCheck() {
      $this->fixture->add(self::$check, FALSE);
      $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(array(array('warning', 'C100', 'Test')), $this->messages);
    }

    /**
     * Test add() and verify()
     *
     */
    #[@test]
    public function withWarningAndErrorChecks() {
      $this->fixture->add(self::$check, FALSE);
      $this->fixture->add(self::$check, TRUE);
      $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(
        array(array('warning', 'C100', 'Test'), array('error', 'C100', 'Test')), 
        $this->messages
      );
    }

    /**
     * Test add() and verify()
     *
     */
    #[@test]
    public function withTwoWarningChecks() {
      $this->fixture->add(self::$check, FALSE);
      $this->fixture->add(self::$check, FALSE);
      $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(
        array(array('warning', 'C100', 'Test'), array('warning', 'C100', 'Test')), 
        $this->messages
      );
    }

    /**
     * Test add() and verify()
     *
     */
    #[@test]
    public function withTwoErrorsChecks() {
      $this->fixture->add(self::$check, TRUE);
      $this->fixture->add(self::$check, TRUE);
      $this->assertFalse($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(
        array(array('error', 'C100', 'Test'), array('error', 'C100', 'Test')), 
        $this->messages
      );
    }
    
    /**
     * Test clear() and verify()
     *
     */
    #[@test]
    public function clearChecks() {
      $this->fixture->add(self::$check, TRUE);
      $this->fixture->clear();
      $this->assertTrue($this->fixture->verify(new StringNode('Test'), $this->scope, $this));
      $this->assertEquals(array(), $this->messages);
    }
  }
?>
