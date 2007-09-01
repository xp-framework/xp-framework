<?php
/* This class is part of the XP framework
 *
 * $Id: IRCColor.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace peer::irc;

  define('IRC_COLOR_DEFAULT',     0);
  define('IRC_COLOR_BLACK',       1);
  define('IRC_COLOR_DARKBLUE',    2);
  define('IRC_COLOR_DARKGREEN',   3);
  define('IRC_COLOR_RED',         4);
  define('IRC_COLOR_PURPLE',      5);
  define('IRC_COLOR_DARKRED',     6);
  define('IRC_COLOR_ORANGE',      7);
  define('IRC_COLOR_YELLOW',      8);
  define('IRC_COLOR_GREEN',       9);
  define('IRC_COLOR_MAGENTA',    10);
  define('IRC_COLOR_STEELBLUE',  11);
  define('IRC_COLOR_BLUE',       12);
  define('IRC_COLOR_PINK',       13);
  define('IRC_COLOR_DARKGRAY',   14);
  define('IRC_COLOR_LIGHTGRAY',  15);
  define('IRC_COLOR_WHITE',      16);


  /**
   * IRC colors
   *
   * Example:
   * <code>
   *   $connection->sendMessage(
   *     '#test', 
   *     '%sThis is red', 
   *     IRCColor::forCode(IRC_COLOR_RED)
   *   );
   * </code>
   *
   * @purpose  Utility class
   */
  class IRCColor extends lang::Object {
  
    /**
     * Retrieves color representation
     *
     * @param   int code
     * @return  string color representation
     */
    public static function forCode($code) {
      return "\3".$code;
    }
  }
?>
