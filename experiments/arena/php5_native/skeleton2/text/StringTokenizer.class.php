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
   *   $st= &new StringTokenizer("Hello World!\nThis is an example", " \n");
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
   * @test  xp://net.xp_framework.unittest.text.StringTokenizerTest
   * @see   php://strtok
   */
  class StringTokenizer extends Object {
    public 
      $delimiters   = '',
      $returnDelims = FALSE;
    
    public
      $_str         = '',
      $_stack       = array();
    
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     * @param   string delimiter default ' '
     * @param   bool returnDelims default FALSE
     */
    public function __construct($str, $delimiters= ' ', $returnDelims= FALSE) {
      $this->delimiters= $delimiters;
      $this->returnDelims= $returnDelims;
      $this->_str= $str;
    }
    
    /**
     * Tests if there are more tokens available
     *
     * @access  public
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return (!empty($this->_stack) or strlen($this->_str) > 0);
    }
    
    /**
     * Returns the next token from this tokenizer's string
     *
     * @access  public
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public function nextToken($delimiters= NULL) {
      if (empty($this->_stack)) {
        $offset= strcspn($this->_str, $delimiters ? $delimiters : $this->delimiters);
        if (!$this->returnDelims || $offset > 0) $this->_stack[]= substr($this->_str, 0, $offset);
        if ($this->returnDelims && $offset < strlen($this->_str)) {
          $this->_stack[]= $this->_str{$offset};
        }
        $this->_str= substr($this->_str, $offset+ 1);
      }
      return array_shift($this->_stack);
    }
  }
?>
