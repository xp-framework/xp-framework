<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\Process;
use unittest\actions\IsPlatform;

/**
 * TestCase for lang.Process' resolve() method
 *
 * @see      xp://lang.Process
 */
class ProcessResolveTest extends TestCase {
  protected $origDir;

  /**
   * Setup test. Backs up current directory.
   */
  public function setUp() {
    $this->origDir= getcwd();
  }
  
  /**
   * Tear down test. Returns the previous working directory.
   */
  public function tearDown() {
    chdir($this->origDir);
  }

  /**
   * Replaces backslashes in the specified path by the new separator. If $skipDrive is set
   * to TRUE, the leading drive letter definition (e.g. 'C:') is removed from the new path.
   *
   * @param  string $path
   * @param  string $newSeparator
   * @param  bool $skipDrive
   * @return string
   */
  private function replaceBackslashSeparator($path, $newSeparator, $skipDrive) {
    $parts= explode('\\', $path);
    if (preg_match('/[a-z]:/i', $parts[0]) != 0 && $skipDrive) array_shift($parts);
    return implode($newSeparator, $parts);
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveFullyQualifiedWithDriverLetter() {
    $this->assertTrue(is_executable(Process::resolve(getenv('WINDIR').'\\EXPLORER.EXE')));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveFullyQualifiedWithDriverLetterWithoutExtension() {
    $this->assertTrue(is_executable(Process::resolve(getenv('WINDIR').'\\EXPLORER')));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveFullyQualifiedWithBackSlash() {
    $path= '\\'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER.EXE', '\\', TRUE);
    chdir('C:');
    $this->assertTrue(is_executable(Process::resolve($path)));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveFullyQualifiedWithSlash() {
    $path= '/'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER.EXE', '/', TRUE);
    chdir('C:');
    $this->assertTrue(is_executable(Process::resolve($path)));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveFullyQualifiedWithoutExtension() {
    $path='\\'.$this->replaceBackslashSeparator(getenv('WINDIR').'\\EXPLORER', '\\', TRUE);
    chdir('C:');
    $this->assertTrue(is_executable(Process::resolve($path)));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveCommandInPath() {
    $this->assertTrue(is_executable(Process::resolve('explorer.exe')));
  }

  #[@test, @action(new IsPlatform('WIN'))]
  public function resolveCommandInPathWithoutExtension() {
    $this->assertTrue(is_executable(Process::resolve('explorer')));
  }

  #[@test, @expect('io.IOException')]
  public function resolveSlashDirectory() {
    Process::resolve('/');
  }

  #[@test, @action(new IsPlatform('WIN')), @expect('io.IOException')]
  public function resolveBackslashDirectory() {
    Process::resolve('\\');
  }

  #[@test, @expect('io.IOException')]
  public function resolveEmpty() {
    Process::resolve('');
  }

  #[@test, @expect('io.IOException')]
  public function resolveNonExistant() {
    Process::resolve('@@non-existant@@');
  }

  #[@test, @expect('io.IOException')]
  public function resolveNonExistantFullyQualified() {
    Process::resolve('/@@non-existant@@');
  }

  #[@test, @action(new IsPlatform('!(WIN|ANDROID)'))]
  public function resolveFullyQualifiedOnPosix() {
    $fq= '/bin/ls';
    $this->assertEquals($fq, Process::resolve($fq));
  }

  #[@test, @action(new IsPlatform('ANDROID'))]
  public function resolveFullyQualifiedOnAndroid() {
    $fq= getenv('ANDROID_ROOT').'/framework/core.jar';
    $this->assertEquals($fq, Process::resolve($fq));
  }

  #[@test, @action(new IsPlatform('!(WIN|ANDROID)'))]
  public function resolve() {
    $this->assertEquals('/bin/ls', Process::resolve('ls'));
  }
}
