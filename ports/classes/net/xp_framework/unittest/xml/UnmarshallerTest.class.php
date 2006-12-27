<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.xml.DialogType',
    'xml.meta.Unmarshaller'
  );

  /**
   * Test Unmarshaller API
   *
   * @see      xp://xml.meta.Unmarshaller
   * @purpose  Unit Test
   */
  class UnmarshallerTest extends TestCase {

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
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType') &&
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
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType') &&
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
      $this->assertClass($dialog, 'net.xp_framework.unittest.xml.DialogType') &&
      $this->assertTrue($dialog->hasButtons()) &&
      $this->assertEquals(2, $dialog->numButtons()) &&

      with ($ok= $dialog->buttonAt(0), $cancel= $dialog->buttonAt(1)); {
        $this->assertClass($ok, 'net.xp_framework.unittest.xml.ButtonType') &&
        $this->assertClass($cancel, 'net.xp_framework.unittest.xml.ButtonType') &&
        $this->assertEquals('ok', $ok->getId()) &&
        $this->assertEquals('cancel', $cancel->getId()) &&
        $this->assertEquals('Yes, go ahead', $ok->getCaption()) &&
        $this->assertEquals('No, please don\'t!', $cancel->getCaption());
      }
    }
  }
?>
