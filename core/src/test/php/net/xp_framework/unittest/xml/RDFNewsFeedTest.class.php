<?php namespace net\xp_framework\unittest\xml;

use unittest\TestCase;
use xml\rdf\RDFNewsFeed;

/**
 * TestCase
 *
 * @see  xp://xml.rdf.RDFNewsFeed
 */
class RDFNewsFeedTest extends TestCase {

  #[@test]
  public function can_create() {
    new RDFNewsFeed();
  }

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

  #[@test]
  public function source_of_newsfeed_with_channel() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', 'http://example.com/channel', 'Description', new \util\Date('2013-02-27 10:37:12 +01:00'));
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

  #[@test]
  public function source_of_newsfeed_with_channel_and_item() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', 'http://example.com/channel', 'Description', new \util\Date('2013-02-27 10:37:12 +01:00'));
    $f->addItem('Item', 'http://example.com/channel/item/1', 'Description', new \util\Date('2013-02-28 14:12:36 +01:00'));
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
            '<rdf:Seq>'.
              '<rdf:li rdf:resource="http://example.com/channel/item/1"></rdf:li>'.
            '</rdf:Seq>'.
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

  #[@test]
  public function source_of_newsfeed_with_channel_and_two_items() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', 'http://example.com/channel', 'Description', new \util\Date('2013-02-27 10:37:12 +01:00'));
    $f->addItem('Item 1', 'http://example.com/channel/item/1', 'Description 1', new \util\Date('2013-02-28 14:12:36 +01:00'));
    $f->addItem('Item 2', 'http://example.com/channel/item/2', 'Description 2', new \util\Date('2013-02-28 14:12:37 +01:00'));
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
            '<rdf:Seq>'.
              '<rdf:li rdf:resource="http://example.com/channel/item/1"></rdf:li>'.
              '<rdf:li rdf:resource="http://example.com/channel/item/2"></rdf:li>'.
            '</rdf:Seq>'.
          '</items>'.
        '</channel>'.
        '<item rdf:about="http://example.com/channel/item/1">'.
          '<title>Item 1</title>'.
          '<link>http://example.com/channel/item/1</link>'.
          '<description>Description 1</description>'.
          '<dc:date>2013-02-28T14:12:36+01:00</dc:date>'.
        '</item>'.
        '<item rdf:about="http://example.com/channel/item/2">'.
          '<title>Item 2</title>'.
          '<link>http://example.com/channel/item/2</link>'.
          '<description>Description 2</description>'.
          '<dc:date>2013-02-28T14:12:37+01:00</dc:date>'.
        '</item>'.
      '</rdf:RDF>',
      $f->getSource(INDENT_NONE)
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function setChannel_only_accepts_a_date() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', '/', 'Description', 'I am not a date');
  }

  #[@test]
  public function setChannel_accepting_date() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', 'http://localhost/', 'Channel description', new \util\Date('1980-05-28'), 'english', 'Alex Kiesel', 'Alex Kiesel', 'rights');

    $this->assertEquals(
      '<rdf:RDF'.
      ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.
      ' xmlns:dc="http://purl.org/dc/elements/1.1/"'.
      ' xmlns="http://purl.org/rss/1.0/">'.
        '<channel rdf:about="http://localhost/">'.
          '<title>Channel</title>'.
          '<link>http://localhost/</link>'.
          '<description>Channel description</description>'.
          '<dc:language>english</dc:language>'.
          '<dc:date>1980-05-28T00:00:00+02:00</dc:date>'.
          '<dc:creator>Alex Kiesel</dc:creator>'.
          '<dc:publisher>Alex Kiesel</dc:publisher>'.
          '<dc:rights>rights</dc:rights>'.
          '<items><rdf:Seq></rdf:Seq></items>'.
        '</channel>'.
      '</rdf:RDF>',
      $f->getSource(INDENT_NONE)
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function addItem_only_accepts_a_date() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', '/', 'Channel desc');
    $f->addItem('Item', '/', 'Desc', 'I am not a date');
  }

  #[@test]
  public function addItem_accepting_date() {
    $f= new RDFNewsFeed();
    $f->setChannel('Channel', 'http://localhost/', 'Channel description', new \util\Date('1980-05-28'), 'english', 'Alex Kiesel', 'Alex Kiesel', 'rights');
    $f->addItem('Item 1', 'http://localhost/item/1', 'Item description', new \util\Date('2013-04-03'));

    $this->assertEquals(
      '<rdf:RDF'.
      ' xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.
      ' xmlns:dc="http://purl.org/dc/elements/1.1/"'.
      ' xmlns="http://purl.org/rss/1.0/">'.
        '<channel rdf:about="http://localhost/">'.
          '<title>Channel</title>'.
          '<link>http://localhost/</link>'.
          '<description>Channel description</description>'.
          '<dc:language>english</dc:language>'.
          '<dc:date>1980-05-28T00:00:00+02:00</dc:date>'.
          '<dc:creator>Alex Kiesel</dc:creator>'.
          '<dc:publisher>Alex Kiesel</dc:publisher>'.
          '<dc:rights>rights</dc:rights>'.
          '<items>'.
            '<rdf:Seq>'.
              '<rdf:li rdf:resource="http://localhost/item/1"></rdf:li>'.
            '</rdf:Seq>'.
          '</items>'.
        '</channel>'.
        '<item rdf:about="http://localhost/item/1">'.
          '<title>Item 1</title>'.
          '<link>http://localhost/item/1</link>'.
          '<description>Item description</description>'.
          '<dc:date>2013-04-03T00:00:00+02:00</dc:date>'.
        '</item>'.
      '</rdf:RDF>',
      $f->getSource(INDENT_NONE)
    );
  }
}
