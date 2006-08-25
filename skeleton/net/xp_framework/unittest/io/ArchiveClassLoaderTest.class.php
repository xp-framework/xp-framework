<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
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
    var
      $classloader = NULL,
      $classname   = '';

    /**
     * Returns class bytes as a stream
     *
     * @access  protected
     * @param   string bytes
     * @return  &io.Stream
     */
    function &classStream($bytes) {
      $cstr= &new Stream();
      $cstr->open(STREAM_MODE_WRITE);
      $cstr->write('<?php '.$bytes.' ?>');
      $cstr->close();
      
      return $cstr;
    }

    /**
     * Sets up test case
     *
     * @access  public
     */
    function setUp() {
      $this->classname= 'ClassUsedForArchiveClassLoader'.ucfirst($this->name).'Test';
      if (class_exists($this->classname)) {
        return throw(new PrerequisitesNotMetError('Class '.$this->classname.' may not exist!'));
      }

      // Create an archive
      $archive= &new Archive(new Stream());
      $archive->open(ARCHIVE_CREATE);
      $archive->add(
        $this->classStream('class '.$this->classname.' extends Object { }'), 
        $this->classname
      );
      $archive->create();
      
      // Setup classloader
      $this->classloader= &new ArchiveClassLoader($archive);
    }

    /**
     * Test loadClass() method
     *
     * @access  public
     */
    #[@test]
    function loadClass() {
      $class= &$this->classloader->loadClass($this->classname);
      $this->assertEquals($class->getName(), $this->classname);
    }
  }
?>
