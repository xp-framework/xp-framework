<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');

  uses(    
    'util.log.Logger',
    'util.log.ColoredConsoleAppender',
    'util.PropertyManager',
    'rdbms.ConnectionManager',
    'rdbms.criterion.Projections',
    'rdbms.criterion.Restrictions',
    'de.schlund.db.rubentest.Ncolortype',
    'de.schlund.db.rubentest.Mmessage'
  );

  // Params
  $p= new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  Logger::getInstance()->getCategory()->addAppender(new ColoredConsoleAppender());
  ConnectionManager::getInstance()->register(DriverManager::getConnection('mysql://test:test@localhost/?autoconnect=1&log=default'));

//   $crit= Criteria::newInstance()->setFetchMode(Fetchmode::join('NcolorColortype.NtextureColor'));
//   foreach (Ncolortype::getPeer()->doSelect($crit) as $ColorColortype) {
//     Console::writeLine('==> '.xp::stringOf($ColorColortype));
//     foreach ($ColorColortype->getNcolorColortypeList() as $Color) {
//       Console::writeLine('====> '.xp::stringOf($Color));
//       Console::writeLine('======> '.xp::stringOf($Color->getNtextureColorList()));
//     }
//     Console::writeLine('=====================================================================================');
//   }

  $crit= Criteria::newInstance()
    ->setFetchMode(Fetchmode::join('Author'))
    ->setFetchMode(Fetchmode::join('Recipient'))
    ->setFetchMode(Fetchmode::join('Recipient.MmessageAuthor'))
  ;
  foreach (Mmessage::getPeer()->doSelect($crit) as $Message) {
    Console::writeLine('==> '.xp::stringOf($Message));
    Console::writeLine('Author =====> '.xp::stringOf($Message->getAuthor()));
    Console::writeLine('Recipient ==> '.xp::stringOf($Message->getRecipient()));
    Console::writeLine('Author of ==> '.xp::stringOf($Message->getRecipient()->getMmessageAuthorList()));
    Console::writeLine('=====================================================================================');
  }

  $crit= Criteria::newInstance()
    ->setFetchMode(Fetchmode::join('Author'))
    ->setFetchMode(Fetchmode::join('Recipient'))
    ->setFetchMode(Fetchmode::join('Recipient.MmessageAuthor'))
  ;
  $it= Mmessage::getPeer()->iteratorFor($crit);

  while ($it->hasNext()) {
    $ele= $it->next();
    Console::writeLine('==> '.xp::stringOf($ele));
    Console::writeLine('Author =====> '.xp::stringOf($ele->getAuthor()));
    Console::writeLine('Recipient ==> '.xp::stringOf($ele->getRecipient()));
    $authIt= $ele->getRecipient()->getMmessageAuthorIterator();
    while ($authIt->hasNext()) {
      Console::writeLine('Author of ==> '.xp::stringOf($authIt->next()));
    }
    Console::writeLine('=====================================================================================');
  }

  Console::writeLine(xp::stringOf($it->next()));

?>
