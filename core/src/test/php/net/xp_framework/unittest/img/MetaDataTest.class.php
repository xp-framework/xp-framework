<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * Base class for EXIF- and IPTC-Data tests
   *
   * @see      xp://net.xp_framework.unittest.img.ExifDataTest
   * @see      xp://net.xp_framework.unittest.img.IptcDataTest
   * @purpose  Unittest
   */
  abstract class MetaDataTest extends TestCase {
  
    /**
     * Returns a file for a classloader resource
     *
     * @param   string name
     * @param   string sub default NULL subpackage
     * @return  io.File
     */
    protected function resourceAsFile($name, $sub= NULL) {
      $package= $this->getClass()->getPackage();
      $container= $sub ? $package->getPackage($sub) : $package;
      return $container->getResourceAsStream($name);
    }

    /**
     * Extract from file and return the instance
     *
     * @param   io.File f
     * @return  lang.Generic the instance
     */
    protected abstract function extractFromFile(File $f);

    /**
     * Test reading Exif data from this file (which is definitely not an
     * image)
     *
     */
    #[@test, @expect('img.ImagingException')]
    public function fromNonImageFile() {
      $this->extractFromFile(new File(__FILE__));
    }

    /**
     * Test reading Exif data from an empty file
     *
     */
    #[@test, @expect('img.ImagingException')]
    public function fromEmptyFile() {
      $this->extractFromFile($this->resourceAsFile('empty.jpg'));
    }
  }
?>
