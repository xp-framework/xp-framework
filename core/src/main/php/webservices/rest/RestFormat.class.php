<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Enum',
    'webservices.rest.RestJsonSerializer',
    'webservices.rest.RestJsonDeserializer',
    'webservices.rest.RestXmlSerializer',
    'webservices.rest.RestXmlDeserializer',
    'webservices.rest.RestFormDeserializer',
    'io.streams.InputStream',
    'io.streams.OutputStream'
  );

  /**
   * Rest format
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestFormatTest
   */
  class RestFormat extends Enum {
    public static $UNKNOWN;
    public static $JSON;
    public static $XML;
    public static $FORM;

    private $serializer, $deserializer;

    static function __static() {
      self::$UNKNOWN= new self(0, 'UNKNOWN', xp::null(), xp::null());
      self::$JSON= new self(1, 'JSON', new RestJsonSerializer(), new RestJsonDeserializer());
      self::$XML= new self(2, 'XML', new RestXmlSerializer(), new RestXmlDeserializer());
      self::$FORM= new self(3, 'FORM', xp::null(), new RestFormDeserializer());
    }

    /**
     * Constructor
     *
     * @param  int ordinal
     * @param  string name
     * @param  webservices.rest.RestSerializer serializer
     * @param  webservices.rest.RestDeserializer deserializer
     */
    public function __construct($ordinal, $name, $serializer, $deserializer) {
      parent::__construct($ordinal, $name);
      $this->serializer= $serializer;
      $this->deserializer= $deserializer;
    }

    /**
     * Get serializer
     *
     * @return webservices.rest.RestSerializer
     */
    public function serializer() {
      return $this->serializer;
    }

    /**
     * Get deserializer
     *
     * @return webservices.rest.RestDeserializer
     */
    public function deserializer() {
      return $this->deserializer;
    }

    /**
     * Deserialize from input
     *
     * @param  io.streams.InputStream in
     * @param  lang.Type type
     * @return var
     */
    public function read(InputStream $in, $type) {
      return $this->deserializer->deserialize($in, $type);
    }

    /**
     * Serialize and write to output
     *
     * @param  io.streams.OutputStream out
     * @param  webservices.rest.Payload value
     */
    public function write(OutputStream $out, Payload $value= NULL) {
      $out->write($this->serializer->serialize($value));
    }

    /**
     * Get format for a given mediatype
     *
     * @param  string mediatype
     * @return self
     */
    public static function forMediaType($mediatype) {
      if ('application/x-www-form-urlencoded' === $mediatype) {
        return self::$FORM;
      } else if ('text/x-json' === $mediatype || 'text/javascript' === $mediatype || preg_match('#[/\+]json$#', $mediatype)) {
        return self::$JSON;
      } else if (preg_match('#[/\+]xml$#', $mediatype)) {
        return self::$XML;
      } else {
        return self::$UNKNOWN;
      }
    }
  }
?>
