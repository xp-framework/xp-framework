<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php'); require('imports.php');
  uses('rdbms.Criteria', 'rdbms.criterion.Restrictions');
  imports('rdbms.criterion.Restrictions::*');
  
  // Without imports
  echo create(new Criteria())
    ->add(Restrictions::anyOf(
      Restrictions::in('id', array(1, 2, 3)),
      Restrictions::equal('name', 'Timm')
    ))
    ->toString(),
  "\n";
  
  // With imports
  echo create(new Criteria())
    ->add(anyOf(
      in('id', array(1, 2, 3)),
      equal('name', 'Timm')
    ))
    ->toString(),
  "\n";
?>
