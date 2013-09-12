<?php namespace net\xp_framework\unittest\io\collections;

/**
 * This base class does not contain any test methods and is meant to
 * be a base class for other io.collections API tests
 *
 * @see   xp://io.collections.IOCollection
 */
abstract class AbstractCollectionTest extends \unittest\TestCase {
  protected
    $fixture = null,
    $sizes   = array(),
    $total   = 0;

  /**
   * Assert an origin is based on a given origin.
   *
   * Assume we have the following situation:
   * <pre>
   *   IOCollection(/)
   *   `- IOCollection(/var)
   *      `- IOCollection(/var/log)
   *         `- IOElement (/var/log/messages)
   * </pre>
   *
   * This asserts that IOElement (/var/log/messages)'s origin is based
   * on IOCollection(/).
   *
   * @param   io.IOCollection base
   * @param   io.IOCollection origin
   * @throws  unittest.AssertionFailedError
   */
  protected function assertOriginBasedOn(\io\collections\IOCollection $base, \io\collections\IOCollection $origin) {
    $search= $origin;
    do {
      if ($search->equals($base)) return;
    } while (null !== ($search= $search->getOrigin()));
    throw new \unittest\AssertionFailedError('Not based on', $origin, $base);
  }

  /**
   * Returns a collection 
   *
   * @param   string name
   * @param   io.collections.IOElement[] elements
   * @return  io.collections.IOCollection
   */
  protected function newCollection($name, $elements= array()) {
    $c= new MockCollection($name);
    foreach ($elements as $element) {
      $c->addElement($element);
    }
    return $c;
  }

  /**
   * Adds an element to the given collection and increases the size counter
   *
   * @param   io.collection.IOCollection c
   * @param   io.collection.IOElement e
   * @return  io.collection.IOElement
   */
  public function addElement(\io\collections\IOCollection $c, \io\collections\IOElement $e) {
    $c->addElement($e);
    $this->total++;
    with ($key= $c->getURI()); {
      isset($this->sizes[$key]) ? $this->sizes[$key]++ : $this->sizes[$key]= 1;
    }
    return $e;
  }

  /**
   * Setup method 
   *
   */
  public function setUp() {
    $this->fixture= new MockCollection('.');
    
    // Warning: Changing this list will make some tests fail!
    $this->addElement($this->fixture, new MockElement(
      './first.txt', 
      1200, 
      \util\Date::fromString('Oct 10  2006'), // accessed
      \util\Date::fromString('Dec 14  2005'), // modified
      \util\Date::fromString('Oct 30  2005')  // created
    ));
    $this->addElement($this->fixture, new MockElement(
      './second.txt', 
      333, 
      \util\Date::fromString('Oct 10  2006'), // accessed
      \util\Date::fromString('Dec 24  2005'), // modified
      \util\Date::fromString('Oct 30  2005')  // created
    ));
    $this->addElement($this->fixture, new MockElement(
      './third.jpg', 
      18882, 
      \util\Date::fromString('Dec 11  2003'), // accessed
      \util\Date::fromString('Dec 10  2003'), // modified
      \util\Date::fromString('Dec 10  2003')  // created
    ));
    $this->addElement($this->fixture, new MockElement(
      './zerobytes.png', 
      0, 
      \util\Date::fromString('Dec 11  2003'), // accessed
      \util\Date::fromString('Dec 10  2003'), // modified
      \util\Date::fromString('Dec 10  2003')  // created
    ));

    with ($sub= $this->addElement($this->fixture, new MockCollection('./sub'))); {
      $this->addElement($sub, new MockElement(
        './sub/IMG_6100.jpg', 
        531718, 
        \util\Date::fromString('Mar  9  2006'), // accessed
        \util\Date::fromString('Mar  9  2006'), // modified
        \util\Date::fromString('Mar  9  2006')  // created
      ));
      $this->addElement($sub, new MockElement(
        './sub/IMG_6100.txt', 
        5932, 
        \util\Date::fromString('Mar 13  2006'), // accessed
        \util\Date::fromString('Mar 13  2006'), // modified
        \util\Date::fromString('Mar 13  2006')  // created
      ));

      with ($sec= $this->addElement($this->fixture, new MockCollection('./sub/sec'))); {
        $this->addElement($sec, new MockElement(
          './sub/sec/lang.base.php', 
          16739, 
          \util\Date::fromString('Oct 11  2006'), // accessed
          \util\Date::fromString('Oct 11  2006'), // modified
          \util\Date::fromString('Feb 21  2002')  // created
        ));
        $this->addElement($sec, new MockElement(
          './sub/sec/__xp__.php', 
          8589, 
          \util\Date::fromString('Oct  8  2006'), // accessed
          \util\Date::fromString('Oct  8  2006'), // modified
          \util\Date::fromString('Jul 23  2006')  // created
        ));
      }
    }
    
    // Self-check
    $this->assertEquals($this->total, array_sum($this->sizes));
  }
}
