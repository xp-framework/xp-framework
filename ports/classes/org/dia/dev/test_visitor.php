<?php
  require('lang.base.php');
  uses(
    'org.dia.DiaUnmarshaller',
    'org.dia.UpdateVisitor'
  );

  // TODO: => unittest...

  Console::writeLine('============= Unmarshaller ==========');
  $Dia= &DiaUnmarshaller::unmarshal('DiaClasses.dia');
  $Dia->saveTo('DiaClasses_parsed.dia', FALSE);

  Console::writeLine('============= Visitor ===============');
  $Visitor= &new UpdateVisitor(array(), FALSE, TRUE);
  $Dia->accept($Visitor);
  $Visitor->finalize();
  $Dia->saveTo('DiaClasses_updated.dia', FALSE);

?>
