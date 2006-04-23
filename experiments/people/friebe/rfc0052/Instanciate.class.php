/* This class is part of the XP framework's experiments
 *
 * $Id$ <?php
 */

import util~cmd~Console;

public class Instanciate {

  public static function main($args) throws Exception {
    try {
      $class= XPClass::forName($args[1]);
    } catch (ClassNotFoundException $e) {
      Console::writeLine($e->toString());
      return 1;
    }
    Console::writeLine($class->getName(), '::newInstance()= ', xp::stringOf($class->newInstance()));
  }
}
