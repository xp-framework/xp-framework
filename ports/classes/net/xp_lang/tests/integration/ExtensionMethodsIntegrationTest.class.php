<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.System',
    'lang.types.String',
    'io.Folder',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   * @see      xp://net.xp_lang.tests.integration.ExtensionMethodsIntegrationTestFixture
   */
  class ExtensionMethodsIntegrationTest extends TestCase {
    protected static $temp= NULL;
    
    /**
     * Compile sourcecode
     *
     */
    #[@beforeClass]
    public static function compile() {
      self::$temp= new Folder(System::tempDir());
    
      // Compiler
      $emitter= new xp·compiler·emit·source·Emitter();
      $files= new FileManager();
      $files->setOutput(self::$temp);
      $task= new CompilationTask(
        new FileSource(ClassLoader::getDefault()->getResourceAsStream('net/xp_lang/tests/integration/src/StringExtensions.xp')),
        new NullDiagnosticListener(),
        $files,
        $emitter
      );
      $task->run();
      
      // Hack to add to include_path - should be done via Runtime::getInstance()->withClassPath() or so
      set_include_path(get_include_path().PATH_SEPARATOR.PATH_SEPARATOR.rtrim(self::$temp->getURI(), DIRECTORY_SEPARATOR));
    }

    /**
     * Run sourcecode in a new VM, using ExtensionMethodsIntegrationTestFixture.
     *
     * @param   string source
     * @return  var result
     * @throws  io.IOException
     */
    protected function run($source) {
      $p= Runtime::getInstance()->newInstance(
        NULL,
        'class', 
        'net.xp_lang.tests.integration.ExtensionMethodsIntegrationTestFixture',
        array()
      );
      $p->in->write($source."\n");
      $p->in->close();
      $e= $p->err->read();
      $o= $p->out->read();
      $p->close();
      if ($e) {
        throw new IOException($e);
      }
      if ('+' === $o[0]) {
        return unserialize(substr($o, 1));
      } else if ('-' === $o[0]) {
        throw new IllegalStateException(substr($o, 1));
      }
      throw new IOException($o);
    }

    /**
     * Test trim() extension method
     *
     */
    #[@test]
    public function trimMethod() {
      $this->assertEquals(
        new String('Hello'), 
        $this->run('return create(new String(" Hello "))->trim(" ");')
      );
    }

    /**
     * Test non-existant extension method
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= '/undefined method lang.types.String::nonExistant/')]
    public function nonExistantMethod() {
      $this->run('return create(new String(" Hello "))->nonExistant();');
    }
  }
?>
