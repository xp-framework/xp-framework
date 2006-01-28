/* This class is part of the XP framework's experiments
 *
 * $Id$ <?php
 */

import util·cmd·Console;

public class Hello {
  public static function main($args) {
    Console::writeLine('Hello ', $args[1]);
  }
}
