<?php
/*
 *
 * $Id:$
 */

  namespace org::dia::dev;

  class TestMarshaller extends lang::Object {

    /**
     *
     * @param   array classnames List of fully qualified class names
     * @param   int recurse default 0
     * @param   bool depend default FALSE
     * @return  &org.dia.DiaDiagram
     */
    public function marshal($classnames, $recurse= 0, $depend= FALSE) {
      // create new DiaDiagram
      $Dia= new ();

      // check classnames?
      foreach ($classnames as $classname) {
        try {
          $Class= lang::XPClass::forName($Classname);
        } catch (::Exception $e) {
          util::cmd::Console::writeLine("CLASS NOT FOUND: $classname!");
        }
      }

      return ::recurse($Dia, $classnames, $recurse, $depend);
    }

    /**
     *
     * @param   &org.dia.DiaDiagram Dia
     * @param   string[] classnames
     * @param   int recurse
     * @param   bool depend
     * @return  &org.dia.DiaDiagram
     */
    public function recurse($Dia, $classnames, $recurse, $depend) {
      $Layer= $Dia->getLayer();


      return $Dia;
    }

  }
?>
