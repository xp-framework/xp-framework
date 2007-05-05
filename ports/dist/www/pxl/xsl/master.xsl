<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Master stylesheet
 !
 ! $Id: master.xsl 5830 2005-09-27 21:20:30Z kiesel $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:param name="__page"/>
  <xsl:param name="__frame"/>
  <xsl:param name="__state"/>
  <xsl:param name="__lang"/>
  <xsl:param name="__product"/>
  <xsl:param name="__sess"/>
  <xsl:param name="__query"/>

  <xsl:include href="date.inc.xsl"/>  
  <xsl:variable name="texts" select="document(concat($__product, '/', $__lang, '/texts.xml'))/texts"/>
  
  <xsl:output
   method="xml"
   encoding="utf-8"
   indent="yes"
  />
  
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
      <xsl:copy-of select="$texts/text[@for= $snippet]"/>
      <xsl:if test="not($texts/text[@for= $snippet])">{<xsl:value-of select="$snippet"/>}</xsl:if>
    </func:result>
  </func:function>

  <!--
   ! Check whether text snippet exists, returns an empty string if not.
   !
   ! @param string snippet
   ! @return string text
   !-->
  <func:function name="func:exists_text">
    <xsl:param name="snippet"/>
    
    <func:result>
      <xsl:choose>
        <xsl:when test="$texts[@for= $snippet]">*</xsl:when>
        <xsl:otherwise>-</xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>
</xsl:stylesheet>
