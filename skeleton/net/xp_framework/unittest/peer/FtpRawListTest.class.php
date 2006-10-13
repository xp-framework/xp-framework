<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'util.profiling.unittest.TestCase',
    'peer.ftp.FtpDir',
    'peer.ftp.FtpEntry',
    'util.Date'
  );

  /**
   * Test parsing of ftp_rawlist() output
   *
   * @see      php://ftp_rawlist
   * @purpose  Unit Test
   */
  class FtpRawListTest extends TestCase {
      
    /**
     * Parse entry from string. FIXME: This method should really be in 
     * FtpDir or a utility class, but not here!
     *
     * @access  protected
     * @param   string entry
     * @return  &peer.ftp.FtpEntry
     */
    function &parseEntryFrom($entry) {
      sscanf(
        $entry, 
        '%s %d %s %s %d %s %d %[^ ] %s',
        $permissions,
        $numlinks,
        $user,
        $group,
        $size,
        $month,
        $day,
        $date,
        $filename
      );
      
      if ('d' == $permissions{0}) {
        $e= &new FtpDir($filename, $this->_hdl);
      } else {
        $e= &new FtpEntry($filename, $this->_hdl);
      }
      with ($e); {
        $d= &new Date($month.' '.$day.' '.(strstr($date, ':') ? date('Y').' '.$date : $date));
        
        // Check for "recent" file which are specified "HH:MM" instead
        // of year for the last 6 month (as specified in coreutils/src/ls.c)
        if (strstr($date, ':')) {
          $now= &Date::now();
          if ($d->getMonth() > $now->getMonth()) $d->year--;
        }
        
        $e->setPermissions(substr($permissions, 1));
        $e->setNumlinks($numlinks);
        $e->setUser($user);
        $e->setGroup($group);
        $e->setSize($size);
        $e->setDate($d);
      }
      return $e;
    }

    /**
     * Test directory
     *
     * @access  public
     */
    #[@test]
    function dotDirectory() {
      $e= &$this->parseEntryFrom('drwx---r-t 37 p159995 ftpusers 4096 Apr 4 20:16 .');

      $this->assertSubclass($e, 'peer.ftp.FtpDir') &&
      $this->assertEquals('.', $e->getName()) &&
      $this->assertEquals(37, $e->getNumlinks()) &&
      $this->assertEquals('p159995', $e->getUser()) &&
      $this->assertEquals('ftpusers', $e->getGroup()) &&
      $this->assertEquals(4096, $e->getSize()) &&
      $this->assertEquals(new Date('04.04.'.date('Y').' 20:16'), $e->getDate()) &&
      $this->assertEquals(704, $e->getPermissions());
    }

    /**
     * Test directory
     *
     * @access  public
     */
    #[@test]
    function regularFile() {
      $e= &$this->parseEntryFrom('-rw----r-- 1 p159995 ftpusers 415 May 23 2000 write.html');

      $this->assertSubclass($e, 'peer.ftp.FtpEntry') &&
      $this->assertEquals('write.html', $e->getName()) &&
      $this->assertEquals(1, $e->getNumlinks()) &&
      $this->assertEquals('p159995', $e->getUser()) &&
      $this->assertEquals('ftpusers', $e->getGroup()) &&
      $this->assertEquals(415, $e->getSize()) &&
      $this->assertEquals(new Date('23.05.2000'), $e->getDate()) &&
      $this->assertEquals(604, $e->getPermissions());
    }
  }
?>
