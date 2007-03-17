<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('net.xp_framework.tools.vm.unittest.emit.php5.AbstractEmitterTest');

  /**
   * Tests PHP5 emitter
   *
   * @purpose  Unit Test
   */
  class TypesEmitterTest extends AbstractEmitterTest {

    /**
     * Tests 
     *
     */
    #[@test]
    public function unassignedVariableCanBecomeAnything() {
      foreach (array('NULL', '1', '1.0', 'array()', '"Hello"', 'new lang.Object();') as $init) {
        $this->emit('$x= '.$init.';');
      }
    }

    /**
     * Tests an untyped argument
     *
     */
    #[@test]
    public function untypedArgument() {
      $this->emit('class Test { public void test($bar) { $bar= 1; } }');
    }

    /**
     * Tests a typed argument
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function typedArgumentMismatch() {
      $this->emit('class Test { public void test(string $bar) { $bar= 1; } }');
    }

    /**
     * Tests an untyped member
     *
     */
    #[@test]
    public function untypedMember() {
      $this->emit('class Test { private $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests a typed member
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function typedMemberMismatch() {
      $this->emit('class Test { private string $bar; public void test() { $this->bar= 1; } }');
    }

    /**
     * Tests binary assignment
     *
     */
    #[@test]
    public function binaryAssign() {
      $this->emit('$product= 0; $product+= 3;');
    }

    /**
     * Tests array assignment via []=
     *
     */
    #[@test]
    public function arrayAssign() {
      $this->emit('$args= array(); $args[]= "Hello";');
    } 

    /**
     * Tests array assignment via []=
     *
     */
    #[@test]
    public function genericArrayAssign() {
      $this->emit('$args= array<string>(); $args[]= "Hello";');
    } 

    /**
     * Tests array assignment via []=
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function genericArrayAssignTypeMismatch() {
      $this->emit('$args= array<int>(); $args[]= "Hello";');
    } 

    /**
     * Tests array assignment via []=
     *
     */
    #[@test]
    public function genericHashAssign() {
      $this->emit('$args= array<int, string>(); $args[]= "Hello";');
    } 

    /**
     * Tests array assignment via []=
     *
     */
    #[@test]
    public function stackTraceElementToString() {
      $this->emit('class ThrowableExcerpt {
       public
         $args     = array();
      
        protected string qualifiedClassName(string $class) {
          return "qname";
        }
        
        public void toString() {
          $args= array();
          if (isset($this->args)) {
            for ($j= 0, $a= sizeof($this->args); $j < $a; $j++) {
              if (is_array($this->args[$j])) {
                $args[]= "array[" ~ sizeof($this->args[$j]) ~ "]";
              } else if (is_object($this->args[$j])) {
                $args[]= $this->qualifiedClassName(get_class($this->args[$j])) ~ "{}";
              } else if (is_string($this->args[$j])) {
                $display= str_replace("%", "%%", addcslashes(substr($this->args[$j], 0, min(
                  (FALSE === $p= strpos($this->args[$j], "\n")) ? 0x40 : $p, 
                  0x40
                )), "\0..\17"));
                $args[]= (
                  "(0x" ~ dechex(strlen($this->args[$j])) ~ ")\"" ~ 
                  $display ~ 
                  "\""
                );
              } else if (is_null($this->args[$j])) {
                $args[]= "NULL";
              } else if (is_scalar($this->args[$j])) {
                $args[]= (string)$this->args[$j];
              } else if (is_resource($this->args[$j])) {
                $args[]= (string)$this->args[$j];
              } else {
                $args[]= "<" ~ gettype($this->args[$j]) ~ ">";
              }
            }
          }
        } 
      }');
    } 

    /**
     * Tests array assignment via [X]=
     *
     */
    #[@test]
    public function arrayOffsetAssign() {
      $this->emit('$args= array(); $args[0]= "Hello";');
    } 

    /**
     * Tests array offset return [X]
     *
     */
    #[@test]
    public function arrayOffsetReturn() {
      $this->emit('class Throwable { 
        public lang.StackTraceElement elementAt(int $o) { 
          $a= array();
          return $a[$o]; 
        } 
      }');
    } 

    /**
     * Tests typed array return
     *
     */
    #[@test]
    public function typedArrayReturn() {
      $this->emit('class Throwable { public lang.StackTraceElement[] getStackTrace() { return array(); } } ');
    }

    /**
     * Tests array return
     *
     */
    #[@test]
    public function arrayReturn() {
      $this->emit('class List { public mixed[] asArray() { return array(); } } ');
    }
  }
?>
