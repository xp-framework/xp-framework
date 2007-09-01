<?php
/*
 *
 * $Id: DiaUnmarshaller.class.php 8894 2006-12-19 11:31:53Z kiesel $
 */

  namespace org::dia;
  ::uses(
    'xml.XPath',
    'xml.XMLFormatException',
    'util.cmd.Console'
  );

  define('DIA_UNM_DEBUG', FALSE);

  /**
   * Unmarshaller for XML diagram files
   *
   * @see     http://www.gnome.org/projects/dia/
   * @ext     dom
   * @purpose Unmarshaller
   */
  class DiaUnmarshaller extends lang::Object {

    /**
     * Unmarshall a XML diagram file into an object structure (DiaDiagram)
     *
     * @param   string filename The diagram filename (.dia)
     * @return  &org.dia.DiaDiagram
     */
    public static function unmarshal($diagram) {
      // suck in XML document
      if (!($dom= domxml_open_file($diagram, DOMXML_LOAD_PARSING, $error))) {
        throw(new xml::XMLFormatException(::xp::stringOf($error)));
      }
      // initialize XPath
      $XPath= new xml::XPath($dom);

      // TODO: if the URI is wrong, unmarshalling won't work!
      $XPath->registerNamespace('dia', 'http://www.lysator.liu.se/~alla/dia/');
      // new (unused): $XPath->registerNamespace('dia', 'http://www.gnome.org/projects/dia/');
      // go
      $Dia= ::recurse($XPath, $dom->document_element(), 'org.dia.DiaDiagram');
      util::cmd::Console::writeLine('DiaUnmarshaller used up '.round(memory_get_usage()/1024, 2).' Kb of memory.');
      return $Dia;
    }

    /**
     * Process XML with the annotated methods of the given class
     *
     * @param   &xml.XPath XPath Instance of xml.XPath
     * @param   &php.DomNode Context The XML node to recurse from
     * @param   string classname Fully qualified class name
     */
    public static function recurse($XPath, $Context, $classname) {
      if (DIA_UNM_DEBUG) util::cmd::Console::writeLine('recurse: '.$Context->tagname()."-> $classname");

      try {
        $Class= lang::XPClass::forName($classname);
      } catch (::Exception $e) {
        $e->printStackTrace();
        exit(-1);
      }
      $Instance= $Class->newInstance(); 
      if (DIA_UNM_DEBUG) util::cmd::Console::writeLine('Instance: '.$Instance->getClassName());

      // loop over class methods with annotation 'fromDia'
      $methods= $Class->getMethods();
      foreach (array_keys($methods) as $key) {
        $Method= $methods[$key];
        if (!$Method->hasAnnotation('fromDia', 'xpath')) continue;
        $name= $Method->getName();
        $xpath= $Method->getAnnotation('fromDia', 'xpath');

        if (DIA_UNM_DEBUG) util::cmd::Console::writeLine("--> Method: $name Xpath: $xpath");

        // continue if this fails: some expressions don't work on WRONG nodes...
        try {
          $result= $XPath->query($xpath, $Context);
        } catch (::Exception $e) {
          if (DIA_UNM_DEBUG) $e->printStackTrace();
          $nodename= $Context->tagname();
          if ($Context->has_attribute('type')) {
            $type= $Context->get_attribute('type');
          } else {
            $type= '<unknown>';
          }
          if (DIA_UNM_DEBUG) util::cmd::Console::writeLine("Warn: XPath= '$xpath' failed on Node= '$nodename' Type= '$type'");
          // skip this method/expression
          continue;
        }

        if (DIA_UNM_DEBUG) util::cmd::Console::writeLine('  + Size of nodeset: '.sizeof($result->nodeset));

        // loop over nodeset
        foreach (array_keys($result->nodeset) as $key) {
          $Node= $result->nodeset[$key];
          
          // key 'value' (simple value)
          if ($Method->hasAnnotation('fromDia', 'value')) {
            $type= $Method->getAnnotation('fromDia', 'value');

            if (DIA_UNM_DEBUG) util::cmd::Console::writeLine("  + Value-type: '$type'");

            // get node value depending on nod type
            if (is('domattribute', $Node)) {
              $nodevalue= $Node->value();
            } elseif (is('domelement', $Node)) {
              // remove whitespace and leading/trailing '#' character
              $content= $Node->get_content();
              if (isset($content) and $content !== '') {
                $nodevalue= substr(trim($Node->get_content()), 1, -1);
              } else {
                $nodevalue= '';
              }
            } elseif (is('domnamespace', $Node)) {
              $nodevalue= array($Node->prefix() => $Node->namespace_uri());
            } else {
              util::cmd::Console::writeLine('Unknown node class: '.::xp::stringOf($Node));
              exit(-1);
            }

            switch ($type) {
              case 'namespace':
                if (!is_array($nodevalue) or empty($nodevalue))
                  util::cmd::Console::writeLine("Nodevalue is no array or is empty: $nodevalue");
                $value= $nodevalue;
                break;
              case 'enum':
              case 'int':
                $nodevalue= cast($nodevalue, 'int');
                if (!is_int($nodevalue))
                  util::cmd::Console::writeLine("Nodevalue is no integer: $nodevalue");
                $value= $nodevalue;
                break;
              case 'real':
                $nodevalue= cast($nodevalue, 'float');
                if (!is_real($nodevalue) and !is_int($nodevalue))
                  util::cmd::Console::writeLine("Nodevalue is no float/integer: $nodevalue");
                $value= $nodevalue;
                break;
              case 'string':
                $nodevalue= cast($nodevalue, 'string');
                if (!is_string($nodevalue))
                  util::cmd::Console::writeLine("Nodevalue is no string: $nodevalue");
                $value= $nodevalue;
                break;
              case 'boolean':
                $value= NULL;
                if ($nodevalue === 'false') $value= FALSE;
                if ($nodevalue === 'true') $value= TRUE;
                if (!is_bool($value))
                  util::cmd::Console::writeLine("Nodevalue is no boolean: $nodevalue");
                break;
              case 'point':
              case 'array': // comma separated list of values
                $value= explode(',', $nodevalue);
                break;
              case 'rectangle':
              case 'arrayarray': // semicolon and comma separated list of values
                $points= explode(';', $nodevalue);
                $value= array();
                foreach ($points as $point) {
                  $value[]= explode(',', $point);
                }
                break;
              case 'font':
                if (is('domelement', $Node)) {
                  $value= array();
                  $value['family']= $Node->get_attribute('family');
                  $value['style']= $Node->get_attribute('style');
                  $value['name']= $Node->get_attribute('name');
                } else {
                  util::cmd::Console::writeLine('Wrong font node: '.::xp::stringOf($Node));
                  exit(-1);
                }
                break;
              default:
                util::cmd::Console::writeLine("!!!Unknown 'value' type: '$type'!");
            }
            $Method->invoke($Instance, array($value));
          }

          // key 'class'
          if ($Method->hasAnnotation('fromDia', 'class')) {
            $classname= $Method->getAnnotation('fromDia', 'class');
            if (DIA_UNM_DEBUG) util::cmd::Console::writeLine("  + Class: $classname");

            // recurse with given classname
            $Obj= ::recurse($XPath, $Node, $classname);
            // hand results over to the method
            $Method->invoke($Instance, array($Obj));
          }
        }
      }

      return $Instance;
    }

  }
?>
