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
      Console::writeLine('[', $class->classType(), ' : ', $class->qualifiedName(), '] {');

      $indent= $offset.$this->indent;
      
      // Tags
      foreach ($class->tags() as $tag) {
        Console::writeLine($indent, '+ ', get_class($tag), ' @', $tag->name(), ' [', $tag->text(), ']');
      }
      
      // Superclass
      if ($class->superclass) {
        Console::write($indent, '+ extends ');
        $this->exportClass($class->superclass, $indent);
      }
      
      // Interfaces
      while ($class->interfaces->hasNext()) {
        $iface= &$class->interfaces->next();
        Console::write($indent, '+ implements ');
        $this->exportClass($iface, $indent);
      }
      
      // Methods
      foreach ($class->methods as $method) {
        Console::writeLine  ($indent.'+ method ', $method->name(), '() {');
        foreach ($method->tags() as $tag) {
          Console::writeLine($indent.$this->indent, '+ ', get_class($tag), ' @', $tag->name(), ' [', $tag->text(), ']');
        }
        Console::writeLine($indent, '}');
      }

      // Used classes
      while ($class->usedClasses->hasNext()) {
        $used= &$class->usedClasses->next();
        Console::write($indent.'+ uses ');
        $this->exportClass($used, $indent);
      }

      Console::writeLine($offset, '}');
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
        Console::writeLinef('%d classes, %.3f seconds', $this->total, $this->timer->elapsedTime());
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
