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
     */
    #[@test]
    public function throwGetsWrapped() {
      $this->assertSourcecodeEquals(
        'throw xp::exception(new lang·IllegalArgumentException(\'Blam!\'));',
        $this->emit('throw new lang.IllegalArgumentException("Blam!");')
      );
    }

    /**
     * Tests try/catch block
     *
     */
    #[@test]
    public function tryCatchBlock() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else { 
          throw $__e; 
        } };'),
        $this->emit('try {
          echo 1;
        } catch (lang.Exception $e) {
          $e->printStackTrace();
        }')
      );
    }

    /**
     * Tests try/catch/finally block
     *
     */
    #[@test]
    public function tryCatchFinallyBlock() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else { 
          echo 2; 
          throw $__e; 
        } }
        echo 2; ;'),
        $this->emit('try {
          echo 1;
        } catch (lang.Exception $e) {
          $e->printStackTrace();
        } finally {
          echo 2;
        }')
      );
    }

    /**
     * Tests try/catch/finally block
     *
     */
    #[@test]
    public function finallyAfterCatchWithReturn() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'class main·FileReader extends lang·Object{
          protected $_f= NULL;
          protected $file= NULL;
          
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
          $f= new main·FileReader($file); 
          try { 
            $f->open(); 
          } catch (XPException $__e) { if ($__e->cause instanceof lang·Exception) { 
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
          } catch (lang.Exception $e) {
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
     */
    #[@test]
    public function systemExit() {
      $this->assertSourcecodeEquals(
        'throw xp::exception(new lang·SystemExit(1));',
        $this->emit('exit(1);')
      );
    }

    /**
     * Tests NULL will be wrapped into a xp::$null, which, on invocation
     * will result in an NPE.
     *
     */
    #[@test, @ignore('Rewriting NULL to xp::$null causes many side effects')]
    public function nullPointer() {
      $this->assertSourcecodeEquals(
        '$s= xp::$null; $s->invoke();',
        $this->emit('$s= NULL; $s->invoke();')
      );
    }

    /**
     * Tests try/catch block
     *
     */
    #[@test]
    public function multipleCatches() {
      $this->assertSourcecodeEquals(
        preg_replace('/\n\s*/', '', 'try { 
          echo 1; 
        } catch (XPException $__e) { if ($__e->cause instanceof lang·IllegalArgumentException) { 
          $e= $__e->cause; 
          $e->printStackTrace();
        } else if ($__e->cause instanceof lang·Exception) { 
          $e= $__e->cause; 
          $e->printStackTrace(); 
        } else { 
          throw $__e; 
        } };'),
        $this->emit('try {
          echo 1;
        } catch (lang.IllegalArgumentException $e) {
          $e->printStackTrace();
        } catch (lang.Exception $e) {
          $e->printStackTrace();
        }')
      );
    }
  }
?>
