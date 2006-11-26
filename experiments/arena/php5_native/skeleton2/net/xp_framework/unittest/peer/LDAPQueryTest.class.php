<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
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
  class LDAPQueryTest extends TestCase {
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setUp() {
      date_default_timezone_set('Europe/Berlin');
    }  
  
    /**    /**
     * Test general functionality with tokenizer
     *
     * @access  public
     */
    #[@test]
    public function testTokenizer() {
      $q= new LDAPQuery();
      
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
     * @access  public
     */
    #[@test]
    public function testTokenizerReplacements() {
      $q= new LDAPQuery();
      
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
     * @access  public
     */
    #[@test]
    public function testDateToken() {
      $q= new LDAPQuery();
      
      $d= new Date(328336200);
      $this->assertEquals($q->prepare('%s', $d), '198005280630Z+0200');
      $this->assertEquals($q->prepare('%d', $d), '198005280630Z+0200');
    }
    
    /**
     * Test NULL tokens
     *
     * @access  public
     */
    #[@test]
    public function testNullTokens() {
      $q= new LDAPQuery();
      
      $this->assertEquals($q->prepare('%d', NULL), 'NULL');
      $this->assertEquals($q->prepare('%c', NULL), 'NULL');
    }
    
    /**
     * Test for argument checks (only scalars or objects may be
     * passed).
     *
     * @access public 
     */
    #[@test, @expect('IllegalArgumentException')]
    public function testNonScalarInput() {
      $q= new LDAPQuery('c=DE', '(%d)', array(1,2));
    }
  }
?>
