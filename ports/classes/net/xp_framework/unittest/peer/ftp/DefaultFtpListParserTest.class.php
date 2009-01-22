<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'peer.ftp.DefaultFtpListParser',
    'peer.ftp.FtpDir',
    'peer.ftp.FtpEntry',
    'util.Date'
  );

  /**
   * Tests default list parser
   *
   * @see      xp://peer.ftp.DefaultFtpListParser
   * @purpose  Unit Test
   */
  class DefaultFtpListParserTest extends TestCase {
    protected
      $parser     = NULL,
      $connection = NULL;

    /**
     * Setup this testcase
     *
     */
    public function setUp() {
      $this->fixture= new DefaultFtpListParser();
      $this->connection= new FtpConnection('ftp://mock/');
    }
    
    /**
     * Test directory
     *
     */
    #[@test]
    public function dotDirectory() {
      $e= $this->fixture->entryFrom('drwx---r-t 37 p159995 ftpusers 4096 Apr 4 2009 .', $this->connection, '/');

      $this->assertSubclass($e, 'peer.ftp.FtpDir');
      $this->assertEquals('/./', $e->getName());
      $this->assertEquals(37, $e->getNumlinks());
      $this->assertEquals('p159995', $e->getUser());
      $this->assertEquals('ftpusers', $e->getGroup());
      $this->assertEquals(4096, $e->getSize());
      $this->assertEquals(new Date('04.04.2009'), $e->getDate());
      $this->assertEquals(704, $e->getPermissions());
    }

    /**
     * Test file
     *
     */
    #[@test]
    public function regularFile() {
      $e= $this->fixture->entryFrom('-rw----r-- 1 p159995 ftpusers 415 May 23 2000 write.html', $this->connection, '/');

      $this->assertSubclass($e, 'peer.ftp.FtpEntry');
      $this->assertEquals('/write.html', $e->getName());
      $this->assertEquals(1, $e->getNumlinks());
      $this->assertEquals('p159995', $e->getUser());
      $this->assertEquals('ftpusers', $e->getGroup());
      $this->assertEquals(415, $e->getSize());
      $this->assertEquals(new Date('23.05.2000'), $e->getDate());
      $this->assertEquals(604, $e->getPermissions());
    }

    /**
     * Test file
     *
     */
    #[@test]
    public function whitespaceInFileName() {
      $e= $this->fixture->entryFrom('-rw----r-- 1 p159995 ftpusers 415 May 23 2000 answer me.html', $this->connection, '/');

      $this->assertSubclass($e, 'peer.ftp.FtpEntry');
      $this->assertEquals('/answer me.html', $e->getName());
      $this->assertEquals(1, $e->getNumlinks());
      $this->assertEquals('p159995', $e->getUser());
      $this->assertEquals('ftpusers', $e->getGroup());
      $this->assertEquals(415, $e->getSize());
      $this->assertEquals(new Date('23.05.2000'), $e->getDate());
      $this->assertEquals(604, $e->getPermissions());
    }
    
    /**
     * Return an entry from a given date in its listing with a given 
     * reference date
     *
     * @param   string listed
     * @param   util.Date ref
     * @return  peer.ftp.FtpEntry
     */
    protected function entryWithDate($listed, Date $ref) {
      return $this->fixture->entryFrom(
        'drwx---r-t 37 p159995 ftpusers 4096 '.$listed.' .', 
        $this->connection, 
        '/', 
        $ref
      );
    }
    
    /**
     * Test compact date format
     *
     */
    #[@test]
    public function compactDate() {
      $ref= new Date('2009-01-22 20:16');
      foreach (array(
        'Jul 23 20:16' => '23.07.2009 20:16',   // 182 days in the future
        'Apr 4 20:16'  => '04.04.2009 20:16',
        'Jan 22 20:16' => '22.01.2009 20:16',   // exactly "today"
        'Dec 1 20:16'  => '01.12.2008 20:16',
        'Jul 24 20:16' => '24.07.2008 20:16',   // 182 days in the past
      ) as $listed => $meaning) {
        $this->assertEquals(new Date($meaning), $this->entryWithDate($listed, $ref)->getDate(), $listed);
      }
    }
  }
?>
