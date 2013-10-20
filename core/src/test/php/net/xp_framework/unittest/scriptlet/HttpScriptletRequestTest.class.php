<?php namespace net\xp_framework\unittest\scriptlet;

use unittest\TestCase;
use peer\URL;
use scriptlet\HttpScriptletRequest;


/**
 * TestCase
 *
 * @see      xp://scriptlet.HttpScriptletRequest
 * @purpose  Unittest
 */
class HttpScriptletRequestTest extends TestCase {

  /**
   * Creates a new request object
   *
   * @see     xp://scriptlet.HttpScriptlet#_setupRequest
   * @param   string method
   * @param   string url
   * @param   [:string] headers
   * @return  scriptlet.HttpScriptletRequest
   */
  protected function newRequest($method, $url, array $headers) {
    $u= parse_url($url);
    isset($u['query']) ? parse_str($u['query'], $params) : $params= array();
  
    $r= new HttpScriptletRequest();
    $r->method= $method;
    $r->setURI(new URL($u['scheme'].'://'.$u['host'].'/'.$u['path']));
    $r->setParams($params);
    $r->setHeaders($headers);
    
    return $r;
  }

  /**
   * Test hasParam()
   *
   */
  #[@test]
  public function doesNotHaveNonExistantParam() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertFalse($r->hasParam('a'));
  }

  /**
   * Test getParam()
   *
   */
  #[@test]
  public function getNonExistantParam() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertNull($r->getParam('a'));
  }

  /**
   * Test getParam()
   *
   */
  #[@test]
  public function getNonExistantParamDefault() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertEquals('default', $r->getParam('a', 'default'));
  }

  /**
   * Test hasParam()
   *
   */
  #[@test]
  public function hasOneParam() {
    $r= $this->newRequest('GET', 'http://localhost/?a=b', array());
    $this->assertTrue($r->hasParam('a'));
  }

  /**
   * Test getParam()
   *
   */
  #[@test]
  public function getOneParamLowerCaseParamMixedCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/?paramname=b', array());
    $this->assertEquals('b', $r->getParam('ParamName'));
  }

  /**
   * Test hasParam()
   *
   */
  #[@test]
  public function hasOneParamLowerCaseParamMixedCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/?paramname=b', array());
    $this->assertTrue($r->hasParam('ParamName'));
  }

  /**
   * Test getParam()
   *
   */
  #[@test]
  public function getOneParamMixedCaseParamLowerCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/?ParamName=b', array());
    $this->assertEquals('b', $r->getParam('paramname'));
  }

  /**
   * Test hasParam()
   *
   */
  #[@test]
  public function hasOneParamMixedCaseParamLowerCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/?ParamName=b', array());
    $this->assertTrue($r->hasParam('paramname'));
  }

  /**
   * Test getParam()
   *
   */
  #[@test]
  public function getOneParam() {
    $r= $this->newRequest('GET', 'http://localhost/?a=b', array());
    $this->assertEquals('b', $r->getParam('a'));
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function twoParams() {
    with ($r= $this->newRequest('GET', 'http://localhost/?a=b&c=d', array())); {
      $this->assertTrue($r->hasParam('a'));
      $this->assertEquals('b', $r->getParam('a'));
      $this->assertTrue($r->hasParam('c'));
      $this->assertEquals('d', $r->getParam('c'));
    }
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function oneParamWithArrayValue() {
    with ($r= $this->newRequest('GET', 'http://localhost/?a[]=1&a[]=2', array())); {
      $this->assertTrue($r->hasParam('a'));
      $this->assertEquals(array('1', '2'), $r->getParam('a'));
    }
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function oneParamWithHashValue() {
    with ($r= $this->newRequest('GET', 'http://localhost/?a[one]=1&a[two]=2', array())); {
      $this->assertTrue($r->hasParam('a'));
      $this->assertEquals(array('one' => '1', 'two' => '2'), $r->getParam('a'));
    }
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function oneParamWithoutValue() {
    with ($r= $this->newRequest('GET', 'http://localhost/?a', array())); {
      $this->assertTrue($r->hasParam('a'));
      $this->assertEquals('', $r->getParam('a'));
    }
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function twoParamsWithoutValue() {
    with ($r= $this->newRequest('GET', 'http://localhost/?a&b', array())); {
      $this->assertTrue($r->hasParam('a'));
      $this->assertEquals('', $r->getParam('a'));
      $this->assertTrue($r->hasParam('b'));
      $this->assertEquals('', $r->getParam('b'));
    }
  }

  /**
   * Test hasParam() and getParam() methods
   *
   */
  #[@test]
  public function dottedParam() {
    with ($r= $this->newRequest('GET', 'http://localhost/?login.SessionId=4711', array())); {
      $this->assertTrue($r->hasParam('login.SessionId'));
      $this->assertEquals('4711', $r->getParam('login.SessionId'));
    }
  }

  /**
   * Test hasParam() and setParam() methods
   *
   */
  #[@test]
  public function setParamAndHasParamRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->setParam('a', 'b');
    $this->assertTrue($r->hasParam('a'));
  }

  /**
   * Test getParam() and setParam() methods
   *
   */
  #[@test]
  public function setParamAndGetParamRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->setParam('a', 'b');
    $this->assertEquals('b', $r->getParam('a'));
  }

  /**
   * Test getParam() and setParam() methods
   *
   */
  #[@test]
  public function setParamAndgetParamRoundtripMixedCaseHeaderLowerCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->setParam('ParamName', 'b');
    $this->assertEquals('b', $r->getParam('paramname'));
  }

  /**
   * Test getParam() and setParam() methods
   *
   */
  #[@test]
  public function setParamAndgetParamRoundtripLowerCaseHeaderMixedCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->setParam('paramname', 'b');
    $this->assertEquals('b', $r->getParam('ParamName'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function paramsEmpty() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertEquals(array(), $r->getParams());
  }

  /**
   * Test getParams()
   *
   */
  #[@test]
  public function params() {
    $r= $this->newRequest('GET', 'http://localhost/?CustomerId=1&Sort=ASC', array());
    $this->assertEquals(array('CustomerId' => '1', 'Sort' => 'ASC'), $r->getParams());
  }

  /**
   * Test getParams()
   *
   */
  #[@test]
  public function paramsLowerCase() {
    $r= $this->newRequest('GET', 'http://localhost/?CustomerId=1&Sort=ASC', array());
    $this->assertEquals(array('customerid' => '1', 'sort' => 'ASC'), $r->getParams(CASE_LOWER));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function headersEmpty() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertEquals(array(), $r->getHeaders());
  }

  /**
   * Test getHeaders()
   *
   */
  #[@test]
  public function headers() {
    $headers= array('Referer' => 'http://example.com/', 'User-Agent' => 'XP');
    $r= $this->newRequest('GET', 'http://localhost/', $headers);
    $this->assertEquals($headers, $r->getHeaders());
  }

  /**
   * Test getHeader()
   *
   */
  #[@test]
  public function getHeader() {
    $r= $this->newRequest('GET', 'http://localhost/', array(
      'Referer' => 'http://example.com/'
    ));
    $this->assertEquals('http://example.com/', $r->getHeader('Referer'));
  }

  /**
   * Test getHeader()
   *
   */
  #[@test]
  public function getNonExistantHeader() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertNull($r->getHeader('User-Agent'));
  }

  /**
   * Test getHeader()
   *
   */
  #[@test]
  public function getNonExistantHeaderDefault() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertEquals('default', $r->getHeader('User-Agent', 'default'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function headerLookupCaseInsensitive() {
    $r= $this->newRequest('GET', 'http://localhost/', array(
      'UPPERCASE' => 1,
    ));

    $this->assertEquals(1, $r->getHeader('uppercase'));
    $this->assertEquals(1, $r->getHeader('UpPeRCaSe'));
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderAndGetHeadersRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('a', 'b');
    $this->assertEquals(array('a' => 'b'), $r->getHeaders());
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderOverwritingAndGetHeadersRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('a', 'b');
    $r->addHeader('A', 'c');
    $this->assertEquals(array('a' => 'c'), $r->getHeaders());
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderAndGetHeaderRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('a', 'b');
    $this->assertEquals('b', $r->getHeader('a'));
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderOverwritingAndGetHeaderRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('a', 'b');
    $r->addHeader('a', 'c');
    $this->assertEquals('c', $r->getHeader('a'));
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderOverwritingCaseInsensitiveAndGetHeaderRoundtrip() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('a', 'b');
    $r->addHeader('A', 'c');
    $this->assertEquals('c', $r->getHeader('a'));
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderAndGetHeaderRoundtripMixedCaseHeaderLowerCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('HeaderName', 'b');
    $this->assertEquals('b', $r->getHeader('headername'));
  }

  /**
   * Test getHeader() and addHeader() methods
   *
   */
  #[@test]
  public function addHeaderAndGetHeaderRoundtripLowerCaseHeaderMixedCaseQuery() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $r->addHeader('headername', 'b');
    $this->assertEquals('b', $r->getHeader('HeaderName'));
  }

  /**
   * Test initially cookies are empty
   *
   */
  #[@test]
  public function cookiesInitiallyEmpty() {
    $r= $this->newRequest('GET', 'http://localhost/', array());
    $this->assertEquals(array(), $r->getCookies());
  }

  /**
   * Test adding cookies works
   *
   */
  #[@test]
  public function addCookie() {
    $r= $this->newRequest('GET', 'http://localhost/', array());

    $this->assertInstanceOf('scriptlet.Cookie', $r->addCookie(new \scriptlet\Cookie('cookie', 'value')));
  }

  /**
   * Test hasCookie finds added cookie
   *
   */
  #[@test]
  public function hasCookieDetectsAddedCookie() {
    $r= $this->newRequest('GET', 'http://localhost/', array());

    $r->addCookie(new \scriptlet\Cookie('cookie', 'value'));
    $this->assertTrue($r->hasCookie('cookie'));
  }

  /**
   * Test
   *
   */
  #[@test]
  public function getCookieReturnsCookie() {
    $r= $this->newRequest('GET', 'http://localhost/', array());

    $r->addCookie(new \scriptlet\Cookie('cookie', 'value'));
    $this->assertEquals('value', $r->getCookie('cookie')->getValue());
  }

  /**
   * Test
   *
   */
  #[@test]
  public function methodName() {
    $_COOKIE['name']= 'value';
    $r= $this->newRequest('GET', 'http://localhost/', array());

    $this->assertEquals('value', $r->getCookie('name')->getValue());
  }
}
