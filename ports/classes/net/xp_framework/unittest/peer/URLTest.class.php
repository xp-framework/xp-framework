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
     * Test setScheme()
     *
     */
    #[@test]
    public function schemeMutability() {
      $this->assertEquals(
        'ftp://localhost', 
        create(new URL('http://localhost'))->setScheme('ftp')->getURL()
      );
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
     * Test setHost()
     *
     */
    #[@test]
    public function hostMutability() {
      $this->assertEquals(
        'http://127.0.0.1', 
        create(new URL('http://localhost'))->setHost('127.0.0.1')->getURL()
      );
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
     * Test setPath()
     *
     */
    #[@test]
    public function pathMutability() {
      $this->assertEquals(
        'http://localhost/index.html', 
        create(new URL('http://localhost'))->setPath('/index.html')->getURL()
      );
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
     * Test getUser() method
     *
     */
    #[@test]
    public function urlEncodedUser() {
      $this->assertEquals('user?', create(new URL('http://user%3F@localhost'))->getUser());
    }

    /**
     * Test setUser()
     *
     */
    #[@test]
    public function userMutability() {
      $this->assertEquals(
        'http://thekid@localhost', 
        create(new URL('http://localhost'))->setUser('thekid')->getURL()
      );
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
    public function urlEncodedPassword() {
      $this->assertEquals('pass?word', create(new URL('http://user:pass%3Fword@localhost'))->getPassword());
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
     * Test setPassword()
     *
     */
    #[@test]
    public function passwordMutability() {
      $this->assertEquals(
        'http://anon:anon@localhost', 
        create(new URL('http://anon@localhost'))->setPassword('anon')->getURL()
      );
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
     * Test setQuery()
     *
     */
    #[@test]
    public function queryMutability() {
      $this->assertEquals(
        'http://localhost?a=b', 
        create(new URL('http://localhost'))->setQuery('a=b')->getURL()
      );
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
     * Test setFragment()
     *
     */
    #[@test]
    public function fragmentMutability() {
      $this->assertEquals(
        'http://localhost#list', 
        create(new URL('http://localhost'))->setFragment('list')->getURL()
      );
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
     * Test setPort()
     *
     */
    #[@test]
    public function portMutability() {
      $this->assertEquals(
        'http://localhost:8081', 
        create(new URL('http://localhost'))->setPort(8081)->getURL()
      );
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
     * Test addParam()
     *
     */
    #[@test]
    public function newParam() {
      $this->assertEquals(
        'http://localhost?a=b', 
        create(new URL('http://localhost'))->addParam('a', 'b')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function additionalParam() {
      $this->assertEquals(
        'http://localhost?a=b&c=d', 
        create(new URL('http://localhost?a=b'))->addParam('c', 'd')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function additionalParams() {
      $this->assertEquals(
        'http://localhost?a=b&c=d&e=f', 
        create(new URL('http://localhost?a=b'))->addParam('c', 'd')->addParam('e', 'f')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function existingParam() {
      $this->assertEquals(
        'http://localhost?a=b&a=b', 
        create(new URL('http://localhost?a=b'))->addParam('a', 'b')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function addNewParams() {
      $this->assertEquals(
        'http://localhost?a=b&c=d', 
        create(new URL('http://localhost'))->addParams(array('a' => 'b', 'c' => 'd'))->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function addAdditionalParams() {
      $this->assertEquals(
        'http://localhost?z=x&a=b&c=d', 
        create(new URL('http://localhost?z=x'))->addParams(array('a' => 'b', 'c' => 'd'))->getURL()
      );
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
