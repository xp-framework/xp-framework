<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'xml.rdf.RDFNewsFeed');

  /**
   * TestCase
   *
   * @see  xp://xml.rdf.RDFNewsFeed
   */
  class RDFNewsFeedTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function can_create() {
      new RDFNewsFeed();
    }

    /**
     * Test getSource()
     *
     */
    #[@test]
    public function source_of_new_newsfeed() {
      $f= new RDFNewsFeed();
      $this->assertEquals(
        '<rdf:RDF'.
        ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.
        ' xmlns:dc="http://purl.org/dc/elements/1.1/"'.
        ' xmlns="http://purl.org/rss/1.0/">'.
        '</rdf:RDF>',
        $f->getSource(INDENT_NONE)
      );
    }

    /**
     * Test getSource() and setChannel()
     *
     */
    #[@test]
    public function source_of_newsfeed_with_channel() {
      $f= new RDFNewsFeed();
      $f->setChannel('Channel', 'http://example.com/channel', 'Description', new Date('2013-02-27 10:37:12 +01:00'));
      $this->assertEquals(
        '<rdf:RDF'.
        ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.
        ' xmlns:dc="http://purl.org/dc/elements/1.1/"'.
        ' xmlns="http://purl.org/rss/1.0/">'.
          '<channel rdf:about="http://example.com/channel">'.
            '<title>Channel</title>'.
            '<link>http://example.com/channel</link>'.
            '<description>Description</description>'.
            '<dc:language></dc:language>'.
            '<dc:date>2013-02-27T10:37:12+01:00</dc:date>'.
            '<dc:creator></dc:creator>'.
            '<dc:publisher></dc:publisher>'.
            '<dc:rights></dc:rights>'.
            '<items><rdf:Seq></rdf:Seq></items>'.
          '</channel>'.
        '</rdf:RDF>',
        $f->getSource(INDENT_NONE)
      );
    }

    /**
     * Test getSource() and addItem()
     *
     */
    #[@test]
    public function source_of_newsfeed_with_channel_and_item() {
      $f= new RDFNewsFeed();
      $f->setChannel('Channel', 'http://example.com/channel', 'Description', new Date('2013-02-27 10:37:12 +01:00'));
      $f->addItem('Item', 'http://example.com/channel/item/1', 'Description', new Date('2013-02-28 14:12:36 +01:00'));
      $this->assertEquals(
        '<rdf:RDF'.
        ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.
        ' xmlns:dc="http://purl.org/dc/elements/1.1/"'.
        ' xmlns="http://purl.org/rss/1.0/">'.
          '<channel rdf:about="http://example.com/channel">'.
            '<title>Channel</title>'.
            '<link>http://example.com/channel</link>'.
            '<description>Description</description>'.
            '<dc:language></dc:language>'.
            '<dc:date>2013-02-27T10:37:12+01:00</dc:date>'.
            '<dc:creator></dc:creator>'.
            '<dc:publisher></dc:publisher>'.
            '<dc:rights></dc:rights>'.
            '<items>'.
              '<rdf:Seq><rdf:li rdf:resource="http://example.com/channel/item/1"></rdf:li></rdf:Seq>'.
            '</items>'.
          '</channel>'.
          '<item rdf:about="http://example.com/channel/item/1">'.
            '<title>Item</title>'.
            '<link>http://example.com/channel/item/1</link>'.
            '<description>Description</description>'.
            '<dc:date>2013-02-28T14:12:36+01:00</dc:date>'.
          '</item>'.
        '</rdf:RDF>',
        $f->getSource(INDENT_NONE)
      );
    }
  }
?>
