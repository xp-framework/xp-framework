<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.Date',
    'util.log.Logger',
    'util.log.ColoredConsoleAppender'
  );
  
  // {{{ proto void format(string[] args)
  //     Formats using php://vsprintf, throws FormatException
  function format($args) {
    if (!$format= vsprintf($args[0], array_slice($args, 1))) {
      throw new FormatException('Format is uncorrect');
    }
    return $format;
  }
  // }}}

  // {{{ main
  Console::writeLinef(
    'Usage: php %s <format_string> <format_arg1> [<format_arg2> [<format_arg3]]',
    basename(__FILE__)
  );
  
  // Set up logger
  $cat= Logger::getInstance()->getCategory();
  $cat->addAppender(new ColoredConsoleAppender());
  
  // Write debug
  $cat->mark();
  $cat->debug('Called', $argv[0], 'with', $argc, 'argument(s):', $argv);
  
  // Try to format given the arguments
  try {
    $format= format(array_slice($argv, 1));
  } catch (FormatException $e) {
    $cat->error('Exception caught:', $e);
    $cat->warn('Exiting at', Date::now());
    exit;
  }
  
  // Print results
  $cat->infof('Result from format: %s', $format);
  // }}}
?>
