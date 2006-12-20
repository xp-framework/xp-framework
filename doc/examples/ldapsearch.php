<?php
/* This file is part of the XP framework's examples
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('peer.ldap.LDAPClient');
  
  $l= new LDAPClient('ldap.openldap.org');
  try {
    $l->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
    $l->connect();
    $l->bind();
    $res= $l->search(
      'ou=People,dc=OpenLDAP,dc=Org', 
      '(objectClass=*)'
    );
  } catch (ConnectException $e) {
    $e->printStackTrace();
    exit(-1);
  } catch (LDAPException $e) {
    $e->printStackTrace();
    exit(-1);
  }
    
  Console::writeLinef('+++ %d entries found:', $res->numEntries());
  while ($entry= $res->getNextEntry()) {
    Console::writeLine($entry->toString());
  }
  
  // Disconnect
  $l->close();
?>
