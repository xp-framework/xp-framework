<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'ant.AntFileset',
    'xml.meta.Unmarshaller'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class AntFilesetTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function verboseStructure() {
      $this->assertStructure(Unmarshaller::unmarshal('
        <fileset dir="${server.src}">
          <patternset>
            <include name="**/*.java"/>
            <exclude name="**/*Test*"/>
          </patternset>
        </fileset>',
        'ant.AntFileset'
      ));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function abbreviatedStructure() {
      $this->assertStructure(Unmarshaller::unmarshal('
        <fileset dir="${server.src}">
          <include name="**/*.java"/>
          <exclude name="**/*Test*"/>
        </fileset>',
        'ant.AntFileset'
      ));      
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function attributeStructure() {
      $this->assertStructure(Unmarshaller::unmarshal('
        <fileset dir="${server.src}"
          includes="**/*.java"
          excludes="**/*Test*"
        />',
        'ant.AntFileset'
      ));
    }

    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function assertStructure($u) {
      $this->assertClass($u, 'ant.AntFileset');
      $this->assertClass($u->patternset, 'ant.AntPatternSet');
      $this->assertEquals(1, sizeof($u->patternset->includes));
      $this->assertClass($u->patternset->includes[0], 'ant.AntPattern');
      $this->assertEquals(1, sizeof($u->patternset->excludes));
      $this->assertClass($u->patternset->excludes[0], 'ant.AntPattern');      
    }
    
  }
?>
