<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.Cookie',
    'util.Date'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.Cookie
   */
  class CookieTest extends TestCase {
  
    /**
     * Asserts a cookie's header value
     *
     * @param   string expected
     * @param   scriptlet.Cookie cookie
     * @throws  unittest.AssertionFailedError  
     */
    protected function assertHeaderEquals($expected, $cookie) {
      $this->assertEquals($expected, $cookie->getHeaderValue());
    }
  
    /**
     * Test most simple form of a cookie, name=value
     *
     */
    #[@test]
    public function nameAndValue() {
      $this->assertHeaderEquals(
        'name=value', 
        new Cookie('name', 'value')
      );
    }

    /**
     * Test expiry date
     *
     */
    #[@test]
    public function withExpiry() {
      $this->assertHeaderEquals(
        'name=value; expires=Wed, 14-Dec-2011 11:55:00 GMT', 
        new Cookie('name', 'value', 1323863700)
      );
    }

    /**
     * Test expiry date
     *
     */
    #[@test]
    public function withExpiryDate() {
      $this->assertHeaderEquals(
        'name=value; expires=Wed, 14-Dec-2011 11:55:00 GMT', 
        new Cookie('name', 'value', new Date('2011-12-14 11:55:00 GMT'))
      );
    }

    /**
     * Test path
     *
     */
    #[@test]
    public function withPath() {
      $this->assertHeaderEquals(
        'name=value; path=/public', 
        new Cookie('name', 'value', 0, '/public')
      );
    }

    /**
     * Test domain
     *
     */
    #[@test]
    public function withDomain() {
      $this->assertHeaderEquals(
        'name=value; domain=example.com', 
        new Cookie('name', 'value', 0, '', 'example.com')
      );
    }

    /**
     * Test secure
     *
     */
    #[@test]
    public function secure() {
      $this->assertHeaderEquals(
        'name=value; secure', 
        new Cookie('name', 'value', 0, '', '', TRUE)
      );
    }

    /**
     * Test httpOnly
     *
     */
    #[@test]
    public function httpOnly() {
      $this->assertHeaderEquals(
        'name=value; HTTPOnly', 
        new Cookie('name', 'value', 0, '', '', FALSE, TRUE)
      );
    }
    
    /**
     * Test parseing header line works
     *
     */
    #[@test]
    public function parseCookie() {
      $cookie= Cookie::parse('Bugzilla_logincookie=e9hR2sFvjX; path=/; expires=Fri, 01-Jan-2038 00:00:00 GMT; secure; HttpOnly');
      
      $this->assertHeaderEquals(
        'Bugzilla_logincookie=e9hR2sFvjX; expires=Fri, 01-Jan-2038 00:00:00 GMT; path=/; secure; HTTPOnly',
        $cookie
      );
    }    
  }
?>
