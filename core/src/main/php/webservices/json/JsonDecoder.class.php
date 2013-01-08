<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'lang.types.String',
    'lang.types.Character',
    'io.streams.MemoryOutputStream',
    'io.streams.OutputStream',
    'io.streams.InputStream',
    'text.StreamTokenizer',
    'text.StringTokenizer',
    'webservices.json.JsonException',
    'webservices.json.IJsonDecoder',
    'webservices.json.JsonParser',
    'webservices.json.JsonLexer'
  );

  /**
   * JSON decoder and encoder
   *
   * Used for converting JSON into PHP and PHP into JSON.
   *
   * Usage
   * =====
   * <code>
   *   uses('webservices.json.JsonDecoder');
   *
   *   $decoder= new JsonDecoder();
   *
   *   $phpData= $decoder->deocode($jsonString);
   *   $jsonString= $decoder->encode($phpData);
   *
   * </code>
   *
   * @test      xp://net.xp_framework.unittest.json.JsonStreamDecodingTest
   * @test      xp://net.xp_framework.unittest.json.JsonStringDecodingTest
   * @test      xp://net.xp_framewort.unittest.json.JsonEncodingTest
   * @see       http://json.org
   * @purpose   JSON en- and decoder
   */
  class JsonDecoder extends Object implements IJsonDecoder {
    protected static $parser= NULL;
  
    static function __static() {
      self::$parser= new JsonParser();
   }

    /**
     * Encode PHP data into JSON
     *
     * encode() gets PHP data and converts this into a JSON string.
     *
     *   <ul>
     *     <li>
     *       If you put in a boolean or NULL, you will retrieve a string within the
     *       name of this boolean.
     *       <code>
     *         $decoder->encode(TRUE); // Will return 'true'
     *       </code>
     *     </li>
     *     <li>
     *       If you put in an integer or a float, you will retrieve a string representation
     *       of this number.
     *       <code>
     *         $decoder->encode(10); // Will return '10'
     *         $decoder->encode(0.1); // Will return '0.1'
     *         $decoder->encode(0.000001); // Will return '1.0E-6', also valid JSON
     *       </code>
     *     </li>
     *     <li>
     *       If you put in a string, you will retrieve this string.
     *     </li>
     *     <li>
     *       If you put in an array, the value you return depends on the array. If
     *       it has only numeric keys (from 0 up to n-1), you will retrieve a JSON
     *       array.
     *       <code>
     *         $decode->encode(array('foo', 'bar')); // Will return '[ "foo" , "bar" ]'
     *       </code>
     *       If the array has string keys or disordered numeric keys, you will retrieve
     *       a JSON object.
     *       <code>
     *         $decode->encode(array('foo1' => 'bar1', 'foo2' => 'bar2'));
     *         // Will return '{ "foo1" : "bar1" , "foo2" : "bar2" }'
     *       <code>
     *     </li>
     *     <li>
     *       If you put in a PHP object, it will be serialized and you will retrieve
     *       it as a JSON object
     *     </li>
     *   </ul>
     *
     * @param   var data
     * @return  string
     * @throws  webservices.json.JsonException
     */
    public function encode($data) {
      $stream= new MemoryOutputStream();
      $this->encodeTo($data, $stream);
      return $stream->getBytes();
    }
    
    /**
     * Escapes escape sequences inside string
     *
     * @param   string in utf8-encoded string
     * @return  string
     */
    protected function escape($in) {
      static $ctrl= array(
        34 => '\\"', 
        92 => '\\\\', 
        47 => '\\/', 
         8 => '\\b',
        12 => '\\f', 
        10 => '\\n', 
        13 => '\\r', 
         9 => '\\t'
      );
      
      $out= '';
      for ($i= 0, $s= strlen($in); $i < $s; $i++) {
        $c= ord($in{$i});
        if (isset($ctrl[$c])) {
          $out.= $ctrl[$c];
        } else if ($c < 0x20) {
          $out.= sprintf('\u%04x', $c);
        } else if ($c < 0x80) {
          $out.= $in{$i};
        } else if ($c < 0xE0) {
          $out.= sprintf('\u%04x', (($c & 0x1F) << 6) | (ord($in{$i+ 1}) & 0x3F));
          $i+= 1;
        } else if ($c < 0xF0) {
          $out.= sprintf('\u%04x', (($c & 0x0F) << 12) | ((ord($in{$i+ 1}) & 0x3F) << 6) | (ord($in{$i+ 2}) & 0x3F));
          $i+= 2;
        } else if ($c < 0xF5) {
          $out.= sprintf('\u%04x', (($c & 0x07) << 18) | ((ord($in{$i+ 1}) & 0x0F) << 12) | ((ord($in{$i+ 2}) & 0x3F) << 6) | (ord($in{$i+ 3}) & 0x3F));
          $i+= 3;
        }
      }
      
      return $out;
    }

    /**
     * Encode PHP data into JSON via stream
     *
     * Gets the PHP data and a stream.<br/>
     * It converts the data to JSON and will put every atom into the stream as soon
     * as it is available.<br/>
     * The usage is similar to encode() except the second argument.
     *
     * @param   var data
     * @param   io.streams.OutputStream stream
     * @return  boolean
     * @throws  webservices.json.JsonException if the data could not be serialized
     */
    public function encodeTo($data, OutputStream $stream) {
      switch (gettype($data)) {
        case 'string': {
          $stream->write('"'.$this->escape(iconv(xp::ENCODING, 'utf-8', $data)).'"');
          return TRUE;
        }
        case 'integer': {
          $stream->write(strval($data));
          return TRUE;
        }
        case 'double': {
          $stream->write(strval($data));
          return TRUE;
        }
        case 'boolean': {
          $stream->write(($data ? 'true' : 'false'));
          return TRUE;
        }
        case 'NULL': {
          $stream->write('null');
          return TRUE;
        }
        
        case 'array': {
          if ($this->_isVector($data)) {
            // Bail out early on bordercase
            if (0 == sizeof($data)) {
              $stream->write('[ ]');
              return TRUE;
           };

            $stream->write('[ ');
            // Get first element
            $stream->write($this->encode(array_shift($data)));
            foreach ($data as $value) {
              $stream->write(' , '.$this->encode($value));
            }

            $stream->write(' ]');
            return TRUE;
          } else {
            // Reset array internal pointer
            reset($data);

            $stream->write('{ ');

            $value= each($data);
            $stream->write($this->encode(
              (string)$value['key']).' : '.$this->encode($value['value']
            ));
            while ($value= each($data)) {
              $stream->write(
                ' , '.$this->encode((string)$value['key']).' : '.$this->encode($value['value'])
              );
            }

            $stream->write(' }');
            return TRUE;
          }
        }

        case 'object': {
          // Converts a string object into an normal json string
          if ($data instanceof String) {
            $stream->write('"'.$this->escape((string)$data->getBytes('utf-8')).'"');
            break;
          }

          if ($data instanceof Generic) {
          
            if (!method_exists($data, '__sleep')) {
              $vars= get_object_vars($data);
            } else {
              $vars= array();
              foreach ($data->__sleep() as $var) $vars[$var]= $data->{$var};
            }
            
            $this->encodeTo($vars, $stream);
            return TRUE;
          }
          
          // Break missing intentially
        }
        
        default: {
          throw new JsonException('Cannot encode data of type '.gettype($data));
        }
      }
    }
    
    /**
     * Parse a source and return the value
     *
     * @param   text.Tokenizer source
     * @param   string targetEncoding defaults to XP default encoding
     * @return  var
     * @throws  webservices.json.JsonException
     */
    protected function parse($source, $targetEncoding= xp::ENCODING) {
      try {
        self::$parser->setTargetEncoding($targetEncoding);
        return self::$parser->parse(new JsonLexer($source));
      } catch (ParseException $e) {
        throw new JsonException($e->getMessage(), $e);
      }
    }
    
    /**
     * Decode a string into a PHP data structure
     *
     * Converts a string into PHP data structures, if the given string is valid JSON.<br/>
     * You will find a description of valid JSON on json.org.<br/>
     * See the list below to find out the corresponding PHP data types.
     *
     * <ul>
     *   <li>
     *     If you put in a JSON boolean or 'null', you will retrieve its PHP value.
     *     <code>
     *       $decoder->decode('true'); // Will return TRUE
     *     </code>
     *   </li>
     *   <li>
     *     If you put in a number, you will retrieve an integer or float, depending
     *     on its value.
     *     <code>
     *         $decoder->decode('10'); // Will return 10 (integer)
     *         $decoder->decode('0.1'); // Will return 0.1 (float)
     *     </code>
     *   </li>
     *   <li>
     *     If you put in a simple string, you will retrieve this string.
     *   </li>
     *   <li>
     *     If you put in a JSON array, you will retrieve a normal PHP array.
     *     <code>
     *       $decode->decode('[ "foo" , "bar" ]'); // Will return array('foo', 'bar')
     *     </code>
     *   </li>
     *   <li>
     *     If you put in an JSON object, you will return a PHP hash map.
     *     <code>
     *       $decode->decode('{ "foo1" : "bar1" , "foo2" : "bar2" }');
     *       // Will return array('foo1' => 'bar1', 'foo2' => 'bar2')
     *     </code>
     *   </li>
     *   <li>
     *     If you put in a serialized PHP object, you will return this as PHP object
     *     as it is given.
     *   </li>
     * </ul>
     *
     * @param   string string
     * @param   string targetEncoding defaults to XP default encoding
     * @return  var
     * @throws  webservices.json.JsonException
     */
    public function decode($string, $targetEncoding= xp::ENCODING) {
      return $this->parse(new StringTokenizer($string), $targetEncoding);
    }

    /**
     * Decode a stream of JSON data into PHP data
     *
     * @param   io.streams.InputStream stream
     * @param   string targetEncoding defaults to XP default encoding
     * @return  var
     * @throws  webservices.json.JsonException
     */
    public function decodeFrom(InputStream $stream, $targetEncoding= xp::ENCODING) {
      return $this->parse(new StreamTokenizer($stream), $targetEncoding);
    }
    
    /**
     * Checks whether an array is a numerically indexed array
     * (a vector) or a key/value hashmap.
     *
     * @param   array data
     * @return  bool
     */
    protected function _isVector($data) {
      $start= 0;
      foreach (array_keys($data) as $key) {
        if ($key !== $start++) return FALSE;
      }
      
      return TRUE;
    }
  } 
?>
