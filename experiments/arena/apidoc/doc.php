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
    var 
      $indent = '',
      $timer  = NULL,
      $total  = 0;

    // {{{ __construct()
    //     Constructor
    function __construct() {
      $this->timer= &new Timer();
    }
    // }}}
    
    // {{{ void exportClass(&ClassDoc class [, string offset = ''])
    //     Export a single class
    function exportClass(&$class, $offset= '') {
      echo '[', $class->qualifiedName(), "] {\n";

      $indent= $offset.$this->indent;
      if ($class->superclass) {
        echo $indent.'+ extends ', $this->exportClass($class->superclass, $indent);
      }
      while ($class->interfaces->hasNext()) {
        $iface= &$class->interfaces->next();
        echo $indent.'+ implements ', $this->exportClass($iface, $indent);
      }
      while ($class->usedClasses->hasNext()) {
        $used= &$class->usedClasses->next();
        echo $indent.'+ uses ', $this->exportClass($used, $indent);
      }
      echo $offset, "}\n";
      $this->total++;
    }
    // }}}

    // {{{ bool start(&RootDoc root)
    //     Entry point method
    function start(&$root) {
      $this->indent= str_repeat(' ', $root->option('indent', 2));
    
      with ($this->total= 0, $this->timer->start()); {
        while ($root->classes->hasNext()) {
          $this->exportClass($root->classes->next(), $indent);
        }

        $this->timer->stop();
        printf("\n%d classes, %.3f seconds\n", $this->total, $this->timer->elapsedTime());
      }
    }
    // }}}
    
    // {{{ array validOptions()
    //     Returns an array of valid options
    function validOptions() {
      return array(
        'indent' => HAS_VALUE
      );
    }
  }
  // }}}

  // {{{ main  
  RootDoc::start(new TreeDoclet(), new ParamString());
  // }}}
?>
