<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet for home page
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
  <xsl:include href="../../layout.xsl"/>
  
  <!--
   ! Template for context navigation
   !
   ! @see      ../../layout.xsl
   ! @purpose  Context navigation
   !-->
  <xsl:template name="context">
  </xsl:template>
  
  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h1><xsl:value-of select="/formresult/item/caption"/></h1>
    <p>
      <xsl:apply-templates select="/formresult/item/body"/>
    </p>

    <em><xsl:value-of select="func:datetime(/formresult/item/created_at)"/></em>
    <br/>
    <br/>
    <a href="../news">Overview</a> | <a href="{/formresult/item/link}">Details</a>
  </xsl:template>
  
</xsl:stylesheet>
