<?php namespace net\xp_framework\unittest\xml;
 
use unittest\TestCase;
use xml\meta\Unmarshaller;
use io\streams\MemoryInputStream;
use xml\parser\StreamInputSource;

/**
 * Test Unmarshaller API
 *
 * @see    xp://xml.meta.Unmarshaller
 */
class UnmarshallerTest extends TestCase {
  protected $fixture= null;

  /**
   * Creates fixture
   */
  public function setUp() {
    $this->fixture= new Unmarshaller();
  }

  #[@test]
  public function idAttribute() {
    $dialog= $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream('
      <dialogtype id="file.open">
        <caption/>
      </dialogtype>')),
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertEquals('file.open', $dialog->getId());
  }

  #[@test]
  public function captionNode() {
    $dialog= $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream('
      <dialogtype id="">
        <caption>Open a file &gt; Choose</caption>
      </dialogtype>')),
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertEquals('Open a file > Choose', $dialog->getCaption());
  }
  
  #[@test]
  public function buttonsNodeSet() {
    $dialog= $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream('
      <dialogtype id="">
        <caption>Really delete the file &quot;Ü&quot;?</caption>
        <button id="ok">Yes, go ahead</button>
        <button id="cancel">No, please don\'t!</button>
      </dialogtype>')), 
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertTrue($dialog->hasButtons());
    $this->assertEquals(2, $dialog->numButtons());

    with ($ok= $dialog->buttonAt(0), $cancel= $dialog->buttonAt(1)); {
      $this->assertClass($ok, 'net.xp_framework.unittest.xml.ButtonType');
      $this->assertClass($cancel, 'net.xp_framework.unittest.xml.ButtonType');
      $this->assertEquals('ok', $ok->getId());
      $this->assertEquals('cancel', $cancel->getId());
      $this->assertEquals('Yes, go ahead', $ok->getCaption());
      $this->assertEquals('No, please don\'t!', $cancel->getCaption());
    }
  }
  
  #[@test]
  public function usingPassWithScalars() {
    $dialog= $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream('
      <dialogtype id="">
        <flags>ON_TOP|MODAL</flags>
      </dialogtype>')), 
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertEquals(array('ON_TOP', 'MODAL'), $dialog->getFlags());
  }
  
  #[@test]
  public function usingPassWithNodes() {
    $dialog= $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream('
      <dialogtype id="">
        <options>
          <option name="width" value="100"/>
          <option name="height" value="100"/>
        </options>
      </dialogtype>')), 
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertEquals(array(
      'width' => '100',
      'height' => '100'
    ), $dialog->getOptions());
  }

  #[@test]
  public function unmarshallingAnInputStream() {
    $dialog= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<dialogtype id="stream.select"/>'), 'memory'),
      'net.xp_framework.unittest.xml.DialogType'
    );
    $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
    $this->assertEquals('stream.select', $dialog->getId());
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function malformedStream() {
    $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<not-valid-xml'), 'memory'), 
      'net.xp_framework.unittest.xml.DialogType'
    );
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function emptyStream() {
    $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream(''), 'memory'), 
      'net.xp_framework.unittest.xml.DialogType'
    );
  }

  #[@test]
  public function deprecatedUsage() {
    $xml= '<dialogtype id="file.open"/>';
    $type= 'net.xp_framework.unittest.xml.DialogType';
    $this->assertEquals(
      Unmarshaller::unmarshal($xml, $type),
      $this->fixture->unmarshalFrom(new StreamInputSource(new MemoryInputStream($xml)), $type)
    );
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function malformedString() {
    Unmarshaller::unmarshal(
      '<not-valid-xml', 
      'net.xp_framework.unittest.xml.DialogType'
    );
  }

  #[@test, @expect('xml.XMLFormatException')]
  public function emptyString() {
    Unmarshaller::unmarshal(
      '', 
      'net.xp_framework.unittest.xml.DialogType'
    );
  }

  #[@test]
  public function nameBasedFactoryToDialog() {
    $object= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<dialog/>')),
      'net.xp_framework.unittest.xml.NameBasedTypeFactory'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.DialogType', $object);
  }

  #[@test]
  public function nameBasedFactoryToButton() {
    $object= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<button/>')),
      'net.xp_framework.unittest.xml.NameBasedTypeFactory'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.ButtonType', $object);
  }

  #[@test, @expect('lang.reflect.TargetInvocationException')]
  public function nameBasedFactoryToUnknown() {
    $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<unknown/>')),
      'net.xp_framework.unittest.xml.NameBasedTypeFactory'
    );
  }

  #[@test]
  public function idBasedFactoryToDialog() {
    $object= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<object id="dialog"/>')),
      'net.xp_framework.unittest.xml.IdBasedTypeFactory'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.DialogType', $object);
  }

  #[@test]
  public function idBasedFactoryToButton() {
    $object= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<object id="button"/>')),
      'net.xp_framework.unittest.xml.IdBasedTypeFactory'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.ButtonType', $object);
  }

  #[@test, @expect('lang.reflect.TargetInvocationException')]
  public function idBasedFactoryToUnknown() {
    $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<object id="unknown"/>')),
      'net.xp_framework.unittest.xml.IdBasedTypeFactory'
    );
  }

  #[@test]
  public function inject() {
    $window= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<window owner-window="main"/>')),
      'net.xp_framework.unittest.xml.WindowType',
      array('windows' => array(
        'main'     => 1,
        'desktop'  => 0
      ))
    );
    $this->assertEquals(1, $window->getOwnerWindow());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function injectionFails() {
    $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<window owner-window="main"/>')),
      'net.xp_framework.unittest.xml.WindowType'
    );
  }

  #[@test]
  public function namespaces() {
    $app= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<app:application xmlns:app="http://projects.xp-framework.net/xmlns/app"/>')),
      'net.xp_framework.unittest.xml.ApplicationType'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.ApplicationType', $app);
  }

  #[@test]
  public function casting() {
    $t= $this->fixture->unmarshalFrom(
      new StreamInputSource(new MemoryInputStream('<input id="name" disabled="true"/>')),
      'net.xp_framework.unittest.xml.TextInputType'
    );
    $this->assertInstanceOf('net.xp_framework.unittest.xml.TextInputType', $t);
    $this->assertTrue($t->getDisabled());
  }
}
