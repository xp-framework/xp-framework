<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Transforms XML bean description into PHP sourcecode
 !
 ! $Id$
 !-->
<xsl:stylesheet
 version="1.0"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:exsl="http://exslt.org/common"
 xmlns:str="http://exslt.org/strings"
 xmlns:set="http://exslt.org/sets"
 xmlns:func="http://exslt.org/functions"
 extension-element-prefixes="func"
>
  <xsl:output method="text" encoding="iso-8859-1" indent="no"/>
  
  <!--
   ! Retrieves short class name for a fully qualified class name
   !
   ! @param   string classname
   ! @return  string
   !-->
  <func:function name="func:shortname">
    <xsl:param name="classname"/>

    <func:result>
      <xsl:value-of select="translate($classname, '.', '·')"/>
    </func:result>
  </func:function>

  <!--
   ! Retrieves package name
   !
   ! @param   string classname
   ! @return  string
   !-->
  <func:function name="func:packagename">
    <xsl:param name="classname"/>

    <xsl:variable name="tokens" select="str:tokenize($classname, '.')"/>
    <func:result>
      <xsl:for-each select="$tokens[position() &lt; count($tokens) - 1]">
        <xsl:value-of select="."/>
        <xsl:if test="position() &lt; last()"><xsl:text>.</xsl:text></xsl:if>
      </xsl:for-each>
    </func:result>
  </func:function>

  <!--
   ! Function that returns a string fully in upper case
   !
   ! @param   string string
   ! @return  string
   !-->
  <func:function name="func:ucfirst">
    <xsl:param name="string"/>
    <func:result>
      <xsl:value-of select="concat(
        translate(substring($string, 1, 1), 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
        substring($string, 2, string-length($string)- 1)
      )"/> 
    </func:result>
  </func:function>  

  <!--
   ! Function that returns a type name
   !
   ! @param   node-set node
   ! @return  string
   !-->
  <func:function name="func:typeOf">
    <xsl:param name="node"/>
    <func:result>
      <xsl:choose>
        <xsl:when test="exsl:node-set($node)/classname"><xsl:value-of select="exsl:node-set($node)/classname"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="exsl:node-set($node)/text()"/></xsl:otherwise>
      </xsl:choose>
    </func:result>
  </func:function>  

  <xsl:template name="interface">
    <xsl:param name="description"/>
    <xsl:param name="interface"/>
    
    <xsl:text><![CDATA[<?php
/* This file is part of the XP framework
 *
 * $Id]]>&#36;<![CDATA[
 */

  $package= ']]></xsl:text>
  <xsl:value-of select="func:packagename(exsl:node-set($interface)/className)"/>
  <xsl:text><![CDATA[';

  /**
   * ]]></xsl:text><xsl:value-of select="$description"/><xsl:text><![CDATA[
   *
   * @purpose  EASC Client stub
   */
  interface ]]></xsl:text><xsl:value-of select="func:shortname(exsl:node-set($interface)/className)"/>
    <xsl:text> {&#10;</xsl:text>
    <xsl:for-each select="set:distinct(exsl:node-set($interface)/methods/values/value/name/text())">
      <xsl:variable name="name" select="."/>
      <xsl:variable name="methods" select="exsl:node-set($interface)/methods/values/value[name = $name]"/>
      
      <!-- Generate different sourcecode depending on whether the method is overloaded or not -->
      <xsl:choose>
        <xsl:when test="count($methods) = 1">
      
          <!-- Not overloaded -->
          <xsl:text><![CDATA[
    /**
     * ]]></xsl:text><xsl:value-of select="func:ucfirst($name)"/><xsl:text><![CDATA[ method
     *
]]></xsl:text>
          <xsl:for-each select="$methods[1]/parameterTypes/values/value">
            <xsl:text>     * @param   </xsl:text>
            <xsl:value-of select="concat(func:typeOf(.), ' arg', position())"/>
            <xsl:text>&#10;</xsl:text>
          </xsl:for-each>
          <xsl:if test="$methods[1]/returnType != ''">
            <xsl:text><![CDATA[     * @return  ]]></xsl:text><xsl:value-of select="func:typeOf($methods[1]/returnType)"/><xsl:text>&#10;</xsl:text>
          </xsl:if><xsl:text><![CDATA[     */
    public function ]]></xsl:text><xsl:value-of select="$name"/><xsl:text>(</xsl:text>
          <xsl:for-each select="$methods[1]/parameterTypes/values/value">
            <xsl:value-of select="concat('$arg', position())"/>
            <xsl:if test="position() &lt; last()">, </xsl:if>
          </xsl:for-each>
          <xsl:text>);&#10;</xsl:text>        
        </xsl:when>
        <xsl:otherwise>
        
          <!-- Overloaded -->
          <xsl:text><![CDATA[
    /**
     * ]]></xsl:text><xsl:value-of select="func:ucfirst($name)"/><xsl:text><![CDATA[ method
     *
     * @param   mixed*
     * @return  mixed
     */
    #[@overloaded(signatures= array(]]>&#10;</xsl:text>
        <xsl:for-each select="$methods">
          <xsl:text>    #  array('</xsl:text>
          <xsl:for-each select="parameterTypes/values/value">
            <xsl:value-of select="func:typeOf(.)"/>
            <xsl:if test="position() &lt; last()">', '</xsl:if>
          </xsl:for-each>
          <xsl:text>')</xsl:text>
          <xsl:if test="position() &lt; last()">,</xsl:if>
          <xsl:text>&#10;</xsl:text>
        </xsl:for-each>
        <xsl:text><![CDATA[    #))]
    public function ]]></xsl:text><xsl:value-of select="$name"/><xsl:text>();&#10;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
    <xsl:text><![CDATA[
  }
?>]]></xsl:text>
  </xsl:template>
  
  <xsl:template match="description[@purpose= 'home']">
    <xsl:call-template name="interface">
      <xsl:with-param name="description" select="concat('Home interface for ', jndiName)"/>
      <xsl:with-param name="interface" select="interfaces/values/value[1]"/>
    </xsl:call-template>
  </xsl:template>

  <xsl:template match="description[@purpose= 'remote']">
    <xsl:call-template name="interface">
      <xsl:with-param name="description" select="concat('Remote interface for ', jndiName)"/>
      <xsl:with-param name="interface" select="interfaces/values/value[2]"/>
    </xsl:call-template>
  </xsl:template>

  <xsl:template match="class">
    <xsl:text><![CDATA[<?php
/* This file is part of the XP framework
 *
 * $Id]]>&#36;<![CDATA[
 */

  /**
   * Generated class
   *
   * @purpose  Wrapper class
   */
  class ]]></xsl:text><xsl:value-of select="func:shortname(@name)"/><xsl:text><![CDATA[ extends Object {
    public
]]></xsl:text>

    <!-- Member variable declarations -->
    <xsl:for-each select="field">
      <xsl:value-of select="concat('      $', @name)"/>
      <xsl:if test="position() &lt; last()">,&#10;</xsl:if>
    </xsl:for-each>
    <xsl:text>;&#10;</xsl:text>

    <!-- Create getters and setters -->
    <xsl:for-each select="field">
      <xsl:text>
    /**
     * Retrieves </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @return  </xsl:text><xsl:if test="contains(@type, '.')">&amp;</xsl:if><xsl:value-of select="@type"/><xsl:text>
     */
    public function </xsl:text>
    <xsl:if test="contains(@type, '.')">&amp;</xsl:if>
    <xsl:text>get</xsl:text><xsl:value-of select="func:ucfirst(@name)"/><xsl:text>() {
      return $this-></xsl:text><xsl:value-of select="@name"/><xsl:text>;
    }
      </xsl:text>
    
      <xsl:text>
    /**
     * Sets </xsl:text><xsl:value-of select="@name"/><xsl:text>
     *
     * @param   </xsl:text><xsl:if test="contains(@type, '.')">&amp;</xsl:if><xsl:value-of select="concat(@type, ' ', @name)"/><xsl:text>
     */
    public function set</xsl:text><xsl:value-of select="func:ucfirst(@name)"/>
      <xsl:text>(</xsl:text><xsl:if test="contains(@type, '.')">&amp;</xsl:if>$<xsl:value-of select="@name"/><xsl:text>) {
      $this-></xsl:text><xsl:value-of select="@name"/>= <xsl:if test="contains(@type, '.')">&amp;</xsl:if>$<xsl:value-of select="@name"/><xsl:text>;
    }&#10;</xsl:text>
  </xsl:for-each>
  
  <!-- Close class declaration -->
<xsl:text><![CDATA[
  }
?>]]></xsl:text>
  </xsl:template>
  
  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>
  
</xsl:stylesheet>
