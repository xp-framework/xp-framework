<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.Tokenizer');
 
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
   * @test     xp://net.xp_framework.unittest.text.StringTokenizerTest
   * @see      xp://text.Tokenizer
   * @purpose  Tokenizer implementation
   */
  class StringTokenizer extends Tokenizer {
    protected
      $_stack = array(),
      $_buf   = '';

    /**
     * Reset this tokenizer
     *
     */
    public function reset() {
      $this->_stack= array();
      $this->_buf= $this->source;
    }
    
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return (!empty($this->_stack) || strlen($this->_buf) > 0);
    }

    /**
     * Push back a string
     *
     * @param   string str
     */
    public function pushBack($str) {
      $this->_buf= $str.implode('', $this->_stack).$this->_buf;
      $this->_stack= array();
    }
        
    /**
     * Returns the next token from this tokenizer's string
     *
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public function nextToken($delimiters= NULL) {
      if (empty($this->_stack)) {
        $offset= strcspn($this->_buf, $delimiters ? $delimiters : $this->delimiters);
        if (!$this->returnDelims || $offset > 0) $this->_stack[]= substr($this->_buf, 0, $offset);
        if ($this->returnDelims && $offset < strlen($this->_buf)) {
          $this->_stack[]= $this->_buf{$offset};
        }
        $this->_buf= substr($this->_buf, $offset+ 1);
      }
      return array_shift($this->_stack);
    }
  }
?>
