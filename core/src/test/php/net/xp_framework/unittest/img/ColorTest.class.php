<?php namespace net\xp_framework\unittest\img;

use img\Color;

/**
 * TestCase
 *
 * @see   xp://img.Color
 */
class ColorTest extends \unittest\TestCase {

  #[@test, @values(['#a7ff03', 'a7ff03', 'A7FF03', '#A7FF03'])]
  public function create_from_hex($value) {
    $c= new Color($value);
    $this->assertEquals(0xA7, $c->red);
    $this->assertEquals(0xFF, $c->green);
    $this->assertEquals(0x03, $c->blue);
  }

  #[@test]
  public function create_from_three_rgb_values() {
    $c= new Color(1, 2, 3);
    $this->assertEquals(1, $c->red);
    $this->assertEquals(2, $c->green);
    $this->assertEquals(3, $c->blue);
  }

  #[@test]
  public function toHex_returns_lowercase_hex_with_leading_hash() {
    $this->assertEquals('#efefef', create(new Color('#efefef'))->toHex());
  }

  #[@test]
  public function string_representation() {
    $this->assertEquals('img.Color@(239, 010, 007)', create(new Color('#ef0a07'))->toString());
  }
}
