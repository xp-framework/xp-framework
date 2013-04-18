<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.casters.ToFileData'
  );
  
  /**
   * Test the ToFileData caster
   *
   * @see       scriptlet.xml.workflow.casters.ToFileData
   * @purpose   ToFileData test
   */
  class ToFileDataTest extends TestCase {

    /**
     * Return the caster
     *
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    protected function caster() {
      return new ToFileData();
    }

    /**
     * Test single file upload
     *
     */
    #[@test]
    public function singleFileUpload() {
      $data= array(
        'name' => 'test.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => '/tmp/php1234',
        'error' => UPLOAD_ERR_OK,
        'size' => 12345
      );

      $casted= $this->caster()->castValue($data);
      $this->assertArray($casted);
      $this->assertEquals(1, count($casted));
      $this->assertClass($casted[0], 'scriptlet.xml.workflow.FileData');
      $this->assertClass($casted[0]->getFile(), 'io.File');
      $this->assertEquals($casted[0]->getName(), 'test.jpg');
      $this->assertEquals($casted[0]->getType(), 'image/jpeg');
      $this->assertEquals($casted[0]->getSize(), 12345);
    }

    /**
     * Multiple files upload
     *
     */
    #[@test]
    public function multipleFilesUpload() {
      $data= array(
        'name' => array('test.jpg', 'test2.jpg'),
        'type' => array('image/jpeg', 'image/jpeg'),
        'tmp_name' => array('/tmp/php1234', '/tmp/php5678'),
        'error' => array(UPLOAD_ERR_OK, UPLOAD_ERR_OK),
        'size' => array(12345, 67890)
      );

      $casted= $this->caster()->castValue($data);
      $this->assertArray($casted);
      $this->assertEquals(2, count($casted));

      $this->assertClass($casted[0], 'scriptlet.xml.workflow.FileData');
      $this->assertClass($casted[0]->getFile(), 'io.File');
      $this->assertEquals($casted[0]->getName(), 'test.jpg');
      $this->assertEquals($casted[0]->getType(), 'image/jpeg');
      $this->assertEquals($casted[0]->getSize(), 12345);

      $this->assertClass($casted[1], 'scriptlet.xml.workflow.FileData');
      $this->assertClass($casted[1]->getFile(), 'io.File');
      $this->assertEquals($casted[1]->getName(), 'test2.jpg');
      $this->assertEquals($casted[1]->getType(), 'image/jpeg');
      $this->assertEquals($casted[1]->getSize(), 67890);
    }
  }
?>
