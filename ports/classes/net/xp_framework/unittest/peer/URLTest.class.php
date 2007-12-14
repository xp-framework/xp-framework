<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'peer.URL');

  /**
   * TestCase
   *
   * @see      xp://peer.URL
   * @purpose  Unittest
   */
  class URLTest extends TestCase {
  
    /**
     * Test getScheme() method
     *
     */
    #[@test]
    public function scheme() {
      $this->assertEquals('http', create(new URL('http://localhost'))->getScheme());
    }

    /**
     * Test getHost() method
     *
     */
    #[@test]
    public function host() {
      $this->assertEquals('localhost', create(new URL('http://localhost'))->getHost());
    }

    /**
     * Test getPath() method
     *
     */
    #[@test]
    public function path() {
      $this->assertEquals('/news/index.html', create(new URL('http://localhost/news/index.html'))->getPath());
    }

    /**
     * Test getPath() method
     *
     */
    #[@test]
    public function emptyPath() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getPath());
    }

    /**
     * Test getPath() method
     *
     */
    #[@test]
    public function slashPath() {
      $this->assertEquals('/', create(new URL('http://localhost/'))->getPath());
    }

    /**
     * Test getPath() method when invoked with a default value
     *
     */
    #[@test]
    public function pathDefault() {
      $this->assertEquals('/', create(new URL('http://localhost'))->getPath('/'));
    }

    /**
     * Test getUser() method
     *
     */
    #[@test]
    public function user() {
      $this->assertEquals('user', create(new URL('http://user@localhost'))->getUser());
    }

    /**
     * Test getUser() method
     *
     */
    #[@test]
    public function emptyUser() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getUser());
    }

    /**
     * Test getUser() method when invoked with a default value
     *
     */
    #[@test]
    public function userDefault() {
      $this->assertEquals('nobody', create(new URL('http://localhost'))->getUser('nobody'));
    }

    /**
     * Test getPassword() method
     *
     */
    #[@test]
    public function password() {
      $this->assertEquals('password', create(new URL('http://user:password@localhost'))->getPassword());
    }

    /**
     * Test getPassword() method
     *
     */
    #[@test]
    public function emptyPassword() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getPassword());
    }

    /**
     * Test getPassword() method when invoked with a default value
     *
     */
    #[@test]
    public function passwordDefault() {
      $this->assertEquals('secret', create(new URL('http://user@localhost'))->getPassword('secret'));
    }


    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function query() {
      $this->assertEquals('a=b', create(new URL('http://localhost?a=b'))->getQuery());
    }

    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function emptyQuery() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getQuery());
    }

    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function questionMarkOnly() {
      $this->assertEquals(NULL, create(new URL('http://localhost?'))->getQuery());
    }

    /**
     * Test getQuery() method when invoked with a default value
     *
     */
    #[@test]
    public function queryDefault() {
      $this->assertEquals('1,2,3', create(new URL('http://localhost'))->getQuery('1,2,3'));
    }

    /**
     * Test getFragment() method
     *
     */
    #[@test]
    public function fragment() {
      $this->assertEquals('top', create(new URL('http://localhost#top'))->getFragment());
    }

    /**
     * Test getFragment() method
     *
     */
    #[@test]
    public function emptyFragment() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getFragment());
    }

    /**
     * Test getFragment() method
     *
     */
    #[@test]
    public function hashOnly() {
      $this->assertEquals(NULL, create(new URL('http://localhost#'))->getFragment());
    }

    /**
     * Test getFragment() method when invoked with a default value
     *
     */
    #[@test]
    public function fragmentDefault() {
      $this->assertEquals('top', create(new URL('http://localhost'))->getFragment('top'));
    }

    /**
     * Test getPort() method
     *
     */
    #[@test]
    public function port() {
      $this->assertEquals(8080, create(new URL('http://localhost:8080'))->getPort());
    }

    /**
     * Test getPort() method
     *
     */
    #[@test]
    public function emptyPort() {
      $this->assertEquals(NULL, create(new URL('http://localhost'))->getPort());
    }

    /**
     * Test getPort() method when invoked with a default value
     *
     */
    #[@test]
    public function portDefault() {
      $this->assertEquals(80, create(new URL('http://localhost'))->getPort(80));
    }

    /**
     * Test getParam() method
     *
     */
    #[@test]
    public function param() {
      $this->assertEquals('b', create(new URL('http://localhost?a=b'))->getParam('a'));
    }
 
    /**
     * Test getParam() method
     *
     */
    #[@test]
    public function nonExistantParam() {
      $this->assertEquals(NULL, create(new URL('http://localhost?a=b'))->getParam('b'));
    }

    /**
     * Test getParam() method
     *
     */
    #[@test]
    public function emptyParam() {
      $this->assertEquals('', create(new URL('http://localhost?x='))->getParam('x'));
    }

    /**
     * Test getParam() method when invoked with a default value
     *
     */
    #[@test]
    public function paramDefault() {
      $this->assertEquals('x', create(new URL('http://localhost?a=b'))->getParam('c', 'x'));
    }
 
    /**
     * Test getParams() method
     *
     */
    #[@test]
    public function params() {
      $this->assertEquals(array('a' => 'b', 'c' => 'd'), create(new URL('http://localhost?a=b&c=d'))->getParams());
    }

    /**
     * Test hasParams() method
     *
     */
    #[@test]
    public function withParams() {
      $this->assertTrue(create(new URL('http://localhost?a=b&c=d'))->hasParams());
    }

    /**
     * Test hasParams() method
     *
     */
    #[@test]
    public function noParams() {
      $this->assertFalse(create(new URL('http://localhost'))->hasParams());
    }

    /**
     * Test equals() method
     *
     */
    #[@test]
    public function sameUrlsAreEqual() {
      $this->assertEquals(new URL('http://localhost'), new URL('http://localhost'));
    }

    /**
     * Test equals() method
     *
     */
    #[@test]
    public function differentUrlsAreNotEqual() {
      $this->assertNotEquals(new URL('http://localhost'), new URL('http://example.com'));
    }

    /**
     * Test hashCode() method
     *
     */
    #[@test]
    public function hashCodesForSameUrls() {
      $this->assertEquals(
        create(new URL('http://localhost'))->hashCode(), 
        create(new URL('http://localhost'))->hashCode()
      );
    }

    /**
     * Test hashCode() method
     *
     */
    #[@test]
    public function hashCodesForDifferentUrls() {
      $this->assertNotEquals(
        create(new URL('http://localhost'))->hashCode(), 
        create(new URL('ftp://localhost'))->hashCode()
      );
    }
  }
?>
