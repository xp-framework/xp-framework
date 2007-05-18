<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.Tokenizer');

  /**
   * Tokenizer / file reader bridge
   *
   * @purpose  Tokenizer
   */
  class ChunkTokenizer extends Tokenizer {

    /**
     * Reset this tokenizer
     *
     */
    public function reset() {
      fseek($this->source, 0, SEEK_SET);
    }
  
    /**
     * Tests if there are more tokens available
     *
     * @return  bool more tokens
     */
    public function nextToken($delimiters= NULL) {
      return fread($this->source, 8192);
    }
  
    /**
     * Returns the next token from this tokenizer's string
     *
     * @param   bool delimiters default NULL
     * @return  string next token
     */
    public function hasMoreTokens() {
      return !feof($this->source);
    }
  }
?>
