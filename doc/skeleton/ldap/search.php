<?php
/* Test ldap functionality
 * 
 * @see http://viki.jones.dk/index.php/LdapTestServer
 *
 * $Id$
 */
  require('lang.base.php');
  uses('peer.ldap.LDAPClient');
  
  $l= &new LDAPClient('ldap.jones.dk');
  try(); {
    $l->connect();
    $l->bind();
    $res= &$l->search(
      'dc=jones, dc=dk', 
      '(objectClass=*)'
    );
  } if (catch('IOException', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  while ($entry= $res->getNextEntry()) {
    var_export($entry);
  }
  
  // Disconnect
  $l->close();
?>
