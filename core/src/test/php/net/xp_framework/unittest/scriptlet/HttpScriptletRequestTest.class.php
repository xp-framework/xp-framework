<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'peer.URL',
    'scriptlet.HttpScriptletRequest'
  );

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
     * Test
     *
     */
    #[@test]
    public function headersEmpty() {
      $r= $this->newRequest('GET', 'http://localhost/', array());
      $this->assertEquals(array(), $r->getHeaders());
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
  }
?>
