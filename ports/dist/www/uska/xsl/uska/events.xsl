<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:include href="layout.xsl"/>
  
  <xsl:template name="context">
    <table class="sidebar" cellpadding="0" cellspacing="0" width="170">
      <tr><td class="sidebar_head">Aktionen</td></tr>
      <tr><td><a href="{func:link('event/edit')}">Neuen Termin eintragen</a></td></tr>
    </table>
  </xsl:template>
  
  <xsl:template name="content">
    <h3>Die nächsten Events:</h3>
    
    
  </xsl:template>
</xsl:stylesheet>
