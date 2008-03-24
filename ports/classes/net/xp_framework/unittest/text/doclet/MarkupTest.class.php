<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.doclet.markup.MarkupBuilder'
  );

  /**
   * TestCase
   *
   * @see      xp://text.doclet.markup.MarkupBuilder
   * @purpose  purpose
   */
  class MarkupTest extends TestCase {
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->builder= new MarkupBuilder();
    }
    
    /**
     * Tears down test case
     *
     */
    public function tearDown() {
      delete($this->builder);
    }
    
    /**
     * Test ul / li
     *
     */
    #[@test]
    public function unorderedList() {
      $this->assertEquals(
        '<ul><li>Item 1</li><li>Item 2</li></ul>',
        $this->builder->markupFor('<ul><li>Item 1</li><li>Item 2</li></ul>')
      );
    }

    /**
     * Test ol / li
     *
     */
    #[@test]
    public function orderedList() {
      $this->assertEquals(
        '<ol><li>Item 1</li><li>Item 2</li></ol>',
        $this->builder->markupFor('<ol><li>Item 1</li><li>Item 2</li></ol>')
      );
    }

    /**
     * Test <tt> ... </tt> is left as-is
     *
     */
    #[@test]
    public function ttIsLeftAsIs() {
      $this->assertEquals(
        'The class <tt>lang.types.ArrayList</tt> is ...',
        $this->builder->markupFor('The class <tt>lang.types.ArrayList</tt> is ...')
      );
    }

    /**
     * Test <pre> ... </pre> becomes preformatted
     *
     */
    #[@test]
    public function preBecomesPreformatted() {
      $this->assertEquals(
        "</p><pre>This is text\nWhitespace is relevant</pre><p>",
        $this->builder->markupFor("<pre>This is text\nWhitespace is relevant</pre>")
      );
    }

    /**
     * Test <xmp> ... </xmp> becomes preformatted
     *
     */
    #[@test]
    public function xmpBecomesPreformatted() {
      $this->assertEquals(
        '</p><pre>Person { id =&gt; 1549 }</pre><p>',
        $this->builder->markupFor('<xmp>Person { id => 1549 }</xmp>')
      );
    }

    /**
     * Test contents of <pre> ... </pre> are trimmed of leading and 
     * trailing newlines (but not whitespace!)
     *
     */
    #[@test]
    public function preformattedIsTrimmed() {
      $this->assertEquals(
        "</p><pre>  This is text\nWhitespace is relevant</pre><p>",
        $this->builder->markupFor("<pre>\n  This is text\nWhitespace is relevant\n</pre>")
      );
    }

    /**
     * Test special characters inside <pre> ... </pre> are escaped
     *
     */
    #[@test]
    public function specialsInsidePreformatted() {
      $this->assertEquals(
        "</p><pre>&lt;&amp;&gt;</pre><p>",
        $this->builder->markupFor("<pre><&></pre>")
      );
    }

    /**
     * Test <code> ... </code>
     *
     */
    #[@test]
    public function code() {
      $this->assertEquals(
        '</p><code>'.
        '<span class="variable">$a</span>'.
        '<span class="default">++;</span>'.
        '</code><p>',
        $this->builder->markupFor('<code>$a++;</code>')
      );
    }

    /**
     * Test <code></code>
     *
     */
    #[@test]
    public function emptyCode() {
      $this->assertEquals(
        '</p><code></code><p>',
        $this->builder->markupFor('<code></code>')
      );
    }

    /**
     * Test contents of <code> ... </code> are trimmed of leading and 
     * trailing newlines (but not whitespace!)
     *
     */
    #[@test]
    public function codeIsTrimmed() {
      $this->assertEquals(
        '</p><code>'.
        '<span class="default">  </span>'.
        '<span class="variable">$a</span>'.
        '<span class="default">++;</span>'.
        '</code><p>',
        $this->builder->markupFor("<code>\n  \$a++;\n</code>")
      );
    }

    /**
     * Test special characters inside <code> ... </code> are escaped
     *
     */
    #[@test]
    public function specialsInsideCode() {
      $this->assertEquals(
        '</p><code>'.
        '<span class="variable">$a</span>'.
        '<span class="default">-&gt;next</span>'.
        '<span class="bracket">()</span>'.
        '<span class="default"> &amp;&amp; </span>'.
        '<span class="variable">$b</span>'.
        '<span class="default"> &lt; </span>'.
        '<span class="number">5</span>'.
        '<span class="default">;</span>'.
        '</code><p>',
        $this->builder->markupFor('<code>$a->next() && $b < 5;</code>')
      );
    }

    /**
     * Test XML in string inside <code> ... </code> 
     *
     */
    #[@test]
    public function xmlInsideCodeString() {
      $this->assertEquals(
        '</p><code>'.
        '<span class="variable">$a</span>'.
        '<span class="default">= </span>'.
        '<span class="string">&quot;&lt;dialog&gt;&lt;button&gt;OK&lt;/button&gt;&lt;/dialog&gt;&quot;</span>'.
        '<span class="default">;</span>'.
        '</code><p>',
        $this->builder->markupFor('<code>$a= "<dialog><button>OK</button></dialog>";</code>')
      );
    }

    /**
     * Test <code> ... </code>
     *
     */
    #[@test]
    public function newLinesInCode() {
      $this->assertEquals(
        '</p><code>'.
        '<span class="variable">$a</span>'.
        '<span class="default">++;<br/></span>'.
        '<span class="variable">$b</span>'.
        '<span class="default">++;</span>'.
        '</code><p>',
        $this->builder->markupFor('<code>$a++;'."\n".'$b++;</code>')
      );
    }

    /**
     * Test double newlines become brs
     *
     */
    #[@test]
    public function doubleNewLine() {
      $this->assertEquals(
        "Sentence 1<br/><br/>\nSentence 2",
        $this->builder->markupFor("Sentence 1\n\nSentence 2")
      );
    }

    /**
     * Test generic declaration is escaped
     *
     */
    #[@test]
    public function genericDeclaration() {
      $this->assertEquals(
        'Generic: util.collections.Vector&lt;lang.types.String&gt;',
        $this->builder->markupFor('Generic: util.collections.Vector<lang.types.String>')
      );
    }

    /**
     * Test ampersand (&) is escaped
     *
     */
    #[@test]
    public function ampersand() {
      $this->assertEquals('&amp;', $this->builder->markupFor('&'));
    }

    /**
     * Test smaller than (<) is escaped
     *
     */
    #[@test]
    public function standaloneSmallerThan() {
      $this->assertEquals('1 &lt; 2', $this->builder->markupFor('1 < 2'));
    }

    /**
     * Test greater than (>) is escaped
     *
     */
    #[@test]
    public function standaloneGreaterThan() {
      $this->assertEquals('2 &gt; 1', $this->builder->markupFor('2 > 1'));
    }

    /**
     * Test something not a tag but enclosed in <> is escaped
     *
     */
    #[@test]
    public function notATag() {
      $this->assertEquals('&lt;&gt;', $this->builder->markupFor('<>'));
    }

    /**
     * Test http://-link
     *
     */
    #[@test]
    public function httpLink() {
      $this->assertEquals(
        '<link href="http://xp-framework.net/"/>',
        $this->builder->markupFor('http://xp-framework.net/')
      );
    }

    /**
     * Test https://-link
     *
     */
    #[@test]
    public function httpsLink() {
      $this->assertEquals(
        '<link href="https://login.xp-framework.net/"/>',
        $this->builder->markupFor('https://login.xp-framework.net/')
      );
    }

    /**
     * Test mailto:-link
     *
     */
    #[@test]
    public function mailtoLink() {
      $this->assertEquals(
        '<mailto recipient="john.doe@example.com"/>',
        $this->builder->markupFor('mailto:john.doe@example.com')
      );
    }

    /**
     * Test bug-link
     *
     */
    #[@test]
    public function bugLink() {
      $this->assertEquals(
        '<bug id="10"/>',
        $this->builder->markupFor('bug #10')
      );
    }

    /**
     * Test blogentry-link
     *
     */
    #[@test]
    public function entryLink() {
      $this->assertEquals(
        '<blogentry id="10"/>',
        $this->builder->markupFor('entry #10')
      );
    }

    /**
     * Test blogcategory-link
     *
     */
    #[@test]
    public function categoryLink() {
      $this->assertEquals(
        '<blogcategory id="10"/>',
        $this->builder->markupFor('category #10')
      );
    }

    /**
     * Test :-) and :)
     *
     */
    #[@test]
    public function regularSmile() {
      $this->assertEquals(
        '<emoticon id="regular_smile" text=":-)"/>', 
        $this->builder->markupFor(':-)'),
        ':-)'
      );
      $this->assertEquals(
        '<emoticon id="regular_smile" text=":)"/>', 
        $this->builder->markupFor(':)'),
        ':)'
      );
    }

    /**
     * Test :-( and :(
     *
     */
    #[@test]
    public function sadSmile() {
      $this->assertEquals(
        '<emoticon id="sad_smile" text=":-("/>', 
        $this->builder->markupFor(':-('),
        ':-('
      );
      $this->assertEquals(
        '<emoticon id="sad_smile" text=":("/>', 
        $this->builder->markupFor(':('),
        ':('
      );
    }

    /**
     * Test ;-) and ;)
     *
     */
    #[@test]
    public function winkSmile() {
      $this->assertEquals(
        '<emoticon id="wink_smile" text=";-)"/>', 
        $this->builder->markupFor(';-)'),
        ';-)'
      );
      $this->assertEquals(
        '<emoticon id="wink_smile" text=";)"/>', 
        $this->builder->markupFor(';)'),
        ';)'
      );
    }

    /**
     * Test h1
     *
     */
    #[@test]
    public function headline1() {
      $this->assertEquals(
        '</p><h1>Headline1</h1><p>', 
        $this->builder->markupFor("\nHeadline1\n=========\n")
      );
    }

    /**
     * Test h2
     *
     */
    #[@test]
    public function headline2() {
      $this->assertEquals(
        '</p><h2>Headline2</h2><p>', 
        $this->builder->markupFor("\nHeadline2\n---------\n")
      );
    }

    /**
     * Test h3
     *
     */
    #[@test]
    public function headline3() {
      $this->assertEquals(
        '</p><h3>Headline3</h3><p>', 
        $this->builder->markupFor("\nHeadline3\n~~~~~~~~~\n")
      );
    }

    /**
     * Test underline
     *
     */
    #[@test]
    public function underline() {
      $this->assertEquals(
        '<u>Underlined</u>', 
        $this->builder->markupFor("_Underlined_")
      );
    }

    /**
     * Test italic
     *
     */
    #[@test]
    public function italic() {
      $this->assertEquals(
        '<i>Italic</i>', 
        $this->builder->markupFor("/Italic/")
      );
    }

    /**
     * Test bold
     *
     */
    #[@test]
    public function bold() {
      $this->assertEquals(
        '<b>Bold</b>', 
        $this->builder->markupFor("*Bold*")
      );
    }
  }
?>
