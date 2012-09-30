<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.unittest.sources.AbstractSource', 
    'io.Folder',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );

  $package= 'xp.unittest.sources';

  /**
   * Source that loads tests from test case classes inside a folder and
   * its subfolders.
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
     * Find first classloader responsible for a given path
     *
     * @param   string path
     * @return  lang.IClassLoader
     */
    protected function findLoaderFor($path) {
      foreach (ClassLoader::getLoaders() as $cl) {
        if (0 === strncmp($cl->path, $path, strlen($cl->path))) return $cl;
      }
      return NULL;      
    }

    /**
     * Get all test cases
     *
     * @param   var[] arguments
     * @return  unittest.TestCase[]
     */
    public function testCasesWith($arguments) {
      if (NULL === ($cl= $this->findLoaderFor($this->folder->getURI()))) {
        throw new IllegalArgumentException($this->folder->toString().' is not in class path');
      }
      $l= strlen($cl->path);
      $e= -strlen(xp::CLASS_FILE_EXT);

      $it= new FilteredIOCollectionIterator(
        new FileCollection($this->folder),
        new ExtensionEqualsFilter(xp::CLASS_FILE_EXT),
        TRUE  // recursive
      );
      $cases= array();
      foreach ($it as $element) {
        $name= strtr(substr($element->getUri(), $l, $e), DIRECTORY_SEPARATOR, '.');
        $class= XPClass::forName($name);
        if (
          !$class->isSubclassOf('unittest.TestCase') ||
          Modifiers::isAbstract($class->getModifiers())
        ) continue;

        $cases= array_merge($cases, $this->testCasesInClass($class, $arguments));
      }

      if (empty($cases)) {
        throw new IllegalArgumentException('Cannot find any test cases in '.$this->folder->toString());
      }
      return $cases;
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
