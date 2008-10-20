<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.collections.iterate.IOCollectionIterator',
    'peer.ftp.collections.FtpCollection',
    'peer.ftp.FtpConnection'
  );

  /**
   * TestCase for FTP collections API
   *
   * @see      xp://peer.ftp.collections.FtpCollection
   * @purpose  Unittest
   */
  class FtpCollectionsTest extends TestCase {
    protected $dir= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $conn= new FtpConnection('ftp://mock');
      $conn->parser= new DefaultFtpListParser();
      $this->dir= newinstance('peer.ftp.FtpDir', array('/', $conn), '{
        public function entries() {
          return new FtpEntryList(array(
            "drwx---r-t  37 p159995  ftpusers     4096 Jul 30 18:59 .",
            "drwx---r-t  37 p159995  ftpusers     4096 Jul 30 18:59 ..",
            "drwxr-xr-x   2 p159995  ftpusers     4096 Mar 19  2007 .ssh",
            "-rw-------   1 p159995  ftpusers     7507 Nov 21  2000 .bash_history",
          ), $this->connection, "/");
        }
      }');
    }
    
    /**
     * Test hasNext() and next() methods
     *
     */
    #[@test]
    public function hasNextAndNext() {
      $results= array();
      for ($c= new IOCollectionIterator(new FtpCollection($this->dir)); $c->hasNext(); ) {
        $results[]= $c->next()->getURI();
      }
      $this->assertEquals(array('/.ssh/', '/.bash_history'), $results);
    }

    /**
     * Test iteration via foreach
     *
     */
    #[@test]
    public function foreachIteration() {
      $results= array();
      foreach (new IOCollectionIterator(new FtpCollection($this->dir)) as $e) {
        $results[]= $e->getURI();
      }
      $this->assertEquals(array('/.ssh/', '/.bash_history'), $results);
    }
  }
?>
