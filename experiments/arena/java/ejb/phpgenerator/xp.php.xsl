<xsl:stylesheet
  version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
  <xsl:output method="text"/>
  
  <xsl:template match="/">
    <xsl:text><![CDATA[<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Class
   *
   * @purpose  Remote interface
   */
  class ]]></xsl:text><xsl:value-of select="/interface/@name"/><xsl:text><![CDATA[ extends Interface {
  
  }
?>]]></xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
