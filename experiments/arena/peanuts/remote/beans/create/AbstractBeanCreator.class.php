<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.cmd.Command', 'io.Folder');

  /**
   * (Insert class' description here)
   *
   * @purpose  Abstract base class
   */
  abstract class AbstractBeanCreator extends Command {
    protected
      $beanClass     = NULL,
      $outputDir     = NULL,
      $packageName   = '',
      $className     = '',
      $remoteMethods = array(),
      $beanType      = '',
      $beanName      = '';

    /**
     * Sets the output directory to write to
     *
     * @param   string name
     */
    #[@arg]
    public function setOutputDirectory($name= '.') {
      $this->outputDir= new Folder($name);
      if (!$this->outputDir->exists()) {
        throw new IllegalArgumentException('Output directory '.$this->outputDir->getURI().' does not exist!');
      }
    }

    /**
     * Sets the bean class to be used
     *
     * @param   string classname fully qualified class name
     */
    #[@arg]
    public function setBeanClass($classname) {
      if (substr($classname, -4) != 'Bean') {
        throw new IllegalArgumentException('Class '.$classname.' must end with "Bean"');
      }
      $this->beanClass= XPClass::forName($classname);
      
      // Sanity check
      if (!$this->beanClass->hasAnnotation('bean')) {
        throw new IllegalArgumentException('Class '.$classname.' does not have a "bean" annotation');
      }

      // Calculate package 
      $classname= $this->beanClass->getName();
      $pos= strrpos($classname, '.');
      $this->packageName= substr($classname, 0, $pos);
      $this->className= substr($classname, $pos+ 1);
      
      // Calculate methods
      foreach ($this->beanClass->getMethods() as $method) {
        if (!$method->hasAnnotation('remote')) continue;

        $this->remoteMethods[]= $method;
      }
      
      // Calculate type and name
      $this->beanType= $this->beanClass->getAnnotation('bean', 'type');
      $this->beanName= $this->beanClass->getAnnotation('bean', 'name');
    }
    
    /**
     * Runs this command
     *
     */
    public function run() {
      foreach ($this->getClass()->getMethods() as $method) {
        if (!$method->hasAnnotation('create')) continue;
        
        $this->out->writeLine('===> Creating ', $method->getAnnotation('create'));
        $method->invoke($this);
      }
    }
  }
?>
