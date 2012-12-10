<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.xml.DialogType',
    'net.xp_framework.unittest.xml.WindowType',
    'net.xp_framework.unittest.xml.ApplicationType',
    'net.xp_framework.unittest.xml.TextInputType',
    'net.xp_framework.unittest.xml.ScrollBarType',
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
     * @param   xml.Node node
     * @return  bool
     */
    public function assertMarshalled($expect, $node) {
      return $this->assertEquals(
        preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
        preg_replace('#>[\s\r\n]+<#', '><', trim($node->getSource(INDENT_DEFAULT)))
      );
    }

    /**
     * Tests marshalTo() returns the node its given
     *
     */
    #[@test]
    public function marshalToReturnsGivenNode() {
      $n= new Node('node');
      $this->assertEquals($n, $this->fixture->marshalTo($n, new Object()));
    }

    /**
     * Tests node name given to marshalTo() is used
     *
     */
    #[@test]
    public function nameOfNodeUsed() {
      $dialog= new DialogType();
      $this->assertMarshalled('
        <dialogtype id="">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>',
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }

    /**
     * Tests marshalTo() creates new node if none is given
     *
     */
    #[@test]
    public function marshalToCreatesNewNodeWhenNoneGiven() {
      $this->assertEquals(new Node('object'), $this->fixture->marshalTo(NULL, new Object()));
    }

    /**
     * Tests marshalTo() uses an xmlwrapped annotation for getting the name
     *
     * <code>
     *   #[@xmlwrapped('scroll')]
     * </code>
     *
     * @see  xp://net.xp_framework.unittest.xml.ScrollBarType
     */
    #[@test]
    public function wrappedAnnotationSuppliesName() {
      $this->assertEquals(new Node('scroll'), $this->fixture->marshalTo(NULL, new ScrollBarType()));
    }

    /**
     * Tests the dialog's id member gets serialized as an id attribute
     *
     */
    #[@test]
    public function idAttribute() {
      $dialog= new DialogType();
      $dialog->setId('file.open');
      
      $this->assertMarshalled('
        <dialogtype id="file.open">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
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
      
      $this->assertMarshalled('
        <dialogtype id="">
          <caption>Open a file &gt; Choose</caption>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }

    /**
     * Tests the dialog's buttons member gets serialized as a nodeset
     *
     */
    #[@test]
    public function buttonsNodeSet() {
      $dialog= new DialogType();
      $dialog->setCaption('Really delete the file "�"?');

      with ($ok= $dialog->addButton(new ButtonType())); {
        $ok->setId('ok');
        $ok->setCaption('Yes, go ahead');
      }
      with ($cancel= $dialog->addButton(new ButtonType())); {
        $cancel->setId('cancel');
        $cancel->setCaption('No, please don\'t!');
      }

      $this->assertMarshalled('
        <dialogtype id="">
          <caption>Really delete the file &quot;�&quot;?</caption>
          <button id="ok">Yes, go ahead</button>
          <button id="cancel">No, please don\'t!</button>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
      );
    }
    
    /**
     * Tests for a new dialog without any members set
     *
     */
    #[@test]
    public function emptyMembers() {
      $dialog= new DialogType();
      $this->assertMarshalled('
        <dialogtype id="">
          <caption/>
          <flags/>
          <options/>
        </dialogtype>', 
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)
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

    /**
     * Tests the deprecated usage
     *
     * @deprecated
     */
    #[@test]
    public function deprecatedUsage() {
      $dialog= new DialogType();
      $this->assertEquals(
        Marshaller::marshal($dialog),
        $this->fixture->marshalTo(new Node('dialogtype'), $dialog)->getSource(INDENT_DEFAULT)
      );
    }

    /**
     * Tests the deprecated usage
     *
     * @deprecated
     */
    #[@test]
    public function deprecatedUsageWithNamespace() {
      $app= new ApplicationType();
      $this->assertEquals(
        Marshaller::marshal($app),
        $this->fixture->marshalTo(new Node('ApplicationType'), $app)->getSource(INDENT_DEFAULT)
      );
    }

    /**
     * Test injection
     *
     * <code>
     *   #[@xmlfactory(element= '@owner-window', inject= array('window'))]
     * </code>
     *
     * @see   xp://net.xp_framework.unittest.xml.WindowType#getOwnerWindowName
     */
    #[@test]
    public function inject() {
      $window= create(new WindowType())->withOwnerWindow(1);
      $this->assertMarshalled(
        '<window owner-window="main"/>',
        $this->fixture->marshalTo(new Node('window'), $window, array('windows' => array(
          'main'     => 1,
          'desktop'  => 0
        )))
      );
    }

    /**
     * Test injection
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function injectionFails() {
      $window= create(new WindowType())->withOwnerWindow(1);
      $this->fixture->marshalTo(new Node('window'), $window);
    }

    /**
     * Test namespaces
     *
     * <code>
     *   #[@xmlns(app = 'http://projects.xp-framework.net/xmlns/app')]
     * </code>
     *
     * @see   xp://net.xp_framework.unittest.xml.ApplicationType
     */
    #[@test]
    public function namespaces() {
      $this->assertMarshalled(
        '<app:application xmlns:app="http://projects.xp-framework.net/xmlns/app"/>',
        $this->fixture->marshalTo(new Node('application'), new ApplicationType())
      );
    }

    /**
     * Tests casting
     *
     * <code>
     *   #[@xmlfactory(element = '@disabled', cast = 'toBool')]
     * </code>
     */
    #[@test]
    public function casting() {
      $t= new TextInputType();
      $t->setId('name');
      $t->setDisabled(TRUE);

      $this->assertMarshalled(
        '<input id="name" disabled="true"/>',
        $this->fixture->marshalTo(new Node('input'), $t)
      );
    }
  }
?>
