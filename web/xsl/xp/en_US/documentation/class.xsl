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

    <!-- see also -->
    <h4 class="context">See also</h4>
    <ul class="context">
      <li>
        <em>(cvsweb)</em>:<br/>
        <a href="#cvs">CVS history</a>
      </li>
      <li>
        <em>(development)</em>:<br/>
        <a href="#source">Source code</a>
      </li>
    </ul>

  </xsl:template>
  
  <xsl:template match="apidoc/comments//text/code">
    <div class="example">
      <code>&lt;?php<xsl:copy-of select="."/>?&gt;</code>
    </div>
  </xsl:template>

  <!--
   ! Template for content
   !
   ! @see      ../../layout.xsl
   ! @purpose  Define main content
   !-->
  <xsl:template name="content">
    <xsl:variable name="current" select="/formresult/breadcrumb/current"/>
    
    <h1>
      <a href="{func:link('documentation')}">api documentation</a> (<xsl:value-of select="$current/@collection"/>)
      <xsl:for-each select="/formresult/breadcrumb/path">
        :: <a href="package?{$current/@collection}/{@qualified}"><xsl:value-of select="."/></a>
      </xsl:for-each>
      :: <xsl:value-of select="$current/@class"/>
    </h1>
        
    <!-- Header -->
    <h3>
      <xsl:value-of select="/formresult/apidoc/comments/class/@model"/> 
      class <xsl:value-of select="/formresult/apidoc/comments/class/@name"/> 
      <xsl:if test="/formresult/apidoc/comments/class/@extends != ''">
        extends <xsl:value-of select="/formresult/apidoc/comments/class/@extends"/> 
      </xsl:if>
    </h3>
    <small>
      <xsl:value-of select="/formresult/apidoc/comments/file/@cvs"/>
    </small>
    <h4>
      Purpose: <xsl:value-of select="/formresult/apidoc/comments/class/purpose"/>
    </h4>
    <p>
      <xsl:apply-templates select="/formresult/apidoc/comments/class/text"/>
    </p>

    <!-- Experimental note -->
    <xsl:if test="/formresult/apidoc/comments/class/@experimental != ''">
      <table width="100%" border="0" cellspacing="0" cellpadding="2" class="intro">
        <tr>
          <td width="1%">
            <img src="/image/tip.gif" width="69" height="52"/>
          </td>
          <td>
            <p>
              <b>This class has been marked as experimental.</b>
              Usage is discouraged as long as this tag exists. You may
              use this class as it is committed for testing or to
              improve the design. However, the API is probably supposed 
              to change.                  
            </p>
          </td>
        </tr>
      </table>
      <br clear="all"/>
    </xsl:if>

    <!-- Deprecation note -->
    <xsl:if test="/formresult/apidoc/comments/class/@deprecated != ''">
      <table width="100%" border="0" cellspacing="0" cellpadding="2" class="intro">
        <tr>
          <td width="1%">
            <img src="/image/tip.gif" width="69" height="52"/>
          </td>
          <td>
            <p>
              <b>This class has been marked as deprecated.</b>
              Usage is discouraged though this class remains in the framework
              for backward compatibility.
            </p>
          </td>
        </tr>
      </table>
      <br clear="all"/>
    </xsl:if>

    <!-- References -->
    <xsl:if test="count(/formresult/apidoc/comments/class/references/reference) &gt; 0">
      <b>See also:</b>
      <ul>
        <xsl:for-each select="/formresult/apidoc/comments/class/references/reference">
          <li><xsl:apply-templates select="link"/></li>
        </xsl:for-each>
      </ul>
    </xsl:if>

    <!-- Extensions -->
    <xsl:if test="count(/formresult/apidoc/comments/class/extensions/extension) &gt; 0">
      <b>Requires PHP extensions:</b>
      <ul>
        <xsl:for-each select="/formresult/apidoc/comments/class/extensions/extension">
          <li>
            <a href="http://php3.de/{.}" target="_blank">
              <xsl:value-of select="."/>
              <img hspace="2" src="/image/arrow.gif" width="11" height="11" border="0"/>
            </a>
          </li>
        </xsl:for-each>
      </ul>
    </xsl:if>
    
    <!-- Method summary -->
    <h3>
      Method summary (<xsl:value-of select="count(/formresult/apidoc/comments/method)"/>)
    </h3>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <xsl:for-each select="/formresult/apidoc/comments/method">
        <xsl:sort select="@name"/>

        <tr>
          <td width="1%" valign="top" nowrap="nowrap"><img width="17" height="17" hspace="4" src="/image/{@access}.gif"/></td>
          <td width="99%" valign="top" colspan="2">
            <b>
              <a href="#{@name}">
                <xsl:value-of select="concat(@access, ' ', @model, ' ', return/type, ' ', @name)"/>
                (<xsl:for-each select="params/param">
                  <xsl:value-of select="concat(type, ' ', name)"/>
                  <xsl:if test="position() != last()">, </xsl:if>
                </xsl:for-each>)
              </a>
            </b>
            <br/>
            <xsl:value-of select="substring-before(concat(text, '.'), '.')"/>
            <br/>
          </td>
        </tr>
      </xsl:for-each>
    </table>
    
    <!-- Method detail -->
    <h3>
      Method detail
    </h3>
    <xsl:for-each select="/formresult/apidoc/comments/method">
      <xsl:sort select="@name"/>

      <h4>
        <img align="left" width="17" height="17" hspace="4" src="/image/{@access}.gif"/>
        <a name="{@name}">
          <xsl:value-of select="concat(@access, ' ', @model, ' ')"/>
          <a href="{func:typehref(return/type)}"><xsl:value-of select="return/type"/></a>
          <xsl:text> </xsl:text>
          <b><xsl:value-of select="@name"/></b>(<xsl:for-each select="params/param">
            <a href="{func:typehref(type)}"><xsl:value-of select="type"/></a>
            <xsl:text> </xsl:text>
            <xsl:value-of select="name"/>
            <xsl:if test="position() != last()">, </xsl:if>
          </xsl:for-each>)
        </a>
      </h4>
      <div style="padding-left: 2em">
        <p>
          <xsl:apply-templates select="text"/>
        </p>
        
        <!-- Parameters, return value and exceptions -->
        <xsl:if test="count(params/param) &gt; 0">
          <b>Parameters:</b>
          <ul>
            <xsl:for-each select="params/param">
              <li>
                <a href="{func:typehref(type)}"><xsl:value-of select="type"/></a>
                <xsl:text> </xsl:text>
                <b><xsl:value-of select="name"/></b>
                <xsl:text> </xsl:text>
                <xsl:value-of select="description"/>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:if>
        <xsl:if test="count(return/type) &gt; 0">
          <b>Returns:</b>
          <ul>
            <li>
              <a href="{func:typehref(return/type)}"><xsl:value-of select="return/type"/></a>
              <xsl:text> </xsl:text>
              <xsl:value-of select="return/description"/>
            </li>
          </ul>
        </xsl:if>
        <xsl:if test="count(throws/throw) &gt; 0">
          <b>Throws:</b>
          <ul>
            <xsl:for-each select="throws/throw">
              <li>
                <a href="{func:typehref(exception)}"><xsl:value-of select="exception"/></a>
                <xsl:text> </xsl:text>
                <xsl:value-of select="condition"/>
              </li>
            </xsl:for-each>
          </ul>
        </xsl:if>
        <xsl:if test="count(references/reference) &gt; 0">
          <b>See also:</b>
          <ul>
            <xsl:for-each select="references/reference">
              <li><xsl:apply-templates select="link"/></li>
            </xsl:for-each>
          </ul>
        </xsl:if>
      </div>
      <hr/>
    </xsl:for-each> 
  </xsl:template>
  
</xsl:stylesheet>
