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
      $_stack = array();
    
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public function hasMoreTokens() {
      return (!empty($this->_stack) || strlen($this->source) > 0);
    }
    
    /**
     * Returns the next token from this tokenizer's string
     *
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public function nextToken($delimiters= NULL) {
      if (empty($this->_stack)) {
        $offset= strcspn($this->source, $delimiters ? $delimiters : $this->delimiters);
        if (!$this->returnDelims || $offset > 0) $this->_stack[]= substr($this->source, 0, $offset);
        if ($this->returnDelims && $offset < strlen($this->source)) {
          $this->_stack[]= $this->source{$offset};
        }
        $this->source= substr($this->source, $offset+ 1);
      }
      return array_shift($this->_stack);
    }
  }
?>
