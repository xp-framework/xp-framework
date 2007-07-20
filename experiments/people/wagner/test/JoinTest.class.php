<?php
/* This file is part of the XP framework
 *
 * $Id$
 */
  uses(
    'util.log.ColoredConsoleAppender',
    'rdbms.criterion.Projections',
    'rdbms.criterion.Restrictions',
    'de.schlund.db.rubentest.Ncolortype',
    'de.schlund.db.rubentest.Mmessage',
    'util.cmd.Command'
  );

  /**
   * test table joins
   *
   * @see      xp://rdbms.Criteria#detFetchMode
   * @purpose  test.rdbms
   */
  class JoinTest extends Command {
    private
      $logger;

    /**
     * Main runner method
     *
     */
    public function run() {
      $crit= Criteria::newInstance()->setFetchmode(Fetchmode::join('NcolorColortype->NtextureColor'));
      foreach (Ncolortype::getPeer()->doSelect($crit) as $ColorColortype) {
        Console::writeLine('==> '.xp::stringOf($ColorColortype));
        foreach ($ColorColortype->getNcolorColortypeList() as $Color) {
          Console::writeLine('====> '.xp::stringOf($Color));
          Console::writeLine('======> '.xp::stringOf($Color->getNtextureColorList()));
        }
        Console::writeLine('=====================================================================================');
      }

      $crit= Criteria::newInstance()
        ->setFetchMode(Fetchmode::join('Author'))
        ->setFetchMode(Fetchmode::join('Recipient'))
        ->setFetchMode(Fetchmode::join('Recipient->MmessageAuthor'))
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
        ->setFetchMode(Fetchmode::join('Recipient->MmessageAuthor'))
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

      $it->next();
    }

    /**
     * set logger
     *
     * @param util.log.LogCategory logger
     */
    #[@inject(type='util.log.LogCategory', name= 'default')]
    public function setLogger($logger) {
      $this->logger= $logger;
      $this->logger->addAppender(new ColoredConsoleAppender());
    }    

  }
?>
