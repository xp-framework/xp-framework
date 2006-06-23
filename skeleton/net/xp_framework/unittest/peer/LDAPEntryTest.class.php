<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'peer.ldap.LDAPEntry',
    'util.profiling.unittest.TestCase'
  );

  /**
   * Test LDAP entry class
   *
   * @see      xp://peer.ldap.LDAPEntry
   * @purpose  Unit Test
   */
  class LDAPEntryTest extends TestCase {
    var
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
     * @access  public
     */    
    function setUp() {
      $this->entry= &new LDAPEntry($this->dn, $this->attributes);
    }

    /**
     * Tests getDN() method
     *
     * @access public 
     */
    #[@test]
    function getDN() {
      $this->assertEquals($this->dn, $this->entry->getDN());
    }

    /**
     * Tests getAttributes() method
     *
     * @access public 
     */
    #[@test]
    function getAttributes() {
      $this->assertEquals($this->attributes, $this->entry->getAttributes());
    }

    /**
     * Tests getAttribute() method for the "cn" attribute
     *
     * @access public 
     */
    #[@test]
    function cnAttribute() {
      $this->assertEquals(array('Friebe, Timm J.'), $this->entry->getAttribute('cn'));
    }

    /**
     * Tests getAttribute() method for the "cn" attribute
     *
     * @access public 
     */
    #[@test]
    function firstCnAttribute() {
      $this->assertEquals('Friebe, Timm J.', $this->entry->getAttribute('cn', 0));
    }

    /**
     * Tests getAttribute() method for a non-existant attribute
     *
     * @access public 
     */
    #[@test]
    function nonExistantAttribute() {
      $this->assertEquals(NULL, $this->entry->getAttribute('@@NON-EXISTANT@@'));
    }

    /**
     * Tests getAttribute() method for the objectClass attribute (which
     * has multiple values).
     *
     * @access public 
     */
    #[@test]
    function objectClassAttribute() {
      $this->assertEquals(
        $this->attributes['objectclass'], 
        $this->entry->getAttribute('objectclass')
      );
    }

    /**
     * Tests static fromData() method
     *
     * @access public 
     */
    #[@test]
    function fromData() {
      $cmp= &LDAPEntry::fromData($data= array(
        'objectclass' => array(
          'count' => 3,
          0 => 'top',
          1 => 'alias',
          2 => 'extensibleObject',
        ),
        0 => 'objectclass',
        'uid' => array(
          'count' => 1,
          0 => 'thekid',
        ),
        1 => 'uid',
        'aliasedobjectname' => array(
          'count' => 1,
          0 => $this->dn,
        ),
        2 => 'aliasedobjectname',
        'count' => 3,
      ));
      $this->assertEquals(array('top', 'alias', 'extensibleObject'), $cmp->getAttribute('objectclass'));
      $this->assertEquals('thekid', $cmp->getAttribute('uid', 0));
      $this->assertEquals($this->dn, $cmp->getAttribute('aliasedobjectname', 0));
    }
  }
?>
