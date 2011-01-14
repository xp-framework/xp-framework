<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class JarFileTest extends ZipFileVendorTest {
  
    /**
     * Test
     *
     */
    public function vendorName() {
      return 'jar';
    }
    
    /**
     * Tests reading an empty zipfile
     *
     */
    #[@test, @ignore('Cannot create empty zipfiles with `jar`')]
    public function emptyZipFile() {
      parent::emptyZipFile();
    }
    
    /**
     * Tests reading an zipfile with one entry called "הצü.txt" in its 
     * root directory.
     *
     */
    #[@test]
    public function umlautZip() {
      parent::umlautZip();
    }
  }
?>
