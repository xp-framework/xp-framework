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
      
      // This testcase is platform-specific
      $platform= $m->getAnnotation('platform');
      if (!preg_match($platform, PHP_OS)) {
        throw new PrerequisitesNotMetError('Does not work on this platform', NULL, $platform);
      }
    }
    
    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
    public function resolveFullyQualifiedWithDriverLetter() {
      $this->assertEquals('C:\\AUTOEXEC.BAT', Process::resolve('C:\\AUTOEXEC.BAT'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
    public function resolveFullyQualifiedWithBackSlash() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', Process::resolve('\\AUTOEXEC.BAT'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
    public function resolveFullyQualifiedWithSlash() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', Process::resolve('/AUTOEXEC.BAT'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
    public function resolveFullyQualifiedWithoutExtension() {
      chdir('C:');
      $this->assertEquals('C:\\AUTOEXEC.BAT', Process::resolve('\\AUTOEXEC'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
    public function resolveCommandInPath() {
      $this->assertEquals(getenv('WINDIR').DIRECTORY_SEPARATOR.'explorer.exe', Process::resolve('explorer.exe'));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('/WIN/i')]
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
    #[@test, @platform('/!(WIN)/i')]
    public function resolveFullyQualified() {
      $this->assertEquals('/bin/sh', Process::resolve('/bin/sh'));
    }

    /**
     * Test resolving a fully qualified name on Posix systems
     *
     */
    #[@test, @platform('/!(WIN)/i')]
    public function resolve() {
      $this->assertEquals('/bin/sh', Process::resolve('sh'));
    }
  }
?>
