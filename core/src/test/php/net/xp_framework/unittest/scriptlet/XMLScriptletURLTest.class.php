<?php namespace net\xp_framework\unittest\scriptlet;


use unittest\TestCase;
use scriptlet\xml\XMLScriptletURL;


/**
 * TestCase for XMLScriptletURL
 *
 * @see       xp://scriptlet.xml.XMLScriptletURL
 * @purpose   TestCase
 */
class XMLScriptletURLTest extends TestCase {

  /**
   * Test URL w/ empty path
   *
   */
  #[@test]
  public function emptyPath() {
    $url= new XMLScriptletURL('http://xp-framework.net/');
    $this->assertEquals('http://xp-framework.net/xml/', $url->getURL());
  }

  /**
   * Test URL has product
   *
   */
  #[@test]
  public function xmlUrlHasProduct() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/xp.de_DE/home');
    $this->assertEquals('xp', $url->getProduct());
  }

  /**
   * Test URL has language
   *
   */
  #[@test]
  public function xmlUrlHasLanguage() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/xp.de_DE/home');
    $this->assertEquals('de_DE', $url->getLanguage());
  }

  /**
   * Test URL has state
   *
   */
  #[@test]
  public function xmlUrlHasState() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/xp.de_DE/home');
    $this->assertEquals('home', $url->getStateName());
  }

  /**
   * Test path element treated as url & product or plain path
   * if only one part is given
   *
   */
  #[@test]
  public function urlEitherHasProductOrLanguageOrNothingAtAll() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/xp/home');
    $this->assertEquals(null, $url->getProduct());
    $this->assertEquals(null, $url->getLanguage());
    $this->assertEquals('xp/home', $url->getStateName());
  }

  /**
   * Test session id
   *
   */
  #[@test]
  public function urlHasPsessionId() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/psessionid=12345/home');
    $this->assertEquals('12345', $url->getSessionId());
  }

  /**
   * Test __page parsing
   *
   */
  #[@test]
  public function urlHasPage() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/psessionid=12345/home?__page=print');
    $this->assertEquals('print', $url->getPage());
  }

  /**
   * Test full URL is conserved
   *
   */
  #[@test]
  public function getURL() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/xp.de_DE.psessionid=foo123456bar/home?key=value');

    $this->assertEquals(
      'http://xp-framework.net/xml/xp.de_DE.psessionid=foo123456bar/home?key=value',
      $url->getURL()
    );
  }

  /**
   * Test port is conserved
   *
   */
  #[@test]
  public function getUrlContainsPort() {
    $url= new XMLScriptletURL('http://xp-framework.net:8080/xml/home');
    $this->assertEquals('http://xp-framework.net:8080/xml/home', $url->getURL());
  }

  /**
   * Test port 80 is stripped for http
   *
   */
  #[@test]
  public function getUrlDoesNotContainDefaultPort() {
    $url= new XMLScriptletURL('http://xp-framework.net:80/xml/home');
    $this->assertEquals('http://xp-framework.net/xml/home', $url->getURL());
  }

  /**
   * Test port 443 is stripped for https
   *
   */
  #[@test]
  public function getUrlDoesNotContainDefaultPortForHttps() {
    $url= new XMLScriptletURL('https://xp-framework.net:443/xml/home');
    $this->assertEquals('https://xp-framework.net/xml/home', $url->getURL());
  }

  /**
   * Test default value stripping
   *
   */
  #[@test]
  public function getURLStripsDefaultValuesButNotState() {
    $url= new XMLScriptletURL('http://xp-framework.net/xml/a.en_US/home');

    $url->setDefaultProduct('a');
    $url->setDefaultLanguage('en_US');
    $url->setDefaultStateName('home');

    $this->assertEquals('http://xp-framework.net/xml/home', $url->getURL());
  }
}
