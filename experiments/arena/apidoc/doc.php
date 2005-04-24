<?php
/* This file is part of the XP framework' experiments
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.profiling.Timer', 'Doclet');

  // {{{ TreeDoclet
  //     Prints a class tree  
  class TreeDoclet extends Doclet {
  
    // {{{ void exportClass(&ClassDoc class [, string indent = ''])
    //     Export a single class
    function exportClass(&$class, $indent= '') {
      echo '[', $class->qualifiedName(), "] {\n";

      if ($class->superclass) {
        echo $indent.'  + extends ', $this->exportClass($class->superclass, $indent.'  ');
      }
      while ($class->interfaces->hasNext()) {
        $iface= &$class->interfaces->next();
        echo $indent.'  + implements ', $this->exportClass($iface, $indent.'  ');
      }
      while ($class->usedClasses->hasNext()) {
        $used= &$class->usedClasses->next();
        echo $indent.'  + uses ', $this->exportClass($used, $indent.'  ');
      }
      echo $indent, "}\n";
    }
    // }}}

    // {{{ bool start(&RootDoc root)
    //     Entry point method
    function start(&$root) {
      with ($timer= &new Timer(), $timer->start()); {
        while ($root->classes->hasNext()) {
          $this->exportClass($root->classes->next());
        }

        $timer->stop();
        printf("\n%.3f seconds\n", $timer->elapsedTime());
      }
    }
  }
  // }}}

  // {{{ main  
  RootDoc::start(new TreeDoclet(), new ParamString());
  // }}}
?>
