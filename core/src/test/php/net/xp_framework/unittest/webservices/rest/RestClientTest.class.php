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

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function singleArgumentExecuteNull() {
      $this->newFixture()->execute(NULL);
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function singleArgumentExecuteThis() {
      $this->newFixture()->execute($this);
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function executeNullTypeNullRequest() {
      $this->newFixture()->execute(NULL, NULL);
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function executeNullType() {
      $this->newFixture()->execute(NULL, new RestRequest());
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function executeNullRequest() {
      $this->newFixture()->execute(Type::$VAR, NULL);
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function executeThisRequest() {
      $this->newFixture()->execute(Type::$VAR, $this);
    }

    /**
     * Test execute()
     *
     */
    #[@test, @expect(class= 'lang.IllegalStateException', withMessage= 'No connection set')]
    public function executeWithoutBase() {
      $this->newFixture()->execute(Type::$VAR, new RestRequest());
    }

    /**
     * Test "text/xml" is supported
     *
     */
    #[@test]
    public function textXmlDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('text/xml')
      );
    }

    /**
     * Test "application/xml" is supported
     *
     */
    #[@test]
    public function applicationXmlDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('application/xml')
      );
    }

    /**
     * Test "text/json" is supported
     *
     */
    #[@test]
    public function textJsonDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('text/json')
      );
    }

    /**
     * Test "text/x-json" is supported
     *
     */
    #[@test]
    public function textXJsonDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('text/x-json')
      );
    }

    /**
     * Test "text/javascript" is supported
     *
     */
    #[@test]
    public function textJavascriptDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('text/javascript')
      );
    }

    /**
     * Test "application/json" is supported
     *
     */
    #[@test]
    public function applicationJsonDeserializer() {
      $this->assertInstanceOf(
        'webservices.rest.RestDeserializer',
        $this->newFixture()->deserializerFor('application/json')
      );
    }

    /**
     * Test "text/html" is not supported
     *
     */
    #[@test]
    public function unknownDeserializer() {
      $this->assertNull($this->newFixture()->deserializerFor('text/html'));
    }
  }
?>
