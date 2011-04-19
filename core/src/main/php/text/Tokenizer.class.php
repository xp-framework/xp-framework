<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * A tokenizer splits input strings into tokens.
   * 
   * @see      http://news.xp-framework.net/article/186/2007/05/11/
   * @see      xp://text.StringTokenizer
   * @see      xp://text.StreamTokenizer
   * @see      php://strtok
   * @purpose  Abstract base class
   */
  abstract class Tokenizer extends Object implements IteratorAggregate {
    public 
      $delimiters   = '',
      $returnDelims = FALSE;
    
    protected
      $iterator     = NULL,
      $source       = NULL;
    
    /**
     * Constructor
     *
     * @param   var source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     */
    public function __construct($source, $delimiters= ' ', $returnDelims= FALSE) {
      $this->delimiters= $delimiters;
      $this->returnDelims= $returnDelims;
      $this->source= $source;
      $this->reset();
    }
    
    /**
     * Returns an iterator for use in foreach()
     *
     * @see     php://language.oop5.iterations
     * @return  php.Iterator
     */
    public function getIterator() {
      if (!$this->iterator) $this->iterator= newinstance('Iterator', array($this), '{
        private $i, $t, $r;
        public function __construct($r) { $this->r= $r; }
        public function current() { return $this->r->nextToken(); }
        public function key() { return $this->i; }
        public function next() { $this->i++; }
        public function rewind() { $this->r->reset(); }
        public function valid() { return $this->r->hasMoreTokens(); }
      }');
      return $this->iterator;
    }

    /**
     * Push back a string
     *
     * @param   string str
     */
    public abstract function pushBack($str);
    
    /**
     * Reset this tokenizer
     *
     */
    public abstract function reset();
    
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public abstract function hasMoreTokens();
    
    /**
     * Returns the next token from this tokenizer's string
     *
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public abstract function nextToken($delimiters= NULL);
  }
?>
