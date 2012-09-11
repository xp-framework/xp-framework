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
    'webservices.rest.RestXmlDeserializer'
  );

  /**
   * Rest format
   *
   */
  class RestFormat extends Enum {
    public static $UNKNOWN;
    public static $JSON;
    public static $XML;
    public static $FORM;

    public $serializer;
    public $deserializer;

    static function __static() {
      self::$UNKNOWN= new self(0, 'UNKNOWN', xp::null(), xp::null());
      self::$JSON= new self(1, 'JSON', new RestJsonSerializer(), new RestJsonDeserializer());
      self::$XML= new self(2, 'XML', new RestXmlSerializer(), new RestXmlDeserializer());
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
     * Read the payload from the request
     *
     * @param  scriptlet.Request request
     * @param  lang.Type type
     * @return var
     */
    public function read($request, $type) {
      return $this->deserializer->deserialize(new MemoryInputStream($request->getData()), $type);
    }

    /**
     * Read the payload from the request
     *
     * @param  scriptlet.Response request
     * @param  var value
     */
    public function write($response, $value) {
      $response->write($this->serializer->serialize($value));
    }

    /**
     * Values method
     *
     * @return lang.Enum[]
     */
    public static function values() {
      return parent::membersOf(__CLASS__);
    }
  }
?>
