<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ClassNotFoundException');

  /**
   * Represents a collection of objects of the same class (or subclass)
   *
   * <code>
   *   $coll= &Collection::forClass('rdbms.DBConnection');
   *
   *   $coll->add(new SybaseConnection(...));       // Works
   *   $coll->add(new MySQLConnection(...));        // Works, too
   *   $coll->add(Date::now());                     // Fails
   *   $coll->add(1);                               // Fails
   * </code>
   *
   * @purpose  "Type-safe" array
   */
  class Collection extends Object {
    var
      $class   = '',
      $list    = array();
      
    var
      $_name   = '';
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   string class
     */
    function __construct($class) {
      
      $this->class= $class;
      $this->_name= xp::reflect($class);
    }
    
    /**
     * Returns a new Collection object for a specified class
     *
     * @access  public
     * @param   string class the fully qualified class name
     * @return  &lang.Collection
     * @throws  lang.ClassNotFoundException
     */
    function &forClass($class) {
      if (!class_exists(xp::reflect($class))) {
        return throw(new ClassNotFoundException('Class "'.$class.'" does not exist'));
      }
      return new Collection($class);
    }
    
    /**
     * Returns the number of elements in this list.
     *
     * @access  public
     * @return  int
     */
    function size() {
      return sizeof($this->list);
    }
    
    /**
     * Returns the element's class name
     *
     * @access  public
     * @return  string
     */
    function getElementClassName() {
      return $this->class;
    }

    /**
     * Returns the element's class name
     *
     * @access  public
     * @return  &lang.XPClass
     */
    function &getElementClass() {
      return XPClass::forName($this->class);
    }
    
    /**
     * Tests if this list has no elements.
     *
     * @access  public
     * @return  bool
     */
    function isEmpty() {
      return empty($this->list);
    }
    
    /**
     * Adds an element to this list
     *
     * @access  public
     * @param   &lang.Object element
     * @return  &lang.Object the added element
     * @throws  lang.IllegalArgumentException
     */
    function &add(&$element) {
      if (!is_a($element, $this->_name)) {
        return throw(new IllegalArgumentException(sprintf(
          'Element is not a %s (but %s)',
          $this->class,
          xp::typeOf($element)
        )));
      }
      $this->list[]= &$element;
      return $element;
    }

    /**
     * Adds an element to the beginning of this list
     *
     * @access  public
     * @param   &lang.Object element
     * @return  &lang.Object the prepended element
     * @throws  lang.IllegalArgumentException
     */
    function &prepend(&$element) {
      if (!is_a($element, $this->_name)) {
        return throw(new IllegalArgumentException(sprintf(
          'Element is not a %s (but %s)',
          $this->class,
          xp::typeOf($element)
        )));
      }
      array_unshift($this->list, $element);
      return $element;
    }

    /**
     * Adds an array of elements to this list
     *
     * @access  public
     * @param   lang.Object[] array
     * @throws  lang.IllegalArgumentException
     */
    function addAll($array) {
      for ($i= 0, $s= sizeof($array); $i < $s; $i++) {
        if (!is_a($array[$i], $this->_name)) {
          return throw(new IllegalArgumentException(sprintf(
            'Element %d is not a %s (but %s)',
            $i,
            $this->class,
            xp::typeOf($array[$i])
          )));
        }
        $this->list[]= &$array[$i];
      }
    }

    /**
     * Prepend an array of elements to this list
     *
     * @access  public
     * @param   lang.Object[] array
     * @throws  lang.IllegalArgumentException
     */
    function prependAll($array) {
      for ($i= 0, $s= sizeof($array); $i < $s; $i++) {
        if (!is_a($array[$i], $this->_name)) {
          return throw(new IllegalArgumentException(sprintf(
            'Element %d is not a %s (but %s)',
            $i,
            $this->class,
            xp::typeOf($array[$i])
          )));
        }
        array_unshift($this->list, $array[$i]);
      }
    }

    /**
     * Replaces the element at the specified position in this list with 
     * the specified element.
     *
     * @access  public
     * @param   int index
     * @param   &lang.Object element
     * @return  &lang.Object the element previously at the specified position.
     */
    function &set($index, &$element) {
      $orig= &$this->list[$index];
      $this->list[$index]= &$element;
      return $orig;
    }
        
    /**
     * Returns the element at the specified position in this list.
     *
     * @access  public
     * @param   int index
     * @return  &lang.Object
     */
    function &get($index) {
      return $this->list[$index];
    }
    
    /**
     * Removes the element at the specified position in this list.
     * Shifts any subsequent elements to the left (subtracts one 
     * from their indices).
     *
     * @access  public
     * @param   int index
     * @return  &lang.Object the element that was removed from the list
     */
    function &remove($index) {
      $element= &$this->list[$index];
      unset($this->list[$index]);
      $this->list= array_values($this->list);
      return $element;
    }
    
    /**
     * Removes all of the elements from this list. The list will be empty 
     * after this call returns.
     *
     * @access  public
     */
    function clear() {
      $this->list= array();
    }
    
    /**
     * Returns an array of this list's elements
     *
     * @access  public
     * @return  lang.Object[]
     */
    function values() {
      return array_values($this->list);
    }
    
    /**
     * Checks if a value exists in this array
     *
     * @access  public
     * @param   &lang.Object element
     * @return  bool
     */
    function contains(&$element) {
      return in_array($element, $this->list, TRUE);
    }
    
    /**
     * Searches for the first occurence of the given argument
     *
     * @access  public
     * @param   &lang.Object element
     * @return  int offset where the element was found or FALSE
     */
    function indexOf(&$element) {
      return array_search($element, $this->list, TRUE);
    }

    /**
     * Searches for the last occurence of the given argument
     *
     * @access  public
     * @param   &lang.Object element
     * @return  int offset where the element was found or FALSE
     */
    function lastIndexOf(&$element) {
      return array_search($element, array_reverse($this->list), TRUE);
    }
  }
?>
