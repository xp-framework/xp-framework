<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntTarget extends Object {
    public
      $name     = '',
      $depends  = array(),
      $tasks    = array();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@name')]
    public function setName($name) {
      $this->name= $name;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getName() {
      return $this->name;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@depends')]
    public function setDepends($depends) {
      $this->depends= explode(',', $depends);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '*', factory= 'taskFromNode')]
    public function addTask($task) {
      $this->tasks[]= $task;
    }    
    
    public function taskFromNode($node) {
      switch ($node) {
        case 'ear': {
          $node= 'jar';
          break;
        }
      }
      
      $classname= sprintf('ant.task.Ant%sTask', ucfirst($node));
      
      // HACK: if a tasks class does not exist, use the default
      try {
        XPClass::forName($classname);
      } catch (ClassNotFoundException $e) {
        $classname= 'ant.task.AntUnknownTask';
      }
      
      return $classname;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function needsToRun() {
      // TBI
      return TRUE;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run(AntProject $project, AntEnvironment $environment) {
    
      if (!$this->needsToRun()) return;
    
      // Check dependencies
      foreach ($this->depends as $target) {
        $project->runTarget($target);
      }
    
      foreach ($this->tasks as $task) {
        $task->run($environment);
      }
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toString() {
      $s= $this->getClassName().'@('.$this->hashCode()."){\n";
      $s.= '  `- name: '.$this->name."\n";
      $s.= '  `- depends: '.implode(', ', $this->depends)."\n";
      $s.= '  `- ['.sizeof($this->tasks)."] tasks\n";
      return $s.'}';

    }    
    
  }
?>
