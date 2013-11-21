<?php namespace xp\unittest;

use unittest\TestListener;
use unittest\TestCase;
use unittest\TestFailure;
use unittest\TestSkipped;
use unittest\TestWarning;
use unittest\TestSuccess;
use unittest\TestResult;
use unittest\PrerequisitesNotMetError;
use io\streams\OutputStreamWriter;
use io\streams\FileInputStream;
use io\streams\TextReader;
use xml\DomXSLProcessor;
use xml\Node;
use xml\CData;
use io\FileUtil;
use io\File;
use lang\Runtime;

/**
 * Coverage listener
 *
 * @ext   xdebug
 */
class CoverageListener extends \lang\Object implements TestListener {
  private $paths= array();
  private $processor= null;
  private $reportFile= 'coverage.html';

  /**
   * register a path to include in coverage report
   *
   * @param string
   */
  #[@arg]
  public function setRegisterPath($path) {
    $this->paths[]= $path;
  }

  /**
   * set path for the report file
   *
   * @param string
   */
  #[@arg]
  public function setReportFile($reportFile) {
    $this->reportFile= $reportFile;
  }

  /**
   * Constructor
   *
   * @param io.streams.OutputStreamWriter out
   */
  public function __construct() {
    if (!Runtime::getInstance()->extensionAvailable('xdebug')) {
      throw new PrerequisitesNotMetError('code coverage not available. Please install the xdebug extension.');
    }

    $this->processor= new DomXSLProcessor();
    $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource('coverage.xsl'));

  }

  /**
   * Called when a test case starts.
   *
   * @param   unittest.TestCase failure
   */
  public function testStarted(\unittest\TestCase $case) {
    // Empty
  }

  /**
   * Called when a test fails.
   *
   * @param   unittest.TestFailure failure
   */
  public function testFailed(\unittest\TestFailure $failure) {
    // Empty
  }

  /**
   * Called when a test errors.
   *
   * @param   unittest.TestFailure error
   */
  public function testError(\unittest\TestError $error) {
    // Empty
  }

  /**
   * Called when a test raises warnings.
   *
   * @param   unittest.TestWarning warning
   */
  public function testWarning(\unittest\TestWarning $warning) {
    // Empty
  }

  /**
   * Called when a test finished successfully.
   *
   * @param   unittest.TestSuccess success
   */
  public function testSucceeded(\unittest\TestSuccess $success) {
    // Empty
  }

  /**
   * Called when a test is not run because it is skipped due to a
   * failed prerequisite.
   *
   * @param   unittest.TestSkipped skipped
   */
  public function testSkipped(\unittest\TestSkipped $skipped) {
    // Empty
  }

  /**
   * Called when a test is not run because it has been ignored by using
   * the @ignore annotation.
   *
   * @param   unittest.TestSkipped ignore
   */
  public function testNotRun(\unittest\TestSkipped $ignore) {
    // Empty
  }

  /**
   * Called when a test run starts.
   *
   * @param   unittest.TestSuite suite
   */
  public function testRunStarted(\unittest\TestSuite $suite) {
    xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
  }

  /**
   * Called when a test run finishes.
   *
   * @param   unittest.TestSuite suite
   * @param   unittest.TestResult result
   */
  public function testRunFinished(\unittest\TestSuite $suite, TestResult $result) {
    $coverage= xdebug_get_code_coverage();
    xdebug_stop_code_coverage();

    $results= array();
    foreach ($coverage as $fileName => $data) {
      foreach ($this->paths as $path) {
        if (substr($fileName, 0, strlen($path)) !== $path) {
          continue;
        }

        $results[dirname($fileName)][basename($fileName)]= $data;
        break;
      }
    }

    $pathsNode= new Node('paths');
    foreach ($results as $pathName => $files) {
      $pathNode= new Node('path');
      $pathNode->setAttribute('name', $pathName);

      foreach ($files as $fileName => $data) {
        $fileNode= new Node('file');
        $fileNode->setAttribute('name', $fileName);

        $num= 1;
        $reader= new TextReader(new FileInputStream($pathName.'/'.$fileName));
        while (($line = $reader->readLine()) !== null) {
          $lineNode = new Node('line', new CData($line));
          if (isset($data[$num])) {
            if (1 === $data[$num]) {
              $lineNode->setAttribute('checked', 'checked');
            } elseif (-1 === $data[$num]) {
              $lineNode->setAttribute('unchecked', 'unchecked');
            }
          }

          $fileNode->addChild($lineNode);
          ++$num;
        }

        $pathNode->addChild($fileNode);
      }
      $pathsNode->addChild($pathNode);
    }
    $pathsNode->setAttribute('time', date('Y-m-d H:i:s'));

    $this->processor->setXMLBuf($pathsNode->getSource());
    $this->processor->run();

    FileUtil::setContents(new File($this->reportFile), $this->processor->output());
  }
}