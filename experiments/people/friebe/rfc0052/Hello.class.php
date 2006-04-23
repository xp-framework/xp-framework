/* This class is part of the XP framework's experiments
 *
 * $Id$ <?php
 */

import util~cmd~Console;

public class Hello {

  [@usage]
  public static function usage() {
    Console::writeLine(
      "Usage: php -dauto_prepend_file=xp.php Hello.class.php <Name>\n".
      "* Name is whom to say hello to"
    );
  }

  public static function main($args) {
    if (!isset($args[1]) || '-?' == $args[1]) return self::usage();

    Console::writeLine('Hello ', $args[1]);
  }
}
