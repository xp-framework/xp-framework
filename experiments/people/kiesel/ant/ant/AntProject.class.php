<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'ant.AntEnvironment',
    'xml.meta.Unmarshaller'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AntProject extends Object {
    public
      $name         = NULL,
      $basedir      = '.',
      $description  = NULL,
      $default      = NULL,
      $properties   = array(),
      $targets      = array();
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public static function fromString($xml) {
      return Unmarshaller::unmarshal($xml, xp::reflect(__CLASS__));
    }
    
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
    #[@xmlmapping(element= 'basedir')]
    public function setBasedir($basedir) {
      $this->basedir= $basedir;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@description|description')]
    public function setDescription($desc) {
      $this->description= trim($desc);
    }    
      
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'property', class='ant.AntProperty')]
    public function addProperty($property) {
      $this->properties[]= $property;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= 'target', class='ant.AntTarget')]
    public function addTarget($target) {
      if (isset($this->targets[$target->getName()])) {
        throw new IllegalArgumentException('Target "'.$target->getName().'" is duplicate.');
      }
      
      $this->targets[$target->getName()]= $target;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    #[@xmlmapping(element= '@default')]
    public function setDefault($default) {
      $this->default= $default;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function runTarget($name) {
      $this->targets[$name]->run($this, $this->environment);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function run($out, $err, $arguments) {
      if (sizeof($arguments) == 0) $arguments= array($this->default);
      
      $target= array_shift($arguments);
      if (!isset($this->targets[$target])) {
        throw new IllegalArgumentException('Target ['.$target.'] does not exist.');
      }
      
      // Setup environment
      $this->environment= new AntEnvironment($out, $err);
      foreach ($this->properties as $p) { $p->run($this->environment); }      
      
      $this->runTarget($target);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function toString() {
      $s= $this->getClassName().rtrim(' '.$this->name.' ').'@('.$this->hashCode()."){\n";
      $s.= '  `- default: '.$this->default."\n";
      $s.= '  `- description: '.$this->description."\n";
      $s.= "\n";
      foreach ($this->properties as $p) {
        $s.= $p->toString()."\n";
      }
      
      foreach ($this->targets as $t) {
        $s.= $t->toString()."\n";
      }
      
      return $s.'}';
    }
  }
?>
