<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.RestDeserializer', 'text.StreamTokenizer');

  /**
   * A deserializer
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestFormDeserializerTest
   */
  class RestFormDeserializer extends RestDeserializer {

    /**
     * Serialize
     *
     * @param   io.streams.InputStream in
     * @param   lang.Type target
     * @return  var
     * @throws  lang.FormatException
     */
    public function deserialize($in, $target) {
      $st= new StreamTokenizer($in, '&');
      $map= array();
      while ($st->hasMoreTokens()) {
        $key= $value= NULL;
        if (2 !== sscanf($t= $st->nextToken(), "%[^=]=%[^\r]", $key, $value)) {
          throw new FormatException('Malformed pair "'.$t.'"');
        }
        $key= urldecode($key);
        if (substr_count($key, '[') !== substr_count($key, ']')) {
          throw new FormatException('Unbalanced [] in query string');
        }
        if ($start= strpos($key, '[')) {    // Array notation
          $base= substr($key, 0, $start);
          isset($map[$base]) || $map[$base]= array();
          $ptr= &$map[$base];
          $offset= 0;
          do {
            $end= strpos($key, ']', $offset);
            if ($start === $end- 1) {
              $ptr= &$ptr[];
            } else {
              $end+= substr_count($key, '[', $start+ 1, $end- $start- 1);
              $ptr= &$ptr[substr($key, $start+ 1, $end- $start- 1)];
            }
            $offset= $end+ 1;
          } while ($start= strpos($key, '[', $offset));
          $ptr= urldecode($value);
        } else {
          $map[$key]= urldecode($value);
        }
      }
      return $this->convert($target, $map);
    }
  }
?>
