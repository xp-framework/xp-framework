<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  uses('util.cmd.Console');

  /**
   * Dumps an API
   *
   * @purpose  purpose
   */
  class Api extends Object {
    var
      $class          = '',
      $showDeclaring  = FALSE;

    /**
     * Set classname
     *
     * @access  public
     * @param   string classname
     */
    #[@arg(position= 0)]
    function setClassname($classname) {
      $this->class= &XPClass::forName($classname);
    }

    /**
     * Set whether declaring classes should be shown
     *
     * @access  public
     * @param   bool show default FALSE
     */
    #[@arg(short= 'd')]
    function setShowDeclaring($show= FALSE) {
      $this->showDeclaring= (bool)$show;
    }

    /**
     * Run this
     *
     * @access  public
     */
    function run() {
      Console::write($this->class->toString());
      Console::writeLine(' extends ', xp::stringOf($this->class->getParentClass()), ' {');
      foreach ($this->class->getFields() as $field) {
        Console::writeLine('  ', xp::stringOf($field));
      }
      if ($this->class->hasConstructor()) {
        Console::writeLine('  ', xp::stringOf($this->class->getConstructor()));
      }
      foreach($this->class->getMethods() as $method) {
        Console::write('  ', xp::stringOf($method));
        $this->showDeclaring && Console::write(' declared in '.xp::stringOf($method->getDeclaringClass()));
        Console::writeLine();
      }
      Console::writeLine('}');
    }
  }
?>
