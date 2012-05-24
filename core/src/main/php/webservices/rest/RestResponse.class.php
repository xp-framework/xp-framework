<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.Streams', 
    'io.streams.MemoryInputStream',
    'webservices.json.JsonFactory',
    'xml.Tree',
    'xml.parser.XMLParser',
    'xml.parser.StreamInputSource',
    'webservices.rest.RestXmlMap'
  );

  /**
   * A REST response
   *
   * @test    xp://net.xp_framework.unittest.webservices.rest.RestResponseTest
   */
  class RestResponse extends Object {
    protected $status= -1;
    protected $message= '';
    protected $deserializer= NULL;
    protected $headers= array();
    protected $type= NULL;
    protected $input= NULL;

    /**
     * Creates a new response
     *
     * @param   int status
     * @param   string message
     * @param   webservices.rest.Deserializer deserializer
     * @param   [:string[]] headers
     * @param   lang.Type type
     * @param   io.streams.InputStream input
     */
    public function __construct($status, $message, $deserializer, $headers, $type, $input) {
      $this->status= $status;
      $this->message= $message;
      $this->deserializer= $deserializer;
      $this->headers= $headers;
      $this->type= $type;
      $this->input= $input;
    }

    /**
     * Get status code
     *
     * @return  int
     */
    public function status() {
      return $this->status;
    }

    /**
     * Get status message
     *
     * @return  string
     */
    public function message() {
      return $this->message;
    }
    
    /**
     * Convert data based on type
     *
     * @param   lang.Type type
     * @param   [:var] data
     * @return  var
     */
    public function convert($type, $data) {
      if (NULL === $type || $type->equals(Type::$VAR)) {  // No conversion
        return $data;
      } else if (NULL === $data) {                        // Valid for any type
        return NULL;
      } else if ($type->equals(XPClass::forName('util.Date'))) {
        return $type->newInstance($data);
      } else if ($type instanceof XPClass) {              // Conversion to a class
        $return= $type->newInstance();
        foreach ($data as $name => $value) {
          foreach ($this->variantsOf($name) as $variant) {
            if ($type->hasField($variant)) {
              $field= $type->getField($variant);
              if ($field->getModifiers() & MODIFIER_PUBLIC) {
                if (NULL !== ($fType= $field->getType())) {
                  $field->set($return, $this->convert(Type::forName($fType), $value));
                } else {
                  $field->set($return, $value);
                }
                continue 2;
              }
            }
            if ($type->hasMethod('set'.$variant)) {
              $method= $type->getMethod('set'.$variant);
              if ($method->getModifiers() & MODIFIER_PUBLIC) {
                if (NULL !== ($param= $method->getParameter(0))) {
                  $method->invoke($return, array($this->convert($param->getType(), $value)));
                } else {
                  $method->invoke($return, array($value));
                }
                continue 2;
              }
            }
          }
        }
        return $return;
      } else if ($type instanceof ArrayType) {
        $return= array();
        foreach ($data as $element) {
          $return[]= $this->convert($type->componentType(), $element);
        }
        return $return;
      } else if ($type instanceof MapType) {
        $return= array();
        foreach ($data as $key => $element) {
          $return[$key]= $this->convert($type->componentType(), $element);
        }
        return $return;
      } else if ($type->equals(Primitive::$STRING)) {
        return (string)$data;
      } else if ($type->equals(Primitive::$INT)) {
        return (int)$data;
      } else if ($type->equals(Primitive::$DOUBLE)) {
        return (double)$data;
      } else if ($type->equals(Primitive::$BOOL)) {
        return (bool)$data;
      } else {
        throw new FormatException('Cannot convert to '.xp::stringOf($type));
      }
    }

    /**
     * Get data
     *
     * @return  var
     */
    public function content() {
      return Streams::readAll($this->input);
    }
    
    /**
     * Copy data
     *
     * @return  var
     */
    public function contentCopy() {
      $data= $this->content();

      // Reassign input, so code relying on the stream delivering bytes
      // can still read them.
      $this->input= new MemoryInputStream($data);
      return $data;
    }

    /**
     * Get data
     *
     * @return  var
     * @throws  webservices.rest.RestException if the status code is > 399
     */
    public function data() {
      if ($this->status > 399) {
        throw new RestException($this->status.': '.$this->message);
      }
 
      if (NULL === $this->deserializer) {
        throw new IllegalArgumentException('Unknown content type "'.$this->headers['Content-Type'][0].'"');
      }

      return $this->deserializer->deserialize($this->input, $this->type);
    }
  }
?>
