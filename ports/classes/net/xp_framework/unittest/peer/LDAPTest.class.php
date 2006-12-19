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
     * @access  public
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
     * @access  public
     */
    public function tearDown() {
      $this->lc->close();
    }
    
    /**
     * Test LDAP search
     *
     * @access  public
     */
    #[@test]
    public function testSearch() {
      $res= &$this->lc->search(
        'ou=People,dc=OpenLDAP,dc=Org', 
        '(objectClass=*)'
      );
      $this->assertClass($res, 'peer.ldap.LDAPSearchResult');
      $this->assertInteger($res->numEntries());
      $entry= &$res->getFirstEntry();
      $this->assertClass($entry, 'peer.ldap.LDAPEntry');
      return $entry;
    }
  }
?>
