<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'peer.ldap.LDAPEntry',
    'unittest.TestCase'
  );

  /**
   * Test LDAP entry class
   *
   * @see      xp://peer.ldap.LDAPEntry
   * @purpose  Unit Test
   */
  class LDAPEntryTest extends TestCase {
    public
      $dn         = 'uid=friebe,ou=People,dc=xp-framework,dc=net',
      $attributes = array(
        'cn'          => array('Friebe, Timm J.'),
        'sn'          => array('Friebe'),
        'givenname'   => array('Timm'),
        'uid'         => array('friebe'),
        'displayname' => array('Friebe, Timm'),
        'mail'        => array('friebe@example.com'),
        'o'           => array('XP-Framework'),
        'ou'          => array('People'),
        'objectclass' => array('top', 'person', 'inetOrgPerson', 'organizationalPerson')
      ),
      $entry      = NULL;

    /**
     * Setup method
     *
     */    
    public function setUp() {
      $this->entry= new LDAPEntry($this->dn, $this->attributes);
    }

    /**
     * Tests getDN() method
     *
     */
    #[@test]
    public function getDN() {
      $this->assertEquals($this->dn, $this->entry->getDN());
    }

    /**
     * Tests getAttributes() method
     *
     */
    #[@test]
    public function getAttributes() {
      $this->assertEquals($this->attributes, $this->entry->getAttributes());
    }

    /**
     * Tests getAttribute() method for the "cn" attribute
     *
     */
    #[@test]
    public function cnAttribute() {
      $this->assertEquals(array('Friebe, Timm J.'), $this->entry->getAttribute('cn'));
    }

    /**
     * Tests getAttribute() method for the "cn" attribute
     *
     */
    #[@test]
    public function firstCnAttribute() {
      $this->assertEquals('Friebe, Timm J.', $this->entry->getAttribute('cn', 0));
    }

    /**
     * Tests getAttribute() method for a non-existant attribute
     *
     */
    #[@test]
    public function nonExistantAttribute() {
      $this->assertEquals(NULL, $this->entry->getAttribute('@@NON-EXISTANT@@'));
    }

    /**
     * Tests getAttribute() method for the objectClass attribute (which
     * has multiple values).
     *
     */
    #[@test]
    public function objectClassAttribute() {
      $this->assertEquals(
        $this->attributes['objectclass'], 
        $this->entry->getAttribute('objectclass')
      );
    }

    /**
     * Tests isA()
     *
     */
    #[@test]
    public function isInetOrgPerson() {
      $this->assertTrue($this->entry->isA('inetOrgPerson'));
    }

    /**
     * Tests isA()
     *
     */
    #[@test]
    public function isNotAliasObject() {
      $this->assertFalse($this->entry->isA('alias'));
    }
  }
?>
