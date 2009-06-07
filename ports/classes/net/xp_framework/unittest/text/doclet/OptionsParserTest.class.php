<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.RootDoc',
    'text.doclet.Doclet'
  );

  /**
   * TestCase
   *
   * @see      xp://text.doclet.RootDoc
   */
  class OptionsParserTest extends TestCase {
    protected $root= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->root= new RootDoc();
    }
    
    /**
     * Returns a doclet that returns the options
     *
     * @return  text.doclet.Doclet
     */
    protected function optionsDoclet() {
      return newinstance('text.doclet.Doclet', array(), '{
        public function start(RootDoc $root) {
          return $this->options;
        }

        public function validOptions() {
          return array("verbose" => OPTION_ONLY, "output" => HAS_VALUE);
        }
      }'); 
    }
    
    /**
     * Test options
     *
     */
    #[@test]
    public function withoutOptions() {
      $r= $this->root->start(
        $this->optionsDoclet(), 
        new ParamString(array($this->name))
      );
      $this->assertEquals(array(), $r);
    }

    /**
     * Test options
     *
     */
    #[@test]
    public function shortVerboseOption() {
      $r= $this->root->start(
        $this->optionsDoclet(), 
        new ParamString(array($this->name, '-verbose'))
      );
      $this->assertEquals(array('verbose' => TRUE), $r);
    }

    /**
     * Test options
     *
     */
    #[@test]
    public function longVerboseOption() {
      $r= $this->root->start(
        $this->optionsDoclet(), 
        new ParamString(array($this->name, '--verbose'))
      );
      $this->assertEquals(array('verbose' => TRUE), $r);
    }

    /**
     * Test options
     *
     */
    #[@test]
    public function shortOutputOption() {
      $r= $this->root->start(
        $this->optionsDoclet(), 
        new ParamString(array($this->name, '-output', '/home/httpd/docs'))
      );
      $this->assertEquals(array('output' => '/home/httpd/docs'), $r);
    }

    /**
     * Test options
     *
     */
    #[@test]
    public function longOutputOption() {
      $r= $this->root->start(
        $this->optionsDoclet(), 
        new ParamString(array($this->name, '--output=/home/httpd/docs'))
      );
      $this->assertEquals(array('output' => '/home/httpd/docs'), $r);
    }
  }
?>
