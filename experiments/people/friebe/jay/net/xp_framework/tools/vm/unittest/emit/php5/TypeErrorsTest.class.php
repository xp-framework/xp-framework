<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.emit.php5.Php5Emitter'
  );

  /**
   * Test types
   *
   * @purpose  Test Case
   */
  class TypeErrorsTest extends TestCase {
  
    /**
     * Asserts the given source raises a type error
     *
     * @access  protected
     * @param   string source
     * @param   string message default ''
     * @throws  unittest.AssertionFailedError
     */
    public function assertTypeError($source, $message= '') {
      $emitter= new Php5Emitter();
      
      $parser= new Parser();
      $emitter->setFilename('source declared in '.$this->getName().'()');
      $emitter->emitAll($parser->parse(new Lexer($source), '<'.$this->getName().'>'));
      
      $errors= $emitter->getErrors();
      foreach ($errors as $e) { if (in_array($e->code, array(3001, 3002))) return; }

      $this->fail('No type errors occured ('.$message.')', $errors, '(type error)');
    }
    
    /**
     * Tests return value incorrectly casted raises an error
     *
     */
    #[@test]
    public function returnIncorrectlyCastedValue() {
      $this->assertTypeError('
        class String {
          protected string $buffer= "";

          public bool matches($pattern) {
            return (int)preg_match($pattern, $this->buffer);
          }
        }
      ');
    }

    /**
     * Tests return value type mismatch raises an error
     *
     */
    #[@test]
    public function returnValueTypeMismatch() {
      $this->assertTypeError('
        class Test {
          public string toString() {
            return 1;
          }
        }
      ');
    }

    /**
     * Tests assigning a typed member to an incorrect type raises an error
     *
     */
    #[@test]
    public function memberReassign() {
      $this->assertTypeError('
        class Test {
          protected int $id= 0;
          
          public void setId() {
            $this->id= "string";
          }
        }
      ', 'hardcoded') &&
      $this->assertTypeError('
        class Test {
          protected int $id= 0;
          
          public void setId(string $name) {
            $this->id= $name;
          }
        }
      ', 'passed');
    }

    /**
     * Tests void method returning something raises an error.
     *
     */
    #[@test]
    public function voidMethodReturningSomething() {
      $this->assertTypeError('
        class Test {
          public void doIt() {
            return 1;
          }
        }
      ');
    }

    /**
     * Tests argument passed w/ incorrect type raises an error
     *
     */
    #[@test]
    public function argumentPassed() {
      $this->assertTypeError('
        class Test {
          public void sayHello(string $name) {
            echo "Hello ", $name;
          }
        }
        new Test()->sayHello(1);
      ');
    }
  }
?>
