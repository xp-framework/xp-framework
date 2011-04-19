<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource', 'io.File');

  /**
   * Source that load tests from a class filename
   *
   * @purpose  Source implementation
   */
  class ClassFileSource extends xp·unittest·sources·AbstractSource {
    protected $file= NULL;
    
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
      $path= dirname($uri);
      $paths= array_flip(array_filter(array_map('realpath', xp::registry('classpath'))));

      // Search class path
      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($paths[$path])) return $this->testCasesInClass(
          XPClass::forName(strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.')),
          $arguments
        );

        $path= substr($path, 0, $pos); 
      }
      
      throw new IllegalArgumentException('Cannot load class from '.$this->file->toString());
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
?>
