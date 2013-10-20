<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestMarshalling',
    'util.Money'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestMarshalling
   */
  class RestMarshallingTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     */
    public function setUp() {
      $this->fixture= new RestMarshalling();
      $this->fixture->addMarshaller('util.Money', newinstance('webservices.rest.TypeMarshaller', array(), '{
        public function marshal($money) {
          return sprintf("%.2f %s", $money->amount()->floatValue(), $money->currency()->name());
        }

        public function unmarshal(Type $t, $input) {
          sscanf($input, "%f %s", $amount, $currency);
          return $t->newInstance($amount, Currency::getInstance($currency));
        }
      }'));
    }
    
    #[@test]
    public function marshal_money() {
      $this->assertEquals(
        '6.10 USD',
        $this->fixture->marshal(new Money(6.10, Currency::$USD))
      );
    }

    #[@test]
    public function marshal_array_of_money() {
      $this->assertEquals(
        array('6.10 USD'),
        $this->fixture->marshal(array(new Money(6.10, Currency::$USD)))
      );
    }

    #[@test]
    public function unmarshal_money() {
      $this->assertEquals(
        new Money(6.10, Currency::$USD),
        $this->fixture->unmarshal(XPClass::forName('util.Money'), '6.10 USD')
      );
    }

    #[@test]
    public function unmarshal_array_of_money() {
      $this->assertEquals(
        array(new Money(6.10, Currency::$USD)),
        $this->fixture->unmarshal(ArrayType::forName('util.Money[]'), array('6.10 USD'))
      );
    }
  }
?>
