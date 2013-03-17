<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestFormat',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream'
  );
  
  /**
   * Test default router
   *
   * @see  xp://webservices.rest.RestFormat
   */
  class RestFormatTest extends TestCase {

    /**
     * Test JSON
     * 
     */
    #[@test]
    public function json_serialize() {
      $res= new MemoryOutputStream();
      RestFormat::$JSON->write($res, new Payload(array('name' => 'Timm')));
      $this->assertEquals('{ "name" : "Timm" }', $res->getBytes());
    }

    /**
     * Test JSON
     * 
     */
    #[@test]
    public function json_deserialize() {
      $req= new MemoryInputStream('{ "name" : "Timm" }');
      $v= RestFormat::$JSON->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_serialize() {
      $res= new MemoryOutputStream();
      RestFormat::$XML->write($res, new Payload(array('name' => 'Timm')));
      $this->assertEquals(
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<root><name>Timm</name></root>', 
        $res->getBytes()
      );
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_deserialize() {
      $req= new MemoryInputStream('<?xml version="1.0" encoding="UTF-8"?>'."\n".'<root><name>Timm</name></root>');
      $v= RestFormat::$XML->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test XML
     * 
     */
    #[@test]
    public function xml_deserialize_without_xml_declaration() {
      $req= new MemoryInputStream('<root><name>Timm</name></root>');
      $v= RestFormat::$XML->read($req, MapType::forName('[:string]'));
      $this->assertEquals(array('name' => 'Timm'), $v); 
    }

    /**
     * Test FORM
     *
     */
    #[@test]
    public function form_deserialize() {
      $req= new MemoryInputStream('name=Timm');
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
