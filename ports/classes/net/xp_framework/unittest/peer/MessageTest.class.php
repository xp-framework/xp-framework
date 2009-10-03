<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'peer.mail.Message'
  );

  /**
   * Test mail Message object
   *
   * @purpose  Unit Test
   */
  class MessageTest extends TestCase {
    protected
      $message= NULL;
     
    /**
     * Setup
     *
     */
    public function setUp() {
      $this->fixture= new Message();
    }
    
    /**
     * Test adding recipient
     *
     */
    #[@test]
    public function addRecipient() {
      $this->fixture->addRecipient(TO, $r= new InternetAddress('timm.friebe@example.com'));
      
      $this->assertEquals($r, $this->fixture->getRecipient(TO));
    }
    
    /**
     * Test adding multiple recipients
     *
     */
    #[@test, @ignore('Broken since getRecipients() use static variables')]
    public function addRecipients() {
      $this->fixture->addRecipient(TO, $r1= new InternetAddress('timm.friebe@example.com'));
      $this->fixture->addRecipient(TO, $r2= new InternetAddress('oliver.hinckel@example.com'));
      
      $this->assertEquals($r1, $this->fixture->getRecipient(TO));
      $this->assertEquals($r2, $this->fixture->getRecipient(TO));
    }
    
    /**
     * Test geting recipients
     *
     */
    #[@test]
    public function getRecipients() {
      $this->fixture->addRecipient(TO, $r1= new InternetAddress('thekid@example.com'));
      $this->fixture->addRecipient(TO, $r2= new InternetAddress('alex@example.com'));
      
      $this->assertEquals(array($r1, $r2), $this->fixture->getRecipients(TO));
    }
    
    /**
     * Test header set
     *
     */
    #[@test]
    public function header() {
      $this->fixture->setHeader('X-Common-Header', 'test');
      
      $this->assertEquals('test', $this->fixture->getHeader('X-Common-Header'));
    }
    
    /**
     * Test header string
     *
     */
    #[@test]
    public function headerString() {
      $this->fixture->setHeader('X-Common-Header', 'test');
      
      $this->assertEquals(
        "X-Common-Header: test\n".
        "Content-Type: text/plain;\n".
        "\tcharset=\"iso-8859-1\"\n".
        "Mime-Version: 1.0\n".
        "Content-Transfer-Encoding: 8bit\n".
        "X-Priority: 3 (Normal)\n".
        "Date: ".$this->fixture->getDate()->toString('r')."\n",
        $this->fixture->getHeaderString()
      );
    }
  }
?>
