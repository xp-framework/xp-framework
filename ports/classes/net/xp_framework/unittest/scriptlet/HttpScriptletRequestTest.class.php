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
     * @param   array<string, string> headers
     * @return  scriptlet.HttpScriptletRequest
     */
    protected function newRequest($method, $url, array $headers) {
      $u= parse_url($url);
      isset($u['query']) ? parse_str($u['query'], $params) : $params= array();
    
      $r= new HttpScriptletRequest();
      $r->method= $method;
      $r->setURI(new URL($u['scheme'].'://'.$u['host'].'/'.$u['path']));
      $r->setParams(array_change_key_case($params, CASE_LOWER));
      $r->headers= array_change_key_case($headers, CASE_LOWER);
      
      return $r;
    }
  
    /**
     * Test hasParam() and getParam() methods
     *
     */
    #[@test]
    public function oneParam() {
      with ($r= $this->newRequest('GET', 'http://localhost/?a=b', array())); {
        $this->assertTrue($r->hasParam('a'));
        $this->assertEquals('b', $r->getParam('a'));
        $this->assertFalse($r->hasParam('c'));
        $this->assertNull($r->getParam('c'));
      }
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
    public function oneParamWithoutValue() {
      with ($r= $this->newRequest('GET', 'http://localhost/?a', array())); {
        $this->assertTrue($r->hasParam('a'));
        $this->assertEquals('', $r->getParam('a'));
        $this->assertFalse($r->hasParam('b'));
        $this->assertNull($r->getParam('b'));
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
     * Test hasParam() and getParam() methods
     *
     */
    #[@test]
    public function noParams() {
      with ($r= $this->newRequest('GET', 'http://localhost/', array())); {
        $this->assertFalse($r->hasParam('any'));
        $this->assertNull($r->getParam('any'));
      }
    }
  }
?>
