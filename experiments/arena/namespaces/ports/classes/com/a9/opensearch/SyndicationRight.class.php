<?php
/* This class is part of the XP framework
 *
 * $Id: SyndicationRight.class.php 9799 2007-03-29 10:21:21Z friebe $ 
 */

  namespace com::a9::opensearch;

  /**
   * Enumeration of syndication rights
   *
   * Meanings:
   * <pre>
   * "open" (SyndicationRight::IS_OPEN)
   *   The search client may request search results. 
   *   The search client may display the search results to end users. 
   *   The search client may send the search results to other search clients. 
   *
   * "limited" (SyndicationRight::IS_LIMITED)
   *   The search client may request search results. 
   *   The search client may display the search results to end users. 
   *   The search client may not send the search results to other search clients. 
   *
   * "private" (SyndicationRight::IS_PRIVATE)
   *   The search client may request search results. 
   *   The search client may not display the search results to end users. 
   *   The search client may not send the search results to other search clients. 
   *
   * "closed" (SyndicationRight::IS_CLOSED)
   *   The search client may not request search results. 
   * </pre>
   *
   * @see      xp://com.a9.opensearch.OpenSearchDescription#setSyndicationRight
   * @purpose  Constant enumeration
   */
  class SyndicationRight extends lang::Object {
    const
      IS_OPEN    = 'open',
      IS_LIMITED = 'limited',
      IS_PRIVATE = 'private',
      IS_CLOSED  = 'closed';
  
  }
?>
