<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'lang.types.String',
    'lang.types.Character',
    'io.Stream',
    'text.StringTokenizer',
    'webservices.json.JsonException',
    'webservices.json.IJsonDecoder'
  );

  // Defines for the tokenizer

  /**
   * JSON decoder and encoder
   *
   * @test      xp://net.xp_framework.unittest.json.JsonDecoderTest
   * @see       http://json.org
   * @purpose   JSON en- and decoder
   */
  class JsonDecoder extends Object implements IJsonDecoder {
    const 
      T_LBRACE    = 0x0000,
      T_RBRACE    = 0x0001,
      T_LBRACKET  = 0x0002,
      T_RBRACKET  = 0x0003,
      T_COMMA     = 0x0004,
      T_COLON     = 0x0005,
      T_VALUE     = 0x1000;

    public
      $stream     = NULL;
    
    public
      $_tokenValue  = NULL;
  
    /**
     * Encode PHP data into 
     *
     * @param   mixed data
     * @return  string
     * @throws  webservices.json.JsonException if the data could not be serialized
     */
    public function encode($data) {
      static $controlChars= array(
        '"'   => '\\"', 
        '\\'  => '\\\\', 
        '/'   => '\\/', 
        "\b"  => '\\b',
        "\f"  => '\\f', 
        "\n"  => '\\n', 
        "\r"  => '\\r', 
        "\t"  => '\\t'
      );
      switch (gettype($data)) {
        case 'string': {
          return '"'.strtr(utf8_encode($data), $controlChars).'"';
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
        
        case 'object': {
          // Convert objects to arrays and store the classname with them as
          // suggested by JSON-RPC
          if ($data instanceof Generic) {
            if (!method_exists($data, '__sleep')) {
              $vars= get_object_vars($data);
            } else {
              $vars= array(
                'constructor' => '__construct()'
              );
              foreach ($data->__sleep() as $var) $vars[$var]= $data->{$var};
            }
            
            // __xpclass__ is an addition to the spec, I added to be able to pass the FQCN
            $data= array_merge(
              array(
                '__jsonclass__' => array('__construct()'),
                '__xpclass__'   => utf8_encode($data->getClassName())
              ),
              $vars
            );
          } else {
            $data= (array)$data;
          }
          
          // Break missing intentially
        }
        
        case 'array': {
          if ($this->_isVector($data)) {
            // Bail out early on bordercase
            if (0 == sizeof($data)) return '[ ]';

            $ret= '[ ';
            foreach ($data as $value) {
              $ret.= $this->encode($value).' , ';
            }

            return substr($ret, 0, -2).']';
          } else {
            $ret= '{ ';

            // Bail out early on bordercase
            if (0 == sizeof($data)) return '{ }';

            foreach ($data as $key => $value) {
              $ret.= $this->encode((string)$key).' : '.$this->encode($value).' , ';
            }

            return substr($ret, 0, -2).'}';
          }
        }
        
        default: {
          throw new JsonException('Cannot encode data of type '.gettype($data));
        }
      }
    }
    
    /**
     * Decode a string into a PHP data structure
     *
     * @param   string string
     * @return  mixed
     */
    public function decode($string) {
      $this->stream= new Stream();
      $this->stream->open(STREAM_MODE_READWRITE);
      $this->stream->write($string);
      $this->stream->rewind();
      
      switch ($this->_getNextToken()) {
        case self::T_LBRACKET: {
          return $this->_decodeArray();
        }
        
        case self::T_LBRACE: {
          return $this->_decodeObject();
        }
        
        case self::T_VALUE: {
          return $this->_getTokenValue();
        }
      }
    }
    
    /**
     * Decode an string into array structure
     *
     * @return  array
     */
    protected function _decodeArray() {
      $array= array();
      do {
        $token= $this->_getNextToken();
        switch ($token) {
          case self::T_LBRACKET: {
            $array[]= $this->_decodeArray();
            break;
          }
          case self::T_LBRACE: {
            $array[]= $this->_decodeObject();
            break;
          }
          case self::T_VALUE: {
            $array[]= $this->_getTokenValue();
            break;
          }
        }
      } while ($token != self::T_RBRACKET);
      return $array;
    }
    
    /**
     * Decode string into object structure
     *
     * @return  stdclass
     */
    protected function _decodeObject() {
      $array= array();
      do {
        $token= $this->_getNextToken();
        switch ($token) {
          case self::T_LBRACKET: {
            $array[$key]= $this->_decodeArray();
            unset($key);
            break;
          }
          case self::T_LBRACE: {
            $array[$key]= $this->_decodeObject();
            unset($key);
            break;
          }
          case self::T_VALUE: {
            if (empty($key)) {
              $key= $this->_getTokenValue();
            } else {
              $array[$key]= $this->_getTokenValue();
              unset($key);
            }
            break;
          }
        }
      } while ($token != self::T_RBRACE);

      // Introspect array to check if this is actually an object
      if (!empty($array['__jsonclass__']) && !empty($array['__xpclass__'])) {
        $inst= XPClass::forName($array['__xpclass__'])->newInstance();
        
        foreach ($array as $key => $value) {
          if (in_array($key, array('__jsonclass__', '__xpclass__'))) continue;
          $inst->{$key}= $value;
        }
        
        if (method_exists($inst, '__wakeup')) $inst->__wakeup();
              
        return $inst;
      }

      return $array;
    }
    
    /**
     * Fetch next token from stream
     *
     * @return  int
     */
    protected function _getNextToken() {
      if ($this->stream->eof()) return -1;
      $this->_trim();
      
      $token= $this->stream->read(1);
      
      switch ($token) {
        case '{': return self::T_LBRACE;
        case '}': return self::T_RBRACE;
        case '[': return self::T_LBRACKET;
        case ']': return self::T_RBRACKET;
        case ',': return self::T_COMMA;
        case ':': return self::T_COLON;
        case 't': {
          $this->_tokenValue= TRUE;
          $this->stream->read(3); // eat "rue"
          return self::T_VALUE;
        }
        case 'f': {
          $this->_tokenValue= FALSE;
          $this->stream->read(4); // eat "alse"
          return self::T_VALUE;
        }
        
        case 'n': {
          $this->_tokenValue= NULL;
          $this->stream->read(3); // eat "ull"
          return self::T_VALUE;
        }
        
        case '"': {
          $this->_tokenValue= $this->_readString();
          return self::T_VALUE;
        }
        
        case '-':
        case '+':
        case '0':
        case '1':
        case '2':
        case '3':
        case '4':
        case '5':
        case '6':
        case '7':
        case '8':
        case '9':
        case '0': {
          $this->stream->seek($this->stream->tell()- 1);
          $this->_tokenValue= $this->_readNumber();
          return self::T_VALUE;
        }
        
        default: 
          throw new JsonException('Invalid character: "'.$token.'" at position '.$this->stream->tell());
      }
    }
    
    /**
     * Fetch token value
     *
     * @return  mixed
     */
    protected function _getTokenValue() {
      return $this->_tokenValue;
    }    
    
    /**
     * Trim string, that is eat up all whitespace
     * (but not from within string)
     *
     */
    protected function _trim() {
      $str= $this->stream->read(10);
      $this->stream->seek($this->stream->tell() - strlen($str) + (strlen($str) - strlen(ltrim($str, ' '))));
    }
    
    /**
     * Decode string from wire
     *
     * @return  string
     * @throws  webservices.json.JsonException if the string could not be parsed
     */
    protected function _readString() {
      do {
        $initpos= $this->stream->tell();
        $offset= 0;
        $str= $this->stream->read();
        $ret= new String();
      
        $esc= FALSE;
        $tokenizer= new StringTokenizer($str, '\"', TRUE);
        $tok= '';
        while (strlen($tok) || $tokenizer->hasMoreTokens()) {
          if (empty($tok)) {
            $tok= $tokenizer->nextToken();
            $offset+= strlen($tok);
          }
          
          if ($esc) {
            $tmp= $tok{0};
            $tok= substr($tok, 1);
            switch ($tmp) {
              case '\\':
              case '"': 
              case '/': $ret->concat($tmp);  break;
              case 't': $ret->concat("\t"); break;
              case 'n': $ret->concat("\n"); break;
              case 'r': $ret->concat("\r"); break;
              case 'b': $ret->concat("\b"); break;
              case 'u': {

                // Read next 4 bytes
                $ret->concat(new Character(hexdec(substr($tok, 0, 4))));
                $tok= substr($tok, 4);
                break;
              }
            }
            
            $esc= FALSE; 
            continue; 
          }
          
          switch ($tok) {
            case '"': {
              $this->stream->seek($initpos + $offset);
              return (string)$ret->getBytes('ISO-8859-15');
            }
            
            case '\\': {
              $esc= TRUE;
              $tok= '';
              break;
            }
            
            default: {
              $ret->concat(new String($tok, 'UTF-8'));
              $tok= '';
              break;
            }
          }
        }
      } while (!$this->stream->eof());
      throw new JsonException('String not well-formed.');
    }
    
    /**
     * Decode number from wire
     *
     * @return  mixed
     */
    protected function _readNumber() {
      $initpos= $this->stream->tell();
      $str= $this->stream->read();
      
      $endpos= strspn($str, '-+0123456789.eE');
      $this->stream->seek($initpos + $endpos);
      $nstr= substr($str, 0, $endpos);
      
      if (FALSE !== (strpos($nstr, '.')) || strpos(strtolower($nstr), 'e')) {
        return floatval($nstr);
      }
      
      return intval($nstr);
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
