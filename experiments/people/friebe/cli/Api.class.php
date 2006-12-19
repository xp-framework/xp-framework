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
    protected
      $class          = '',
      $showDeclaring  = FALSE;

    /**
     * Set classname
     *
     * @param   string classname
     */
    #[@arg(position= 0)]
    public function setClassname($classname) {
      $this->class= &XPClass::forName($classname);
    }

    /**
     * Set whether declaring classes should be shown
     *
     * @param   bool show default FALSE
     */
    #[@arg(short= 'd')]
    public function setShowDeclaring($show= FALSE) {
      $this->showDeclaring= (bool)$show;
    }

    /**
     * Run this command
     *
     */
    public function run() {
      Console::write($this->class->toString());
      Console::writeLine(' extends ', $this->class->getParentClass()->toString(), ' {');
      foreach ($this->class->getFields() as $field) {
        Console::writeLine('  ', $field->toString());
      }
      if ($this->class->hasConstructor()) {
        Console::writeLine('  ', $this->class->getConstructor()->toString());
      }
      foreach($this->class->getMethods() as $method) {
        Console::write('  ', $method->toString());
        $this->showDeclaring && Console::write(' declared in '.$method->getDeclaringClass()->toString());
        Console::writeLine();
      }
      Console::writeLine('}');
    }
  }
?>
