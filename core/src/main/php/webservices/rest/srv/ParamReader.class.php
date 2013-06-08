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
   * Reads request parameters
   */
  abstract class ParamReader extends Enum {
    protected static $sources= array();
    public static $COOKIE, $HEADER, $PARAM, $PATH, $BODY;

    static function __static() {
      self::$sources['cookie']= self::$COOKIE= newinstance(__CLASS__, array(1, 'cookie'), '{
        static function __static() { }
        public function read($name, $target, $request) {
          if (NULL === ($cookie= $request->getCookie($name, NULL))) return NULL;
          return $cookie->getValue();
        }
      }');
      self::$sources['header']= self::$HEADER= newinstance(__CLASS__, array(2, 'header'), '{
        static function __static() { }
        public function read($name, $target, $request) {
          return $request->getHeader($name, NULL);
        }
      }');
      self::$sources['param']= self::$PARAM= newinstance(__CLASS__, array(3, 'param'), '{
        static function __static() { }
        public function read($name, $target, $request) {
          return $request->getParam($name, NULL);
        }
      }');
      self::$sources['path']= self::$PATH= newinstance(__CLASS__, array(4, 'path'), '{
        static function __static() { }
        public function read($name, $target, $request) {
          return isset($target["segments"][$name]) ? rawurldecode($target["segments"][$name]) : NULL;
        }
      }');
      self::$sources['body']= self::$BODY= newinstance(__CLASS__, array(5, 'body'), '{
        static function __static() { }
        public function read($name, $target, $request) {
          return RestFormat::forMediaType($target["input"])->read($request->getInputStream(), Type::$VAR); 
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
     * Read this parameter from the given request
     *
     * @param   string name
     * @param   [:var] target Routing target
     * @param   scriptlet.Request request
     */
    public abstract function read($name, $target, $request);
  }
?>