<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestClient',
    'io.streams.MemoryInputStream',
    'net.xp_framework.unittest.webservices.rest.IssueWithField',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreField',
    'net.xp_framework.unittest.webservices.rest.IssueWithSetter',
    'net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreSetter'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestClient
   */
  class RestClientExecutionTest extends TestCase {
    protected $fixture= NULL;
    protected static $conn= NULL;   

    /**
     * Creates dummy connection class
     *
     */
    #[@beforeClass]
    public static function dummyConnectionClass() {
      self::$conn= ClassLoader::defineClass('RestClientExecutionTest_Connection', 'peer.http.HttpConnection', array(), '{
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
    public function fixtureWith($status, $body= NULL, $headers= array()) {
      $fixture= new RestClient();
      $fixture->setConnection(self::$conn->newInstance($status, $body, $headers));
      return $fixture;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function status() {
      $fixture= $this->fixtureWith(HttpConstants::STATUS_OK, '');
      $response= $fixture->execute(new RestRequest());
      $this->assertEquals(HttpConstants::STATUS_OK, $response->status());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function content() {
      $fixture= $this->fixtureWith(HttpConstants::STATUS_NOT_FOUND, 'Error');
      $response= $fixture->execute(new RestRequest());
      $this->assertEquals('Error', $response->content());
    }

    /**
     * Test
     *
     */
    #[@test, @expect('webservices.rest.RestException')]
    public function exception() {
      $fixture= $this->fixtureWith(new ConnectException('Cannot connect'));
      $fixture->execute(new RestRequest());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function jsonContent() {
      $fixture= $this->fixtureWith(HttpConstants::STATUS_OK, '{ "title" : "Found a bug" }', array(
        'Content-Type' => 'application/json'
      ));
      $response= $fixture->execute(new RestRequest());
      $this->assertEquals(array('title' => 'Found a bug'), $response->data());
    }
    
    /**
     * Helper method
     *
     * @param   string type
     * @throws  unittest.AssertionFailedError
     */
    protected function assertTypedJsonContentWith($type) {
      $fixture= $this->fixtureWith(HttpConstants::STATUS_OK, '{ "issue_id" : 1, "title" : "Found a bug" }', array(
        'Content-Type' => 'application/json'
      ));
      $class= XPClass::forName($type);
      $response= $fixture->execute($class, new RestRequest());
      $this->assertEquals($class->newInstance(1, 'Found a bug'), $response->data());
    }


    /**
     * Test
     *
     */
    #[@test]
    public function typedJsonContentWithUnderscoreField() {
      $this->assertTypedJsonContentWith('net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreField');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function typedJsonContentWithField() {
      $this->assertTypedJsonContentWith('net.xp_framework.unittest.webservices.rest.IssueWithField');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function typedJsonContentWithUnderscoreSetter() {
      $this->assertTypedJsonContentWith('net.xp_framework.unittest.webservices.rest.IssueWithUnderscoreSetter');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function typedJsonContentWithSetter() {
      $this->assertTypedJsonContentWith('net.xp_framework.unittest.webservices.rest.IssueWithSetter');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function typedJsonContentNull() {
      $fixture= $this->fixtureWith(
        HttpConstants::STATUS_OK, 
        '{ "issue_id" : 1, "title" : null }', 
        array('Content-Type' => 'application/json')
      );
      $class= Type::forName('net.xp_framework.unittest.webservices.rest.IssueWithField');
      $response= $fixture->execute($class, new RestRequest());
      $this->assertEquals(new net·xp_framework·unittest·webservices·rest·IssueWithField(1, NULL), $response->data());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function typedArrayJsonContent() {
      $fixture= $this->fixtureWith(
        HttpConstants::STATUS_OK, 
        '[ { "issue_id" : 1, "title" : "Found a bug" }, { "issue_id" : 2, "title" : "Another" } ]', 
        array('Content-Type' => 'application/json')
      );
      $class= Type::forName('net.xp_framework.unittest.webservices.rest.IssueWithField[]');
      $response= $fixture->execute($class, new RestRequest());
      $list= $response->data();
      $this->assertEquals(new net·xp_framework·unittest·webservices·rest·IssueWithField(1, 'Found a bug'), $list[0]);
      $this->assertEquals(new net·xp_framework·unittest·webservices·rest·IssueWithField(2, 'Another'), $list[1]);
    }
  }
?>
