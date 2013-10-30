<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestClient;
use io\streams\MemoryInputStream;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestClient
 */
class RestClientExecutionTest extends TestCase {
  protected $fixture= null;
  protected static $conn= null;   

  /**
   * Creates dummy connection class
   *
   */
  #[@beforeClass]
  public static function dummyConnectionClass() {
    self::$conn= \lang\ClassLoader::defineClass('RestClientExecutionTest_Connection', 'peer.http.HttpConnection', array(), '{
      protected $result= NULL;
      protected $exception= NULL;

      public function __construct($status, $body, $headers) {
        parent::__construct("http://test");
        if ($status instanceof Throwable) {
          $this->exception= $status;
        } else {
          $this->result= "HTTP/1.1 ".$status."\r\n";
          foreach ($headers as $name => $value) {
            $this->result.= $name.": ".$value."\r\n";
          }
          $this->result.= "\r\n".$body;
        }
      }
      
      public function send(HttpRequest $request) {
        if ($this->exception) {
          throw $this->exception;
        } else {
          return new HttpResponse(new MemoryInputStream($this->result));
        }
      }
    }');
  }
  
  /**
   * Creates a new fixture
   *
   * @param   var status either an int with a status code or an exception object
   * @param   string body default NULL
   * @param   [:string] headers default [:]
   * @return  webservices.rest.RestClient
   */
  public function fixtureWith($status, $body= null, $headers= array()) {
    $fixture= new RestClient();
    $fixture->setConnection(self::$conn->newInstance($status, $body, $headers));
    return $fixture;
  }

  #[@test]
  public function status() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_OK, '');
    $response= $fixture->execute(new \webservices\rest\RestRequest());
    $this->assertEquals(\peer\http\HttpConstants::STATUS_OK, $response->status());
  }

  #[@test]
  public function content() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_NOT_FOUND, 'Error');
    $response= $fixture->execute(new \webservices\rest\RestRequest());
    $this->assertEquals('Error', $response->content());
  }

  #[@test, @expect('webservices.rest.RestException')]
  public function exception() {
    $fixture= $this->fixtureWith(new \peer\ConnectException('Cannot connect'));
    $fixture->execute(new \webservices\rest\RestRequest());
  }
  
  #[@test]
  public function jsonContent() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_OK, '{ "title" : "Found a bug" }', array(
      'Content-Type' => 'application/json'
    ));
    $response= $fixture->execute(new \webservices\rest\RestRequest());
    $this->assertEquals(array('title' => 'Found a bug'), $response->data());
  }

  #[@test]
  public function xmlContent() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_OK, '<issue><title>Found a bug</title></issue>', array(
      'Content-Type' => 'text/xml'
    ));
    $response= $fixture->execute(new \webservices\rest\RestRequest());
    $this->assertEquals(array('title' => 'Found a bug'), $response->data(\lang\Type::forName('[:var]')));
  }
  
  #[@test]
  public function customContent() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_NO_CONTENT, '', array(
      'Content-Type' => 'application/json'
    ));
    $class= \lang\Type::forName('net.xp_framework.unittest.webservices.rest.CustomRestResponse');
    $response= $fixture->execute($class, new \webservices\rest\RestRequest());
    $this->assertInstanceOf($class, $response);
    $this->assertNull($response->data());
  }

  #[@test]
  public function deprecatedExecuteOverloading() {
    $fixture= $this->fixtureWith(\peer\http\HttpConstants::STATUS_OK, '{ "title" : "Found a bug" }', array(
      'Content-Type' => 'application/json'
    ));
    $response= $fixture->execute('[:var]', new \webservices\rest\RestRequest());
    $this->assertEquals(array('title' => 'Found a bug'), $response->data());
  }
}
