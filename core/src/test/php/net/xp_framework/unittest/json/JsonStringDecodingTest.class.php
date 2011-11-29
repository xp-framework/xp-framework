<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.json.JsonDecodingTest');

  /**
   * Testcase for JsonDecoder which decodes strings
   *
   * @see   xp://webservices.json.JsonDecoder
   */
  class JsonStringDecodingTest extends JsonDecodingTest {

    /**
     * Returns decoded input
     *
     * @param   string input
     * @return  var
     */
    protected function decode($input) {
      return $this->fixture->decode($input);
    }
  }
?>
