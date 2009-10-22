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
    protected
      $origDir  = NULL;
  
    /**
     * Setup test. Verifies this test is for a certain platform
     *
     */
    public function setUp() {
      $this->origDir= getcwd();

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
     * Tear down test.
     *
     */
    public function tearDown() {
      chdir($this->origDir);
    }

    /**
     * Replaces backslashes in the specified path by the new separator. If $skipDrive is set
     * to TRUE, the leading drive letter definition (e.g. 'C:') is removed from the new path.
     *
     * @param  string  path
     * @param  string  newSeparator
     * @param  boolean skipDrive
     * @return string
     */
    private function replaceBackslashSeparator($path, $newSeparator, $skipDrive) {
      $parts= explode('\\', $path);
      if (preg_match('/[a-z]:/i', $parts[0]) != 0 && $skipDrive) array_shift($parts);

      return implode($newSeparator, $parts);
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithDriverLetter() {
      $this->assertTrue(is_executable(Process::resolve(getenv('WINDIR').'\\EXPLORER.EXE')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithBackSlash() {
      $path= '\\'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER.EXE', '\\', TRUE);

      chdir('C:');
      $this->assertTrue(is_executable(Process::resolve($path)));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithSlash() {
      $path= '/'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER.EXE', '/', TRUE);

      chdir('C:');
      $this->assertTrue(is_executable(Process::resolve($path)));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveFullyQualifiedWithoutExtension() {
      $path='\\'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER', '\\', true);

      chdir('C:');
      $this->assertTrue(is_executable(Process::resolve($path)));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveCommandInPath() {
      $this->assertTrue(is_executable(Process::resolve('explorer.exe')));
    }

    /**
     * Test resolving a fully qualified name on Windows
     *
     */
    #[@test, @platform('WIN')]
    public function resolveCommandInPathWithoutExtension() {
      $this->assertTrue(is_executable(Process::resolve('explorer')));
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
