<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'lang.reflect';

  /**
   * Represents a method's parameter
   *
   * @see      xp://lang.reflect.Method#getParameter
   * @see      xp://lang.reflect.Method#getParameters
   * @see      xp://lang.reflect.Method#numParameters
   * @purpose  purpose
   */
  class lang·reflect·Parameter extends Object {
    protected
      $_reflect = NULL,
      $_details = NULL;

    /**
     * Constructor
     *
     * @param   php.ReflectionParameter reflect
     * @param   array details
     */    
    public function __construct($reflect, $details) {
      $this->_reflect= $reflect;
      $this->_details= $details;
    }

    /**
     * Get parameter's name.
     *
     * @return  string
     */
    public function getName() {
      return $this->_reflect->getName();
    }

    /**
     * Get parameter's type.
     *
     * @return  lang.Type
     */
    public function getType() {
      if (
        !($details= XPClass::detailsForMethod($this->_details[0], $this->_details[1])) ||  
        !isset($details[DETAIL_ARGUMENTS][$this->_details[2]])
      ) {   // Unknown or unparseable, return ANYTYPE
        return Type::$VAR;
      }
      return Type::forName(ltrim($details[DETAIL_ARGUMENTS][$this->_details[2]], '&'));
    }

    /**
     * Get parameter's type.
     *
     * @return  string
     */
    public function getTypeName() {
      if (
        !($details= XPClass::detailsForMethod($this->_details[0], $this->_details[1])) ||  
        !isset($details[DETAIL_ARGUMENTS][$this->_details[2]])
      ) {   // Unknown or unparseable, return ANYTYPE
        return 'var';
      }
      return ltrim($details[DETAIL_ARGUMENTS][$this->_details[2]], '&');
    }

    /**
     * Get parameter's type restriction.
     *
     * @return  lang.Type or NULL if there is no restriction
     */
    public function getTypeRestriction() {
      if ($this->_reflect->isArray()) {
        return Primitive::$ARRAY;
      } else if ($c= $this->_reflect->getClass()) {
        return new XPClass($c);
      } else {
        return NULL;
      }
    }

    /**
     * Retrieve whether this argument is optional
     *
     * @return  bool
     */
    public function isOptional() {
      return $this->_reflect->isOptional();
    }

    /**
     * Get default value.
     *
     * @throws  lang.IllegalStateException in case this argument is not optional
     * @return  mixed
     */
    public function getDefaultValue() {
      if ($this->_reflect->isOptional()) {
        return $this->_reflect->getDefaultValue();
      }

      throw new IllegalStateException('Parameter "'.$this->_reflect->getName().'" has no default value');
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s<%s %s%s>',
        $this->getClassName(),
        $this->getType()->toString(),
        $this->_reflect->getName(),
        $this->_reflect->isOptional() ? '= '.xp::stringOf($this->_reflect->getDefaultValue()) : ''
      );
    }
  }
?>
