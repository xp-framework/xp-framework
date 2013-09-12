<?php namespace xp\unittest\sources;

use io\File;
use lang\IllegalArgumentException;

/**
 * Source that load tests from a class filename
 */
class ClassFileSource extends AbstractSource {
  protected $file= null;
  
  /**
   * Constructor
   *
   * @param   io.File file
   * @throws  lang.IllegalArgumentException if the given file does not exist
   */
  public function __construct(File $file) {
    if (!$file->exists()) {
      throw new IllegalArgumentException('File "'.$file->getURI().'" does not exist!');
    }
    $this->file= $file;
  }

  /**
   * Get all test cases
   *
   * @param   var[] arguments
   * @return  unittest.TestCase[]
   */
  public function testCasesWith($arguments) {
    $uri= $this->file->getURI();
    $cl= \lang\ClassLoader::getDefault()->findUri($uri);
    if (is(null, $cl)) {
      throw new IllegalArgumentException('Cannot load class from '.$this->file->toString());
    }

    return $this->testCasesInClass($cl->loadUri($uri), $arguments);
  }
  
  /**
   * Creates a string representation of this source
   *
   * @return  string
   */
  public function toString() {
    return $this->getClassName().'['.$this->file->toString().']';
  }
}
