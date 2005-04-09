<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <!--
   ! Gettext function
   !
   ! @param   string for
   ! @return  string text snippet
   !-->
  <func:function name="func:get_text">
    <xsl:param name="for"/>
    <xsl:variable name="snippet" select="exsl:node-set($texts)/text[@for = $for]"/>

    <func:result>
      <xsl:copy-of select="$snippet"/>
      
      <!-- DEBUG -->
      <xsl:if test="not($snippet)">{<xsl:value-of select="$for"/>}</xsl:if>
    </func:result>
  </func:function>
</xsl:stylesheet>
