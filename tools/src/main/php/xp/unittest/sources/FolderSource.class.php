<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('xp.unittest.sources.AbstractSource', 'io.Folder');

  $package= 'xp.unittest.sources';

  /**
   * Source that load tests from a class filename
   *
   * @purpose  Source implementation
   */
  class xp·unittest·sources·FolderSource extends xp·unittest·sources·AbstractSource {
    protected $folder= NULL;
    
    /**
     * Constructor
     *
     * @param   io.Folder folder
     * @throws  lang.IllegalArgumentException if the given folder does not exist
     */
    public function __construct(Folder $folder) {
      if (!$folder->exists()) {
        throw new IllegalArgumentException('Folder "'.$folder->getURI().'" does not exist!');
      }
      $this->folder= $folder;
    }

    /**
     * 
     */
    protected function testCasesInPackage($package) {
      $r= array();

      // Classes inside package itself
      foreach ($package->getClasses() as $class) {
        if (
          !$class->isSubclassOf('unittest.TestCase') ||
          Modifiers::isAbstract($class->getModifiers())
        ) continue;
        foreach ($this->testCasesInClass($class) as $testcase) {
          $r[]= $testcase;
        }
      }

      // Subpackages
      foreach ($package->getPackages() as $package) {
        $r= array_merge($r, $this->testCasesInPackage($package));
      }
      return $r;
    }

    /**
     * Get all test cases
     *
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesWith($arguments) {
      $uri= $this->folder->getURI();
      $path= $uri;
      $paths= array_flip(array_filter(array_map('realpath', xp::registry('classpath'))));

      // Search class path
      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($paths[$path])) return $this->testCasesInPackage(
          Package::forName(strtr(substr($uri, strlen($path)+ 1), DIRECTORY_SEPARATOR, '.'))
        );
        $path= substr($path, 0, $pos); 
      }
      
      throw new IllegalArgumentException('Cannot find any test cases in '.$this->folder->toString());
    }
    
    /**
     * Creates a string representation of this source
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->folder->toString().']';
    }
  }
?>
