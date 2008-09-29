<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'text.doclet.TagletManager',
    'util.Hashmap',
    'unittest.TestCase'
  );

  /**
   * TestCase to verify parsing api doc comments.
   *
   * @see      xp://text.doclet.Doc#parseDetails
   * @purpose  Unittest
   */
  class CommentParserTest extends TestCase {

    /**
     * Helper method which parses the raw doc comment
     *
     * @param   string rawComment
     * @return  util.Hashmap
     */
    protected function parseDetail($rawComment) {
      $tm= TagletManager::getInstance();

      $stripped= preg_replace('/[\r\n][\s\t]+\* ?/', "\n", trim($rawComment, "/*\n\r\t "));
      $tagstart= FALSE === ($p= strpos($stripped, "\n@")) ? strlen($stripped)+ 1 : $p;

      $this->detail= array(
        'text' => substr($stripped, 0, $tagstart- 1),
        'tags' => array()
      );

      if ($t= strtok(trim(substr($stripped, $tagstart)), '@')) do {
        list($kind, $rest)= explode(' ', trim($t), 2);

        if ($tag= $tm->make($this, $kind, trim($rest))) {
          $this->detail['tags'][$kind][]= $tag;
        }
      } while ($t= strtok('@'));
      
      return new Hashmap($this->detail);
    }
    
    /**
     * Returns tags of specified kind. Also performs sanity-check. Basically 
     * a workaround for:
     * <code>
     *   $tags= $detail->get('tags')[$kind];
     * </code>
     * ...which is unfortunately not supported by PHP.
     *
     * @param   array<string, text.doclet.Tag[]> tags
     * @param   string kind
     * @return  text.doclet.Tag[] tags of specified kind 
     * @throws  unittest.AssertionFailedError in case tags does not have a key "kind"
     */
    protected function tags($tags, $kind) {
      $this->assertTrue(is_array($tags[$kind]));
      return $tags[$kind];
    }
  
    /**
     * Test a comment containing no tags.
     *
     */
    #[@test]
    public function commentWithoutTags() {
      $detail= $this->parseDetail('
        /**
         * This is a comment
         * 
         */
      ');
      $this->assertEquals('This is a comment', $detail->get('text'));
      $this->assertEmpty($detail->get('tags'));
    }

    /**
     * Test a comment contain a see-tag
     *
     */
    #[@test]
    public function commentWithSeeTag() {
      $detail= $this->parseDetail('
        /**
         * Replaces text using regular expressions.
         * 
         * @see   php://preg_replace
         */
      ');
      $this->assertEquals('Replaces text using regular expressions.', $detail->get('text'));
      with ($t= $this->tags($detail->get('tags'), 'see')); {
        $this->assertEquals(1, sizeof($t));
        $this->assertClass($t[0], 'text.doclet.SeeTag');
        $this->assertEquals('php', $t[0]->scheme);
        $this->assertEquals('preg_replace', $t[0]->urn);
      }
    }

    /**
     * Test a comment contain a see-tag and return-tag
     *
     */
    #[@test]
    public function commentWithMultipleTags() {
      $detail= $this->parseDetail('
        /**
         * Replaces text using regular expressions.
         * 
         * @see     php://preg_replace
         * @return  int number of matches
         */
      ');
      $this->assertEquals('Replaces text using regular expressions.', $detail->get('text'));
      with ($see= $this->tags($detail->get('tags'), 'see')); {
        $this->assertEquals(1, sizeof($see));
        $this->assertClass($see[0], 'text.doclet.SeeTag');
        $this->assertEquals('php', $see[0]->scheme);
        $this->assertEquals('preg_replace', $see[0]->urn);
      }
      with ($ret= $this->tags($detail->get('tags'), 'return')); {
        $this->assertEquals(1, sizeof($ret));
        $this->assertClass($ret[0], 'text.doclet.ReturnTag');
        $this->assertEquals('int', $ret[0]->type);
        $this->assertEquals('number of matches', $ret[0]->text);
      }
    }

    /**
     * Test a comment with inline code
     *
     */
    #[@test]
    public function commentWithInlineCode() {
      $detail= $this->parseDetail('
        /**
         * Example:
         * <code>
         *   $b= new Binford();
         *   echo $b->getClass()->getName();
         * </code>
         * 
         */
      ');
      $this->assertEquals(
        'Example:'."\n".
        '<code>'."\n".
        '  $b= new Binford();'."\n".
        '  echo $b->getClass()->getName();'."\n".
        '</code>',
        $detail->get('text')
      );
    }

    /**
     * Test a comment with inline code
     *
     */
    #[@test]
    public function commentWithInlineCommentedCode() {
      $detail= $this->parseDetail('
        /**
         * Example:
         * <code>
         *   $b= new Binford(73);   // *BLAM*
         * </code>
         * 
         */
      ');
      $this->assertEquals(
        'Example:'."\n".
        '<code>'."\n".
        '  $b= new Binford(73);   // *BLAM*'."\n".
        '</code>',
        $detail->get('text')
      );
    }
  }
?>
