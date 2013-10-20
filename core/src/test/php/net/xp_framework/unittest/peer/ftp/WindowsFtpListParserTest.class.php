<?php namespace net\xp_framework\unittest\peer\ftp;
 
use unittest\TestCase;
use peer\ftp\WindowsFtpListParser;
use peer\ftp\FtpDir;
use peer\ftp\FtpEntry;
use util\Date;


/**
 * Tests Windows list parser
 *
 * @see      xp://peer.ftp.WindowsFtpListParser
 * @purpose  Unit Test
 */
class WindowsFtpListParserTest extends TestCase {
  protected
    $parser     = null,
    $connection = null;
  
  /**
   * Setup this testcase
   *
   */
  public function setUp() {
    $this->parser= new WindowsFtpListParser();
    $this->connection= new \peer\ftp\FtpConnection('ftp://mock/');
  }
  
  /**
   * Test directory
   *
   */
  #[@test]
  public function directory() {
    $e= $this->parser->entryFrom('01-04-06  04:51PM       <DIR>          _db_import', $this->connection, '/');

    $this->assertSubclass($e, 'peer.ftp.FtpDir');
    $this->assertEquals('/_db_import/', $e->getName());
    $this->assertEquals(0, $e->getNumlinks());
    $this->assertEquals(null, $e->getUser());
    $this->assertEquals(null, $e->getGroup());
    $this->assertEquals(0, $e->getSize());
    $this->assertEquals(new Date('04.01.2006 16:51'), $e->getDate());
    $this->assertEquals(0, $e->getPermissions());
  }

  /**
   * Test file
   *
   */
  #[@test]
  public function regularFile() {
    $e= $this->parser->entryFrom('11-08-06  10:04AM                   27 info.txt', $this->connection, '/');

    $this->assertSubclass($e, 'peer.ftp.FtpEntry');
    $this->assertEquals('/info.txt', $e->getName());
    $this->assertEquals(0, $e->getNumlinks());
    $this->assertEquals(null, $e->getUser());
    $this->assertEquals(null, $e->getGroup());
    $this->assertEquals(27, $e->getSize());
    $this->assertEquals(new Date('08.11.2006 10:04'), $e->getDate());
    $this->assertEquals(0, $e->getPermissions());
  }
}
