<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class IRCColor extends Object {
    const
      IRC_COLOR_DEFAULT = 0,
      IRC_COLOR_BLACK = 1,
      IRC_COLOR_DARKBLUE = 2,
      IRC_COLOR_DARKGREEN = 3,
      IRC_COLOR_RED = 4,
      IRC_COLOR_PURPLE = 5,
      IRC_COLOR_DARKRED = 6,
      IRC_COLOR_ORANGE = 7,
      IRC_COLOR_YELLOW = 8,
      IRC_COLOR_GREEN = 9,
      IRC_COLOR_MAGENTA = 10,
      IRC_COLOR_STEELBLUE = 11,
      IRC_COLOR_BLUE = 12,
      IRC_COLOR_PINK = 13,
      IRC_COLOR_DARKGRAY = 14,
      IRC_COLOR_LIGHTGRAY = 15,
      IRC_COLOR_WHITE = 16;

  
    /**
     * Retrieves color representation
     *
     * @model   static
     * @access  public
     * @param   int code
     * @return  string color representation
     */
    public static function forCode($code) {
      return "\3".$code;
    }
  }
?>
