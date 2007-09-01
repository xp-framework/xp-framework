<?php
/* This class is part of the XP framework
 *
 * $Id: LDAPQueryTest.class.php 9461 2007-02-15 15:39:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::peer;
 
  ::uses(
    'peer.ldap.LDAPQuery',
    'util.Date',
    'unittest.TestCase'
  );

  /**
   * Test LDAP client
   *
   * @see      xp://peer.ldap.LDAPClient
   * @purpose  Unit Test
   */
  class LDAPQueryTest extends unittest::TestCase {
    /**
     * Test general functionality with tokenizer
     *
     */
    #[@test]
    public function testTokenizer() {
      $q= new peer::ldap::LDAPQuery();
      
      // Test a default query string
      $this->assertEquals($q->prepare('(&(objectClass=*)(uid=%s))', 'kiesel'), '(&(objectClass=*)(uid=kiesel))');
      
      // Test % token as first char
      $this->assertEquals($q->prepare('%s', 'foo bar'), 'foo bar');
      
      // Test numbered tokens
      $this->assertEquals($q->prepare('%2$s %1$s', 'bar', 'foo'), 'foo bar');
    }
    
    /**
     * Test various replacement rules in the tokenizer.
     *
     */
    #[@test]
    public function testTokenizerReplacements() {
      $q= new peer::ldap::LDAPQuery();
      
      // Test character replacements
      $this->assertEquals($q->prepare('%s', 'foo(bar'), 'foo\\28bar');
      $this->assertEquals($q->prepare('%s', 'foo)bar'), 'foo\\29bar');
      $this->assertEquals($q->prepare('%s', 'foo\\bar'), 'foo\\5cbar');
      $this->assertEquals($q->prepare('%s', 'foo*bar'), 'foo\\2abar');
      $this->assertEquals($q->prepare('%s', 'foo'.chr(0).'bar'), 'foo\\00bar');
      $this->assertEquals($q->prepare('foo%%bar', 'a'), 'foo%bar');
      
      // Test copy-through token
      $this->assertEquals($q->prepare('%c', 'foo(*'.chr(0).'\\)bar'), 'foo(*'.chr(0).'\\)bar');
    }
    
    /**
     * Test date tokens.
     *
     */
    #[@test]
    public function testDateToken() {
      $q= new peer::ldap::LDAPQuery();
      
      $d= new util::Date(328336200);
      $this->assertEquals($q->prepare('%s', $d), '198005280630Z+0200');
      $this->assertEquals($q->prepare('%d', $d), '198005280630Z+0200');
    }
    
    /**
     * Test NULL tokens
     *
     */
    #[@test]
    public function testNullTokens() {
      $q= new peer::ldap::LDAPQuery();
      
      $this->assertEquals($q->prepare('%d', NULL), 'NULL');
      $this->assertEquals($q->prepare('%c', NULL), 'NULL');
    }
    
    /**
     * Test for argument checks (only scalars or objects may be
     * passed).
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function testNonScalarInput() {
      $q= new peer::ldap::LDAPQuery('c=DE', '(%d)', array(1,2));
    }
  }
?>
