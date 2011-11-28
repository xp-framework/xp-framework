<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.json.JsonDecodingTest',
    'io.streams.MemoryInputStream'
  );

  /**
   * Testcase for JsonDecoder which decodes streams
   *
   * @see   xp://webservices.json.JsonDecoder
   */
  class JsonStreamDecodingTest extends JsonDecodingTest {

    /**
     * Returns decoded input
     *
     * @param   string input
     * @return  var
     */
    protected function decode($input) {
      return $this->fixture->decodeFrom(new MemoryInputStream($input));
    }
  }
?>
