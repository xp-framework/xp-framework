<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.bootstrap.SandboxSourceRunner'
  );

  /**
   * TestCase
   *
   * @see      xp://net.xp_framework.unittest.bootstrap.SandboxSourceRunner
   * @purpose  Abstract base class
   */
  abstract class AbstractBootstrapTestCase extends TestCase {
    protected
      $exe      = NULL,
      $sandbox  = NULL;

    /**
     * Constructor
     *
     * @param   string name
     * @param   string exe default NULL
     */
    public function __construct($name, $exe= NULL) {
      parent::__construct($name);
      $this->exe= $exe ? $exe : getenv('_');
    }
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      try {
        $this->sandbox= new SandboxSourceRunner($this->exe);
      } catch (IllegalArgumentException $e) {
        throw new PrerequisitesNotMetError($e->getMessage(), $e);
      }
    }
    
    /**
     * Helper method
     *
     * @param   int expected
     * @param   string src
     * @throws  unittest.AssertionFailedError
     */
    protected function assertExitCode($expected, $src) {
      $exitCode= $this->sandbox->run($src);
      if ($expected !== $exitCode) {
        $this->fail(
          $this->sandbox->getExecutable().": {\n".
          '  '.implode("\n  ", $this->sandbox->getStderr())."\n".
          '  '.implode("\n  ", $this->sandbox->getStdout())."\n".
          "}\n", 
          'exit code '.$exitCode,
          'exit code '.$expected
        );
      }
    }
  }
?>
