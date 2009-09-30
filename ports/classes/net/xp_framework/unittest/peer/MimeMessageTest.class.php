<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'peer.mail.MimeMessage'
  );

  /**
   * Test mime Message object
   *
   * @purpose  Unit Test
   */
  class MimeMessageTest extends TestCase {
    protected
      $fixture= NULL;
      
    /**
     * Setup
     *
     */
    public function setUp() {
      $this->fixture= new MimeMessage();
    }
    
    /**
     * Test adding single simple (text/plain) mime part
     *
     */
    #[@test]
    public function addSimplePart() {
      $this->fixture->addPart(new MimePart('Part #1', 'text/plain'));
      
      $this->assertEquals(
        "Mime-Version: 1.0\n".
        "Content-Type: text/plain;\n".
        "\tcharset=\"iso-8859-1\"\n".
        "X-Priority: 3 (Normal)\n".
        "Date: ".$this->fixture->getDate()->toString('r')."\n",
        $this->fixture->getHeaderString()
      );
      $this->assertEquals('Part #1', $this->fixture->getBody());
    }
    
    /**
     * Test adding complex (image/gif) mime part
     *
     */
    #[@test]
    public function addComplextPart() {
      $this->fixture->addPart(new MimePart('Part #1', 'image/gif'));
      
      $this->assertEquals(
        "Mime-Version: 1.0\n".
        "Content-Type: image/gif;\n".
        "\tcharset=\"iso-8859-1\"\n".
        "X-Priority: 3 (Normal)\n".
        "Date: ".$this->fixture->getDate()->toString('r')."\n",
        $this->fixture->getHeaderString()
      );
      $this->assertEquals('Part #1', $this->fixture->getBody());
    }
    
    /**
     * Test adding multiple simple parts
     *
     */
    #[@test]
    public function addSimpleParts() {
      $this->fixture->addPart(new MimePart('Part #1', 'text/plain'));
      $this->fixture->addPart(new MimePart('Part #2', 'text/plain'));
      
      $this->assertEquals(
        "Mime-Version: 1.0\n".
        "Content-Type: multipart/mixed; boundary=\"".$this->fixture->getBoundary()."\";\n".
        "\tcharset=\"iso-8859-1\"\n".
        "X-Priority: 3 (Normal)\n".
        "Date: ".$this->fixture->getDate()->toString('r')."\n",
        $this->fixture->getHeaderString()
      );
      $this->assertEquals(
        "This is a multi-part message in MIME format.\n".
        "\n".
        "--".$this->fixture->getBoundary()."\n".
        "Content-Type: text/plain; charset=\"iso-8859-1\"\n".
        "\n".
        "Part #1\n".
        "\n".
        "--".$this->fixture->getBoundary()."\n".
        "Content-Type: text/plain; charset=\"iso-8859-1\"\n".
        "\n".
        "Part #2\n".
        "\n".
        "--".$this->fixture->getBoundary()."--\n",
        $this->fixture->getBody()
      );
    }
    
    /**
     * Test adding multiple complex parts
     *
     */
    #[@test]
    public function addComplexParts() {
      $this->fixture->addPart(new MimePart('Part #1', 'image/gif'));
      $this->fixture->addPart(new MimePart('Part #2', 'image/gif'));
      
      $this->assertEquals(
        "Mime-Version: 1.0\n".
        "Content-Type: multipart/mixed; boundary=\"".$this->fixture->getBoundary()."\";\n".
        "\tcharset=\"iso-8859-1\"\n".
        "X-Priority: 3 (Normal)\n".
        "Date: ".$this->fixture->getDate()->toString('r')."\n",
        $this->fixture->getHeaderString()
      );
      $this->assertEquals(
        "This is a multi-part message in MIME format.\n".
        "\n".
        "--".$this->fixture->getBoundary()."\n".
        "Content-Type: image/gif; charset=\"iso-8859-1\"\n".
        "\n".
        "Part #1\n".
        "\n".
        "--".$this->fixture->getBoundary()."\n".
        "Content-Type: image/gif; charset=\"iso-8859-1\"\n".
        "\n".
        "Part #2\n".
        "\n".
        "--".$this->fixture->getBoundary()."--\n",
        $this->fixture->getBody()
      );
    }
  }
?>
