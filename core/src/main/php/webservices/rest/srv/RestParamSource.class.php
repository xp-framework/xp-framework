<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.RestDeserializer');

  /**
   * Abstract base class
   *
   */
  abstract class RestParamSource extends Object {
    protected static $sources= array();
    protected $convert= NULL;

    /**
     * Creates this parameter source
     *
     */
    public function __construct() {
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
     * @return lang.XPClass
     */
    public static function forName($name) {
      if (isset(self::$sources[$name])) return self::$sources[$name];
      
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
    public abstract function read($type, $route, $request);
  }
?>