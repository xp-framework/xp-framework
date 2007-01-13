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

  <func:function name="func:bitset">
    <xsl:param name="value"/>
    <xsl:param name="bit"/>

    <func:result>
      <xsl:choose>
        <xsl:when test="(floor($value div $bit) mod 2) != 0">1</xsl:when>
        <xsl:otherwise>0</xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <func:function name="func:stringOf">
    <xsl:param name="value"/>
    <xsl:param name="type"/>

    <func:result>
      <xsl:choose>
        <xsl:when test="$type= 'core:date' and $value/@xsi:type = 'xsd:object'">
          <!-- FIXME Use func:date() -->
          <xsl:value-of select="concat($value/mday, '.', $value/mon, '.', $value/year)"/>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="$value"/></xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>

  <!--
   ! Template for core:string type. Renders an input type="text".
   !
   !-->
  <xsl:template match="wrapper/param[@type = 'core:string']">
    <xsl:variable name="name" select="@name"/>
    <input type="text" name="{@name}" value="{/formresult/formvalues/param[@name = $name]}"/>
  </xsl:template>

  <!--
   ! Template for core:number type. Renders an input type="text".
   !
   !-->
  <xsl:template match="wrapper/param[@type = 'core:number']">
    <xsl:variable name="name" select="@name"/>
    <input type="text" name="{@name}" size="10" value="{/formresult/formvalues/param[@name = $name]}"/>
  </xsl:template>

  <!--
   ! Template for core:bool type. Renders a single checkbox.
   !
   !-->
  <xsl:template match="wrapper/param[@type = 'core:bool']">
    <xsl:variable name="name" select="@name"/>
    <input type="checkbox" name="{@name}"/>
  </xsl:template>

  <!--
   ! Template for core:date type. Renders a dateselector
   !
   !-->
  <xsl:template match="wrapper/param[@type = 'core:date']">
    <xsl:variable name="name" select="@name"/>
    <input type="text" name="{@name}" size="10" value="{func:stringOf(
      /formresult/formvalues/param[@name = $name],
      @type
    )}"/>
    (TT.MM.JJJJ)<!-- FIXME: Use func:datef or something -->
  </xsl:template>

  <!--
   ! Template for core:text type. Renders a textarea with 60 columns and 4 rows.
   !
   !-->
  <xsl:template match="wrapper/param[@type = 'core:text']">
    <xsl:variable name="name" select="@name"/>
    <textarea name="{$name}" cols="60" rows="4">
      <xsl:value-of select="/formresult/formvalues/param[@name = $name]"/>
    </textarea>
  </xsl:template>

  <xsl:template name="realize-form">
    <xsl:choose>
      <!-- Success -->
      <xsl:when test="$handler/@status = 'success'">
        SUBMIT: SUCCESS
        <a href="../jobs">List</a>
      </xsl:when>
      
      <!-- Reloaded -->
      <xsl:when test="$handler/@status = 'reloaded'">
        SUBMIT: SUCCESS | RELOADED
        <a href="../jobs">List</a>
      </xsl:when>
     
      <!-- Setup failure -->
      <xsl:when test="$handler/@status = 'failed'">
        SETUP: FAILED
        <a href="../jobs">List</a>
      </xsl:when>
      
      <!-- Normal form (re-)display -->
      <xsl:otherwise>
        <xsl:if test="count(/formresult/formerrors/error) &gt; 0">
          <xsl:variable name="errortext">
            <ul>
              <xsl:for-each select="/formresult/formerrors/error[@checker = $handler/@name]">
                <li>
                  <xsl:value-of select="@field"/>:
                  <xsl:value-of select="concat('error#', @checker, '.', @type)"/>
                </li>
              </xsl:for-each>
            </ul>
          </xsl:variable>
        </xsl:if>
        
        <form method="POST" name="{$handler/@name}">
          <input type="hidden" name="__handler" value="{$handler/@id}"/>

          <table class="form" cellspacing="0" cellpadding="4" width="100%" border="0">
            <tr>
              <th class="form" colspan="3">
                <h3><xsl:value-of select="$handler/@name"/></h3>
              </th>
            </tr>
            <xsl:for-each select="$handler/wrapper/param[func:bitset(number(@occurrence), 4) = 0]"> 
              <xsl:variable name="name" select="@name"/>
              <tr>
                <td valign="top">
                  <label><xsl:value-of select="$name"/></label>
                  <xsl:if test="func:bitset(number(@occurrence), 1) = 0"><sup style="color: red">*</sup></xsl:if>
                </td>
                <td valign="top">
                  <xsl:if test="/formresult/formerrors/error[@field = $name]">
                    X
                  </xsl:if>
                </td>
                <td valign="top">
                  <xsl:apply-templates select=".">
                    <xsl:with-param name="form" select="$handler/@name"/>
                  </xsl:apply-templates>
                </td>
              </tr>
            </xsl:for-each>
            <tr>
              <td colspan="3" align="right">
                <a href="../jobs">List</a> | <input class="button_apply" type="submit" value="OK"/>
              </td>
            </tr>
          </table>
        </form>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="realize-view">
    <xsl:choose>
      <!-- Success -->
      <xsl:when test="$handler/@status = 'success'">
        SUBMIT: SUCCESS
        <a href="../jobs">List</a>
      </xsl:when>
      
      <!-- Reloaded -->
      <xsl:when test="$handler/@status = 'reloaded'">
        SUBMIT: SUCCESS | RELOADED
        <a href="../jobs">List</a>
      </xsl:when>
     
      <!-- Setup failure -->
      <xsl:when test="$handler/@status = 'failed'">
        SETUP: FAILED
        <a href="../jobs">List</a>
      </xsl:when>
      
      <!-- Normal form (re-)display -->
      <xsl:otherwise>
        <xsl:if test="count(/formresult/formerrors/error) &gt; 0">
          <xsl:variable name="errortext">
            <ul>
              <xsl:for-each select="/formresult/formerrors/error[@checker = $handler/@name]">
                <li>
                  <xsl:value-of select="@field"/>:
                  <xsl:value-of select="concat('error#', @checker, '.', @type)"/>
                </li>
              </xsl:for-each>
            </ul>
          </xsl:variable>
        </xsl:if>
        
        <form method="POST" name="{$handler/@name}">
          <input type="hidden" name="__handler" value="{$handler/@id}"/>

          <table class="form" cellspacing="0" cellpadding="4" width="100%" border="0">
            <tr>
              <th class="form" colspan="3">
                <h3><xsl:value-of select="$handler/@name"/></h3>
              </th>
            </tr>
            <xsl:for-each select="$handler/wrapper/param[func:bitset(number(@occurrence), 4) = 0]"> 
              <xsl:variable name="name" select="@name"/>
              <tr>
                <td valign="top">
                  <label><xsl:value-of select="$name"/></label>
                </td>
                <td valign="top">
                  <xsl:if test="/formresult/formerrors/error[@field = $name]">
                    X
                  </xsl:if>
                </td>
                <td valign="top">
                  <xsl:value-of select="func:stringOf(
                    /formresult/formvalues/param[@name = $name],
                    @type
                  )"/>
                </td>
              </tr>
            </xsl:for-each>
            <tr>
              <td colspan="3" align="right">
                <a href="../jobs">List</a> | <input class="button_apply" type="submit" value="OK"/>
              </td>
            </tr>
          </table>
        </form>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>
