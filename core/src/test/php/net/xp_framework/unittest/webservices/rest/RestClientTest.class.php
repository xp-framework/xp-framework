<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestClient'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestClient
   */
  class RestClientTest extends TestCase {
    const BASE_URL = 'http://example.com';

    /**
     * Creates a new RestClient fixture with a given base
     *
     * @param   var base
     * @return  webservices.rest.RestClient
     */
    protected function newFixture($base= NULL) {
      return new RestClient($base);
    }
    
    /**
     * Test getBase()
     *
     */
    #[@test]
    public function stringBase() {
      $this->assertEquals(
        new URL(self::BASE_URL), 
        $this->newFixture(self::BASE_URL)->getBase()
      );
    }

    /**
     * Test getBase()
     *
     */
    #[@test]
    public function nullBase() {
      $this->assertNull($this->newFixture()->getBase());
    }

    /**
     * Test getBase()
     *
     */
    #[@test]
    public function urlBase() {
      $this->assertEquals(
        new URL(self::BASE_URL), 
        $this->newFixture(new URL(self::BASE_URL))->getBase()
      );
    }

    /**
     * Test setBase()
     *
     */
    #[@test]
    public function setBase() {
      $fixture= $this->newFixture();
      $fixture->setBase(self::BASE_URL);
      $this->assertEquals(new URL(self::BASE_URL), $fixture->getBase());
    }

    /**
     * Test withBase()
     *
     */
    #[@test]
    public function withBase() {
      $fixture= $this->newFixture();
      $this->assertEquals($fixture, $fixture->withBase(self::BASE_URL));
      $this->assertEquals(new URL(self::BASE_URL), $fixture->getBase());
    }

    /**
     * Test setConnection()
     *
     */
    #[@test]
    public function setConnection() {
      $fixture= $this->newFixture();
      $fixture->setConnection(new HttpConnection(self::BASE_URL));
      $this->assertEquals(new URL(self::BASE_URL), $fixture->getBase());
    }
  }
?>
