<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * JSON decoder and encoder
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class JsonDecoder extends Object {
  
    /**
     * Encode PHP data into 
     *
     * @access  
     * @param   
     * @return  
     */
    function encode($data) {
      static $controlChars= array(
        '"' => '\\"', 
        '\\'  => '\\\\', 
        '/'   => '\\/', 
        "\b"  => '\\b',
        "\f"  => '\\f', 
        "\n"  => '\\n', 
        "\r"  => '\\r', 
        "\t"  => '\\t'
      );
      Console::writeLine(gettype($data));
      switch (gettype($data)) {
        case 'string': {
          return '"'.strtr($data, $controlChars).'"';
        }
        case 'integer': {
          return (string)$data;
        }
        case 'double': {
          return strval($data);
        }
        case 'boolean': {
          return ($data ? 'true' : 'false');
        }
        case 'NULL': {
          return 'null';
        }
        
        case 'array': {
          $ret= '[ ';
          foreach ($data as $value) {
            $ret.= $this->encode($value).' , ';
          }
          
          return substr($ret, 0, -2).']';
        }
        
        case 'object': {
          $ret= '{ ';
          foreach (get_object_vars($data) as $key => $value) {
            $ret.= $this->encode((string)$key).' : '.$this->encode($value).' , ';
          }
          
          return substr($ret, 0, -2).'}';
        }
        
        default: {
          return throw(new IllegalArgumentException('Cannot encode data of type '.gettype($data)));
        }
      }
    }
    
    /**
     * Decode a string into a PHP data structure
     *
     * @access  public
     * @param   string string
     * @return  mixed
     */
    function decode($string) {
      // XXX TBI
    }
  } implements(__FILE__, 'org.json.IJsonDecoder');
?>
