<?xml version="1.0" encoding="iso-8859-1"?>
<!--
 ! Wizard input element functions for 3-columned-form. These functions
 ! render a 3-columned form, that consists of these columns:
 !
 !   * description (name calculated with "wizard#$name"
 !   * error column (shows a red cross when input element returned an error)
 !   * input element column
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

  <func:function name="func:wizard_error_text">
    <xsl:param name="name"/>
    <xsl:param name="field"/>
    <xsl:param name="type"/>
    
    <func:result>
      <xsl:copy-of select="func:get_text(concat($name, '#error-', @field, '-', @type))"/>
    </func:result>
    
    <!--
      <xsl:choose>
        <xsl:when test="func:exists_text(concat($name, '#error-', @field, '-', @type)) = '*'"><xsl:copy-of select="func:get_text(concat($name, '#error-', @field, '-', @type))"/></xsl:when>
        <xsl:when test="func:exists_text(concat($name, '#error-', @field)) != '-'"><xsl:copy-of select="func:get_text(concat($name, '#error-', @field))"/></xsl:when>
        <xsl:when test="func:exists_text(concat($name, '#error')) != '-'"><xsl:copy-of select="func:get_text(concat($name, '#error'))"/></xsl:when>
        <xsl:otherwise>Ein Fehler ist im Feld "<xsl:value-of select="@field"/>" aufgetreten, s.u.</xsl:otherwise>
      </xsl:choose>
    </func:result>
    -->
  </func:function>

  <func:function name="func:display_wizard_error">
    <xsl:param name="name"/>
    
    <func:result>
      <xsl:if test="/formresult/handlers/handler[@name= $name]/@status = 'failed' or /formresult/handlers/handler[@name= $name]/@status = 'errors'">
        <xsl:variable name="errortext">
          <xsl:copy-of select="func:get_text(concat($name, '#form-error'))"/>
          <ul>
            <xsl:for-each select="/formresult/formerrors/error[@checker = $name]">
              <li><xsl:value-of select="func:wizard_error_text($name, @field, @type)"/></li>
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
  
  <!--
   ! Renders the first two columns.
   !
   ! @access  protected
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @return  node-set
   !-->
  <func:function name="func:_wizard_row_start">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    
    <func:result>
      <td width="150"><xsl:value-of select="func:get_text(concat($wizard, '#', $name))"/></td>
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

  <!--
   ! Renders a input-field wizard-row.
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @param   int size default 40
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_input">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    <xsl:param name="size" select="40"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
        <td><input type="text" name="{$name}" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}"/></td>
      </tr>
    </func:result>
  </func:function>

  <!--
   ! Renders a read-only input row.
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @param   int size default 40
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_print">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    <xsl:param name="size" select="40"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
        <td>
          <input type="text" name="__ignorethis" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}" disabled="disabled"/>
          <input type="hidden" name="{$name}" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}"/>
        </td>
      </tr>
    </func:result>
  </func:function>

  <!--
   ! Renders a input-field wizard-row of type "password"
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @param   int size default 40
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_password">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    <xsl:param name="size" select="40"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
        <td><input type="password" name="{$name}" value="{/formresult/formvalues/param[@name= $name]}" size="{$size}"/></td>
      </tr>
    </func:result>
  </func:function>

  <!--
   ! Renders a checkbox-field wizard-row.
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_checkbox">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    
    <xsl:variable name="checked" select="/formresult/formvalues/param[@name= $name]"/>
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
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

  <!--
   ! Renders a textarea wizard-row.
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @param   int cols default 80
   ! @param   int rows default 4
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_textarea">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    <xsl:param name="cols" select="80"/>
    <xsl:param name="rows" select="4"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
        <td>
          <textarea name="{$name}" rows="{$rows}" cols="{$cols}" wrap="virtual">
            <xsl:value-of select="/formresult/formvalues/param[@name= $name]"/>
          </textarea>
        </td>
      </tr>
    </func:result>
  </func:function>

  <!--
   ! Renders a selectbox wizard-row. If gettext is set to 0, the option names
   ! are passed through without resolving their names with get_text(), useful
   ! for "calculated" options.
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @param   node-set options
   ! @param   int gettext default 1
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_select">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
    <xsl:param name="options"/>
    <xsl:param name="gettext" select="1"/>
    
    <xsl:variable name="selected" select="/formresult/formvalues/param[@name= $name]"/>
    
    <func:result>
      <tr>
        <xsl:copy-of select="func:_wizard_row_start($wizard, $name)"/>
        <td>
          <select name="{$name}">
            <xsl:for-each select="exsl:node-set($options)/option">
              <xsl:variable name="id" select="@id"/>
              <option value="{$id}">
                <xsl:if test="$selected = $id">
                  <xsl:attribute name="selected">selected</xsl:attribute>
                </xsl:if>
                <xsl:if test="$gettext = 1"><xsl:value-of select="func:get_text(concat($wizard, '-', $name, '#', .))"/></xsl:if>
                <xsl:if test="$gettext = 0"><xsl:value-of select="."/></xsl:if>
              </option>
            </xsl:for-each>
          </select>
        </td>
      </tr>
    </func:result>
  </func:function>
  
  <!--
   ! Renders a separator line
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_separator">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
  
    <func:result>
      <tr>
        <td colspan="3" class="separator">
         <span class="separator"> &#160;<xsl:value-of select="func:get_text(concat($wizard, '#separator-', $name))"/>&#160;</span>
        </td>
      </tr>
    </func:result>
  </func:function>
  
  <!--
   ! Renders the submit button
   !
   ! @access  public
   ! @param   string wizard name of wizard
   ! @param   string name name of input element
   ! @return  node-set
   !-->
  <func:function name="func:wizard_row_submit">
    <xsl:param name="wizard"/>
    <xsl:param name="name"/>
  
    <func:result>
      <tr>
        <td colspan="3" align="right">
          <input class="button_apply" name="submit" type="submit" value="{func:get_text(concat($wizard, '#submit-', $name))}"/>
        </td>
      </tr>
    </func:result>
  </func:function>
</xsl:stylesheet>
