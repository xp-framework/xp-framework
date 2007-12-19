<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.text.AbstractTokenizerTest',
    'io.streams.MemoryInputStream',
    'text.StreamTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.StringTokenizer
   * @purpose  TestCase
   */
  class StreamTokenizerTest extends AbstractTokenizerTest {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE) {
      return new StreamTokenizer(new MemoryInputStream($source), $delimiters, $returnDelims);
    }
  }
?>
