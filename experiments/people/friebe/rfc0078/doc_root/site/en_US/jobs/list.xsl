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
 xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:output method="html" encoding="iso-8859-1" indent="no"/>
  <xsl:param name="__page"/>
  <xsl:param name="__frame"/>
  <xsl:param name="__state"/>
  <xsl:param name="__lang"/>
  <xsl:param name="__product"/>
  <xsl:param name="__sess"/>
  <xsl:param name="__query"/>

  <xsl:template match="field[@xsi:type= 'xsd:object']">
    <!-- FIXME: Need xp:util.Date instead of xsd:... -->
    <xsl:value-of select="concat(mday, '.', mon, '.', year)"/>
  </xsl:template>

  <xsl:template match="field[.= '']">
    <span style="color: #666666">(n/a)</span>
  </xsl:template>
  
  <xsl:template match="field">
    <xsl:value-of select="."/>
  </xsl:template>

  <xsl:template match="/">
    <html>
      <head>
        <title><xsl:value-of select="$__state"/> Editor</title>
        <link rel="stylesheet" type="text/css" href="/style.css"/>
      </head>
      <body>
        <div id="main">
          <h1>
            <xsl:value-of select="$__state"/>
          </h1>
          <a href="{$__state}/new?">
            NEW
          </a>
          <table class="facade" cellspacing="0" cellpadding="2" id="{/formresult/collection/@class}">
            <tr>
              <xsl:for-each select="/formresult/collection/fields/field">
                <xsl:variable name="field" select="string(.)"/>
                <th id="{$field}">
                  <xsl:value-of select="$field"/>
                </th>
              </xsl:for-each>
              <th>(Actions)</th>
            </tr>
            <xsl:for-each select="/formresult/collection/entity">
              <tr class="seq{position() mod 2}">
                <xsl:for-each select="field">
                  <td>
                    <xsl:apply-templates select="."/>
                  </td>
                </xsl:for-each>
                <td>
                  <a href="{$__state}/edit?{@id}">
                    EDIT
                  </a> |
                  <a href="{$__state}/delete?{@id}">
                    DELETE
                  </a>
                </td>
              </tr>
            </xsl:for-each>
          </table>
        </div>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
