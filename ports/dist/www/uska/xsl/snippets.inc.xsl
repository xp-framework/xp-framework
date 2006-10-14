<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <xsl:variable name="texts" select="document(concat($__product, '/', $__lang, '/texts.xml'))/texts"/>
  
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
