<?php
/* This class is part of the XP framework
 *
 * $Id: ArchiveClassLoaderTest.class.php 8518 2006-11-20 19:31:40Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'util.collections.HashSet',
    'io.cca.ArchiveClassLoader',
    'io.cca.Archive',
    'io.Stream'
  );

  /**
   * TestCase
   *
   * @see      xp://io.cca.ArchiveClassLoader
   * @purpose  purpose
   */
  class ArchiveClassLoaderTest extends TestCase {
    public
      $classloader     = NULL,
      $classname       = '',
      $interfacename   = '';

    /**
     * Returns class bytes as a stream
     *
     * @access  protected
     * @param   string bytes
     * @return  &io.Stream
     */
    public function &classStream($bytes) {
      $cstr= new Stream();
      $cstr->open(STREAM_MODE_WRITE);
      $cstr->write('<?php '.$bytes.' ?>');
      $cstr->close();
      
      return $cstr;
    }
    
    /**
     * Creates a unique class name for the running test case
     *
     * @access  protected
     * @param   string prefix default ''
     * @return  string
     * @throws  lang.IllegalStateException in case the generated class name already exists!
     */
    public function testClassName($prefix= '') {
      $classname= $prefix.'ClassUsedForArchiveClassLoader'.ucfirst($this->name).'Test';
      if (class_exists($classname)) {
        throw(new IllegalStateException('Class '.$this->classname.' may not exist!'));
      }
      return $classname;
    }

    /**
     * Sets up test case
     *
     * @access  public
     */
    public function setUp() {
      try {
        $this->classname= $this->testClassName();
        $this->interfacename= $this->testClassName('I');
      } catch (IllegalStateException $e) {
        throw(new PrerequisitesNotMetError($e->getMessage()));
      }

      // Create an archive
      $archive= new Archive(new Stream());
      $archive->open(ARCHIVE_CREATE);
      $archive->add(
        $this->classStream(
          'class '.$this->classname.' extends Object { 
            function compare($a, $b) {
              return strcmp($a, $b);
            }
          } implements(__FILE__, "'.$this->interfacename.'", "util.Comparator");
        '), 
        $this->classname
      );
      $archive->add(
        $this->classStream(
          'class '.$this->interfacename.' extends Interface { 
        
          }
        '), 
        $this->interfacename
      );
      $archive->create();
      
      // Setup classloader
      $this->classloader= new ArchiveClassLoader($archive);
    }

    /**
     * Test loadClass() method
     *
     * @access  public
     */
    #[@test]
    public function loadClass() {
      $class= &$this->classloader->loadClass($this->classname);
      $class && $this->assertEquals($class->getName(), $this->classname);
    }
    
    /**
     * Test class implements the interface from the archive
     *
     * @access  public
     */
    #[@test]
    public function classImplementsArchivedInterface() {
      if (
        $class= &$this->classloader->loadClass($this->classname) &&
        $interface= &$this->classloader->loadClass($this->interfacename)
      ) {
        $interfaces= new HashSet();
        $interfaces->addAll($class->getInterfaces());
        $this->assertTrue($interfaces->contains($interface));
      }
    }

    /**
     * Test class implements the interface from the archive
     *
     * @access  public
     */
    #[@test]
    public function classImplementsComparatorInterface() {
      if (
        $class= &$this->classloader->loadClass($this->classname) &&
        $interface= &XPClass::forName('util.Comparator')
      ) {
        $interfaces= new HashSet();
        $interfaces->addAll($class->getInterfaces());
        $this->assertTrue($interfaces->contains($interface));
      }
    }
  }
?>
