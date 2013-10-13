<?php namespace net\xp_framework\unittest\text\csv;

use unittest\TestCase;
use text\csv\AbstractCsvProcessor;
use text\csv\processors\constraint\Optional;
use text\csv\processors\constraint\Required;


/**
 * TestCase
 *
 * @see      xp://text.csv.AbstractCsvProcessor
 */
class ProcessorAccessorsTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= newinstance('text.csv.AbstractCsvProcessor', array(), '{
    }');
  }
  
  /**
   * Test setting and getting cell processors
   *
   */
  #[@test]
  public function setProcessors() {
    $processors= array(new Optional(), new Required());
    $this->fixture->setProcessors($processors);
    $this->assertEquals($processors, $this->fixture->getProcessors());
  }

  /**
   * Test setting and getting cell processors
   *
   */
  #[@test]
  public function withProcessors() {
    $processors= array(new Optional(), new Required());
    $this->fixture->withProcessors($processors);
    $this->assertEquals($processors, $this->fixture->getProcessors());
  }

  /**
   * Test withProcessor() method returns fixture
   *
   */
  #[@test]
  public function withProcessorsReturnsFixture() {
    $this->assertEquals($this->fixture, $this->fixture->withProcessors(array()));
  }

  /**
   * Test adding processors
   *
   */
  #[@test]
  public function addProcessor() {
    $processors= array(new Optional(), new Required());
    $this->fixture->addProcessor($processors[0]);
    $this->fixture->addProcessor($processors[1]);
    $this->assertEquals($processors, $this->fixture->getProcessors());
  }
  /**
   * Test adding processors
   *
   */
  #[@test]
  public function addProcessorReturnsProcessor() {
    $processor= new Optional();
    $this->assertEquals($processor, $this->fixture->addProcessor($processor));
  }
}
