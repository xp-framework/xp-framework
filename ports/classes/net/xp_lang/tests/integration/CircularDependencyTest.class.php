<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.System',
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
   * @see      xp://xp.compiler.task.CompilationTask
   */
  class CircularDependencyTest extends TestCase {
    protected $emitter= NULL;
    protected $files= NULL;
    
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->emitter= new xp·compiler·emit·source·Emitter();
      $this->files= new FileManager();
      $this->files->addSourcePath(dirname(__FILE__).'/src');    // FIXME: ClassPathManager?
      $this->files->setOutput(new Folder(System::tempDir()));
    }
    
    /**
     * Compile source
     *
     * @param   string resource
     * @return  xp.compiler.types.Types
     * @throws  xp.compiler.CompilationException
     */
    protected function compileSource($resource) {
      $task= new CompilationTask(
        new FileSource($this->getClass()->getPackage()->getPackage('src')->getResourceAsStream($resource)),
        new NullDiagnosticListener(),
        $this->files,
        $this->emitter
      );
      return $task->run();
    }
    
    /**
     * Tears down 
     *
     */
    public function tearDown() {
      delete($this->emitter);
      delete($this->files);
    }
    
    /**
     * Test class A which requires class B which requires class A
     *
     */
    #[@test]
    public function aba() {
      $this->compileSource('A.xp')->name();
    }
  }
?>
