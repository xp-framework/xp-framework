<?php namespace net\xp_framework\unittest\scriptlet;


use unittest\TestCase;
use scriptlet\HttpScriptletURL;


/**
 * Test for HttpScriptletURL
 *
 * @see       xp://scriptlet.HttpScriptletURL
 * @purpose   Test HttpScriptletURL
 */
class HttpScriptletURLTest extends TestCase {

  /**
   * Test URL w/ empty path
   *
   */
  #[@test]
  public function urlWithEmptyPath() {
    $url= new HttpScriptletURL('http://xp-framework.net/');
    $this->assertEquals('http://xp-framework.net/', $url->getURL());
  }

  /**
   * Test URL w/ simple parameter
   *
   */
  #[@test]
  public function urlHasParameter() {
    $url= new HttpScriptletURL('http://xp-framework.net/home?key=value');
    $this->assertTrue($url->hasParam('key'));
  }

  /**
   * Test URL w/ session id does not have parameter "psessionid"
   *
   */
  #[@test]
  public function urlWithSessionHasNoPsessionParameter() {
    $url= new HttpScriptletURL('http://xp-framework.net/home?key=value&psessionid=foobar');
    $this->assertFalse($url->hasParam('psessionid'));

    $this->assertEquals('http://xp-framework.net/home?key=value&psessionid=foobar', $url->getURL());
  }

  /**
   * Test URL w/ session id does not have parameter "psessionid" but
   * does print it in string representation as such.
   *
   */
  #[@test]
  public function urlWithSessionRetainsId() {
    $url= new HttpScriptletURL('http://xp-framework.net/home?psessionid=foobar');

    $this->assertEquals('http://xp-framework.net/home?psessionid=foobar', $url->getURL());
    $this->assertFalse($url->hasParam('psessionid'));
  }

  /**
   * Test URL w/ session also retains fragment
   *
   */
  #[@test]
  public function urlWithSessionAndFragment() {
    $url= new HttpScriptletURL('http://xp-framework.net/home?psessionid=foobar#sect4');
    $this->assertEquals('http://xp-framework.net/home?psessionid=foobar#sect4', $url->getURL());
  }
}
