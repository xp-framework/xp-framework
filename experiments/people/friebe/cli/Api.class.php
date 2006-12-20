<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */

  uses('util.cmd.Command');

  /**
   * Dumps an API
   *
   * @purpose  purpose
   */
  class Api extends Command {
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
      $this->class= XPClass::forName($classname);
    }

    /**
     * Set whether declaring classes should be shown
     *
     */
    #[@arg(short= 'd')]
    public function setShowDeclaring() {
      $this->showDeclaring= TRUE;
    }

    /**
     * Run this command
     *
     */
    public function run() {
      $this->out->write($this->class->toString());
      $this->out->writeLine(' extends ', $this->class->getParentClass()->toString(), ' {');
      foreach ($this->class->getFields() as $field) {
        $this->out->writeLine('  ', $field->toString());
      }
      if ($this->class->hasConstructor()) {
        $this->out->writeLine('  ', $this->class->getConstructor()->toString());
      }
      foreach($this->class->getMethods() as $method) {
        $this->out->write('  ', $method->toString());
        $this->showDeclaring && $this->out->write(' declared in '.$method->getDeclaringClass()->toString());
        $this->out->writeLine();
      }
      $this->out->writeLine('}');
    }
  }
?>
