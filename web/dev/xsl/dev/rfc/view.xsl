<?xml version="1.0" encoding="UTF-8"?>
<!--
 ! RFC view page
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:exsl="http://exslt.org/common"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:func="http://exslt.org/functions"
 xmlns:php="http://php.net/xsl"
 xmlns:xp="http://xp-framework.net/xsl"
 extension-element-prefixes="func"
 exclude-result-prefixes="func php exsl xsl xp"
>

  <xsl:include href="../layout.inc.xsl"/>

  <!-- 
   ! Transform <code> ... </code> to <pre class="code"> ... </pre>
   ! because of IE's lacking support for white-space: pre
   !-->
  <xsl:template match="code">
    <pre class="code">
      <xsl:apply-templates/>
    </pre>
  </xsl:template>

  <!-- 
   ! Add fixes to <pre> ... </pre> for IE
   !-->
  <xsl:template match="pre">
    <xsl:variable name="key" select="generate-id(.)"/>
    <pre id="{$key}">
      <xsl:apply-templates/>
    </pre>
    <!-- IE fix -->
    <xsl:comment>[if lt IE 8]&gt;
      &lt;script language="JavaScript"&gt;
        document.getElementById('<xsl:value-of select="$key"/>').style.width= document.body.offsetWidth - 320;
      &lt;/script&gt;
    &lt;![endif]</xsl:comment>
  </xsl:template>

  <!--
   ! Turn "h2"-headlines into scroll links
   !-->
  <xsl:template match="h2">
    <a name="#{string(.)}"><h2><xsl:value-of select="."/></h2></a>
  </xsl:template>

  <!--
   ! Links to documentation
   !-->
  <xsl:template match="link[@rel= 'doc']">
    <a href="/xml/doc?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'package']">
    <a href="/xml/api/package?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to api docs
   !-->
  <xsl:template match="link[@rel= 'class']">
    <a href="/xml/api/class?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to a state in this site
   !-->
  <xsl:template match="link[@rel= 'state']">
    <a href="/xml/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to an XP RFC
   !-->
  <xsl:template match="link[@rel= 'rfc']">
    <a href="/xml/rfc/view?{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links to XP-RFCs
   !-->
  <xsl:template match="rfc">
    <a href="/xml/rfc/view?{@id}">RFC #<xsl:value-of select="@id"/></a>
  </xsl:template>

  <!--
   ! Links to a PHP manual page
   !-->
  <xsl:template match="link[@rel= 'php']">
    <a href="http://de3.php.net/{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Links without caption
   !-->
  <xsl:template match="link[string(.) = '']">
    <a href="{@rel}://{@href}"><xsl:value-of select="concat(@rel, '://', @href)"/></a>
  </xsl:template>

  <!--
   ! Links
   !-->
  <xsl:template match="link">
    <a href="{@rel}://{@href}"><xsl:value-of select="."/></a>
  </xsl:template>

  <!--
   ! Summary
   !-->
  <xsl:template match="summary">
    <fieldset class="summary">
      <xsl:apply-templates/>
    </fieldset>
  </xsl:template>

  <xsl:template match="content">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:copy>
  </xsl:template>
  
  <xsl:template name="content">
    <table id="main" cellpadding="0" cellspacing="10">
      <tr>
        <td id="content">
          <div id="breadcrumb">
            <a href="{xp:link('home')}">Developer Zone</a> &#xbb;
            <a href="{xp:link('rfc')}">RFCs</a> &#xbb;
            <a href="{xp:link(concat('rfc/view?', /formresult/rfc/@number))}">#<xsl:value-of select="/formresult/rfc/@number"/></a>
          </div>
          
          <h1>
            <xsl:value-of select="/formresult/rfc/title"/>
          </h1>

          <fieldset class="summary">
            <img src="/image/{/formresult/rfc/status/@id}.png" width="16" height="16"/>&#160;
            <b>
              Status: <xsl:value-of select="/formresult/rfc/status/@id"/>
              <xsl:if test="/formresult/rfc/status != ''">
                <xsl:text>,&#160;</xsl:text>
                <xsl:value-of select="/formresult/rfc/status"/>
              </xsl:if>
            </b>
            <ul>
              <li>Created: <xsl:value-of select="/formresult/rfc/created"/></li>
              <li>Categories: 
                <xsl:for-each select="/formresult/rfc/category">
                  <xsl:value-of select="."/>
                  <xsl:if test="position() &lt; last()">, </xsl:if>
                </xsl:for-each>
              </li>
              <li>Author: 
                <acronym title="{/formresult/rfc/author/realname}">
                  <xsl:value-of select="/formresult/rfc/author/cn"/>
                </acronym>
              </li>
              <xsl:if test="/formresult/rfc/contributor">
                <li>Contributors: 
                  <xsl:for-each select="/formresult/rfc/contributor">
                    <acronym title="{realname}">
                      <xsl:value-of select="cn"/>
                    </acronym>
                    <xsl:if test="position() &lt; last()">, </xsl:if>
                  </xsl:for-each>
                </li>
              </xsl:if>
            </ul>
          </fieldset>

          <xsl:apply-templates select="/formresult/rfc/scope"/>
          <br clear="all"/>
          
          <xsl:apply-templates select="/formresult/rfc/content"/>
          <br clear="all"/>
        </td>
        <td id="context">
          <h3>Table of contents</h3>
          <ul style="margin-left: 6px">
            <xsl:for-each select="/formresult/rfc/content/h2">
              <li><a href="#{string(.)}"><xsl:value-of select="."/></a></li>
            </xsl:for-each>
          </ul>
        </td>
      </tr>
    </table>
  </xsl:template>

  <xsl:template name="html-title">
    #<xsl:value-of select="/formresult/rfc/@number"/> - RFCs - XP Framework Developer Zone
  </xsl:template>
  
</xsl:stylesheet>
