<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  uses('peer.ldap.LDAPClient');
  
  $query= str_replace(array('(', ')'), array(), $_REQUEST['q']);
  if (strlen($query) < 1) { exit; }
  
  // Execute LDAP query
  try(); {
    $ldap= &new LDAPClient('openldap.org');
    $ldap->connect();
    $ldap->bind();
  } if (catch('LDAPException', $e)) {
    exit;
  }
  
  try(); {
    $res= &$ldap->search(
      'o=foobar,c=DE', 
      sprintf('(&(objectClass=Person)(|(cn=%1$s*)(sn=%1$s*)(displayName=%1$s*)))', $query),
      array(), 0, 0
    );
  } if (catch('LDAPException', $e)) {
    exit;
  }
  
  header('Content-type: text/plain');
  if ($res->numEntries() == 0) exit;
  
  // Make proposals
  $i= 0;
  if ($res->numEntries() >= 1) {
    $entry= &$res->getFirstEntry();
    $first= $entry;

    // Clear old content from selectbox
    printf('if (document.getElementById("personinput").value == "%s") {', $_REQUEST['q']);
    printf('var selectbox= document.getElementById("person-select");');
    printf('for (i= 0; i <= selectbox.length; i++) { selectbox.options[i]= null; }');
    printf('selectbox.length= 0;');
    
    while ($i++ < 10 && $entry= &$res->getNextEntry()) {
    
      // Add entry to selectbox...
      printf('entry= new Option("%s", "%s");', 
        utf8_encode($entry->getAttribute('cn', 0)),
        utf8_encode($entry->getAttribute('uid', 0))
      );
      printf('selectbox.options[selectbox.length]= entry;');
    }

    // Autocomplete textbox with first entry
    printf('var l= document.getElementById("personinput").value.length;');
    printf('document.getElementById("personinput").value= "%s";', utf8_encode($first->getAttribute('cn', 0)));

    printf('var selection= new SelectionArea();');
    printf('selection.setSelectionRange("personinput", l, -1);');
    printf('}');
  }
?>
