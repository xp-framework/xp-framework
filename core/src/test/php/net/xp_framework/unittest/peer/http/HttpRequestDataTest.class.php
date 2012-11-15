<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.http.AbstractHttpRequestDataTest',
    'lang.ClassLoader',
    'peer.header.ContentLengthHeader',
    'peer.header.ContentTypeHeader',
    'peer.http.HttpRequestData'
  );

  /**
   * TestCase
   *
   * @see       xp://peer.http.HttpRequestData
   * @purpose   Testcase
   */
  class HttpRequestDataTest extends AbstractHttpRequestDataTest {

    protected
      $expectedData= array();

    /**
     * Setup the expected data
     */
    public function setUp() {
      parent::setUp();
      ClassLoader::getDefault()->defineClass('SerializableObj', 'Object', array('Serializable'), '{
        public function serialize() { return "abcd";}
        public function unserialize($serialized) { }
      }');

      $this->expectedData= array(
        'text'  => array(
          'in'      =>  'This is some content for header tests',
          'out'     =>  'This is some content for header tests',
          'headers' =>  array(
            create(new ContentLengthHeader(37))->toString(),
            create(new ContentTypeHeader(HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY))->toString()
          ),
        ),
        'array' =>  array(
          'in'      =>  array(
            'var1'  =>  'value1',
            'var2'  =>  'value2',
            'var3'  =>  array(
              'value3.1',
              'value3.2',
              'value3.3'
            )
          ),
          'out'     =>  'var1=value1&var2=value2&var3[0]=value3.1&var3[1]=value3.2&var3[2]=value3.3',
          'headers' =>  array(
            create(new ContentLengthHeader(74))->toString(),
            create(new ContentTypeHeader(HttpRequestData::DEFAULT_CONTENTTYPE_ARRAY))->toString()
          )
        ),
        'object' =>  array(
          'in'      =>  new SerializableObj(),
          'out'     =>  'abcd',
          'headers' =>  array(
            create(new ContentLengthHeader(4))->toString(),
            create(new ContentTypeHeader(HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY))->toString()
          )
        ),
        'invalid_object'  =>  array(
          'in'  =>  new stdClass()
        )
      );
    }

    /**
     * Method to return header test object for abstract
     *
     * @return peer.http.AbstractHttpRequestData
     */
    protected function getNewHeaderTestObj() {
      return new HttpRequestData('This is some content for header tests');
    }

    /**
     * Will test default headers for text content
     */
    #[@test]
    public function testDefaultHeadersText() {
      $rdObj= new HttpRequestData($this->expectedData['text']['in']);
      $headersSerialized= $this->serializeHeaders($rdObj->getHeaders());
      $this->assertEquals($this->expectedData['text']['headers'], $headersSerialized);
    }

    /**
     * Will test default headers for array content
     */
    #[@test]
    public function testDefaultHeadersArray() {
      $rdObj= new HttpRequestData($this->expectedData['array']['in']);
      $headersSerialized= $this->serializeHeaders($rdObj->getHeaders());
      $this->assertEquals($this->expectedData['array']['headers'], $headersSerialized);
    }

    /**
     * Will test default headers for serializable object content
     */
    #[@test]
    public function testDefaultHeadersObject() {
      $rdObj= new HttpRequestData($this->expectedData['object']['in']);
      $headersSerialized= $this->serializeHeaders($rdObj->getHeaders());
      $this->assertEquals($this->expectedData['object']['headers'], $headersSerialized);
    }

    /**
     * Will test content for text
     */
    #[@test]
    public function testContentText() {
      $rdObj= new HttpRequestData($this->expectedData['text']['in']);
      $content= $rdObj->getData();
      $this->assertEquals($this->expectedData['text']['out'], $content);
    }

    /**
     * Will test content for array
     */
    #[@test]
    public function testContentArray() {
      $rdObj= new HttpRequestData($this->expectedData['array']['in']);
      $content= $rdObj->getData();
      $this->assertEquals($this->expectedData['array']['out'], $content);
    }

    /**
     * Will test content for object
     */
    #[@test]
    public function testContentObj() {
      $rdObj= new HttpRequestData($this->expectedData['object']['in']);
      $content= $rdObj->getData();
      $this->assertEquals($this->expectedData['object']['out'], $content);
    }


    /**
     * Will test invalid content
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function testContentInvalidObj() {
      new HttpRequestData($this->expectedData['invalid_object']['in']);
    }

  }
?>
