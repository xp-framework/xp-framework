<?php
/* This file is part of the XP framework's RFC #0084
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ Rule
  //     Base class for all other rules
  class Rule extends Object {
  
  }
  // }}}
  
  // {{{ MovedRule
  //     Indicates something was moved to ports/classes
  class MovedRule extends Rule {
    var $new= NULL;
    
    function __construct($new) {
      $this->new= $new;
    }

    function toString() {
      return 'moved to '.$this->new.' in ports/classes';
    }
  }
  // }}}

  // {{{ RenamedRule
  //     Indicates something was renamed in skeleton
  class RenamedRule extends Rule {
    var $new= NULL;
    
    function __construct($new) {
      $this->new= $new;
    }

    function toString() {
      return 'renamed to '.$this->new;
    }
  }
  // }}}

  // {{{ DeprecatedRule
  //     Indicates something was deprecated and thus removed
  class DeprecatedRule extends Rule {
    var $alternatives= array();
    
    function __construct($alternatives= array()) {
      $this->alternatives= $alternatives;
    }
    
    function toString() {
      return ('deprecated without replacement.'.(empty($this->alternatives) 
        ? ''
        : 'Alternative APIs '.implode(', ', $this->alternatives)
      ));
    }
  }
  // }}}
  
  // {{{ Report
  //     Abstract base class for other reports
  class Report extends Object {
    var $messages= array();
    
    function add(&$f, $messages) { 
      $this->messages[$f->getURI()]= $messages;
    }

    function summarize($rules) { }
  }
  // }}}
  
  // {{{ TextReport
  //     Report implementation which will output text/plain
  class TextReport extends Report {

    function summarize(&$collection, &$out, $rules) {
      $out->open(FILE_MODE_WRITE);
      foreach ($this->messages as $uri => $messages) {
        $out->write('== Report for '.str_replace($collection->getURI(), '', $uri)." ==\n");
        foreach ($messages as $package => $occurrences) {
          $out->write(sprintf(
            "   Use of package %s - this package has been %s {\n",
            $package,
            $rules[$package]->toString()
          ));

          foreach ($occurrences as $occurrence) {
            $out->write(sprintf(
              "   %s\n   %s^-- line %d, offset %d\n",
              $occurrence[2],
              str_repeat(' ', $occurrence[1]),
              $occurrence[0],
              $occurrence[1]
            ));
          }

          $out->write("   }\n");
        }
        $out->write("\n");
      }
      $out->close();
    }
    
    function toString() {
      return 'textual report';
    }
  }
  // }}}

  // {{{ HtmlReport
  //     Report implementation which will output HTML
  class HtmlReport extends Report {

    function summarize(&$collection, &$out, $rules) {
      $css= '
        body {
          font: 10pt Arial, helvetica;
        }
        pre, code {
          font: 10pt "Courier new", courier;
          margin: 0;
        }
        code {
          background-color: #efefef;
          white-space: pre;
        }
        pre {
          margin-bottom: 8px;
        }
        fieldset {
          margin-bottom: 10px;
          border: 1px solid #cccccc;
        }
        fieldset legend {
          font-weight: bold;
        }
        li {
          font-weight: bold;
        }
        .renamedrule {
          color: #000099;
        }
        .movedrule {
          color: #ffa800;
        }
        .deprecatedrule {
          color: #990000;
        }
      ';

      $out->open(FILE_MODE_WRITE);
      $out->write('<html><head><title>RFC #0084 Migration report</title><style type="text/css">'.$css.'</style></head><body>');
      foreach ($this->messages as $uri => $messages) {
        $out->write('<fieldset><legend>Report for '.str_replace($collection->getURI(), '', $uri)."</legend><ul>");
        foreach ($messages as $package => $occurrences) {
          $out->write(sprintf(
            "<li><span class='%s'>Use of package %s - this package has been %s</span><br/>\n",
            get_class($rules[$package]),
            $package,
            $rules[$package]->toString()
          ));

          foreach ($occurrences as $occurrence) {
            $out->write(sprintf(
              "<code>%s</code>\n<pre>%s^-- line %d, offset %d</pre>\n",
              $occurrence[2],
              str_repeat(' ', $occurrence[1]),
              $occurrence[0],
              $occurrence[1]
            ));
          }

          $out->write("</li>\n");
        }
        $out->write("</ul></fieldset>\n");
      }
      $out->write('</body>');
      $out->close();
    }
    
    function toString() {
      return 'HTML report';
    }
  }
  // }}}
  
  // {{{ void performWork(&io.collections.IOElement e, &Report r, array<string, &Rule> rules) 
  //     Migrates the given element
  function performWork(&$e, &$r, $rules) {
    $f= &new File($e->getURI());
    $f->open(FILE_MODE_READ);
    $l= 1;
    $messages= array();
    while (!$f->eof()) {
      $line= $f->readLine();
      foreach (array_keys($rules) as $package) {
        if (FALSE === ($p= strpos($line, $package))) continue;

        isset($messages[$package]) || $messages[$package]= array();
        $messages[$package][]= array($l, $p, $line);
      }
      $l++;
    }
    $f->close();
    
    if (empty($messages)) {
      Console::write('.');
    } else {
      Console::write('o');
      $r->add($f, $messages);
    }
    delete($f);
  }
  // }}}

  // {{{ Rule definitions
  $rules= array(
    'gui.gtk'                 => new MovedRule('org.gnome'),
    'org.json'                => new RenamedRule('webservices.json'),
    'xml.xmlrpc'              => new RenamedRule('webservices.xmlrpc'),
    'xml.soap'                => new RenamedRule('webservices.soap'),
    'xml.wddx'                => new RenamedRule('webservices.wddx'),
    'xml.uddi'                => new RenamedRule('webservices.uddi'),
    'xml.xp'                  => new DeprecatedRule(array('xml.meta')),
    'io.cca'                  => new DeprecatedRule(array('lang.archive')),
    'util.profiling.unittest' => new RenamedRule('unittest'),
    'util.archive'            => new MovedRule('org.gnu.tar'),
    'util.adt'                => new DeprecatedRule(array('util.collections')),
    'util.registry'           => new DeprecatedRule(),
    'util.mp3'                => new MovedRule('de.fraunhofer.mp3'),
    'peer.ajp'                => new MovedRule('org.apache.ajp'),
    'peer.cvsclient'          => new MovedRule('org.cvshome'),
    'text.apidoc'             => new DeprecatedRule(array('text.doclet')),
    'text.translator'         => new MovedRule('net.schweikhardt'),
    'net.planet-xp'           => new DeprecatedRule(),
  );
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Creates a report for all files in a given directpry

Usage: php report.php <base_directory> [-O output]
__
    );
    exit(1);
  }

  switch ($p->value('report', 'r', 'text')) {
    case 'text': case 't': $report= &new TextReport(); break;
    case 'html': case 'h': $report= &new HtmlReport(); break;
    default: Console::writeLine('Unknown report type'); exit(2);
  }

  $scan= $p->value(1);
  $out= &new File($p->value('output', 'O', 'rfc-0084_'.basename($scan).'.report'));

  Console::writeLine('===> Generating ', $report->toString(), ' for ', $scan, ' to ', $out->getURI());
  for (
    $c= &new FileCollection($scan),
    $it= &new FilteredIOCollectionIterator($c, new ExtensionEqualsFilter('.php'), TRUE);
    $it->hasNext();
  ) {
    performWork($it->next(), $report, $rules);
  }
  
  Console::writeLine();
  Console::writeLine('---> Creating summary');
  $report->summarize($c, $out, $rules);
  Console::writeLine('===> Done');
  // }}}
?>
