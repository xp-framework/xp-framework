<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.collections.HashSet');

  define('TX_NOT_SUPPORTED',  0);
  define('TX_REQUIRED',       1);
  define('TX_SUPPORTS',       2);
  define('TX_REQUIRES_NEW',   3);
  define('TX_MANDATORY',      4);
  define('TX_NEVER',          5);
  define('TX_UNKNOWN',        6);

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
      $transactionType  = 0;

    /**
     * Set Name
     *
     * @access  public
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set ReturnType
     *
     * @access  public
     * @param   string returnType
     */
    public function setReturnType($returnType) {
      $this->returnType= $returnType;
    }

    /**
     * Get ReturnType
     *
     * @access  public
     * @return  string
     */
    public function getReturnType() {
      return $this->returnType;
    }

    /**
     * Set ParameterTypes
     *
     * @access  public
     * @param   lang.ArrayList<string> parameterTypes
     */
    public function setParameterTypes(&$parameterTypes) {
      $this->parameterTypes= &$parameterTypes;
    }

    /**
     * Get ParameterTypes
     *
     * @access  public
     * @return  lang.ArrayList<string>
     */
    public function &getParameterTypes() {
      return $this->parameterTypes;
    }

    /**
     * Set Roles
     *
     * @access  public
     * @param   lang.ArrayList<string> roles
     */
    public function setRoles(&$roles) {
      $this->roles= &$roles;
    }

    /**
     * Get Roles
     *
     * @access  public
     * @return  lang.ArrayList<string>
     */
    public function &getRoles() {
      return $this->roles;
    }

    /**
     * Set TransactionType
     *
     * @access  public
     * @param   int transactionType
     */
    public function setTransactionType($transactionType) {
      $this->transactionType= $transactionType;
    }

    /**
     * Get TransactionType
     *
     * @access  public
     * @return  int
     */
    public function getTransactionType() {
      return $this->transactionType;
    }
    
    /**
     * Returns a string representation of a type argument
     *
     * @access  protected
     * @param   mixed arg
     * @return  string
     */
    public function typeString($arg) {
      return NULL === $arg ? 'void' : (is('ClassReference', $arg) ? $arg->referencedName() : $arg);
    }

    /**
     * Retrieve a set of classes used in this interface
     *
     * @access  public
     * @return  remote.ClassReference[]
     */
    public function classSet() {
      $set= new HashSet(); 
      if (->returnTypeis('ClassReference', $this)) $set->add($this->returnType);
      for ($i= 0, $s= sizeof($this->parameterTypes->values); $i < $s; $i++) {
        if (!->parameterTypes->values[]is('ClassReference', $i)) continue;
        $set->add($this->parameterTypes->values[$i]);
      }
      return $set->toArray();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      static $transactionTypes= array(
        TX_NOT_SUPPORTED   => 'NOT_SUPPORTED',
        TX_REQUIRED        => 'REQUIRED',
        TX_SUPPORTS        => 'SUPPORTS',
        TX_REQUIRES_NEW    => 'REQUIRES_NEW',
        TX_MANDATORY       => 'MANDATORY',
        TX_NEVER           => 'NEVER',
        TX_UNKNOWN         => 'UNKNOWN'
      );

      return sprintf(
        '%s@{ @Transaction(type= %s) %s%s %s(%s) }',
        $this->getClassName(),
        $transactionTypes[$this->transactionType],
        $this->roles->values ? '@Security(roles= ['.implode(', ', $this->roles->values).']) ' : '',
        $this->typeString($this->returnType),
        $this->name,
        implode(', ', array_map(array(&$this, 'typeString'), $this->parameterTypes->values))
      );
    }
  }
?>
