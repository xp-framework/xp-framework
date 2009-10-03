<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'lang.Process'
  );

  /**
   * TestCase for lang.Process' resolve() method
   *
   * @see      xp://lang.Process
   */
  class ProcessResolveTest extends TestCase {
  
    /**
     * Setup test. Verifies this test is for a certain platform
     *
     */
    public function setUp() {
      $m= $this->getClass()->getMethod($this->name);
      if (!$m->hasAnnotation('platform')) return;
      
      // This testcase is platform-specific - the platform annotation may be 
      // written as "WIN" (meaning this works only on operating systems whose
      // names contain "WIN" - e.g. Windows)  or as "!BSD" (this means this 
      // test will not run on OSes with "BSD" in their names but on any other)
      $platform= $m->getAnnotation('platform');
      if ('!' === $platform{0}) {
        $r= !preg_match('/'.substr($platform, 1).'/i', PHP_OS);
      } else {
        $r= preg_match('/'.$platform.'/i', PHP_OS);
      }
      
      if (!$r) {
        throw new PrerequisitesNotMetError('Test not intended for this platform', NULL, $platform);
      }
    }
    
    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithDriverLetter() {
      $this->assertEquals('C:\\AUTOEXEC.BAT', strtoupper(Process::resolve('C:\\AUTOEXEC.BAT')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithBackSlash() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', strtoupper(Process::resolve('\\AUTOEXEC.BAT')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithSlash() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', strtoupper(Process::resolve('/AUTOEXEC.BAT')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithoutExtension() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', strtoupper(Process::resolve('\\AUTOEXEC')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveCommandInPath() {
      $this->assertEquals(getenv('WINDIR').DIRECTORY_SEPARATOR.'explorer.exe', Process::resolve('explorer.exe'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveCommandInPathWithoutExtension() {
      $this->assertEquals(getenv('WINDIR').DIRECTORY_SEPARATOR.'explorer.exe', Process::resolve('explorer'));
    }

    /**
     * Test resolving a non-existant command
     *
     */
    #[@test, @expect('io.IOException')]
    public function resolveDirectory() {
      Process::resolve('/');
    }

    /**
     * Test resolving a non-existant command
     *
     */
    #[@test, @expect('io.IOException')]
    public function resolveNonExistant() {
      Process::resolve('@@non-existant@@');
    }

    /**
     * Test resolving a non-existant command
     *
     */
    #[@test, @expect('io.IOException')]
    public function resolveNonExistantFullyQualified() {
      Process::resolve('/@@non-existant@@');
    }

    /**
     * Test resolving a fully qualified name on Posix systems
     *
     */
    #[@test, @platform('!WIN')]
    public function resolveFullyQualified() {
      $this->assertEquals('/bin/ls', Process::resolve('/bin/ls'));
    }

    /**
     * Test resolving a fully qualified name on Posix systems
     *
     */
    #[@test, @platform('!WIN')]
    public function resolve() {
      $this->assertEquals('/bin/ls', Process::resolve('ls'));
    }
  }
?>
