<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A string tokenizer allows you to break a string into tokens,
   * these being delimited by any character in the delimiter.
   * 
   * Example:
   * <code>
   *   $st= new StringTokenizer("Hello World!\nThis is an example", " \n");
   *   while ($st->hasMoreTokens()) {
   *     printf("- %s\n", $st->nextToken());
   *   }
   * </code>
   *
   * This would output:
   * <pre>
   *   - Hello
   *   - World!
   *   - This
   *   - is
   *   - an
   *   - example
   * </pre>
   *
   * @see php://strtok
   */
  class StringTokenizer extends Object {
    public
      $delim,
      $tok;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     * @param   string delim default ' '
     */
    public function __construct($str, $delim= ' ') {
      $this->delim= $delim;
      $this->tok= strtok($str, $this->delim);
      
    }
    
    /**
     * Tests if there are more tokens available
     *
     * @access  public
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return ($this->tok !== FALSE);
    }
    
    /**
     * Returns the next token from this tokenizer's string
     *
     * @access  public
     * @return  string next token
     */
    public function nextToken() {
      $tok= $this->tok;
      $this->tok= strtok($this->delim);
      return $tok;
    }
  }
?>
