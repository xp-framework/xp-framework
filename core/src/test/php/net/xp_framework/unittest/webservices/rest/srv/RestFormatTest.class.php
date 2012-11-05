<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.srv.RestFormat',
    'scriptlet.Request',
    'scriptlet.Response',
    'io.streams.MemoryOutputStream'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.srv.RestDefaultRouter
   */
  class RestFormatTest extends TestCase {
    protected static $request;
    protected static $response;

    /**
     * Creates request and response dummies
     */
    #[@beforeClass]
    public static function createDummies() {
      self::$request= ClassLoader::defineClass('RestFormat_Request', 'lang.Object', array('scriptlet.Request'), '{
        public $content;
        public function __construct($payload) { $this->content= new MemoryInputStream($payload); }
        public function hasSession() { }
        public function getSession() { }
        public function getCookies() { }
        public function hasCookie($name) { }
        public function getCookie($name, $default= NULL) { }
        public function getHeader($name, $default= NULL) { }
        public function getParam($name, $default= NULL) { }
        public function hasParam($name) { }
        public function getParams() { }
        public function getURL() { }
        public function getMethod() { }
        public function getQueryString() { }
        public function getInputStream() { return $this->content; }
      }');
      self::$response= ClassLoader::defineClass('RestFormat_Response', 'lang.Object', array('scriptlet.Response'), '{
        public $content;
        public function __construct() { $this->content= new MemoryOutputStream(); }
        public function setCookie($cookie) { }
        public function setHeader($name, $value) { }
        public function setStatus($sc) { }
        public function setContentLength($length) { }
        public function setContentType($type) { }
        public function isCommitted() { }
        public function flush() { }
        public function getOutputStream() { return $this->content; }
      }');
    }

    /**
     * Test JSON
     * 
     */
    #[@test]
    public function json_serialize() {
      $res= self::$response->newInstance();
      RestFormat::$JSON->write($res, array('name' => 'Timm'));
      $this->assertEquals('{ "name" : "Timm" }', $res->content->getBytes());
    }

    /**
     * Test JSON
     * 
     */
    #[@test]
    public function json_deserialize() {
      $req= self::$request->newInstance('{ "name" : "Timm" }');
      $v= RestFormat::$JSON->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_serialize() {
      $res= self::$response->newInstance();
      RestFormat::$XML->write($res, array('name' => 'Timm'));
      $this->assertEquals(
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<root><name>Timm</name></root>', 
        $res->content->getBytes()
      );
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_deserialize() {
      $req= self::$request->newInstance('<?xml version="1.0" encoding="UTF-8"?>'."\n".'<root><name>Timm</name></root>');
      $v= RestFormat::$XML->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_deserialize_without_xml_declaration() {
      $req= self::$request->newInstance('<root><name>Timm</name></root>');
      $v= RestFormat::$XML->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test FORM
     *
     */
    #[@test]
    public function form_deserialize() {
      $req= self::$request->newInstance('name=Timm');
      $v= RestFormat::$FORM->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v);
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function application_x_www_form_urlencoded_mediatype() {
      $this->assertEquals(RestFormat::$FORM, RestFormat::forMediaType('application/x-www-form-urlencoded'));
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function text_json_mediatype() {
      $this->assertEquals(RestFormat::$JSON, RestFormat::forMediaType('text/json'));
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function application_json_mediatype() {
      $this->assertEquals(RestFormat::$JSON, RestFormat::forMediaType('application/json'));
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function text_xml_mediatype() {
      $this->assertEquals(RestFormat::$XML, RestFormat::forMediaType('text/xml'));
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function application_xml_mediatype() {
      $this->assertEquals(RestFormat::$XML, RestFormat::forMediaType('application/xml'));
    }

    /**
     * Test forMediaType()
     *
     */
    #[@test]
    public function application_octet_stream_mediatype() {
      $this->assertEquals(RestFormat::$UNKNOWN, RestFormat::forMediaType('application/octet-stream'));
    }
  }
?>
