<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource', 'io.File');

  $package= 'xp.unittest.sources';

  /**
   * Source that load tests from a class filename
   *
   * @purpose  Source implementation
   */
  class xp·unittest·sources·ClassFileSource extends xp·unittest·sources·AbstractSource {
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
      $cl= ClassLoader::getDefault()->findUri($uri);
      if (is(NULL, $cl)) {
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
?>
