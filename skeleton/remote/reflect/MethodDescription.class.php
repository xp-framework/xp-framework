<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.HashSet', 'remote.reflect.TransactionTypeDescription');

  /**
   * Describes an EJB's method
   *
   * @see      xp://remote.Remote
   * @purpose  Reflection
   */
  class MethodDescription extends Object {
    public
      $name             = '',
      $returnType       = '',
      $parameterTypes   = NULL,
      $roles            = NULL,
      $transactionType  = NULL;

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set ReturnType
     *
     * @param   string returnType
     */
    public function setReturnType($returnType) {
      $this->returnType= $returnType;
    }

    /**
     * Get ReturnType
     *
     * @return  string
     */
    public function getReturnType() {
      return $this->returnType;
    }

    /**
     * Set ParameterTypes
     *
     * @param   lang.ArrayList<string> parameterTypes
     */
    public function setParameterTypes($parameterTypes) {
      $this->parameterTypes= $parameterTypes;
    }

    /**
     * Get ParameterTypes
     *
     * @return  lang.ArrayList<string>
     */
    public function getParameterTypes() {
      return $this->parameterTypes;
    }

    /**
     * Set Roles
     *
     * @param   lang.ArrayList<string> roles
     */
    public function setRoles($roles) {
      $this->roles= $roles;
    }

    /**
     * Get Roles
     *
     * @return  lang.ArrayList<string>
     */
    public function getRoles() {
      return $this->roles;
    }

    /**
     * Set TransactionType
     *
     * @param   remote.reflect.TransactionTypeDescription transactionType
     */
    public function setTransactionType(TransactionTypeDescription $transactionType) {
      $this->transactionType= $transactionType;
    }

    /**
     * Get TransactionType
     *
     * @return  remote.reflect.TransactionTypeDescription
     */
    public function getTransactionType() {

      // BC: Older versions of xp-easc serialized enums to their ordinal value,
      // setting the transactionType member to that int directly.
      if (!$this->transactionType instanceof TransactionTypeDescription) {
        static $bcMap= array(
          0 => 'NOT_SUPPORTED',
          1 => 'REQUIRED',
          2 => 'SUPPORTS',
          3 => 'REQUIRES_NEW',
          4 => 'MANDATORY',
          5 => 'NEVER',
          6 => 'UNKNOWN'
        );
        
        return TransactionTypeDescription::valueOf($bcMap[$this->transactionType]);
      }
      return $this->transactionType;
    }
    
    /**
     * Returns a string representation of a type argument
     *
     * @param   var arg
     * @return  string
     */
    protected function typeString($arg) {
      return NULL === $arg ? 'void' : ($arg instanceof ClassReference ? $arg->referencedName() : $arg);
    }

    /**
     * Retrieve a set of classes used in this interface
     *
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= new HashSet(); 
      if ($this->returnType instanceof ClassReference) $set->add($this->returnType);

      for ($i= 0, $s= sizeof($this->parameterTypes->values); $i < $s; $i++) {
        if (!($this->parameterTypes->values[$i] instanceof ClassReference)) continue;
        $set->add($this->parameterTypes->values[$i]);
      }
      return $set->toArray();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s@{ @Transaction(type= %s) %s%s %s(%s) }',
        $this->getClassName(),
        $this->getTransactionType()->name,
        $this->roles->values ? '@Security(roles= ['.implode(', ', $this->roles->values).']) ' : '',
        $this->typeString($this->returnType),
        $this->name,
        implode(', ', array_map(array($this, 'typeString'), $this->parameterTypes->values))
      );
    }
  }
?>
