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
  <xsl:include href="../layout.xsl"/>
  
  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <h2>
      <a href="{func:link('static')}">Home</a> &#xbb; 
      <a href="{func:link(concat('album/view?', /formresult/album/@name))}">
        <xsl:value-of select="/formresult/album/@title"/>
      </a> &#xbb; 
      <xsl:value-of select="/formresult/selected/name"/>
    </h2>

    <br clear="all"/> 
    <table width="100%">
      <tr>
        <td align="left">
          <xsl:if test="/formresult/selected/@id &gt; 0">
            <a href="{func:link(concat(
              'image/view?', 
              /formresult/album/@name, ',',
              /formresult/selected/@type, ',',
              /formresult/selected/@chapter, ',',
              /formresult/selected/@id - 1
            ))}">PREV</a>
          </xsl:if>
        </td>
        <td align="right">
          <xsl:if test="/formresult/selected/@last = ''">
            <a href="{func:link(concat(
              'image/view?', 
              /formresult/album/@name, ',',
              /formresult/selected/@type, ',',
              /formresult/selected/@chapter, ',',
              /formresult/selected/@id + 1
            ))}">NEXT</a>
          </xsl:if>
        </td>
      </tr>
    </table>
    <img src="/albums/{/formresult/album/@name}/{/formresult/selected/name}"/>
    
    <!--
    <xmp>
      <xsl:copy-of select="/formresult/selected"/>
    </xmp>
    -->
  </xsl:template>
  
</xsl:stylesheet>
