<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractCollectionTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::io::collections;

  ::uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.io.collections.MockCollection'
  );

  /**
   * This base class does not contain any test methods and is meant to
   * be a base class for other io.collections API tests
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  Unit test
   */
  class AbstractCollectionTest extends unittest::TestCase {
    public
      $fixture = NULL,
      $sizes   = array(),
      $total   = 0;

    /**
     * Adds an element to the given collection and increases the size counter
     *
     * @return  &io.collection.IOCollection c
     * @return  &io.collection.IOElement e
     * @return  &io.collection.IOElement
     */
    public function addElement($c, $e) {
      $c->addElement($e);
      $this->total++;
      ::with ($key= $c->getURI()); {
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
        'first.txt', 
        1200, 
        util::Date::fromString('Oct 10  2006'), // accessed
        util::Date::fromString('Dec 14  2005'), // modified
        util::Date::fromString('Oct 30  2005')  // created
      ));
      $this->addElement($this->fixture, new MockElement(
        'second.txt', 
        333, 
        util::Date::fromString('Oct 10  2006'), // accessed
        util::Date::fromString('Dec 24  2005'), // modified
        util::Date::fromString('Oct 30  2005')  // created
      ));
      $this->addElement($this->fixture, new MockElement(
        'third.jpg', 
        18882, 
        util::Date::fromString('Dec 11  2003'), // accessed
        util::Date::fromString('Dec 10  2003'), // modified
        util::Date::fromString('Dec 10  2003')  // created
      ));
      $this->addElement($this->fixture, new MockElement(
        'zerobytes.png', 
        0, 
        util::Date::fromString('Dec 11  2003'), // accessed
        util::Date::fromString('Dec 10  2003'), // modified
        util::Date::fromString('Dec 10  2003')  // created
      ));

      ::with ($sub= $this->addElement($this->fixture, new MockCollection('sub'))); {
        $this->addElement($sub, new MockElement(
          'sub/IMG_6100.jpg', 
          531718, 
          util::Date::fromString('Mar  9  2006'), // accessed
          util::Date::fromString('Mar  9  2006'), // modified
          util::Date::fromString('Mar  9  2006')  // created
        ));
        $this->addElement($sub, new MockElement(
          'sub/IMG_6100.txt', 
          5932, 
          util::Date::fromString('Mar 13  2006'), // accessed
          util::Date::fromString('Mar 13  2006'), // modified
          util::Date::fromString('Mar 13  2006')  // created
        ));

        ::with ($sec= $this->addElement($this->fixture, new MockCollection('sub/sec'))); {
          $this->addElement($sec, new MockElement(
            'sub/sec/lang.base.php', 
            16739, 
            util::Date::fromString('Oct 11  2006'), // accessed
            util::Date::fromString('Oct 11  2006'), // modified
            util::Date::fromString('Feb 21  2002')  // created
          ));
          $this->addElement($sec, new MockElement(
            'sub/sec/__xp__.php', 
            8589, 
            util::Date::fromString('Oct  8  2006'), // accessed
            util::Date::fromString('Oct  8  2006'), // modified
            util::Date::fromString('Jul 23  2006')  // created
          ));
        }
      }
      
      // Self-check
      $this->assertEquals($this->total, array_sum($this->sizes));
    }
  }
?>
