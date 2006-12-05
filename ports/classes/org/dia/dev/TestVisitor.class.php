<?php
/*
 *
 * $Id:$
 */

  uses('util.Visitor');

  /**
   *
   */
  class TestVisitor extends Object {

    var
      $depth= 0;

    function visit(&$Object) {
      if (is('org.dia.DiaObject', $Object))
        Console::writeLine("DiaObject: ".$Object->getName());
      if (is('org.dia.DiaComposite', $Object)) {
        $name= $Object->getName();
        if (isset($name))
          Console::writeLine("DiaComposite: $name");
      }
    } 

  } implements(__FILE__, 'util.Visitor');
?>
