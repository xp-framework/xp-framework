<?php
/*
 *
 * $Id$
 */

  uses('util.Visitor');

  /**
   *
   */
  class TestVisitor extends Object implements Visitor {

    public
      $depth= 0;

    public function visit($Object) {
      if (is('org.dia.DiaObject', $Object))
        Console::writeLine("DiaObject: ".$Object->getName());
      if (is('org.dia.DiaComposite', $Object)) {
        $name= $Object->getName();
        if (isset($name))
          Console::writeLine("DiaComposite: $name");
      }
    } 

  } 
?>
