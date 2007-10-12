<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.mail.store.ImapStore'
  );

  /**
   * TestCase for peer.mail.store.ImapStore
   *
   * @see       xp://peer.mail.store.ImapStore
   * @see       xp://peer.mail.store.CclientStore
   * @purpose   Test ImapStore class
   */
  class ImapStoreTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= newinstance('peer.mail.store.ImapStore', array(), '{
        public $connect= array();
        
        protected function _connect($mbx, $user, $pass, $flags) {
          $this->connect= array(
            "mbx"   => $mbx, 
            "user"  => $user, 
            "pass"  => $pass, 
            "flags" => $flags
          );
          return TRUE;
        }
      }');
    }
    
    /**
     * Test parsing of DSN for imap
     *
     */
    #[@test]
    public function connectImap() {
      $this->fixture->connect('imap://example.org');
      $this->assertEquals('{example.org:143/imap}', $this->fixture->connect['mbx']);
    }
    
    /**
     * Test parsing of DSN for imaps
     *
     */
    #[@test]
    public function connectImaps() {
      $this->fixture->connect('imaps://example.org');
      $this->assertEquals('{example.org:993/imap/ssl}', $this->fixture->connect['mbx']);
    }

    /**
     * Test parsing of DSN for imapt
     *
     */
    #[@test]
    public function connectImapt() {
      $this->fixture->connect('imapt://example.org');
      $this->assertEquals('{example.org:993/imap/tls}', $this->fixture->connect['mbx']);
    }

    /**
     * Test parsing of DSN for imaps without validating certificate
     *
     */
    #[@test]
    public function connectImapsNoValidate() {
      $this->fixture->connect('imaps://example.org?novalidate-cert=1');
      $this->assertEquals('{example.org:993/imap/ssl/novalidate-cert}', $this->fixture->connect['mbx']);
    }
    
    /**
     * Test parsing of DSN for imapt without validating certificate
     *
     */
    #[@test]
    public function connectImaptNoValidate() {
      $this->fixture->connect('imapt://example.org?novalidate-cert=1');
      $this->assertEquals('{example.org:993/imap/tls/novalidate-cert}', $this->fixture->connect['mbx']);
    }
    
    /**
     * Test parsing of DSN with nondefault port
     *
     */
    #[@test]
    public function connectImapNonStandardPort() {
      $this->fixture->connect('imap://example.org:566');
      $this->assertEquals('{example.org:566/imap}', $this->fixture->connect['mbx']);
    }
  }
?>
