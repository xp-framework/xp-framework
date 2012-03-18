<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestRequest'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestRequest
   */
  class RestRequestTest extends TestCase {
    
    /**
     * Test
     *
     */
    #[@test]
    public function getResource() {
      $fixture= new RestRequest('/issues');
      $this->assertEquals('/issues', $fixture->getResource());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function setResource() {
      $fixture= new RestRequest();
      $fixture->setResource('/issues');
      $this->assertEquals('/issues', $fixture->getResource());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withResource() {
      $fixture= new RestRequest();
      $this->assertEquals($fixture, $fixture->withResource('/issues'));
      $this->assertEquals('/issues', $fixture->getResource());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function getMethod() {
      $fixture= new RestRequest(NULL, HttpConstants::GET);
      $this->assertEquals(HttpConstants::GET, $fixture->getMethod());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function setMethod() {
      $fixture= new RestRequest();
      $fixture->setMethod(HttpConstants::GET);
      $this->assertEquals(HttpConstants::GET, $fixture->getMethod());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withMethod() {
      $fixture= new RestRequest();
      $this->assertEquals($fixture, $fixture->withMethod(HttpConstants::GET));
      $this->assertEquals(HttpConstants::GET, $fixture->getMethod());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function setBody() {
      $request= new RequestData('{ "title" : "New issue" }');
      $fixture= new RestRequest();
      $fixture->setBody($request);
      $this->assertEquals($request, $fixture->getBody());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withBody() {
      $request= new RequestData('{ "title" : "New issue" }');
      $fixture= new RestRequest();
      $this->assertEquals($fixture, $fixture->withBody($request));
      $this->assertEquals($request, $fixture->getBody());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function hasNoBody() {
      $fixture= new RestRequest();
      $this->assertFalse($fixture->hasBody());
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function hasBody() {
      $fixture= new RestRequest();
      $fixture->setBody(new RequestData('{ "title" : "New issue" }'));
      $this->assertTrue($fixture->hasBody());
    }

    /**
     * Test payload
     *
     */
    #[@test]
    public function hasBodyWithJsonPayload() {
      $fixture= new RestRequest();
      $fixture->setPayload(array('title' => 'New issue'), new RestJsonSerializer());
      $this->assertTrue($fixture->hasBody());
    }

    /**
     * Test payload
     *
     */
    #[@test]
    public function getBodyWithJsonPayload() {
      $fixture= new RestRequest();
      $fixture->setPayload(array('title' => 'New issue'), new RestJsonSerializer());
      $this->assertEquals('{ "title" : "New issue" }', $fixture->getBody()->data);
    }

    /**
     * Test payload
     *
     */
    #[@test]
    public function hasBodyWithXmlPayload() {
      $fixture= new RestRequest();
      $fixture->setPayload(array('title' => 'New issue'), new RestXmlSerializer());
      $this->assertTrue($fixture->hasBody());
    }

    /**
     * Test payload
     *
     */
    #[@test]
    public function getBodyWithXmlPayload() {
      $fixture= new RestRequest();
      $fixture->setPayload(array('title' => 'New issue'), new RestXmlSerializer());
      $this->assertEquals(
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".
        '<root><title>New issue</title></root>', 
        $fixture->getBody()->data
      );
    }

    /**
     * Test payload
     *
     */
    #[@test]
    public function getBodyWithXmlPayloadAndRootNode() {
      $fixture= new RestRequest();
      $fixture->setPayload(array('title' => 'New issue'), new RestXmlSerializer('issue'));
      $this->assertEquals(
        '<?xml version="1.0" encoding="UTF-8"?>'."\n".
        '<issue><title>New issue</title></issue>', 
        $fixture->getBody()->data
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function noParameters() {
      $fixture= new RestRequest();
      $this->assertEquals(array(), $fixture->getParameters());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function oneParameter() {
      $fixture= new RestRequest();
      $fixture->addParameter('filter', 'assigned');
      $this->assertEquals(
        array('filter' => 'assigned'), 
        $fixture->getParameters()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoParameters() {
      $fixture= new RestRequest('/issues');
      $fixture->addParameter('filter', 'assigned');
      $fixture->addParameter('state', 'open');
      $this->assertEquals(
        array('filter' => 'assigned', 'state' => 'open'), 
        $fixture->getParameters()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function noSegments() {
      $fixture= new RestRequest();
      $this->assertEquals(array(), $fixture->getSegments());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function oneSegment() {
      $fixture= new RestRequest();
      $fixture->addSegment('user', 'thekid');
      $this->assertEquals(
        array('user' => 'thekid'), 
        $fixture->getSegments()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoSegments() {
      $fixture= new RestRequest('/issues');
      $fixture->addSegment('user', 'thekid');
      $fixture->addSegment('repo', 'xp-framework');
      $this->assertEquals(
        array('user' => 'thekid', 'repo' => 'xp-framework'), 
        $fixture->getSegments()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withSegmentReturnsThis() {
      $fixture= new RestRequest('/users/{user}');
      $this->assertEquals($fixture, $fixture->withSegment('user', 'thekid'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function withParameterReturnsThis() {
      $fixture= new RestRequest('/issues');
      $this->assertEquals($fixture, $fixture->withParameter('filter', 'assigned'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function targetWithoutParameters() {
      $fixture= new RestRequest('/issues');
      $this->assertEquals('/issues', $fixture->getTarget());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function targetWithSegmentParameter() {
      $fixture= new RestRequest('/users/{user}');
      $fixture->addSegment('user', 'thekid');
      $this->assertEquals('/users/thekid', $fixture->getTarget());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function targetWithTwoSegmentParameters() {
      $fixture= new RestRequest('/repos/{user}/{repo}');
      $fixture->addSegment('user', 'thekid');
      $fixture->addSegment('repo', 'xp-framework');
      $this->assertEquals('/repos/thekid/xp-framework', $fixture->getTarget());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function targetWithSegmentParametersAndConstantsMixed() {
      $fixture= new RestRequest('/repos/{user}/{repo}/issues/{id}');
      $fixture->addSegment('user', 'thekid');
      $fixture->addSegment('repo', 'xp-framework');
      $fixture->addSegment('id', 1);
      $this->assertEquals('/repos/thekid/xp-framework/issues/1', $fixture->getTarget());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function noHeaders() {
      $fixture= new RestRequest();
      $this->assertEquals(array(), $fixture->getHeaders());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function oneHeader() {
      $fixture= new RestRequest();
      $fixture->addHeader('Accept', 'text/xml');
      $this->assertEquals(
        array('Accept' => 'text/xml'), 
        $fixture->getHeaders()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoHeaders() {
      $fixture= new RestRequest('/issues');
      $fixture->addHeader('Accept', 'text/xml');
      $fixture->addHeader('Referer', 'http://localhost');
      $this->assertEquals(
        array('Accept' => 'text/xml', 'Referer' => 'http://localhost'), 
        $fixture->getHeaders()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function relativeResourceWithEndingSlash() {
      $fixture= new RestRequest('issues');
      $this->assertEquals('/rest/api/v2/issues', $fixture->getTarget('/rest/api/v2/'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function relativeResourceWithoutEndingSlash() {
      $fixture= new RestRequest('issues');
      $this->assertEquals('/rest/api/v2/issues', $fixture->getTarget('/rest/api/v2'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function acceptOne() {
      $fixture= new RestRequest('/issues');
      $fixture->addAccept('text/xml');
      $this->assertEquals(
        array('Accept' => 'text/xml'), 
        $fixture->getHeaders()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function accept() {
      $fixture= new RestRequest('/issues');
      $fixture->addAccept('text/xml');
      $fixture->addAccept('text/json');
      $this->assertEquals(
        array('Accept' => 'text/xml, text/json'), 
        $fixture->getHeaders()
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function acceptWithQ() {
      $fixture= new RestRequest('/issues');
      $fixture->addAccept('text/xml', '0.5');
      $fixture->addAccept('text/json', '0.8');
      $this->assertEquals(
        array('Accept' => 'text/xml;q=0.5, text/json;q=0.8'), 
        $fixture->getHeaders()
      );
    }
  }
?>
