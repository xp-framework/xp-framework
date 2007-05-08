<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * A tokenizer splits input strings into tokens.
   * 
   * @see      xp://text.StringTokenizer
   * @see      xp://text.StreamTokenizer
   * @see      php://strtok
   * @purpose  Abstract base class
   */
  abstract class Tokenizer extends Object {
    public 
      $delimiters   = '',
      $returnDelims = FALSE;
    
    protected
      $source       = NULL;
    
    /**
     * Constructor
     *
     * @param   mixed source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     */
    public function __construct($source, $delimiters= ' ', $returnDelims= FALSE) {
      $this->delimiters= $delimiters;
      $this->returnDelims= $returnDelims;
      $this->source= $source;
    }
    
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
