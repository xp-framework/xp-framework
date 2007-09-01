<?php
/*
 *
 * $Id:$
 */

  namespace org::dia::dev;

  ::uses('util.Visitor');

  /**
   *
   */
  class TestVisitor extends lang::Object implements util::Visitor {

    public
      $depth= 0;

    public function visit($Object) {
      if (is('org.dia.DiaObject', $Object))
        util::cmd::Console::writeLine("DiaObject: ".$Object->getName());
      if (is('org.dia.DiaComposite', $Object)) {
        $name= $Object->getName();
        if (isset($name))
          util::cmd::Console::writeLine("DiaComposite: $name");
      }
    } 

  } 
?>
