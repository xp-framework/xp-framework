<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.ClassNotFoundException',
    'lang.IllegalArgumentException',
    'lang.XPClass',
    'peer.Header'
  );

  /**
   * Factory for generating specific Headers
   * Keep in mind, not all Headers are valid for Requests/Responses
   *
   * If a header is requested with a type that does not reflect a xp classname,
   * a peer.Header with the type as name is returned instead.
   *
   * TODO - extend with more headers
   *
   * @purpose  Create Headers
   */
  class HeaderFactory extends Object {

    const
      HEADER_CONTENTDISPOSITION=  'peer.header.ContentDispositionHeader',
      HEADER_CONTENTENCODING=     'peer.header.ContentEncodingHeader',
      HEADER_CONTENTLANGUAGE=     'peer.header.ContentLanguageHeader',
      HEADER_CONTENTLENGTH=       'peer.header.ContentLengthHeader',
      HEADER_CONTENTLOCATION=     'peer.header.ContentLocationHeader',
      HEADER_CONTENTMD5=          'peer.header.ContentMD5Header',
      HEADER_CONTENTRANGE=        'peer.header.ContentRangeHeader',
      HEADER_CONTENTTYPE=         'peer.header.ContentTypeHeader';

    /**
     * Create the requested request header.
     * All additional params will be handed over as params to the Header
     * Will only return valid request headers and throw an exception otherwise
     *
     * @param string type
     * @return peer.Header
     * @throws lang.IllegalArgumentException on empty/wrong type or value
     */
    public static function getRequestHeader($type) {
      $header= call_user_func_array(array('HeaderFactory', 'getHeader'), func_get_args());
      if(!$header->isRequestHeader()) {
        throw new IllegalArgumentException('A response only header may not be used in a request');
      }
      return $header;
    }

    /**
     * Create the requested request header.
     * All additional params will be handed over as params to the Header
     * Will only return valid request headers and throw an exception otherwise
     *
     * @param   string type
     * @return  peer.Header
     * @throws  lang.IllegalArgumentException on empty/wrong type or value
     */
    public static function getResponseHeader($type) {
      $header= call_user_func_array(array('HeaderFactory', 'getHeader'), func_get_args());
      if(!$header->isResponseHeader()) {
        throw new IllegalArgumentException('A request only header may not be used in a response');
      }
      return $header;
    }

    /**
     * General function to return a correct formatted header for the given type and values
     *
     * TBD: Should this be public and thus creating a backdoor for requesting headers without further request/response check
     *
     * @param   string type
     * @return  peer.Header
     * @throws  lang.IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    public static function getHeader($type) {
      self::raiseExceptionOnInvalid($type);
      if(self::isPossibleXPClassname($type)) {
        $args= func_get_args();
        array_shift($args);
        $class= self::getXPClass($type);
        return call_user_func_array(array($class, 'newInstance'), $args);
      } else {
        return new Header($type, func_get_arg(1));
      }
    }

    /**
     * Will return the class for the given type
     *
     * @param   string header type
     * @return  lang.XPClass class object
     * @throws  lang.IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    protected static function getXPClass($type) {
      try {
        $class= XPClass::forName($type);
        if(!$class->isSubclassOf('peer.Header')) {
          throw new IllegalArgumentException('Given type is no Header');
        }
      } catch (ClassNotFoundException $ex) {
        throw new IllegalArgumentException('Invalid type \''.$type.'\' given. Class not found.');
      }
      return $class;
    }

    /**
     * Will check if the given type is a possible XPClassname and thus invalid Header name
     * (Headers may not include a dot)
     *
     * This is used to determine, if a specified header is to be loaded or if the factory falls back to common peer.Header
     *
     * @param   string type
     * @return  bool
     * @throws  lang.IllegalArgumentException on empty type
     */
    private static function isPossibleXPClassname($type) {
      return (FALSE === strpos($type, '.')) ? FALSE : TRUE;
    }

    /**
     * Convenience function
     * Will simply raise an exception if the given type is invalid
     * empty or no string
     * 
     * @param   string type the header type to test
     * @return  void
     * @throws  lang.IllegalArgumentException on empty or no string type
     */
    private static function raiseExceptionOnInvalid($type) {
      if (empty($type) || !is_string($type)) {
        throw new IllegalArgumentException('A header type has to be given');
      }
    }

    /**
     * Will return the header name for the given header type if not empty
     *
     * @param   string type the header type
     * @return  string
     * @throws  lang.IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    public static function getNameForType($type) {
      self::raiseExceptionOnInvalid($type);
      if(self::isPossibleXPClassname($type)) {
        $class= self::getXPClass($type);
        return $class->getConstant('NAME');
      } else {
        return $type;
      }
    }
  }
?>
