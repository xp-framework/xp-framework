<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.Enum',
    'webservices.rest.RestFormat',
    'webservices.rest.RestDeserializer'
  );

  /**
   * Abstract base class
   *
   */
  abstract class ParamReader extends Enum {
    protected static $sources= array();
    public static $COOKIE, $HEADER, $PARAM, $PATH, $BODY;
    protected $convert= NULL;

    static function __static() {
      self::$sources['cookie']= self::$COOKIE= newinstance(__CLASS__, array(1, 'cookie'), '{
        static function __static() { }
        public function read($name, $type, $target, $request) {
          if (NULL === ($cookie= $request->getCookie($name, NULL))) return NULL;
          return $this->convert->convert($type, $cookie->getValue());
        }
      }');
      self::$sources['header']= self::$HEADER= newinstance(__CLASS__, array(2, 'header'), '{
        static function __static() { }
        public function read($name, $type, $target, $request) {
          return $this->convert->convert($type, $request->getHeader($name, NULL));
        }
      }');
      self::$sources['param']= self::$PARAM= newinstance(__CLASS__, array(3, 'param'), '{
        static function __static() { }
        public function read($name, $type, $target, $request) {
          return $request->hasParam($name)
            ? $this->convert->convert($type, $request->getParam($name))
            : NULL
          ;
        }
      }');
      self::$sources['path']= self::$PATH= newinstance(__CLASS__, array(4, 'path'), '{
        static function __static() { }
        public function read($name, $type, $target, $request) {
          return isset($target["segments"][$name])
            ? $this->convert->convert($type, rawurldecode($target["segments"][$name]))
            : NULL
          ;
        }
      }');
      self::$sources['body']= self::$BODY= newinstance(__CLASS__, array(5, 'body'), '{
        static function __static() { }
        public function read($name, $type, $target, $request) {
          return RestFormat::forMediaType($target["input"])->read($request->getInputStream(), $type); 
        }
      }');
    }

    /**
     * Creates this parameter source
     *
     */
    public function __construct($ordinal, $name) {
      parent::__construct($ordinal, $name);
      $this->convert= newinstance('webservices.rest.RestDeserializer', array(), '{
        public function deserialize($in, $target) {
          throw new IllegalStateException("Unused");
        }
      }');
    }

    /**
     * Factory method
     *
     * @param  string name
     * @return self
     */
    public static function forName($name) {
      if (isset(self::$sources[$name])) {
        return self::$sources[$name];
      }
      throw new IllegalArgumentException('Invalid parameter source "'.$name.'"');
    }

    /**
     * Convert a given value
     *
     * @param  lang.Type target
     * @param  var value
     * @return var
     */
    public function convert($target, $value) {
      return $this->convert->convert($target, $value);
    }

    /**
     * Read this parameter from the given request
     *
     */
    public abstract function read($name, $type, $route, $request);
  }
?>