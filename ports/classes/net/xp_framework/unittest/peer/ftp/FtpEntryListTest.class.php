<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.ftp.FtpEntryList',
    'peer.ftp.FtpConnection'
  );

  /**
   * TestCase FTP listing functionality
   *
   * @see      xp://peer.ftp.FtpListIterator
   * @see      xp://peer.ftp.FtpEntryList
   * @purpose  Unittest
   */
  class FtpEntryListTest extends TestCase {
    protected
      $conn          = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->conn= new FtpConnection('ftp://mock');
      $this->conn->parser= new DefaultFtpListParser();
    }
    
    /**
     * Iterates on a given list
     *
     * @param   string[] list
     * @return  string[] results each element as {qualified.className}({elementName})
     */
    protected function iterationOn($list) {
      $it= new FtpListIterator($list, $this->conn);
      $r= array();
      foreach ($it as $entry) {
        $r[]= $entry->getClassName().'('.$entry->getName().')';
      }
      return $r;
    }
    
    /**
     * Creates a list fixture
     *
     * @return  peer.ftp.FtpEntryList
     */
    protected function listFixture() {
      return new FtpEntryList(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..',
        'drwxr-xr-x   2 p159995  ftpusers     4096 Mar  9  2007 secret',
        '-rw-r--r--   1 p159995  ftpusers       82 Oct 31  2006 wetter.html',
        '-rw-------   1 p159995  ftpusers      102 Dec 14  2007 .htaccess'
      ), $this->conn);
    }

    /**
     * Test keys contain names and values are instances of FtpDir / FtpFile.
     *
     */
    #[@test]
    public function iteration() {
      $names= array('/secret/', '/wetter.html', '/.htaccess');
      $classes= array('peer.ftp.FtpDir', 'peer.ftp.FtpFile', 'peer.ftp.FtpFile');
      $offset= 0;

      foreach ($this->listFixture() as $key => $entry) {
        $this->assertClass($entry, $classes[$offset]);
        $this->assertEquals($names[$offset], $key);
        $offset++;
      } 
    }

    /**
     * Test keys contain names and values are instances of FtpDir / FtpFile.
     *
     */
    #[@test]
    public function asArray() {
      $names= array('/secret/', '/wetter.html', '/.htaccess');
      $classes= array('peer.ftp.FtpDir', 'peer.ftp.FtpFile', 'peer.ftp.FtpFile');
      $offset= 0;

      foreach ($this->listFixture()->asArray() as $entry) {
        $this->assertClass($entry, $classes[$offset]);
        $this->assertEquals($names[$offset], $entry->getName());
        $offset++;
      } 
    }

    /**
     * Test isEmpty() method
     *
     */
    #[@test]
    public function emptyDirectoryIsEmpty() {
      $this->assertTrue(create(new FtpEntryList(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..'
      ), $this->conn))->isEmpty());
    }

    /**
     * Test isEmpty() method
     *
     */
    #[@test]
    public function fixtureIsEmpty() {
      $this->assertFalse($this->listFixture()->isEmpty());
    }

    /**
     * Test size() method
     *
     */
    #[@test]
    public function emptyDirectorySize() {
      $this->assertEquals(0, create(new FtpEntryList(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..'
      ), $this->conn))->size());
    }

    /**
     * Test size() method
     *
     */
    #[@test]
    public function fixtureSize() {
      $this->assertEquals(3, $this->listFixture()->size());
    }
    
    /**
     * Test iterating on an empty directory
     *
     */
    #[@test]
    public function emptyDirectory() {
      $this->assertEquals(array(), $this->iterationOn(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..'
      ))); 
    }

    /**
     * Test iterating on an directory with one file
     *
     */
    #[@test]
    public function directoryWithOneFile() {
      $this->assertEquals(array('peer.ftp.FtpFile(/wetter.html)'), $this->iterationOn(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..',
        '-rw-r--r--   1 p159995  ftpusers       82 Oct 31  2006 wetter.html'
      ))); 
    }

    /**
     * Test iterating on an directory with one directory
     *
     */
    #[@test]
    public function directoryWithOneDir() {
      $this->assertEquals(array('peer.ftp.FtpDir(/secret/)'), $this->iterationOn(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..',
        'drwxr-xr-x   2 p159995  ftpusers     4096 Mar  9  2007 secret'
      ))); 
    }
    
    /**
     * Test iterating on an directory with directories and files
     *
     */
    #[@test]
    public function directoryWithDirsAndFiles() {
      $this->assertEquals(array('peer.ftp.FtpDir(/secret/)', 'peer.ftp.FtpFile(/wetter.html)', 'peer.ftp.FtpFile(/.htaccess)'), $this->iterationOn(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..',
        'drwxr-xr-x   2 p159995  ftpusers     4096 Mar  9  2007 secret',
        '-rw-r--r--   1 p159995  ftpusers       82 Oct 31  2006 wetter.html',
        '-rw-------   1 p159995  ftpusers      102 Dec 14  2007 .htaccess'
      ))); 
    }

    /**
     * Test iterating on an directory with one directory
     *
     */
    #[@test]
    public function dotDirectoriesAtEnd() {
      $this->assertEquals(array('peer.ftp.FtpDir(/secret/)'), $this->iterationOn(array(
        'drwxr-xr-x   2 p159995  ftpusers     4096 Mar  9  2007 secret',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..'
      ))); 
    }

    /**
     * Test iterating on an directory with one directory
     *
     */
    #[@test]
    public function dotDirectoriesMixedWithRegularResults() {
      $this->assertEquals(array('peer.ftp.FtpDir(/secret/)', 'peer.ftp.FtpFile(/wetter.html)', 'peer.ftp.FtpFile(/.htaccess)'), $this->iterationOn(array(
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 .',
        'drwxr-xr-x   2 p159995  ftpusers     4096 Mar  9  2007 secret',
        '-rw-r--r--   1 p159995  ftpusers       82 Oct 31  2006 wetter.html',
        'drwx---r-t  36 p159995  ftpusers     4096 May 14 17:44 ..',
        '-rw-------   1 p159995  ftpusers      102 Dec 14  2007 .htaccess'
      ))); 
    }
  }
?>
