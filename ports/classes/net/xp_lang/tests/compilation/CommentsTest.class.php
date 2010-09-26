<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   */
  class CommentsTest extends TestCase {
    protected $scope;
    protected $emitter;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->emitter= new xp·compiler·emit·source·Emitter();
      $this->scope= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), Syntax::forName('xp')),
        new NullDiagnosticListener(),
        new FileManager(),
        $this->emitter
      ));
    }

    /**
     * Compile class from source and return compiled type
     *
     * @param   string src
     * @return  xp.compiler.types.Types
     */
    protected function compile($src) {
      $r= $this->emitter->emit(
        Syntax::forName('xp')->parse(new MemoryInputStream($src)),
        $this->scope
      );
      $r->executeWith(array());
      return XPClass::forName($r->type()->name());
    }

    /**
     * Test XPClass::getComment() on compiled type
     *
     */
    #[@test]
    public function classWithoutComment() {
      $this->assertNull($this->compile('class ClassWithoutComment { }')->getComment());
    }

    /**
     * Test XPClass::getComment() on compiled type
     *
     */
    #[@test]
    public function classWithComment() {
      $class= $this->compile('
        /**
         * Person class
         */
        class ClassWithComment { }'
      );
      $this->assertEquals('Person class', $class->getComment());
    }

    /**
     * Test XPClass::getComment() on compiled type
     *
     */
    #[@test]
    public function classWithCommentWithDocTags() {
      $class= $this->compile('
        /**
         * Person class
         *
         * @see   xp://net.xp_lang.tests.compilation.CommentsTest
         */
        class ClassWithCommentWithDocTags { }'
      );
      $this->assertEquals('Person class', $class->getComment());
    }

    /**
     * Test Method::getComment() on compiled type
     *
     */
    #[@test]
    public function methodWithoutComment() {
      $class= $this->compile('
        class MethodWithoutComment { 
          public static void main(string[] $args) { }
        }'
      );
      $this->assertNull($class->getMethod('main')->getComment());
    }

    /**
     * Test Method::getComment() on compiled type
     *
     */
    #[@test]
    public function methodWithComment() {
      $class= $this->compile('
        class MethodWithComment {

          /**
           * Entry point method
           */
          public static void main(string[] $args) { }
        }'
      );
      $this->assertEquals('Entry point method', $class->getMethod('main')->getComment());
    }

    /**
     * Test Method::getComment() on compiled type
     *
     */
    #[@test]
    public function methodWithCommentWithDocTags() {
      $class= $this->compile('
        class MethodWithCommentWithDocTags {

          /**
           * Entry point method
           *
           * @see   xp://net.xp_lang.tests.compilation.CommentsTest
           */
          public static void main(string[] $args) { }
        }'
      );
      $this->assertEquals('Entry point method', $class->getMethod('main')->getComment());
    }
  }
?>
