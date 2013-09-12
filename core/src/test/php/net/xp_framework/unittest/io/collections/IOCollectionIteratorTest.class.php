<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\iterate\IOCollectionIterator;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\AccessedAfterFilter;
use io\collections\iterate\AccessedBeforeFilter;
use io\collections\iterate\CreatedAfterFilter;
use io\collections\iterate\CreatedBeforeFilter;
use io\collections\iterate\IterationFilter;
use io\collections\iterate\ModifiedAfterFilter;
use io\collections\iterate\ModifiedBeforeFilter;
use io\collections\iterate\NameMatchesFilter;
use io\collections\iterate\NameEqualsFilter;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\UriMatchesFilter;
use io\collections\iterate\SizeBiggerThanFilter;
use io\collections\iterate\SizeEqualsFilter;
use io\collections\iterate\SizeSmallerThanFilter;
use io\collections\iterate\AllOfFilter;
use io\collections\iterate\AnyOfFilter;

/**
 * Unit tests for I/O collection iterator classes
 *
 * @see      xp://io.collections.IOCollectionIterator
 * @see      xp://io.collections.FilteredIOCollectionIterator
 * @purpose  Unit test
 */
class IOCollectionIteratorTest extends AbstractCollectionTest {

  /**
   * Test IOCollectionIterator
   *
   */
  #[@test]
  public function iteration() {
    for ($it= new IOCollectionIterator($this->fixture), $i= 0; $it->hasNext(); $i++) {
      $element= $it->next();
      $this->assertSubclass($element, 'io.collections.IOElement');
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
  }

  /**
   * Test IOCollectionIterator
   *
   */
  #[@test]
  public function recursiveIteration() {
    for ($it= new IOCollectionIterator($this->fixture, true), $i= 0; $it->hasNext(); $i++) {
      $element= $it->next();
      $this->assertSubclass($element, 'io.collections.IOElement');
    }
    $this->assertEquals($this->total, $i);
  }

  /**
   * Test use within foreach()
   *
   */
  #[@test]
  public function foreachLoop() {
    foreach (new IOCollectionIterator($this->fixture) as $i => $e) {
      $this->assertSubclass($e, 'io.collections.IOElement');
    }
    $this->assertEquals($this->sizes[$this->fixture->getURI()]- 1, $i);
  }

  /**
   * Test use within foreach()
   *
   */
  #[@test]
  public function foreachLoopRecursive() {
    foreach (new IOCollectionIterator($this->fixture, true) as $i => $e) {
      $this->assertSubclass($e, 'io.collections.IOElement');
    }
    $this->assertEquals($this->total- 1, $i);
  }

  /**
   * Helper method
   *
   * @param   io.collections.iterate.Filter filter
   * @param   bool recursive default FALSE
   * @return  string[] an array of the elements' URIs
   */
  protected function filterFixtureWith($filter, $recursive= false) {
    $elements= array();
    for (
      $it= new FilteredIOCollectionIterator($this->fixture, $filter, $recursive);
      $it->hasNext(); 
    ) {
      $e= $it->next();
      $this->assertSubclass($e, 'io.collections.IOElement');
      $elements[]= $e->getURI();
    }
    return $elements;
  }

  /**
   * Test FilteredIOCollectionIterator
   *
   */
  #[@test]
  public function filteredIteration() {
    $this->assertEquals(
      $this->sizes[$this->fixture->getURI()],
      sizeof($this->filterFixtureWith(new NullFilter(), false))
    );
  }

  /**
   * Test FilteredIOCollectionIterator
   *
   */
  #[@test]
  public function filteredRecursiveIteration() {
    $this->assertEquals(
      $this->total,
      sizeof($this->filterFixtureWith(new NullFilter(), true))
    );
  }

  /**
   * Test NameMatchesFilter
   *
   * @see     xp://io.collections.iterate.NameMatchesFilter
   */
  #[@test]
  public function nameMatches() {
    $this->assertEquals(
      array('./first.txt', './second.txt'), 
      $this->filterFixtureWith(new NameMatchesFilter('/\.txt$/'), false)
    );
  }

  /**
   * Test NameMatchesFilter
   *
   * @see     xp://io.collections.iterate.NameMatchesFilter
   */
  #[@test]
  public function nameMatchesRecursive() {
    $this->assertEquals(
      array('./first.txt', './second.txt', './sub/IMG_6100.txt'), 
      $this->filterFixtureWith(new NameMatchesFilter('/\.txt$/'), true)
    );
  }

  /**
   * Test NameEqualsFilter
   *
   * @see     xp://io.collections.iterate.NameMatchesFilter
   */
  #[@test]
  public function nameEquals() {
    $this->assertEquals(
      array(), 
      $this->filterFixtureWith(new NameEqualsFilter('__xp__.php'), false)
    );
  }

  /**
   * Test NameEqualsFilter
   *
   * @see     xp://io.collections.iterate.NameMatchesFilter
   */
  #[@test]
  public function nameEqualsRecursive() {
    $this->assertEquals(
      array('./sub/sec/__xp__.php'), 
      $this->filterFixtureWith(new NameEqualsFilter('__xp__.php'), true)
    );
  }

  /**
   * Test extensionEqualsFilter
   *
   * @see     xp://io.collections.iterate.extensionMatchesFilter
   */
  #[@test]
  public function extensionEquals() {
    $this->assertEquals(
      array(), 
      $this->filterFixtureWith(new ExtensionEqualsFilter('.php'), false)
    );
  }

  /**
   * Test extensionEqualsFilter
   *
   * @see     xp://io.collections.iterate.extensionMatchesFilter
   */
  #[@test]
  public function extensionEqualsRecursive() {
    $this->assertEquals(
      array('./sub/sec/lang.base.php', './sub/sec/__xp__.php'), 
      $this->filterFixtureWith(new ExtensionEqualsFilter('.php'), true)
    );
  }

  /**
   * Test UriMatchesFilter
   *
   * @see     xp://io.collections.iterate.UriMatchesFilter
   */
  #[@test]
  public function uriMatches() {
    $this->assertEquals(
      array('./first.txt', './second.txt'),
      $this->filterFixtureWith(new UriMatchesFilter('/\.txt$/'), false)
    );
  }

  /**
   * Test UriMatchesFilter
   *
   * @see     xp://io.collections.iterate.UriMatchesFilter
   */
  #[@test]
  public function uriMatchesRecursive() {
    $this->assertEquals(
      array('./sub/', './sub/IMG_6100.jpg', './sub/IMG_6100.txt', './sub/sec/', './sub/sec/lang.base.php', './sub/sec/__xp__.php'),
      $this->filterFixtureWith(new UriMatchesFilter('/sub/'), true)
    );
  }

  /**
   * Test UriMatchesFilter
   *
   * @see     xp://io.collections.iterate.UriMatchesFilter
   */
  #[@test]
  public function uriMatchesDirectorySeparators() {
    with ($src= $this->addElement($this->fixture, new MockCollection('./sub/src'))); {
      $this->addElement($src, new MockElement('./sub/src/Generic.xp')); 
    }
    $this->assertEquals(
      array('./sub/src/Generic.xp'),
      $this->filterFixtureWith(new UriMatchesFilter('/sub\/src\/.+/'), true)
    );
  }

  /**
   * Test UriMatchesFilter
   *
   * @see     xp://io.collections.iterate.UriMatchesFilter
   */
  #[@test]
  public function uriMatchesPlatformDirectorySeparators() {
    $mockName= '.'.DIRECTORY_SEPARATOR.'sub'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Generic.xp';
    with ($src= $this->addElement($this->fixture, new MockCollection('.'.DIRECTORY_SEPARATOR.'sub'.DIRECTORY_SEPARATOR.'src'))); {
      $this->addElement($src, new MockElement($mockName));
    }
    $this->assertEquals(
      array($mockName),
      $this->filterFixtureWith(new UriMatchesFilter('/sub\/src\/.+/'), true)
    );
  }
  
  /**
   * Test SizeEqualsFilter
   *
   * @see     xp://io.collections.iterate.SizeEqualsFilter
   */
  #[@test]
  public function zeroBytes() {
    $this->assertEquals(
      array('./zerobytes.png'), 
      $this->filterFixtureWith(new SizeEqualsFilter(0), false)
    );
  }

  /**
   * Test SizeBiggerThanFilter
   *
   * @see     xp://io.collections.iterate.SizeBiggerThanFilter
   */
  #[@test]
  public function bigFiles() {
    $this->assertEquals(
      array('./sub/IMG_6100.jpg'), 
      $this->filterFixtureWith(new SizeBiggerThanFilter(500000), true)
    );
  }

  /**
   * Test SizeBiggerThanFilter
   *
   * @see     xp://io.collections.iterate.SizeBiggerThanFilter
   */
  #[@test]
  public function smallFiles() {
    $this->assertEquals(
      array('./second.txt', './zerobytes.png'), 
      $this->filterFixtureWith(new SizeSmallerThanFilter(500), true)
    );
  }

  /**
   * Test AccessedAfterFilter
   *
   * @see     xp://io.collections.iterate.AccessedAfterFilter
   */
  #[@test]
  public function accessedAfter() {
    $this->assertEquals(
      array('./first.txt', './second.txt', './sub/sec/lang.base.php', './sub/sec/__xp__.php'), 
      $this->filterFixtureWith(new AccessedAfterFilter(new \util\Date('Oct  1  2006')), true)
    );
  }

  /**
   * Test AccessedBeforeFilter
   *
   * @see     xp://io.collections.iterate.AccessedBeforeFilter
   */
  #[@test]
  public function accessedBefore() {
    $this->assertEquals(
      array('./third.jpg', './zerobytes.png'), 
      $this->filterFixtureWith(new AccessedBeforeFilter(new \util\Date('Dec 14  2004')), true)
    );
  }

  /**
   * Test ModifiedAfterFilter
   *
   * @see     xp://io.collections.iterate.ModifiedAfterFilter
   */
  #[@test]
  public function modifiedAfter() {
    $this->assertEquals(
      array('./sub/sec/lang.base.php', './sub/sec/__xp__.php'), 
      $this->filterFixtureWith(new ModifiedAfterFilter(new \util\Date('Oct  7  2006')), true)
    );
  }

  /**
   * Test ModifiedBeforeFilter
   *
   * @see     xp://io.collections.iterate.ModifiedBeforeFilter
   */
  #[@test]
  public function modifiedBefore() {
    $this->assertEquals(
      array('./third.jpg', './zerobytes.png'), 
      $this->filterFixtureWith(new ModifiedBeforeFilter(new \util\Date('Dec 14  2004')), true)
    );
  }

  /**
   * Test CreatedAfterFilter
   *
   * @see     xp://io.collections.iterate.CreatedAfterFilter
   */
  #[@test]
  public function createdAfter() {
    $this->assertEquals(
      array('./sub/sec/__xp__.php'), 
      $this->filterFixtureWith(new CreatedAfterFilter(new \util\Date('Jul  1  2006')), true)
    );
  }

  /**
   * Test CreatedBeforeFilter
   *
   * @see     xp://io.collections.iterate.CreatedBeforeFilter
   */
  #[@test]
  public function createdBefore() {
    $this->assertEquals(
      array('./sub/sec/lang.base.php'), 
      $this->filterFixtureWith(new CreatedBeforeFilter(new \util\Date('Feb 22  2002')), true)
    );
  }

  /**
   * Test AllOfFilter
   *
   * @see     xp://io.collections.iterate.AllOfFilter
   */
  #[@test]
  public function allOf() {
    $this->assertEquals(
      array('./third.jpg'), 
      $this->filterFixtureWith(new AllOfFilter(array(
        new ModifiedBeforeFilter(new \util\Date('Dec 14  2004')),
        new ExtensionEqualsFilter('jpg')
      )), true)
    );
  }

  /**
   * Test AnyOfFilter
   *
   * @see     xp://io.collections.iterate.AnyOfFilter
   */
  #[@test]
  public function anyOf() {
    $this->assertEquals(
      array('./first.txt', './second.txt', './zerobytes.png', './sub/IMG_6100.txt'), 
      $this->filterFixtureWith(new AnyOfFilter(array(
        new SizeSmallerThanFilter(500),
        new ExtensionEqualsFilter('txt')
      )), true)
    );
  }

  /**
   * Test getOrigin()
   *
   */
  #[@test]
  public function originBasedOn() {
    $c= $this->newCollection('/home', array(
      new MockElement('.nedit'),
      $this->newCollection('/home/bin', array(
        new MockElement('xp')
      ))
    ));
    
    foreach (new IOCollectionIterator($c, true) as $i => $e) {
      $this->assertOriginBasedOn($c, $e->getOrigin());
    }
  }

  /**
   * Test getOrigin()
   *
   */
  #[@test]
  public function originEqualsBase() {
    $c= $this->newCollection('/home', array(
      new MockElement('.nedit'),
      $this->newCollection('/home/bin', array(
        new MockElement('xp')
      ))
    ));
    
    foreach (new IOCollectionIterator($c) as $i => $e) {
      $this->assertEquals($c, $e->getOrigin());
    }
  }

  /**
   * Test getOrigin()
   *
   */
  #[@test]
  public function originEquals() {
    $c= $this->newCollection('/home', array(
      new MockElement('.nedit'),
      $bin= $this->newCollection('/home/bin', array(
        new MockElement('xp.exe')
      ))
    ));
    
    foreach (new FilteredIOCollectionIterator($c, new ExtensionEqualsFilter('.exe'), true) as $i => $e) {
      $this->assertNotEquals($c, $e->getOrigin());
      $this->assertEquals($bin, $e->getOrigin());
    }
  }
}
