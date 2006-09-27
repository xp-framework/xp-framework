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
  class ExceptionEmitterTest extends AbstractEmitterTest {

    /**
     * Tests throw gets wrapped by xp::exception()
     *
     * @access  public
     */
    #[@test]
    function throwGetsWrapped() {
      $this->assertSourcecodeEquals(
        'throw xp::exception(new xp·lang·IllegalArgumentException(\'Blam!\'));',
        $this->emit('throw new xp~lang~IllegalArgumentException("Blam!");')
      );
    }

    /**
     * Tests try/catch block
     *
     * @access  public
     */
    #[@test]
    function tryCatchBlock() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof xp·lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else { 
          throw $__e; 
        } };'),
        $this->emit('try {
          echo 1;
        } catch (xp~lang~Exception $e) {
          $e->printStackTrace();
        }')
      );
    }

    /**
     * Tests try/catch/finally block
     *
     * @access  public
     */
    #[@test]
    function tryCatchFinallyBlock() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof xp·lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else { 
          echo 2; 
          throw $__e; 
        } }
        echo 2; ;'),
        $this->emit('try {
          echo 1;
        } catch (xp~lang~Exception $e) {
          $e->printStackTrace();
        } finally {
          echo 2;
        }')
      );
    }

    /**
     * Tests try/catch/finally block
     *
     * @access  public
     */
    #[@test]
    function finallyAfterCatchWithReturn() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class FileReader extends xp·lang·Object{
          protected $_f;
          protected $file;
          
          public function __construct($file){
            $this->file= $file; 
          }
          
          public function open(){
            $this->_f= fopen($this->file, \'r\'); 
          }
          
          public function close(){
            fclose($this->_f); 
          }
        }; 
        
        function openFile($file) {
          $f= new FileReader($file); 
          try { 
            $f->open(); 
          } catch (XPException $__e) { if ($__e->cause instanceof xp·lang·Exception) { 
            $e= $__e->cause; 
            $e->printStackTrace();
            $f->close(); 
            return FALSE;
          } else { 
            $f->close(); 
            throw $__e; 
          } }
          $f->close(); ; 
          return TRUE; 
        };'),
        $this->emit('class FileReader {
          protected resource $_f;
          protected string $file;

          public __construct($file) { 
            $this->file= $file;
          }

          public void open() {
            $this->_f= fopen($this->file, "r");
          }

          public void close() { 
            fclose($this->_f);
          }
        }
        
        function openFile($file) {
          $f= new FileReader($file);
          try {
            $f->open();
          } catch (xp~lang~Exception $e) {
            $e->printStackTrace();
            return FALSE;
          } finally {
            $f->close();
          }
          return TRUE;
        }')
      );
    }
    
    /**
     * Tests exit() gets transformed to a SystemExit exception
     *
     * @access  public
     */
    #[@test]
    function systemExit() {
      $this->assertSourcecodeEquals(
        'throw xp::exception(new xp·lang·SystemExit(1));',
        $this->emit('exit(1);')
      );
    }

    /**
     * Tests NULL will be wrapped into a xp::$null, which, on invocation
     * will result in an NPE.
     *
     * @access  public
     */
    #[@test, @ignore('Rewriting NULL to xp::$null causes many side effects')]
    function nullPointer() {
      $this->assertSourcecodeEquals(
        '$s= xp::$null; $s->invoke();',
        $this->emit('$s= NULL; $s->invoke();')
      );
    }

    /**
     * Tests try/catch block
     *
     * @access  public
     */
    #[@test]
    function multipleCatches() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof xp·lang·IllegalArgumentException) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else if ($__e->cause instanceof xp·lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace(); 
        } else { 
          throw $__e; 
        } };'),
        $this->emit('try {
          echo 1;
        } catch (xp~lang~IllegalArgumentException $e) {
          $e->printStackTrace();
        } catch (xp~lang~Exception $e) {
          $e->printStackTrace();
        }')
      );
    }
  }
?>
