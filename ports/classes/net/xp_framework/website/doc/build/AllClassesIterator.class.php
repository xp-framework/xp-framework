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
    protected
      $aggregate = NULL,
      $classpath = '';
    
    public
      $root      = NULL;
  
    /**
     * Constructor
     *
     * @param   io.collections.iterate.IOCollectionIterator aggregate
     * @param   string classpath
     */
    public function __construct($aggregate, $classpath) {
      $this->aggregate= $aggregate;
      $this->classpath= array_flip(array_map('realpath', $classpath));
    }

    /**
     * Helper method. Infers class name from an IOElement
     *
     * @param   io.IOElement element
     * @return  string
     * @throws  lang.IllegalArgumentException in case class name cannot be inferred
     */
    protected function classNameForElement($element) {
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
     * @return  bool
     */
    public function hasNext() {
      return $this->aggregate->hasNext();
    }
    
    /**
     * Returns the next element in the iteration.
     *
     * @return  mixed
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      return $this->root->classNamed($this->classNameForElement($this->aggregate->next()));
    }
  }
?>
