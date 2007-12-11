<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.collections.HashSet',
    'lang.archive.ArchiveClassLoader',
    'lang.archive.Archive',
    'io.TempFile'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.archive.ArchiveClassLoader
   * @purpose  purpose
   */
  class ArchiveClassLoaderTest extends TestCase {
    public
      $classloader     = NULL,
      $classname       = '',
      $interfacename   = '';

    /**
     * Adds sourcecode to a given XAR archive
     *
     * @param   lang.archive.Archive a
     * @param   string name
     * @param   string bytes sourcecode
     */
    protected function add(Archive $a, $name, $bytes) {
      $a->addFileBytes($name.'.class.php', $path= '', $name.'.class.php', '<?php '.$bytes.' ?>');
    }
    
    /**
     * Creates a unique class name for the running test case
     *
     * @param   string prefix default ''
     * @return  string
     * @throws  lang.IllegalStateException in case the generated class name already exists!
     */
    protected function testClassName($prefix= '') {
      $classname= $prefix.'ClassUsedForArchiveClassLoader'.ucfirst($this->name).'Test';
      if (class_exists($classname)) {
        throw new IllegalStateException('Class '.$this->classname.' may not exist!');
      }
      return $classname;
    }

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      try {
        $this->classname= $this->testClassName();
        $this->interfacename= $this->testClassName('I');
      } catch (IllegalStateException $e) {
        throw new PrerequisitesNotMetError($e->getMessage());
      }

      // Create an archive
      $this->tempfile= new TempFile($this->name);
      $archive= new Archive($this->tempfile);
      $archive->open(ARCHIVE_CREATE);

      $this->add($archive, $this->classname, '
        uses("util.Comparator", "'.$this->interfacename.'");
        class '.$this->classname.' extends Object implements '.$this->interfacename.', Comparator { 
          public function compare($a, $b) {
            return strcmp($a, $b);
          }
        }
      ');
      $this->add($archive, $this->interfacename, 'interface '.$this->interfacename.' { } ');
      $archive->create();
      
      // Setup classloader
      $this->classloader= new ArchiveClassLoader($archive);
      ClassLoader::getDefault()->registerLoader($this->classloader, TRUE);
    }
    
    /**
     * Test loadClass() method
     *
     */
    #[@test]
    public function loadClass() {
      $this->assertEquals($this->classloader->loadClass($this->classname)->getName(), $this->classname);
    }

    /**
     * Test class implements the interface from the archive
     *
     */
    #[@test]
    public function classImplementsArchivedInterface() {
      $class= $this->classloader->loadClass($this->classname);
      $interface= $this->classloader->loadClass($this->interfacename);

      $interfaces= new HashSet();
      $interfaces->addAll($class->getInterfaces());
      $this->assertTrue($interfaces->contains($interface));
    }

    /**
     * Test class implements the interface from the archive
     *
     */
    #[@test]
    public function classImplementsComparatorInterface() {
      $class= $this->classloader->loadClass($this->classname);
      $interface= XPClass::forName('util.Comparator');
      $interfaces= new HashSet();
      $interfaces->addAll($class->getInterfaces());
      $this->assertTrue($interfaces->contains($interface));
    }
  }
?>
