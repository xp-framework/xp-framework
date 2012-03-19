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
    protected $message= '';
    protected $deserializer= NULL;
    protected $headers= array();
    protected $type= NULL;
    protected $input= NULL;

    /**
     * Creates a new response
     *
     * @param   int status
     * @param   string message
     * @param   webservices.rest.Deserializer deserializer
     * @param   [:string[]] headers
     * @param   lang.Type type
     * @param   io.streams.InputStream input
     */
    public function __construct($status, $message, $deserializer, $headers, $type, $input) {
      $this->status= $status;
      $this->message= $message;
      $this->deserializer= $deserializer;
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
     * Get status message
     *
     * @return  string
     */
    public function message() {
      return $this->message;
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
     * @throws  webservices.rest.RestException if the status code is > 399
     */
    public function data() {
      if ($this->status > 399) {
        throw new RestException($this->status.': '.$this->message);
      }
 
      if (NULL === $this->deserializer) {
        throw new IllegalArgumentException('Unknown content type "'.$this->headers['Content-Type'][0].'"');
      }

      return $this->deserializer->deserialize($this->input, $this->type);
    }
  }
?>
