<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Wrapper generator
 !
 ! $Id$
 !-->
<xsl:stylesheet 
 version="1.0" 
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:cus="http://www.schlund.de/pustefix/customize"
 xmlns:pfx="http://www.schlund.de/pustefix/core"
 xmlns:ixsl="http://www.w3.org/1999/XSL/TransformOutputAlias"
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
>

  <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
  <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>

  <!--
   ! Template that creates a name for use within the sourcecode
   !
   ! @param  string string
   !-->
  <xsl:template name="name">
    <xsl:param name="string"/>
  
    <xsl:value-of select="concat(
      translate(substring($string, 1, 1), $lcletters, $ucletters),
      translate(substring($string, 2), '.', '_')
    )"/>
  </xsl:template>

  <!--
   ! Template that creates a short class name
   !
   ! @param  string string
   !-->  
  <xsl:template name="classname">
    <xsl:param name="string"/>
    <xsl:param name="trim" select="'#'"/>

    <xsl:choose>
      <xsl:when test="contains($string, '.')">
        <xsl:call-template name="classname">
          <xsl:with-param name="string" select="substring-after($string, '.')"/>
          <xsl:with-param name="trim" select="$trim"/>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="substring-before(concat($string, $trim), $trim)"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template> 
</xsl:stylesheet>
