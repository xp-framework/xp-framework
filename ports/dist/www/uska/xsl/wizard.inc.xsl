<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Date functions
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

  <func:function name="func:box">
    <xsl:param name="style" select="'success'"/>
    <xsl:param name="text"/>
    
    <func:result>
      <table border="0" class="box_{$style}" cellspacing="0" cellpadding="4">
        <tr>
          <td width="1%">
            <img src="/image/icons/wiz_{$style}.png"/>
          </td>
          <td>
            <xsl:copy-of select="$text"/>
          </td>
        </tr>  
      </table>
      <br clear="all"/>
    </func:result>
  </func:function>

  <func:function name="func:display_wizard_error">
    <xsl:param name="name"/>
    
    <func:result>
      <xsl:if test="/formresult/handlers/handler[@name= $name]/@status = 'failed' or /formresult/handlers/handler[@name= $name]/@status = 'errors'">
        <xsl:variable name="errortext">
          <xsl:copy-of select="func:get_text(concat($name, '#form-error'))"/>
          <ul>
            <xsl:for-each select="/formresult/formerrors/error[@checker = $name]">
              <li><xsl:copy-of select="func:get_text(concat($name, '#', @field, '-', @type))"/></li>
            </xsl:for-each>
          </ul>
        </xsl:variable>

        <xsl:copy-of select="func:box('error', $errortext)"/>
      </xsl:if>
    </func:result>
  </func:function>
  
  <func:function name="func:display_wizard_success">
    <xsl:param name="name"/>
    <func:result>
      <xsl:if test="/formresult/handlers/handler[@name= $name]/@status = 'success'">
        <xsl:copy-of select="func:box('success', func:get_text(concat($name, '#form-success')))"/>
      </xsl:if>
    </func:result>
  </func:function>

  <func:function name="func:display_wizard_reload">
    <xsl:param name="name"/>
    <func:result>
      <xsl:if test="/formresult/handlers/handler[@name= $name]/@status = 'reloaded'">
        <xsl:copy-of select="func:box('error', func:get_text(concat($name, '#form-reloaded')))"/>
      </xsl:if>
    </func:result>
  </func:function>
  
  <func:function name="func:_wizard_row_start">
    <xsl:param name="name"/>
    
    <func:result>
      <td width="150"><xsl:value-of select="func:get_text(concat('wizard#', $name))"/></td>
      <td width="16">
        <xsl:choose>
          <xsl:when test="/formresult/formerrors/error[@field= $name]">
            <img src="/image/icons/error.png" width="16" height="16" border="0"/>
          </xsl:when>
          <xsl:otherwise>&#160;</xsl:otherwise>
        </xsl:choose>
      </td>
    </func:result>
  </func:function>
  
  <func:function name="func:wizard_row_input">
    <xsl:param name="name"/>
    <xsl:param name="size" select="40"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($name)"/>
        <td><input type="text" name="{$name}" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}"/></td>
      </tr>
    </func:result>
  </func:function>

  <func:function name="func:wizard_row_password">
    <xsl:param name="name"/>
    <xsl:param name="size" select="40"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($name)"/>
        <td><input type="password" name="{$name}" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}"/></td>
      </tr>
    </func:result>
  </func:function>

  <func:function name="func:wizard_row_checkbox">
    <xsl:param name="name"/>
    
    <xsl:variable name="checked" select="/formresult/formvalues/param[@name= $name]"/>
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($name)"/>
        <td>
          <input type="hidden" name="{$name}" value="0"/>
          <input type="checkbox" name="{$name}" value="1">
            <xsl:if test="$checked = 1">
              <xsl:attribute name="checked">checked</xsl:attribute>
            </xsl:if>
          </input>
        </td>
      </tr>
    </func:result>
  </func:function>

  <func:function name="func:wizard_row_textarea">
    <xsl:param name="name"/>
    <xsl:param name="cols" select="80"/>
    <xsl:param name="rows" select="4"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($name)"/>
        <td>
          <textarea name="{$name}" rows="{$rows}" cols="{$cols}" wrap="virtual">
            <xsl:value-of select="/formresult/formvalue/param[@name= $name]"/>
          </textarea>
        </td>
      </tr>
    </func:result>
  </func:function>

  <func:function name="func:wizard_row_select">
    <xsl:param name="name"/>
    <xsl:param name="options"/>
    <xsl:param name="gettext" select="1"/>
    
    <xsl:variable name="selected" select="/formresult/formvalues/param[@name= $name]"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($name)"/>
        <td>
          <select name="{$name}">
            <xsl:for-each select="exsl:node-set($options)/option">
              <xsl:variable name="id" select="@id"/>
              <option value="{$id}">
                <xsl:if test="$selected = $id">
                  <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <xsl:if test="$gettext = 1"><xsl:value-of select="func:get_text(concat($name, '#', .))"/></xsl:if>
                <xsl:if test="$gettext = 0"><xsl:value-of select="."/></xsl:if>
              </option>
            </xsl:for-each>
          </select>
        </td>
      </tr>
    </func:result>
  </func:function>
  
  <func:function name="func:wizard_separator">
    <xsl:param name="name"/>
  
    <func:result>
      <tr>
        <td colspan="3" class="separator">
         <span class="separator"> &#160;<xsl:value-of select="func:get_text(concat('separator#', $name))"/>&#160;</span>
        </td>
      </tr>
    </func:result>
  </func:function>
  
    
</xsl:stylesheet>
