<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Stylesheet include for text display
 !
 ! $Id: news.inc.xsl 5134 2005-05-17 20:04:52Z friebe $
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>

  <!--
   ! Displays emoticons
   !
   ! @purpose  Text markup
   !-->
  <xsl:template match="emoticon">
    <img 
     src="/image/icons/{@id}.gif" 
     width="13" 
     height="13" 
     border="0" 
     hspace="1" 
     vspace="0" 
     alt="{@text}"
   />
  </xsl:template>

  <!--
   ! Displays bugzilla link
   !
   ! @purpose  Text markup
   !-->  
  <xsl:template match="bug">
    <a target="_bugs" title="Opens bug #{@id} in a new window" href="http://bugs.xp-framework.net/show_bug.cgi?id={@id}">
      <xsl:text>bug #</xsl:text><xsl:value-of select="@id"/>
    </a>
  </xsl:template>

  <!--
   ! Displays link to another blog entry
   !
   ! @purpose  Text markup
   !-->  
  <xsl:template match="blogentry">
    <a title="Link to entry {@id}" href="{func:link(concat('news/view?', @id))}">
      <xsl:text>entry #</xsl:text><xsl:value-of select="@id"/>
    </a>
  </xsl:template>

  <!--
   ! Displays link to another blog category
   !
   ! @purpose  Text markup
   !-->  
  <xsl:template match="blogcategory">
    <a title="Link to category {@id}" href="{func:link(concat('news/bycategory?', @id))}">
      <xsl:text>category #</xsl:text><xsl:value-of select="@id"/>
    </a>
  </xsl:template>

  <!--
   ! Displays external link
   !
   ! @purpose  Text markup
   !-->  
  <xsl:template match="link">
    <a target="_ext" title="External link to {@href}" href="{@href}">
      <xsl:value-of select="@href"/>
    </a>
  </xsl:template>

  <!--
   ! Displays mailto link
   !
   ! @purpose  Text markup
   !-->  
  <xsl:template match="mailto">
    <a title="Send an email to {@recipient}" href="mailto:{@recipient}">
      <xsl:value-of select="@recipient"/>
    </a>
  </xsl:template>

  <!--
   ! Template for img.
   ! @purpose  Migration of images
   !-->  
  <xsl:template match="img">
    <img>
      <xsl:copy-of select="@*"/>
      <xsl:attribute name="src"><xsl:value-of select="concat(
        'http://blog.xp-framework.net',
        @src
      )"/></xsl:attribute>
    </img>
  </xsl:template>

  <!--
   ! Displays code 
   !
   ! @purpose  Text markup
   !-->
  <xsl:template match="code">
    <pre class="code">&lt;?php<xsl:apply-templates/>?&gt;</pre>
  </xsl:template>
</xsl:stylesheet>
