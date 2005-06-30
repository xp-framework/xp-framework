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
  <xsl:output method="html" encoding="iso-8859-1" indent="no"/>
  <xsl:include href="snippets.inc.xsl"/>
  <xsl:include href="date.inc.xsl"/>
  
  <xsl:param name="__page"/>
  <xsl:param name="__frame"/>
  <xsl:param name="__state"/>
  <xsl:param name="__lang"/>
  <xsl:param name="__product"/>
  <xsl:param name="__sess"/>
  <xsl:param name="__query"/>
  
  <xsl:variable name="texts" select="document(concat($__product, '/', $__lang, '/texts.xml'))/texts"/>

  <!--
   ! Function that returns a fully qualified link to a specified target
   !
   ! @param  string target
   !-->
  <func:function name="func:link">
    <xsl:param name="target"/>
    <xsl:variable name="sess">
      <xsl:if test="$__sess != ''">.psessionid=<xsl:value-of select="$__sess"/></xsl:if>
    </xsl:variable>

    <func:result>
      <xsl:value-of select="concat(
        '/xml/', 
        $__product, 
        '.', 
        $__lang,
        $sess,
        '/',
        $target
      )"/>
    </func:result>
  </func:function>

  <!--
   ! Template that matches on everything and copies it through
   ! one to one.
   !
   !-->
  <xsl:template match="*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy> 
  </xsl:template>
  
  <!--
   ! Retrieve text snippets
   !
   ! @param string snippet
   ! @return string text
   !-->
  <func:function name="func:get_text">
    <xsl:param name="snippet"/>
    
    <func:result>
      <xsl:value-of select="$texts[@for= $snippet]"/>
      <xsl:if test="not($texts[@for= $snippet])">{<xsl:value-of select="$snippet"/>}</xsl:if>
    </func:result>
  </func:function>
</xsl:stylesheet>
