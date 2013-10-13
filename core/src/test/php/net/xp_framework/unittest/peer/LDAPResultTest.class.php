<?php namespace net\xp_framework\unittest\peer;
 
use peer\ldap\LDAPClient;
use peer\ldap\LDAPSearchResult;
use unittest\TestCase;


/**
 * Test LDAP client
 *
 * @see      xp://peer.ldap.LDAPClient
 * @purpose  Unit Test
 */
class LDAPResultTest extends TestCase {
  protected
    $lc               = null;
    
  protected static
    $previouslyFailed = false;
    
  /**
   * Setup method
   *
   */
  public function setUp() {
    if (self::$previouslyFailed) {
      throw new \unittest\PrerequisitesNotMetError('Previously failed to set up.');
    }
    
    if (!extension_loaded('ldap')) {
      throw new \unittest\PrerequisitesNotMetError('LDAP extension not available.'); 
    }
    
    $this->lc= new LDAPClient('ldap.openldap.org');
    try {
      $this->lc->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
      $this->lc->connect();
      $this->lc->bind();
    } catch (\peer\ConnectException $e) {
      self::$previouslyFailed= true;
      throw (new \unittest\PrerequisitesNotMetError(
        PREREQUISITE_INITFAILED,
        $e,
        array('connect', 'ldapv3://ldap.openldap.org')
      ));
    } catch (\peer\ldap\LDAPException $e) {
      self::$previouslyFailed= true;
      throw (new \unittest\PrerequisitesNotMetError(
        PREREQUISITE_INITFAILED,
        $e,
        array('bind', 'ldapv3://ldap.openldap.org')
      ));
    }
  }
  
  /**
   * Tear down this test case.
   *
   */
  public function tearDown() {
    $this->lc->close();
  }

  /**
   * Test LDAP search
   *
   */
  #[@test]
  public function testSearch() {
    $res= $this->lc->search(
      'ou=People,dc=OpenLDAP,dc=Org', 
      '(objectClass=*)'
    );
    $this->assertClass($res, 'peer.ldap.LDAPSearchResult');
    $this->assertNotEquals(0, $res->numEntries());
    $entry= $res->getFirstEntry();
    $this->assertClass($entry, 'peer.ldap.LDAPEntry');
    return $entry;
  }

  /**
   * Test call of nextEntry() when there are
   * no more resultsets 
   *
   */
  #[@test]
  public function testNoMoreResults() {
    $res= $this->lc->search(
      'cn=Directory Manager,dc=OpenLDAP,dc=Org', 
      '(objectClass=*)'
    );
    $this->assertEquals(1, $res->numEntries());
    $entry= $res->getFirstEntry();
    $this->assertFalse($res->getNextEntry());
  }

  /**
   * Multiple calls of getFirstEntry()
   *
   */
  #[@test]
  public function testFirstEntry() {
    $res= $this->lc->search(
      'ou=People,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    
    // First entry
    $this->assertEquals('Kurt', $res->getFirstEntry()->getAttribute('givenname', 0));
    
    // Second entry
    $this->assertEquals('Howard', $res->getNextEntry()->getAttribute('givenname', 0));
    
    // Jump to first entry
    $this->assertEquals('Pagan', $res->getFirstEntry()->getAttribute('description', 0));
    $this->assertEquals('KDZ', $res->getFirstEntry()->getAttribute('initials', 0));
    
    // Get second entry again
    $this->assertEquals('Howard', $res->getNextEntry()->getAttribute('givenname', 0));
    
    // Third entry
    $this->assertEquals('Stig', $res->getNextEntry()->getAttribute('givenname', 0));
  }

  /**
   * Test to get first entry on first call
   * of getNextEntry()
   *
   */
  #[@test]
  public function omitFirstEntry() {
    $res= $this->lc->search(
      'ou=People,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    
    $this->assertEquals('Kurt', $res->getNextEntry()->getAttribute('givenname', 0));
  }

  /**
   * Test ldap entry object
   *
   */
  #[@test]
  public function testEntry() {
    $res= $this->lc->search(
      'ou=People,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    
    $entry= $res->getFirstEntry();

    $this->assertClass($entry, 'peer.ldap.LDAPEntry');
    $this->assertNotEmpty($entry->getDN());
    $this->assertArray($attributes= $entry->getAttributes());
  }
  
  /**
   * Test searching with empty resultset
   *
   */
  #[@test]
  public function emptyResult() {
    $res= $this->lc->search(
      'ou=Groups,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    $this->assertEquals(false, $res->getFirstEntry());      
  }
  
  /**
   * Test empty resultset
   *
   */
  #[@test]
  public function testEmptyResult() {
    $res= $this->lc->search(
      'ou=Groups,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    $this->assertEquals(false, $res->getNextEntry());      
  }
  
  /**
   * Test non-empty resultset
   *
   */
  #[@test]
  public function readEntries() {
    $res= $this->lc->search(
      'ou=Groups,dc=OpenLDAP,dc=Org',
      '(objectClass=*)'
    );
    $this->assertEquals(true, $res->getEntry(0) instanceof \peer\ldap\LDAPEntry);
  }

  /**
   * Test empty resultset
   *
   */
  #[@test]
  public function readEmptyEntries() {
    $res= $this->lc->search(
      'ou=Groups,dc=OpenLDAP,dc=Org',
      '(objectClass=person)'
    );
    $this->assertEquals(false, $res->getEntry(0));      
  }
}
