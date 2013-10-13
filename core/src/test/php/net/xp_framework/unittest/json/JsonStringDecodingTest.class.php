<?php namespace net\xp_framework\unittest\json;



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
  protected function decode($input, $targetEncoding= 'iso-8859-1') {
    return $this->fixture->decode($input, $targetEncoding);
  }
}
