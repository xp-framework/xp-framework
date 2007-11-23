<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'peer.ldap.LDAPClient',
    'unittest.TestCase'
  );

  /**
   * Test LDAP client
   *
   * @see      xp://peer.ldap.LDAPClient
   * @purpose  Unit Test
   */
  class LDAPTest extends TestCase {
    public
      $lc      = NULL;
      
    /**
     * Setup function
     *
     */
    public function setUp() {
      $this->lc= new LDAPClient('ldap.openldap.org');
      try {
        $this->lc->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        $this->lc->connect();
        $this->lc->bind();
      } catch (ConnectException $e) {
        throw (new PrerequisitesNotMetError(
          PREREQUISITE_INITFAILED,
          $e,
          array('connect', 'ldapv3://ldap.openldap.org')
        ));
      } catch (LDAPException $e) {
        throw (new PrerequisitesNotMetError(
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
     * Test LDAP read
     *
     * @param   
     * @return  
     */
    #[@test]
    public function readEntry() {
      $res= $this->lc->read(new LDAPEntry('uid=kurt,ou=People,dc=OpenLDAP,dc=Org'));
      $this->assertEquals('uid=kurt,ou=People,dc=OpenLDAP,dc=Org', $res->getDN());
      var_dump($res);
    }
    
    /**
     * Test LDAP read on non-existing object
     *
     */
    #[@test, @expect('peer.ldap.LDAPException')]
    public function readNonExistingEntry() {
      $this->assertEquals(NULL, $this->lc->read(new LDAPEntry('uid=unknown,ou=People,dc=OpenLDAP,dc=Org')));
    }
  }
?>
