<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestJsonDeserializer',
    'net.xp_framework.unittest.webservices.rest.CustomRestResponse'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestResponse
   */
  class CustomRestResponseTest extends TestCase {

    /**
     * Creates a new custom response
     *
     * @param   int status
     * @param   string body
     * @return  net.xp_framework.unittest.webservices.rest.CustomRestResponse
     */
    protected function newResponse($status, $body) {
      return new CustomRestResponse(
        new HttpResponse(new MemoryInputStream(sprintf(
          "HTTP/1.1 %d Test\r\nContent-Type: application/json\r\nContent-Length: %d\r\n\r\n%s",
          $status,
          strlen($body),
          $body
        ))),
        new RestJsonDeserializer(),
        Type::forName('[:var]')
      );
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function ok() {
      $this->assertEquals(array(), $this->newResponse(200, '{ }')->data());
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function customErrorHandling() {
      try {
        $this->newResponse(400, '{ "server.message" : "Operation timed out" }')->data();
        $this->fail('No exception caught', NULL, 'CustomRestException');
      } catch (CustomRestException $expected) {
        $this->assertEquals('Operation timed out', $expected->serverMessage());
      }
    }

    /**
     * Test data()
     *
     */
    #[@test]
    public function customContentHandling() {
      $this->assertEquals(NULL, $this->newResponse(204, '')->data());
    }
  }
?>
