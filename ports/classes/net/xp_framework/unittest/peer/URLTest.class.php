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
     * Test setUser() method
     *
     */
    #[@test]
    public function setUrlEncodedUser() {
      $this->assertEquals('http://user%3F@localhost', create(new URL('http://localhost'))->setUser('user?')->getURL());
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
     * Test setPassword() method
     *
     */
    #[@test]
    public function setUrlEncodedPassword() {
      $this->assertEquals('http://user:pass%3Fword@localhost', create(new URL('http://user@localhost'))->setPassword('pass?word')->getURL());
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
    public function queryModifiedByParams() {
      $this->assertEquals(
        'a=b&c=d', 
        create(new URL('http://localhost?a=b'))->addParam('c', 'd')->getQuery()
      );
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
    public function parameterLessQuery() {
      $this->assertEquals('1549', create(new URL('http://localhost/?1549'))->getQuery());
    }

    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function addToParameterLessQuery() {
      $this->assertEquals('1549&a=b', create(new URL('http://localhost/?1549'))->addParam('a', 'b')->getQuery());
    }

    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function createParameterLessQueryWithAdd() {
      $this->assertEquals('1549', create(new URL('http://localhost/'))->addParam('1549')->getQuery());
    }

    /**
     * Test getQuery() method
     *
     */
    #[@test]
    public function createParameterLessQueryWithSet() {
      $this->assertEquals('1549', create(new URL('http://localhost/'))->setParam('1549')->getQuery());
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
     * Test getParam() method
     *
     */
    #[@test]
    public function getParameterLessQuery() {
      $this->assertEquals('', create(new URL('http://localhost/?1549'))->getParam('1549'));
    }

    /**
     * Test hasParam() method
     *
     */
    #[@test]
    public function hasParameterLessQuery() {
      $this->assertTrue(create(new URL('http://localhost/?1549'))->hasParam('1549'));
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
     * Test getParam() method with an array parameter
     *
     */
    #[@test]
    public function getArrayParameter() {
      $this->assertEquals(array('b'), create(new URL('http://localhost?a[]=b'))->getParam('a'));
    }

    /**
     * Test getParam() method with an array parameter
     *
     */
    #[@test]
    public function getEncodedArrayParameter() {
      $this->assertEquals(array('='), create(new URL('http://localhost?a[]=%3D'))->getParam('a'));
    }

    /**
     * Test getParam() method with array parameters
     *
     */
    #[@test]
    public function getArrayParameters() {
      $this->assertEquals(array('b', 'c'), create(new URL('http://localhost?a[]=b&a[]=c'))->getParam('a'));
    }

    /**
     * Test getParam() method with array parameters
     *
     */
    #[@test]
    public function getArrayParametersAsHash() {
      $this->assertEquals(
        array('name' => 'b', 'color' => 'c'), 
        create(new URL('http://localhost?a[name]=b&a[color]=c'))->getParam('a')
      );
    }

    /**
     * Test getParam() method with array parameters
     *
     */
    #[@test]
    public function getArrayParametersAsHashWithEncodedNames() {
      $this->assertEquals(
        array('=name=' => 'b', '=color=' => 'c'), 
        create(new URL('http://localhost?a[%3Dname%3D]=b&a[%3Dcolor%3D]=c'))->getParam('a')
      );
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test]
    public function arrayOffsetsInDifferentArrays() {
      $this->assertEquals(
        array('a' => array('c'), 'b' => array('d')), 
        create(new URL('http://localhost/?a[]=c&b[]=d'))->getParams()
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function duplicateOffsetsOverwriteEachother() {
      $this->assertEquals(
        array('c'), 
        create(new URL('http://localhost/?a[0]=b&a[0]=c'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function duplicateNamesOverwriteEachother() {
      $this->assertEquals(
        array('name' => 'c'), 
        create(new URL('http://localhost/?a[name]=b&a[name]=c'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function twoDimensionalArray() {
      $this->assertEquals(
        array(array('b')), 
        create(new URL('http://localhost/?a[][]=b'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function threeDimensionalArray() {
      $this->assertEquals(
        array(array(array('b'))), 
        create(new URL('http://localhost/?a[][][]=b'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function arrayOfHash() {
      $this->assertEquals(
        array(array(array('name' => 'b'))), 
        create(new URL('http://localhost/?a[][][name]=b'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function hashOfArray() {
      $this->assertEquals(
        array('name' => array(array('b'))), 
        create(new URL('http://localhost/?a[name][][]=b'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function hashOfArrayOfHash() {
      $this->assertEquals(
        array('name' => array(array('key' => 'b'))), 
        create(new URL('http://localhost/?a[name][][key]=b'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function hashNotationWithoutValues() {
      $this->assertEquals(
        array('name' => '', 'color' => ''), 
        create(new URL('http://localhost/?a[name]&a[color]'))->getParam('a')
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function arrayNotationWithoutValues() {
      $this->assertEquals(
        array('', ''), 
        create(new URL('http://localhost/?a[]&a[]'))->getParam('a')
      );
    }

    /**
     * Test getParams() method with array parameters
     *
     */
    #[@test]
    public function getArrayParams() {
      $this->assertEquals(
        array('a' => array('b', 'c')), 
        create(new URL('http://localhost?a[]=b&a[]=c'))->getParams()
      );
    }

    /**
     * Test getParam() with array parameters
     *
     */
    #[@test]
    public function mixedOffsetsAndKeys() {
      $this->assertEquals(
        array(0 => 'b', 'name' => 'c', 1 => 'd'), 
        create(new URL('http://localhost/?a[]=b&a[name]=c&a[]=d'))->getParam('a')
      );
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test]
    public function nestedBraces() {
      $this->assertEquals(
        array('a' => array('nested[]' => 'b')), 
        create(new URL('http://localhost/?a[nested[]]=b'))->getParams()
      );
    }
 
    /**
     * Test getParams() with array parameters
     *
     */
    #[@test]
    public function nestedBracesTwice() {
      $this->assertEquals(
        array('a' => array('nested[a]' => 'b', 'nested[b]' => 'c')), 
        create(new URL('http://localhost/?a[nested[a]]=b&a[nested[b]]=c'))->getParams()
      );
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test]
    public function nestedBracesChained() {
      $this->assertEquals(
        array('a' => array('nested[a]' => array('c'))), 
        create(new URL('http://localhost/?a[nested[a]][]=c'))->getParams()
      );
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test]
    public function unnamedArrayParameterDoesNotCreateArray() {
      $this->assertEquals(
        array('[]' => 'c'), 
        create(new URL('http://localhost/?[]=c'))->getParams()
      );
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
    public function addNewParam() {
      $this->assertEquals(
        'http://localhost?a=b', 
        create(new URL('http://localhost'))->addParam('a', 'b')->getURL()
      );
    }

    /**
     * Test setParam()
     *
     */
    #[@test]
    public function setNewParam() {
      $this->assertEquals(
        'http://localhost?a=b', 
        create(new URL('http://localhost'))->setParam('a', 'b')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function addAdditionalParam() {
      $this->assertEquals(
        'http://localhost?a=b&c=d', 
        create(new URL('http://localhost?a=b'))->addParam('c', 'd')->getURL()
      );
    }

    /**
     * Test setParam()
     *
     */
    #[@test]
    public function setAdditionalParam() {
      $this->assertEquals(
        'http://localhost?a=b&c=d', 
        create(new URL('http://localhost?a=b'))->setParam('c', 'd')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function addAdditionalParamChained() {
      $this->assertEquals(
        'http://localhost?a=b&c=d&e=f', 
        create(new URL('http://localhost?a=b'))->addParam('c', 'd')->addParam('e', 'f')->getURL()
      );
    }

    /**
     * Test setParam()
     *
     */
    #[@test]
    public function setAdditionalParamChained() {
      $this->assertEquals(
        'http://localhost?a=b&c=d&e=f', 
        create(new URL('http://localhost?a=b'))->setParam('c', 'd')->setParam('e', 'f')->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addExistingParam() {
      create(new URL('http://localhost?a=b'))->addParam('a', 'b');
    }

    /**
     * Test setParam()
     *
     */
    #[@test]
    public function setExistingParam() {
      $this->assertEquals(
        'http://localhost?a=c', 
        create(new URL('http://localhost?a=b'))->setParam('a', 'c')->getURL()
      );
    }

    /**
     * Test addParams()
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addExistingParams() {
      create(new URL('http://localhost?a=b'))->addParams(array('a' => 'b'));
    }

    /**
     * Test addParams()
     *
     */
    #[@test]
    public function addExistingParamsDoesNotPartiallyModify() {
      $original= 'http://localhost?a=b';
      $u= new URL($original);
      try {
        $u->addParams(array('c' => 'd', 'a' => 'b'));
        $this->fail('Existing parameter "a" not detected', NULL, 'lang.IllegalArgumentException');
      } catch (IllegalArgumentException $expected) { }
      $this->assertEquals($original, $u->getURL());
    }

    /**
     * Test setParams()
     *
     */
    #[@test]
    public function setExistingParams() {
      $this->assertEquals(
        'http://localhost?a=c', 
        create(new URL('http://localhost?a=b'))->setParams(array('a' => 'c'))->getURL()
      );
    }

    /**
     * Test addParams()
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
     * Test setParams()
     *
     */
    #[@test]
    public function setNewParams() {
      $this->assertEquals(
        'http://localhost?a=b&c=d', 
        create(new URL('http://localhost'))->setParams(array('a' => 'b', 'c' => 'd'))->getURL()
      );
    }

    /**
     * Test addParams()
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
     * Test setParams()
     *
     */
    #[@test]
    public function setAdditionalParams() {
      $this->assertEquals(
        'http://localhost?z=x&a=b&c=d', 
        create(new URL('http://localhost?z=x'))->setParams(array('a' => 'b', 'c' => 'd'))->getURL()
      );
    }

    /**
     * Test addParam()
     *
     */
    #[@test]
    public function addArrayParam() {
      $u= new URL('http://localhost/');
      $u->addParam('x', array('y', 'z'));
      $this->assertEquals('http://localhost/?x[]=y&x[]=z', $u->getURL());
    }

    /**
     * Test setParam()
     *
     */
    #[@test]
    public function setArrayParam() {
      $u= new URL('http://localhost/');
      $u->setParam('x', array('y', 'z'));
      $this->assertEquals('http://localhost/?x[]=y&x[]=z', $u->getURL());
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
    public function withArrayParams() {
      $this->assertTrue(create(new URL('http://localhost?a[]=b&a[]=d'))->hasParams());
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
     * Test hasParam() method
     *
     */
    #[@test]
    public function withParam() {
      $this->assertTrue(create(new URL('http://localhost?a=b&c=d'))->hasParam('a'));
    }

    /**
     * Test hasParam() method
     *
     */
    #[@test]
    public function withArrayParam() {
      $this->assertTrue(create(new URL('http://localhost?a[]=b&a[]=d'))->hasParam('a'));
    }

    /**
     * Test hasParam() method
     *
     */
    #[@test]
    public function withNonExistantParam() {
      $this->assertFalse(create(new URL('http://localhost?a=b&c=d'))->hasParam('d'));
    }

    /**
     * Test hasParam() method
     *
     */
    #[@test]
    public function noParam() {
      $this->assertFalse(create(new URL('http://localhost'))->hasParam('a'));
    }

    /**
     * Test hasParam() method
     *
     */
    #[@test]
    public function hasDotParam() {
      $this->assertTrue(create(new URL('http://localhost/?a.b=c'))->hasParam('a.b'));
    }

    /**
     * Test getParam() method
     *
     */
    #[@test]
    public function getDotParam() {
      $this->assertEquals('c', create(new URL('http://localhost/?a.b=c'))->getParam('a.b'));
    }

    /**
     * Test getParams() method
     *
     */
    #[@test]
    public function getDotParams() {
      $this->assertEquals(array('a.b' => 'c'), create(new URL('http://localhost/?a.b=c'))->getParams());
    }

    /**
     * Test addParam() method
     *
     */
    #[@test]
    public function addDotParam() {
      $this->assertEquals('a.b=c', create(new URL('http://localhost/'))->addParam('a.b', 'c')->getQuery());
    }

    /**
     * Test removeParam() method
     *
     */
    #[@test]
    public function removeExistingParam() {
      $this->assertEquals(new URL('http://localhost'), create(new URL('http://localhost?a=b'))->removeParam('a'));
    }

    /**
     * Test removeParam() method
     *
     */
    #[@test]
    public function removeNonExistantParam() {
      $this->assertEquals(new URL('http://localhost'), create(new URL('http://localhost'))->removeParam('a'));
    }

    /**
     * Test removeParam() method
     *
     */
    #[@test]
    public function removeExistingArrayParam() {
      $this->assertEquals(new URL('http://localhost'), create(new URL('http://localhost?a[]=b&a[]=c'))->removeParam('a'));
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

    /**
     * Test hashCode() method
     *
     */
    #[@test]
    public function hashCodeRecalculated() {
      $u= new URL('http://localhost');
      $u->addParam('a', 'b');
      
      $this->assertNotEquals(
        create(new URL('http://localhost'))->hashCode(), 
        $u->hashCode()
      );
    }

    /**
     * Test URL parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function schemeOnlyUnparseable() {
      new URL('http://');
    }

    /**
     * Test URL parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function schemeSeparatorOnlyUnparseable() {
      new URL('://');
    }

    /**
     * Test URL parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingSchemeUnparseable() {
      new URL(':///path/to/file');
    }

    /**
     * Test URL parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyUnparseable() {
      new URL('');
    }

    /**
     * Test URL parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function withoutSchemeUnparseable() {
      new URL('/path/to/file');
    }

    /**
     * Test malformed query string parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingClosingBracket() {
      new URL('http://example.com/?a[=c');
    }

    /**
     * Test malformed query string parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingOpeningBracket() {
      new URL('http://example.com/?a]=c');
    }

    /**
     * Test malformed query string parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unbalancedOpeningBrackets() {
      new URL('http://example.com/?a[[[]]=c');
    }

    /**
     * Test malformed query string parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unbalancedClosingBrackets() {
      new URL('http://example.com/?a[[]]]=c');
    }

    /**
     * Test malformed query string parsing
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingClosingBracketAfterClosed() {
      new URL('http://example.com/?a[][=c');
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingClosingBracketInNested() {
      new URL('http://localhost/?a[nested[a]=c');
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingClosingBracketInNestedAfterClosed() {
      new URL('http://localhost/?a[][nested[a]=c');
    }

    /**
     * Test getParams() with array parameters
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function missingClosingBracketInNestedBeforeClosed() {
      new URL('http://localhost/?a[nested[a][]=c');
    }
    
    /**
     * Test associative arrays in url parameters, e.g. data[key]=value gets
     * reported correctly by getParam()
     *
     */
    #[@test]
    public function parseEncodedAssociativeArray() {
      $u= new URL('http://example.com/ajax?load=getXML&data%5BprojectName%5D=project&data%5BlangCode%5D=en');
      $this->assertEquals(
        array('projectName' => 'project', 'langCode' => 'en'),
        $u->getParam('data')
      );
    }

    /**
     * Test associative arrays in url parameters, e.g. data[key]=value gets
     * reported correctly by getParam()
     *
     */
    #[@test]
    public function parseUnencodedAssociativeArray() {
      $u= new URL('http://example.com/ajax?load=getXML&data[projectName]=project&data[langCode]=en');
      $this->assertEquals(
        array('projectName' => 'project', 'langCode' => 'en'),
        $u->getParam('data')
      );
    }

    /**
     * Test addParam() method handles associative arrays in url parameters
     * correctly.
     *
     */
    #[@test]
    public function addParamAssociativeAray() {
      $u= new URL('http://example.com/ajax?load=getXML');
      $u->addParam('data', array('projectName' => 'project', 'langCode' => 'en'));
      $this->assertEquals(
        'load=getXML&data[projectName]=project&data[langCode]=en',
        $u->getQuery()
      );
    }

    /**
     * Test addParams() method handles associative arrays in url parameters
     * correctly.
     *
     */
    #[@test]
    public function addParamsAssociativeAray() {
      $u= new URL('http://example.com/ajax?load=getXML');
      $u->addParams(array('data' => array('projectName' => 'project', 'langCode' => 'en')));
      $this->assertEquals(
        'load=getXML&data[projectName]=project&data[langCode]=en',
        $u->getQuery()
      );
    }

    /**
     * Test getQuery() method handles associative arrays in url parameters
     * correctly.
     *
     */
    #[@test]
    public function associativeArrayQueryCalculation() {
      $u= new URL('http://example.com/ajax?load=getXML&data%5BprojectName%5D=project&data%5BlangCode%5D=en');
      $this->assertEquals(
        'load=getXML&data[projectName]=project&data[langCode]=en',
        $u->getQuery()
      );
    }
    
    /**
     * Test getQuery() method handles two-dimensional associative arrays in
     * url parameters correctly.
     *
     */
    #[@test]
    public function associativeArrayTwoDimensionalQueryCalculation() {
      $u= new URL('http://example.com/ajax?load=getXML&data%5Bproject%5D%5BName%5D=project&data%5Bproject%5D%5BID%5D=1337&data%5BlangCode%5D=en');
      $this->assertEquals(
        'load=getXML&data[project][Name]=project&data[project][ID]=1337&data[langCode]=en',
        $u->getQuery()
      );
    }
    
    /**
     * Test getQuery() method handles more-dimensional associative arrays in
     * url parameters correctly.
     *
     */
    #[@test]
    public function associativeArrayMoreDimensionalQueryCalculation() {
      $u= new URL('http://example.com/ajax?load=getXML&data%5Bproject%5D%5BName%5D%5BValue%5D=project&data%5Bproject%5D%5BID%5D%5BValue%5D=1337&data%5BlangCode%5D=en');
      $this->assertEquals(
        'load=getXML&data[project][Name][Value]=project&data[project][ID][Value]=1337&data[langCode]=en',
        $u->getQuery()
      );
    }

    /**
     * Test getURL() with an empty parameter in query string
     *
     */
    #[@test]
    public function getURLWithEmptyParameter() {
      $this->assertEquals('http://example.com/test?a=v1&b&c=v2', create(new URL('http://example.com/test?a=v1&b=&c=v2'))->getURL());
    }

    /**
     * Test getURL() with an empty parameter in query string
     *
     */
    #[@test]
    public function getURLWithParameterWithoutValue() {
      $this->assertEquals('http://example.com/test?a=v1&b&c=v2', create(new URL('http://example.com/test?a=v1&b&c=v2'))->getURL());
    }

    /**
     * Test getURL() after setQuery('')
     *
     */
    #[@test]
    public function getURLAfterSettingEmptyQueryString() {
      $this->assertEquals('http://example.com/test', create(new URL('http://example.com/test'))->setQuery('')->getURL());
    }

    /**
     * Test getURL() after setQuery(NULL)
     *
     */
    #[@test]
    public function getURLAfterSettingNullQueryString() {
      $this->assertEquals('http://example.com/test', create(new URL('http://example.com/test'))->setQuery(NULL)->getURL());
    }

    /**
     * Test getURL() with empty query string in constructor
     *
     */
    #[@test]
    public function getURLWithEmptyQueryStringConstructor() {
      $this->assertEquals('http://example.com/test', create(new URL('http://example.com/test?'))->getURL());
    }
  }
?>
