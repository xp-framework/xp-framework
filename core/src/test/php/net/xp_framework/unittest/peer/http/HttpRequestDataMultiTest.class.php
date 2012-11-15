<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.peer.http.AbstractHttpRequestDataTest',
    'peer.http.HttpRequestDataMulti',
    'peer.http.HttpConstants',
    'peer.http.HttpRequestData'
  );

  /**
   * TestCase
   *
   * @see       xp://peer.http.HttpRequestDataMulti
   * @purpose   Testcase
   */
  class HttpRequestDataMultiTest extends AbstractHttpRequestDataTest {

    protected
      $expectedData= array();

    public function setUp() {
      parent::setUp();
      $obj= $this->getNewHeaderTestObj();
      $boundary= $obj->getBoundary();
      $crLFLength= strlen(HttpConstants::CRLF);
      $boundaryLength= strlen($boundary);
      $ctLengthNoArray= strlen(HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY);
      $ctLengthArray= strlen(HttpRequestData::DEFAULT_CONTENTTYPE_ARRAY);

      $this->expectedData= array(
        'empty' =>  array(
          'obj'       =>  $obj,
          'out'       =>  '--'.$boundary.'--'.HttpConstants::CRLF,
          'headers' =>  array(
            create(new ContentLengthHeader(4+$boundaryLength+$crLFLength))->toString(),
            create(new ContentTypeHeader(HttpRequestDataMulti::DEFAULT_CONTENTTYPE_MULTIPART, NULL, $boundary))->toString()
          )
        ),
        'boundary' =>  array(
          'obj'       =>  $obj,
          'boundary'  =>  'blabla',
          'out'       =>  '--'.'blabla'.'--'.HttpConstants::CRLF,
          'headers' =>  array(
            create(new ContentLengthHeader(10+$crLFLength))->toString(),
            create(new ContentTypeHeader(HttpRequestDataMulti::DEFAULT_CONTENTTYPE_MULTIPART, NULL, 'blabla'))->toString()
          )
        ),
        'single'  => array(
          'obj'     =>  $obj,
          'in'      =>  'This is some content for header tests',
          'out'     =>  '--'.$boundary.HttpConstants::CRLF.
                        'Content-Length: 37'.HttpConstants::CRLF.
                        'Content-Type: '.HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY.HttpConstants::CRLF.
                        'This is some content for header tests'.HttpConstants::CRLF.
                        '--'.$boundary.'--'.HttpConstants::CRLF,
          'headers' =>  array(
            create(new ContentLengthHeader(75+2*$boundaryLength+5*$crLFLength+$ctLengthNoArray))->toString(),
            create(new ContentTypeHeader(HttpRequestDataMulti::DEFAULT_CONTENTTYPE_MULTIPART, NULL, $boundary))->toString()
          ),
        ),
        'multi' =>  array(
          'obj'     =>  $obj,
          'in'      =>  array(
            'part1' =>  'MultipartTest part uno',
            'part2' =>  array(
              'var1'  =>  'value1',
              'var2'  =>  'value2',
              'var3'  =>  array(
                'value3.1',
                'value3.2',
                'value3.3'
              )
            ),
            'part3' =>  create(new HttpRequestData('HttpRequestDataObj'))->withHeader(array('x-custom-test-header', 'testTEST'))
          ),
          'out'     =>  '--'.$boundary.HttpConstants::CRLF.
                        'Content-Length: 22'.HttpConstants::CRLF.
                        'Content-Type: '.HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY.HttpConstants::CRLF.
                        'MultipartTest part uno'.HttpConstants::CRLF.
                        '--'.$boundary.HttpConstants::CRLF.
                        'Content-Length: 74'.HttpConstants::CRLF.
                        'Content-Type: '.HttpRequestData::DEFAULT_CONTENTTYPE_ARRAY.HttpConstants::CRLF.
                        'var1=value1&var2=value2&var3[0]=value3.1&var3[1]=value3.2&var3[2]=value3.3'.HttpConstants::CRLF.
                        '--'.$boundary.HttpConstants::CRLF.
                        'Content-Length: 18'.HttpConstants::CRLF.
                        'Content-Type: '.HttpRequestData::DEFAULT_CONTENTTYPE_NOARRAY.HttpConstants::CRLF.
                        'x-custom-test-header: testTEST'.HttpConstants::CRLF.
                        'HttpRequestDataObj'.HttpConstants::CRLF.
                        '--'.$boundary.'--'.HttpConstants::CRLF,
          'headers' =>  array(
            create(new ContentLengthHeader(250+4*$boundaryLength+14*$crLFLength+2*$ctLengthNoArray+$ctLengthArray))->toString(),
            create(new ContentTypeHeader(HttpRequestDataMulti::DEFAULT_CONTENTTYPE_MULTIPART, NULL, $boundary))->toString()
          )
        )
      );
    }

    /**
     * Method to return header test object for abstract
     *
     * @return peer.http.AbstractHttpRequestData
     */
    protected function getNewHeaderTestObj() {
      return new HttpRequestDataMulti();
    }

    /**
     * Will test empty content headers
     */
    #[@test]
    public function testEmptyContentHeaders() {
      $mrdObj= $this->expectedData['empty']['obj'];
      $expectedHeaders= $this->expectedData['empty']['headers'];
      $headersSerialized= $this->serializeHeaders($mrdObj->getHeaders());
      $this->assertEquals($expectedHeaders, $headersSerialized);
    }

    /**
     * Will test empty content
     */
    #[@test]
    public function testEmptyContent() {
      $mrdObj= $this->expectedData['empty']['obj'];
      $expectedContent= $this->expectedData['empty']['out'];
      $this->assertEquals($expectedContent, $mrdObj->getData());
    }

    /**
     * Will test headers for custom boundary
     */
    #[@test]
    public function testSetBoundaryHeaders() {
      $mrdObj= $this->expectedData['boundary']['obj'];
      $mrdObj->withBoundary($this->expectedData['boundary']['boundary']);
      $expectedHeaders= $this->expectedData['boundary']['headers'];
      $headersSerialized= $this->serializeHeaders($mrdObj->getHeaders());
      $this->assertEquals($expectedHeaders, $headersSerialized);
    }

    /**
     * Will test content for custom boundary
     */
    #[@test]
    public function testSetBoundaryContent() {
      $mrdObj= $this->expectedData['boundary']['obj'];
      $mrdObj->withBoundary($this->expectedData['boundary']['boundary']);
      $expectedContent= $this->expectedData['boundary']['out'];
      $this->assertEquals($expectedContent, $mrdObj->getData());
    }

    /**
     * Will test headers for single part
     */
    #[@test]
    public function testSingleWithPartHeaders() {
      $mrdObj= $this->expectedData['single']['obj'];
      $mrdObj->withPart($this->expectedData['single']['in']);
      $expectedHeaders= $this->expectedData['single']['headers'];
      $headersSerialized= $this->serializeHeaders($mrdObj->getHeaders());
      $this->assertEquals($expectedHeaders, $headersSerialized);
    }

    /**
     * Will test content for single part
     */
    #[@test]
    public function testSingleWithPartContent() {
      $mrdObj= $this->expectedData['single']['obj'];
      $mrdObj->withPart($this->expectedData['single']['in']);
      $expectedContent= $this->expectedData['single']['out'];
      $this->assertEquals($expectedContent, $mrdObj->getData());
    }

    /**
     * Will test headers for single part with array casting
     */
    #[@test]
    public function testSingleWithPartsHeaders() {
      $mrdObj= $this->expectedData['single']['obj'];
      $mrdObj->withParts($this->expectedData['single']['in']);
      $expectedHeaders= $this->expectedData['single']['headers'];
      $headersSerialized= $this->serializeHeaders($mrdObj->getHeaders());
      $this->assertEquals($expectedHeaders, $headersSerialized);
    }

    /**
     * Will test content for single part with array casting
     */
    #[@test]
    public function testSingleWithPartsContent() {
      $mrdObj= $this->expectedData['single']['obj'];
      $mrdObj->withParts($this->expectedData['single']['in']);
      $expectedContent= $this->expectedData['single']['out'];
      $this->assertEquals($expectedContent, $mrdObj->getData());
    }

    /**
     * Will test headers for several part
     */
    #[@test]
    public function testMultiWithPartsHeaders() {
      $mrdObj= $this->expectedData['multi']['obj'];
      $mrdObj->withParts($this->expectedData['multi']['in']);
      $expectedHeaders= $this->expectedData['multi']['headers'];
      $headersSerialized= $this->serializeHeaders($mrdObj->getHeaders());
      $this->assertEquals($expectedHeaders, $headersSerialized);
    }

    /**
     * Will test content for several part
     */
    #[@test]
    public function testMultiWithPartsContent() {
      $mrdObj= $this->expectedData['multi']['obj'];
      $mrdObj->withParts($this->expectedData['multi']['in']);
      $expectedContent= $this->expectedData['multi']['out'];
      $this->assertEquals($expectedContent, $mrdObj->getData());
    }
  }
?>
