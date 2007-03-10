<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.XPIterator');

  /**
   * Iterates on all classes using an IOCollectionIterator
   *
   * @purpose  Iterator  
   */
  class AllClassesIterator extends Object implements XPIterator {
    var
      $aggregate = NULL,
      $classpath = '',
      $root      = NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   io.collections.iterate.IOCollectionIterator aggregate
     * @param   string classpath
     */
    function __construct($aggregate, $classpath) {
      $this->aggregate= $aggregate;
      $this->classpath= array_flip(array_map('realpath', explode(PATH_SEPARATOR, $classpath)));
    }

    /**
     * Helper method. Infers class name from an IOElement
     *
     * @access  protected
     * @param   io.IOElement element
     * @return  string
     * @throws  lang.IllegalArgumentException in case class name cannot be inferred
     */
    function classNameForElement($element) {
      $uri= realpath($element->getURI());
      $path= dirname($uri);

      while (FALSE !== ($pos= strrpos($path, DIRECTORY_SEPARATOR))) { 
        if (isset($this->classpath[$path])) {
          return strtr(substr($uri, strlen($path)+ 1, -10), DIRECTORY_SEPARATOR, '.'); 
        }

        $path= substr($path, 0, $pos); 
      }

      throw new IllegalArgumentException('Cannot infer classname from '.$element->toString());
    }
    
  
    /**
     * Returns true if the iteration has more elements. (In other words, 
     * returns true if next would return an element rather than throwing 
     * an exception.)
     *
     * @access  public
     * @return  bool
     */
    function hasNext() {
      return $this->aggregate->hasNext();
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @access  public
     * @return  mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    function next() {
      return $this->root->classNamed($this->classNameForElement($this->aggregate->next()));
    }
  }
?>
