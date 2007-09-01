<?php
/* This class is part of the XP framework
 *
 * $Id: StreamTokenizerTest.class.php 10307 2007-05-08 21:15:27Z friebe $ 
 */

  namespace net::xp_framework::unittest::text;

  ::uses(
    'net.xp_framework.unittest.text.TokenizerTest',
    'io.streams.MemoryInputStream',
    'text.StreamTokenizer'
  );

  /**
   * Test string tokenizing.
   *
   * @see      xp://text.StringTokenizer
   * @purpose  TestCase
   */
  class StreamTokenizerTest extends TokenizerTest {
  
    /**
     * Retrieve a tokenizer instance
     *
     * @param   string source
     * @param   string delimiters default ' '
     * @param   bool returnDelims default FALSE
     * @return  text.Tokenizer
     */
    protected function tokenizerInstance($source, $delimiters= ' ', $returnDelims= FALSE) {
      return new text::StreamTokenizer(new io::streams::MemoryInputStream($source), $delimiters, $returnDelims);
    }
  }
?>
