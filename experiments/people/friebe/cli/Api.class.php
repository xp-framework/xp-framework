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
      $class= '';

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
      foreach($this->class->getMethods() as $field) {
        Console::writeLine('  ', xp::stringOf($field));
      }
      Console::writeLine('}');
    }
  }
?>
