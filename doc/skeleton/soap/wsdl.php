<?php
  require('lang.base.php');
  uses('xml.wsdl.WsdlDocument');
  
  $d= &new WsdlDocument('urn:Faq', 'urn:Faq');
  $d->addNamespace('faqns', 'urn:Faq');
  
  // Article[] getArticles(int faq_id, int category_id)
  $d->addMessage(new WsdlMessage('getArticles', array(
    'faq_id'        => 'int',
    'category_id'   => 'int'
  )));
  $d->addMessage(new WsdlMessage('getArticlesResponse', array(
    'return'        => array('Articles', 'faqns')
  )));

  // Category[] getCategories(int faq_id, int category_id, int depth)
  $d->addMessage(new WsdlMessage('getCategories', array(
    'faq_id'        => 'int',
    'category_id'   => 'int',
    'depth'         => 'int'
  )));
  $d->addMessage(new WsdlMessage('getCategoriesResponse', array(
    'return'        => array('Categories', 'faqns')
  )));
  
  echo $d->getSource(0);
?>
