<?php
/* This class is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('io.File', 'io.FileUtil');
  
  if ($argc != 3) {
    printf(
      "Replaces key/value pairs within a file and writes the output directly to\n".
      "the file\n".
      "\n".
      "Usage: php %1\$s filename replacement_list\n".
      "       Replacment list is a file with key => value pairs on each line.\n".
      "       The keys will be replaced by the values.\n".
      "\n".
      "       Note: keys and values must be quoted (either with single or double\n".
      "       quotes). The rules for escaping are the same as in PHP.\n".
      "\n".
      "Example: find rdbms/ -name *.class.php -exec php %1\$s {} migrate.in\n".
      "\n".
      "migrate.in:\n".
      "  '\$db->insert_id();' => '\$db->identity();'\n".
      "  '\$q= \$db->query'    => '\$q= &$db->query\n".
      "  \"\\n// TBD\\n\"        => ''\n".
      "\n",
      basename($argv[0])
    );
    exit(-2);
  }

  $replacements= &new File($argv[2]);
  try(); {
    eval('$r= array('.str_replace("\n", ",\n", FileUtil::getContents($replacements)).');');
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  printf("===> Replacing in %s using %s\n", $argv[1], var_export($r, 1));
  $candidate= &new File($argv[1]);
  try(); {
    $c= FileUtil::getContents($candidate);
    $r= FileUtil::setContents($candidate, strtr($c, $r));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  printf("---> %d bytes written to %s\n", $r, $candidate->getURI());
  exit(0);
?>
