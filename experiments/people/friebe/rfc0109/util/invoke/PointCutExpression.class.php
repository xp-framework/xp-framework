<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.invoke.Invocation');

  /**
   * (Insert class' description here)
   *
   * @see      reference
   * @purpose  purpose
   */
  class PointCutExpression extends Object {
    const
      CONSTRUCT  = 'new',
      CALL       = 'call';
  
    protected
      $joinpoint= '';

    /**
     * Constructor
     *
     * @param   string expression
     */
    public function __construct($expression) {
      sscanf($expression, '%[^:]:%[^$]', $this->joinpoint, $spec);
      switch ($this->joinpoint) {
        case self::CONSTRUCT:
          break;
        
        case self::CALL:
          sscanf($spec, '%[^:]::%[^(](%[^)])', $class, $method, $this->narguments);
          $this->class= XPClass::forName($class);
          $this->method= '*' == $method ? NULL : $this->class->getMethod($method);
          break;
        
        default:
          throw new FormatException('Unknown joinpoint "'.$this->joinpoint.'" in expression "'.$expression.'"');
      }
    }
  
    /**
     * Get Joinpoint
     *
     * @return  string
     */
    public function getJoinpoint() {
      return $this->joinpoint;
    }
    
    /**
     * Returns whether this pointcut expression matches a given Invocation
     *
     * @param   util.invoke.Invocation inv
     * @return  bool
     */
    public function matches(Invocation $inv) {
      return (
        ($inv->getCallingClass()->equals($this->class)) &&
        (($this->method === NULL) || $inv->getCallingMethod()->equals($this->method)) &&
        (('*' == $this->narguments) || ($inv->numArguments() == $this->narguments))
      );
    }
  }
?>
