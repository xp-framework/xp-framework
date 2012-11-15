<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.Header'
  );

  /**
   * TestCase
   * For our general header tests we use a custom header
   *
   * @see       xp://peer.http.HttpRequestData
   * @purpose   Testcase
   */
  abstract class AbstractHttpRequestDataTest extends TestCase {

    protected
      $expectedHeaders=  array();


    /**
     * Has to return a new test obj for testing the header functionalities
     *
     * @return peer.http.AbstractHttpRequestData
     */
    abstract protected function getNewHeaderTestObj();

    /**
     * set up the custom test header
     */
    public function setUp() {
      $this->expectedHeaders=  array(
        'obj'   =>  array(
          'o1'  =>  new Header('x-custom-unittest-header1', 'object-value1'),
          'o2'  =>  new Header('x-custom-unittest-header2', 'object-value2')
        ),
        'array' =>  array(
          'a1'  =>  array('x-custom-unittest-header1', 'array-value1'),
          'a2'  =>  array('x-custom-unittest-header2', 'array-value2')
        )
      );
    }

    /**
     * Test add header object
     *
     */
    #[@test]
    public function testAddHeaderObj() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['obj']['o1'];

      $expectedHeaders= $rdObj->getHeaders();
      $expectedHeaders[]= $headerToAdd;
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $rdObj->addHeader($headerToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
    }

    /**
     * Test add header objects
     *
     */
    #[@test]
    public function testAddHeadersObj() {
      $rdObj= $this->getNewHeaderTestObj();
      $headersToAdd= $this->expectedHeaders['obj'];

      $expectedHeaders= $rdObj->getHeaders();
      foreach($headersToAdd as $headerToAdd) {
        $expectedHeaders[]= $headerToAdd;
      }
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $rdObj->addHeaders($headersToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
    }

    /**
     * Test with header object
     *
     */
    #[@test]
    public function testWithHeaderObj() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['obj']['o1'];

      $expectedHeaders= $rdObj->getHeaders();
      $expectedHeaders[]= $headerToAdd;
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $returnObj= $rdObj->withHeader($headerToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
      $this->assertEquals($rdObj, $returnObj);
    }

    /**
     * Test with header objects
     *
     */
    #[@test]
    public function testWithHeadersObj() {
      $rdObj= $this->getNewHeaderTestObj();
      $headersToAdd= $this->expectedHeaders['obj'];

      $expectedHeaders= $rdObj->getHeaders();
      foreach($headersToAdd as $headerToAdd) {
        $expectedHeaders[]= $headerToAdd;
      }
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $returnObj= $rdObj->withHeaders($headersToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
      $this->assertEquals($rdObj, $returnObj);
    }

    /**
     * Test add header array
     *
     */
    #[@test]
    public function testAddHeaderArray() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['array']['a1'];

      $expectedHeaders= $rdObj->getHeaders();
      $expectedHeaders[]= $headerToAdd;
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $rdObj->addHeader($headerToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
    }

    /**
     * Test add header objects
     *
     */
    #[@test]
    public function testAddHeadersArray() {
      $rdObj= $this->getNewHeaderTestObj();
      $headersToAdd= $this->expectedHeaders['array'];

      $expectedHeaders= $rdObj->getHeaders();
      foreach($headersToAdd as $headerToAdd) {
        $expectedHeaders[]= $headerToAdd;
      }
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $rdObj->addHeaders($headersToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
    }

    /**
     * Test with header object
     *
     */
    #[@test]
    public function testWithHeaderArray() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['array']['a1'];

      $expectedHeaders= $rdObj->getHeaders();
      $expectedHeaders[]= $headerToAdd;
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $returnObj= $rdObj->withHeader($headerToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
      $this->assertEquals($rdObj, $returnObj);
    }

    /**
     * Test with header objects
     *
     */
    #[@test]
    public function testWithHeadersArray() {
      $rdObj= $this->getNewHeaderTestObj();
      $headersToAdd= $this->expectedHeaders['array'];

      $expectedHeaders= $rdObj->getHeaders();
      foreach($headersToAdd as $headerToAdd) {
        $expectedHeaders[]= $headerToAdd;
      }
      $expectedHeaders= $this->serializeHeaders($expectedHeaders);

      $returnObj= $rdObj->withHeaders($headersToAdd);
      $headers= $this->serializeHeaders($rdObj->getHeaders());

      $this->assertEquals($expectedHeaders, $headers);
      $this->assertEquals($rdObj, $returnObj);
    }



    /**
     * Test get header for type
     *
     */
    #[@test]
    public function testGetHeadersForType() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['array']['a1'];
      $headerType= $this->expectedHeaders['array']['a1'][0];

      $expectedHeader= $this->serializeHeaders($headerToAdd);

      $rdObj->addHeader($headerToAdd);
      $header= $this->serializeHeaders($rdObj->getHeadersForType($headerType));

      $this->assertEquals($expectedHeader, $header);
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function testHasHeaderTrue() {
      $rdObj= $this->getNewHeaderTestObj();
      $headerToAdd= $this->expectedHeaders['array']['a1'];
      $headerType= $this->expectedHeaders['array']['a1'][0];

      $rdObj->addHeader($headerToAdd);

      $this->assertEquals(TRUE, $rdObj->hasHeader($headerType));
    }

    /**
     * Create new Header without content
     *
     */
    #[@test]
    public function testHasHeaderFalse() {
      $rdObj= $this->getNewHeaderTestObj();
      $this->assertEquals(FALSE, $rdObj->hasHeader('x-no-valid-set-header'));
    }


    /**
     * Helper function
     * Will serialize a header or array of headers
     *
     * @param peer.Header|[:var] headers
     * @return string|[:string]
     */
    protected function serializeHeaders($headers) {
      if($headers instanceof Header) {
        return $headers->toString();
      }
      if(is_array($headers) && is_string(reset($headers))) {
        return implode(': ', $headers);
      }
      $serializedHeaders= array();
      foreach($headers as $header) {
        $serializedHeaders[]= $this->serializeHeaders($header);
      }
      return $serializedHeaders;
    }
  }
?>
