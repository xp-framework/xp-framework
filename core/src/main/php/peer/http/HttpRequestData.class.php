<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'lang.IllegalArgumentException',
    'peer.http.AbstractHttpRequestData',
    'peer.http.RequestData'
  );

  /**
   * HttpRequestData
   *
   * @see      xp://peer.http.HttpRequest#setBody
   * @see      xp://peer.http.HttpRequest#setParameters
   * @purpose  Pass request data directly to
   */
  class HttpRequestData extends AbstractHttpRequestData {

    const
      DEFAULT_CONTENTTYPE_ARRAY=    'application/x-www-form-urlencoded',
      DEFAULT_CONTENTTYPE_NOARRAY=  'text/plain';

    protected
      $defaultType= null;

    /**
     * Constructor
     *
     * Params might be extended with language, location, MD5 and/or range
     * Objects may be given, but by default only objects implementing Serializable
     * will not trigger an exception. Additionally text/plain will be used for them by default.
     *
     * @param   int/string/array data
     * @throws  lang.IllegalArgumentException
     * @see Serializable
     */
    public function __construct($data) {
      $this->guessSetDefaultType($data);
      $dataHeaders= array();
      if ($data instanceof RequestData) {
        $dataHeaders= $data->getHeaders();
        $data= $data->getData();
      }
      $encodedData= $this->encodeData($data);
      parent::__construct($encodedData);
      if(!empty($dataHeaders)) {
        $this->addHeaders($dataHeaders);
      }
    }

    /**
     * Returns the default type.
     *
     * @return string
     */
    protected function getDefaultType() {
      return $this->defaultType;
    }

    /**
     * Will try to guess the type depending on the given data
     *
     * @param   mixed   data
     * @return  string  guessed content type
     */
    protected function guessSetDefaultType(&$data) {
      if (is_array($data)) {
        $this->defaultType= self::DEFAULT_CONTENTTYPE_ARRAY;
      } else {
        $this->defaultType= self::DEFAULT_CONTENTTYPE_NOARRAY;
      }
    }

    /**
     * Will return the data in a serialized form or content url encoded
     * Handles array values with unrestricted depth.
     *
     * @param   mixed   data
     * @param   mixed   prefix
     * @return  string
     * @throws  lang.IllegalArgumentException on object given that does not implement serializable
     */
    protected function encodeData(&$data, $prefix= '') {
      $prefix= trim($prefix);

      if (is_object($data)) {
        if ($data instanceof Serializable) {
          return $prefix.$data->serialize();
        }
        throw new IllegalArgumentException('Data of type '.get_class($data).' not supported if not serializable');
      } else if (is_array($data)) {
        $aEncoded= array();
        foreach ($data as $name => $value) {
          if ('' !== $prefix) {
            $name= $prefix.'['.$name.']';
          }
          if (is_array($value)) {
            $aEncoded[]= $this->encodeData($value, $name);
          } else {
            $aEncoded[]= $name.'='.urlencode(
              $this->encodeData($value)
            );
          }
        }
        return implode('&', $aEncoded);
      } else {
        return $prefix.$data;
      }
    }
  }
?>
