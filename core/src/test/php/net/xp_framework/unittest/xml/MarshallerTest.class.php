<?php namespace net\xp_framework\unittest\xml;
 
use unittest\TestCase;
use xml\meta\Marshaller;

/**
 * Test Marshaller API
 *
 * @see   xp://xml.meta.Marshaller
 */
class MarshallerTest extends TestCase {
  protected $fixture= null;

  /**
   * Creates fixture
   */
  public function setUp() {
    $this->fixture= new Marshaller();
  }

  /**
   * Compares XML after stripping all whitespace between tags of both 
   * expected and actual strings.
   *
   * @see     xp://unittest.TestCase#assertEquals
   * @param   string $expect
   * @param   xml.Node $node
   * @throws  unittest.AssertionFailedError
   */
  public function assertMarshalled($expect, $node) {
    $this->assertEquals(
      preg_replace('#>[\s\r\n]+<#', '><', trim($expect)),
      preg_replace('#>[\s\r\n]+<#', '><', trim($node->getSource(INDENT_DEFAULT)))
    );
  }

  #[@test]
  public function marshalToReturnsGivenNode() {
    $n= new \xml\Node('node');
    $this->assertEquals($n, $this->fixture->marshalTo($n, new \lang\Object()));
  }

  #[@test]
  public function nameOfNodeUsed() {
    $dialog= new DialogType();
    $this->assertMarshalled('
      <dialogtype id="">
        <caption/>
        <flags/>
        <options/>
      </dialogtype>',
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)
    );
  }

  #[@test]
  public function marshalToCreatesNewNodeWhenNoneGiven() {
    $this->assertEquals(new \xml\Node('object'), $this->fixture->marshalTo(null, new \lang\Object()));
  }

  #[@test]
  public function classAnnotationSuppliesName() {
    $this->assertEquals(new \xml\Node('scroll'), $this->fixture->marshalTo(null, new ScrollBarType()));
  }

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
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)
    );
  }
  
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
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)
    );
  }

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

    $this->assertMarshalled('
      <dialogtype id="">
        <caption>Really delete the file &quot;Ü&quot;?</caption>
        <button id="ok">Yes, go ahead</button>
        <button id="cancel">No, please don\'t!</button>
        <flags/>
        <options/>
      </dialogtype>', 
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)
    );
  }
  
  #[@test]
  public function emptyMembers() {
    $dialog= new DialogType();
    $this->assertMarshalled('
      <dialogtype id="">
        <caption/>
        <flags/>
        <options/>
      </dialogtype>', 
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)
    );
  }

  #[@test]
  public function asTree() {
    $dialog= new DialogType();
    $dialog->setId('file.open');

    $node= $this->fixture->marshalTo(new \xml\Node('dialog'), $dialog);
    $this->assertInstanceOf('xml.Node', $node);
    $this->assertEquals('dialog', $node->getName());
    $this->assertEquals('file.open', $node->getAttribute('id'));
  }

  #[@test]
  public function deprecatedUsage() {
    $dialog= new DialogType();
    $this->assertEquals(
      Marshaller::marshal($dialog),
      $this->fixture->marshalTo(new \xml\Node('dialogtype'), $dialog)->getSource(INDENT_DEFAULT)
    );
  }

  #[@test]
  public function deprecatedUsageWithNamespace() {
    $app= new ApplicationType();
    $this->assertEquals(
      Marshaller::marshal($app),
      $this->fixture->marshalTo(new \xml\Node('ApplicationType'), $app)->getSource(INDENT_DEFAULT)
    );
  }

  #[@test]
  public function inject() {
    $window= create(new WindowType())->withOwnerWindow(1);
    $this->assertMarshalled(
      '<window owner-window="main"/>',
      $this->fixture->marshalTo(new \xml\Node('window'), $window, array('windows' => array(
        'main'     => 1,
        'desktop'  => 0
      )))
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function injectionFails() {
    $window= create(new WindowType())->withOwnerWindow(1);
    $this->fixture->marshalTo(new \xml\Node('window'), $window);
  }

  #[@test]
  public function namespaces() {
    $this->assertMarshalled(
      '<app:application xmlns:app="http://projects.xp-framework.net/xmlns/app"/>',
      $this->fixture->marshalTo(new \xml\Node('application'), new ApplicationType())
    );
  }

  #[@test]
  public function casting() {
    $t= new TextInputType();
    $t->setId('name');
    $t->setDisabled(true);

    $this->assertMarshalled(
      '<input id="name" disabled="true"/>',
      $this->fixture->marshalTo(new \xml\Node('input'), $t)
    );
  }
}
