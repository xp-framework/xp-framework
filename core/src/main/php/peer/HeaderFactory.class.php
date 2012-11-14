<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'lang.ClassNotFoundException',
    'lang.IllegalArgumentException',
    'lang.XPClass'
  );

  /**
   * Factory for generating Headers
   * Keep in mind, not all Headers are valid for Requests/Responses
   *
   * TODO - extend with more headers
   *
   * @purpose  Create Headers
   */
  class HeaderFactory extends Object {

    const
      TYPE_CONTENT_DISPOSITION=  'peer.header.ContentDispositionHeader',
      TYPE_CONTENT_ENCODING=     'peer.header.ContentEncodingHeader',
      TYPE_CONTENT_LANGUAGE=     'peer.header.ContentLanguageHeader',
      TYPE_CONTENT_LENGTH=       'peer.header.ContentLengthHeader',
      TYPE_CONTENT_LOCATION=     'peer.header.ContentLocationHeader',
      TYPE_CONTENT_MD5=          'peer.header.ContentMD5Header',
      TYPE_CONTENT_RANGE=        'peer.header.ContentRangeHeader',
      TYPE_CONTENT_TYPE=         'peer.header.ContentTypeHeader';

    /**
     * Create the requested request header.
     * All additional params will be handed over as params to the Header
     * Will only return valid request headers and throw an exception otherwise
     *
     * @param string type
     * @return peer.Header
     * @throws IllegalArgumentException on empty/wrong type or value
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
     * @throws  IllegalArgumentException on empty/wrong type or value
     */
    public static function getResponseHeader($type) {
      $header= call_user_func_array(array('HeaderFactory', 'getHeader'), func_get_args());
      if(!$header->isResponseHeader()) {
        throw new IllegalArgumentException('A response only header may not be used in a request');
      }
      return $header;
    }

    /**
     * General function to return a correct formatted header for the given type and values
     *
     * TBD: Should this be public and thus creating a backdoor for requesting headers without further request/response check
     *
     * @param   string header type
     * @return  peer.Header
     * @throws  IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    public static function getHeader($type) {
      $class= self::getXPClass($type);
      $args= func_get_args();
      array_shift($args);
      $header= call_user_func_array(array($class, 'newInstance'), $args);
      return $header;
    }

    /**
     * Will return the class for the given type
     *
     * @param   string header type
     * @return  lang.XPClass class object
     * @throws  IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    protected static function getXPClass($type) {
      if (empty($type)) {
        throw new IllegalArgumentException('A header type has to be given');
      }
      try {
        $class= XPClass::forName($type);
      } catch (ClassNotFoundException $ex) {
        throw new IllegalArgumentException('Header for type \''.$type.'\' not found');
      }
      return $class;
    }

    /**
     * Will return the header name for the given type if found
     *
     * @param   string header type
     * @return  string
     * @throws  IllegalArgumentException on empty/wrong type or initializing errors (missing params)
     */
    public static function getNameForType($type) {
      $class= self::getXPClass($type);
      return $class->getConstant('NAME');
    }
  }
?>
