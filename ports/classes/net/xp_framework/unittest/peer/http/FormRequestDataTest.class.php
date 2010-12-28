<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.http.FormRequestData'
  );

  /**
   * TestCase
   *
   * @see       xp://peer.http.FormRequestData
   * @see       xp://peer.http.FormData
   * @purpose   Testcase
   */
  class FormRequestDataTest extends TestCase {
    protected
      $fixture  = NULL;

    /**
     * Setup test case
     *
     */
    public function setUp() {
      $this->fixture= new FormRequestData();
    }

    /**
     * Test adding new parameters
     *
     */
    #[@test]
    public function addPart() {
      $data= new FormData('key', 'value');
      $this->assertEquals($data, $this->fixture->addPart($data));
    }

    /**
     * Test adding new parameters
     *
     */
    #[@test]
    public function withPart() {
      $data= new FormData('key', 'value');
      $this->assertEquals($this->fixture, $this->fixture->withPart($data));
    }

    /**
     * Test representation of value w/ default content-type and charset
     *
     */
    #[@test]
    public function simpleMimeRepresentation() {
      $this->fixture->addPart(new FormData('key', 'value'));

      $this->assertEquals(
        "\r\n--".$this->fixture->getBoundary()."\r\n".
        "Content-Disposition: form-data; name=\"key\"\r\n\r\n".
        "value\r\n--".$this->fixture->getBoundary()."--\r\n",

        $this->fixture->getData()
      );
    }

    /**
     * Test representation of value w/ non-default mime-type
     *
     */
    #[@test]
    public function noDefaultTypeMimeRepresentation() {
      $this->fixture->addPart(new FormData('key', 'value', 'text/html'));

      $this->assertEquals(
        "\r\n--".$this->fixture->getBoundary()."\r\n".
        "Content-Disposition: form-data; name=\"key\"\r\n".
        "Content-Type: text/html\r\n\r\n".
        "value\r\n--".$this->fixture->getBoundary()."--\r\n",

        $this->fixture->getData()
      );
    }

    /**
     * Test representation of value w/ default content-type but
     * non-default charset
     *
     */
    #[@test]
    public function noDefaultCharsetMimeRepresentation() {
      $this->fixture->addPart(new FormData('key', 'value', 'text/plain', 'UTF-8'));

      $this->assertEquals(
        "\r\n--".$this->fixture->getBoundary()."\r\n".
        "Content-Disposition: form-data; name=\"key\"\r\n".
        "Content-Type: text/plain; charset=\"UTF-8\"\r\n\r\n".
        "value\r\n--".$this->fixture->getBoundary()."--\r\n",

        $this->fixture->getData()
      );
    }
  }
?>
