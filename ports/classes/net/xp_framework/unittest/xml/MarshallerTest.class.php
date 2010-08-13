<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.xml.DialogType',
    'xml.meta.Marshaller'
  );

  /**
   * Test Marshaller API
   *
   * @see      xp://xml.meta.Marshaller
   * @purpose  Unit Test
   */
  class MarshallerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new Marshaller();
    }

    /**
     * Compares XML after stripping all whitespace between tags of both 
     * expected and actual strings.
     *
     * @see     xp://unittest.TestCase#assertEquals
     * @param   string expect
     * @param   string actual
     * @return  bool
     */
    public function assertXmlEquals($expect, $actual) {
      return $this->assertEquals(
        preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($actual))
      );
    }

    /**
     * Tests the dialog's id member gets serialized as an id attribute
     *
     */
    #[@test]
    public function idAttribute() {
      $dialog= new DialogType();
      $dialog->setId('file.open');
      
      $this->assertXmlEquals('
        <dialogtype id="file.open">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        Marshaller::marshal($dialog)
      );
    }
    
    /**
     * Tests the dialog's caption member gets serialized as a node
     *
     */
    #[@test]
    public function captionNode() {
      $dialog= new DialogType();
      $dialog->setCaption('Open a file > Choose');
      
      $this->assertXmlEquals('
        <dialogtype id="">
          <caption>Open a file &gt; Choose</caption>
          <flags/>
          <options/>
        </dialogtype>', 
        Marshaller::marshal($dialog)
      );
    }

    /**
     * Tests the dialog's buttons member gets serialized as a nodeset
     *
     */
    #[@test]
    public function buttonsNodeSet() {
      $dialog= new DialogType();
      $dialog->setCaption('Really delete the file "Ü"?');

      with ($ok= $dialog->addButton(new ButtonType())); {
        $ok->setId('ok');
        $ok->setCaption('Yes, go ahead');
      }
      with ($cancel= $dialog->addButton(new ButtonType())); {
        $cancel->setId('cancel');
        $cancel->setCaption('No, please don\'t!');
      }

      $this->assertXmlEquals('
        <dialogtype id="">
          <caption>Really delete the file &quot;Ü&quot;?</caption>
          <button id="ok">Yes, go ahead</button>
          <button id="cancel">No, please don\'t!</button>
          <flags/>
          <options/>
        </dialogtype>', 
        Marshaller::marshal($dialog)
      );
    }
    
    /**
     * Tests for a new dialog without any members set
     *
     */
    #[@test]
    public function emptyMembers() {
      $dialog= new DialogType();
      $this->assertXmlEquals('
        <dialogtype id="">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        Marshaller::marshal($dialog)
      );
    }

    /**
     * Tests the dialog's id member gets serialized as an id attribute
     *
     */
    #[@test]
    public function asTree() {
      $dialog= new DialogType();
      $dialog->setId('file.open');

      $node= $this->fixture->marshalTo(new Node('dialog'), $dialog);
      $this->assertInstanceOf('xml.Node', $node);
      $this->assertEquals('dialog', $node->getName());
      $this->assertEquals('file.open', $node->getAttribute('id'));
    }
  }
?>
