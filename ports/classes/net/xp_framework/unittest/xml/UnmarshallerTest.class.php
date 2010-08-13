<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.xml.DialogType',
    'xml.meta.Unmarshaller',
    'io.streams.MemoryInputStream',
    'xml.parser.StreamInputSource'
  );

  /**
   * Test Unmarshaller API
   *
   * @see      xp://xml.meta.Unmarshaller
   * @purpose  Unit Test
   */
  class UnmarshallerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new Unmarshaller();
    }

    /**
     * Tests the id attribute gets unserialized as the dialog's id member
     *
     */
    #[@test]
    public function idAttribute() {
      $dialog= Unmarshaller::unmarshal('
        <dialogtype id="file.open">
          <caption/>
        </dialogtype>',
        'net.xp_framework.unittest.xml.DialogType'
      );
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
      $this->assertEquals('file.open', $dialog->getId());
    }

    /**
     * Tests the caption node gets unserialized as the dialog's caption member
     *
     */
    #[@test]
    public function captionNode() {
      $dialog= Unmarshaller::unmarshal('
        <dialogtype id="">
          <caption>Open a file &gt; Choose</caption>
        </dialogtype>',
        'net.xp_framework.unittest.xml.DialogType'
      );
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
      $this->assertEquals('Open a file > Choose', $dialog->getCaption());
    }
    
    /**
     * Tests the buttons get unserialized to a button collection
     *
     */
    #[@test]
    public function buttonsNodeSet() {
      $dialog= Unmarshaller::unmarshal('
        <dialogtype id="">
          <caption>Really delete the file &quot;Ü&quot;?</caption>
          <button id="ok">Yes, go ahead</button>
          <button id="cancel">No, please don\'t!</button>
        </dialogtype>', 
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
    
    /**
     * Test pass attribute when used with scalar results, e.g.
     *
     * #[@xmlmapping(element= 'flags', pass= array(count(.)))]
     */
    #[@test]
    public function usingPassWithScalars() {
      $dialog= Unmarshaller::unmarshal('
        <dialogtype id="">
          <flags>ON_TOP|MODAL</flags>
        </dialogtype>', 
        'net.xp_framework.unittest.xml.DialogType'
      );
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
      $this->assertEquals(array('ON_TOP', 'MODAL'), $dialog->getFlags());
    }
    
    /**
     * Test pass attribute when used with nodeset results, e.g.
     *
     * #[@xmlmapping(element= 'option', pass= array('@name'))]
     */
    #[@test]
    public function usingPassWithNodes() {
      $dialog= Unmarshaller::unmarshal('
        <dialogtype id="">
          <options>
            <option name="width" value="100"/>
            <option name="height" value="100"/>
          </options>
        </dialogtype>', 
        'net.xp_framework.unittest.xml.DialogType'
      );
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
      $this->assertEquals(array(
        'width' => '100',
        'height' => '100'
      ), $dialog->getOptions());
    }

    /**
     * Test unmarshalling from a stream
     *
     */
    #[@test]
    public function unmarshallingAnInputStream() {
      $dialog= $this->fixture->unmarshalFrom(
        new StreamInputSource(new MemoryInputStream('<dialogtype id="stream.select"/>'), 'memory'),
        'net.xp_framework.unittest.xml.DialogType'
      );
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType');
      $this->assertEquals('stream.select', $dialog->getId());
    }

    /**
     * Test unmarshalling malformed data
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function malformedString() {
      Unmarshaller::unmarshal(
        '<not-valid-xml', 
        'net.xp_framework.unittest.xml.DialogType'
      );
    }

    /**
     * Test unmarshalling empty data
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function emptyString() {
      Unmarshaller::unmarshal(
        '', 
        'net.xp_framework.unittest.xml.DialogType'
      );
    }

    /**
     * Test unmarshalling malformed data
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function malformedStream() {
      $this->fixture->unmarshalFrom(
        new StreamInputSource(new MemoryInputStream('<not-valid-xml'), 'memory'), 
        'net.xp_framework.unittest.xml.DialogType'
      );
    }

    /**
     * Test unmarshalling empty data
     *
     */
    #[@test, @expect('xml.XMLFormatException')]
    public function emptyStream() {
      $this->fixture->unmarshalFrom(
        new StreamInputSource(new MemoryInputStream(''), 'memory'), 
        'net.xp_framework.unittest.xml.DialogType'
      );
    }
  }
?>
