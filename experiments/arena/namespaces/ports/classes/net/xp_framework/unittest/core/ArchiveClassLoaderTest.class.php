<?php
/* This class is part of the XP framework
 *
 * $Id: ArchiveClassLoaderTest.class.php 9025 2006-12-29 14:03:23Z friebe $ 
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
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
  class ArchiveClassLoaderTest extends unittest::TestCase {
    public
      $classloader     = NULL,
      $classname       = '',
      $interfacename   = '';

    /**
     * Returns class bytes as a stream
     *
     * @param   string bytes
     * @return  &io.Stream
     */
    protected function classStream($bytes) {
      $cstr= new io::Stream();
      $cstr->open(STREAM_MODE_WRITE);
      $cstr->write('<?php '.$bytes.' ?>');
      $cstr->close();
      
      return $cstr;
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
        throw(new lang::IllegalStateException('Class '.$this->classname.' may not exist!'));
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
      } catch (lang::IllegalStateException $e) {
        throw new PrerequisitesNotMetError($e->getMessage());
      }

      // Create an archive
      $archive= new lang::archive::Archive($this->tempfile= new io::TempFile($this->name));
      $archive->open(ARCHIVE_CREATE);
      $archive->add(
        $this->classStream(
          'uses("util.Comparator", "'.$this->interfacename.'");
           class '.$this->classname.' extends Object implements '.$this->interfacename.', Comparator { 
            public function compare($a, $b) {
              return strcmp($a, $b);
            }
          }
        '), 
        $this->classname.'.class.php'
      );
      $archive->add(
        $this->classStream(
          'interface '.$this->interfacename.' {
        
          }
        '), 
        $this->interfacename.'.class.php'
      );
      $archive->::create();
      fputs(STDERR, $this->name.' '.::xp::typeOf($archive->file)."\n");
      
      // Setup classloader
      $this->classloader= new lang::archive::ArchiveClassLoader($archive);
    }
    
    /**
     * Tears down test case
     *
     */
    public function tearDown() {
      $this->tempfile->close();
      $this->tempfile->unlink();
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

      $interfaces= new util::collections::HashSet();
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
      $interface= lang::XPClass::forName('util.Comparator');
      $interfaces= new util::collections::HashSet();
      $interfaces->addAll($class->getInterfaces());
      $this->assertTrue($interfaces->contains($interface));
    }
  }
?>
