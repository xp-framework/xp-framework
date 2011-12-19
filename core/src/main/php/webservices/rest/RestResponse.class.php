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
    protected $content= '';
    protected $headers= array();
    protected $type= NULL;
    protected $input= NULL;

    /**
     * Creates a new response
     *
     * @param   int status
     * @param   string content
     * @param   [:string[]] headers
     * @param   lang.Type type
     * @param   io.streams.InputStream input
     */
    public function __construct($status, $content, $headers, $type, $input) {
      $this->status= $status;
      $this->content= $content;
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
     * Calculate variants of a given name
     *
     * @param   string name
     * @return  string[] names
     */
    protected function variantsOf($name) {
      $variants= array($name);
      $chunks= explode('_', $name);
      if (sizeof($chunks) > 1) {      // product_id => productId
        $variants[]= array_shift($chunks).implode(array_map('ucfirst', $chunks));
      }
      return $variants;
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
                  $method->invoke($return, $this->convert($param->getType(), $value)); 
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
     */
    public function data() {
      $header= substr($this->content, 0, strcspn($this->content, ';'));
      switch ($header) {
        case 'application/json':
          return $this->convert($this->type, JsonFactory::create()->decode(Streams::readAll($this->input)));
        
        case 'text/xml':
          $tree= new Tree();
          create(new XMLParser())->withCallback($tree)->parse(new StreamInputSource($this->input));
          return $this->convert($this->type, new RestXmlMap($tree->root));

        default:
          throw new IllegalArgumentException('Unknown content type "'.$header.'"');
      }
    }
  }
?>
