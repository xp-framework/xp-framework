<?php
/* This class is part of the XP framework
 *
 * $Id: WindowsFtpListParserTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::peer;
 
  ::uses(
    'unittest.TestCase',
    'peer.ftp.WindowsFtpListParser',
    'peer.ftp.FtpDir',
    'peer.ftp.FtpEntry',
    'util.Date'
  );

  /**
   * Tests Windows list parser
   *
   * @see      xp://peer.ftp.WindowsFtpListParser
   * @purpose  Unit Test
   */
  class WindowsFtpListParserTest extends unittest::TestCase {
    
    /**
     * Setup this testcase
     *
     */
    public function setUp() {
      $this->fixture= new peer::ftp::WindowsFtpListParser();
    }
    
    /**
     * Test directory
     *
     */
    #[@test]
    public function directory() {
      $e= $this->fixture->entryFrom('01-04-06  04:51PM       <DIR>          _db_import');

      $this->assertSubclass($e, 'peer.ftp.FtpDir') &&
      $this->assertEquals('_db_import', $e->getName()) &&
      $this->assertEquals(0, $e->getNumlinks()) &&
      $this->assertEquals(NULL, $e->getUser()) &&
      $this->assertEquals(NULL, $e->getGroup()) &&
      $this->assertEquals(0, $e->getSize()) &&
      $this->assertEquals(new util::Date('04.01.2006 16:51'), $e->getDate()) &&
      $this->assertEquals(0, $e->getPermissions());
    }

    /**
     * Test file
     *
     */
    #[@test]
    public function regularFile() {
      $e= $this->fixture->entryFrom('11-08-06  10:04AM                   27 info.txt');

      $this->assertSubclass($e, 'peer.ftp.FtpEntry') &&
      $this->assertEquals('info.txt', $e->getName()) &&
      $this->assertEquals(0, $e->getNumlinks()) &&
      $this->assertEquals(NULL, $e->getUser()) &&
      $this->assertEquals(NULL, $e->getGroup()) &&
      $this->assertEquals(27, $e->getSize()) &&
      $this->assertEquals(new util::Date('08.11.2006 10:04'), $e->getDate()) &&
      $this->assertEquals(0, $e->getPermissions());
    }
  }
?>
