<?php namespace xp\runtime;

/**
 * Converts markdown to plain text w/ ASCII "art". Does not assume
 * to be a full-fledged, spec-compliant markdown parser!
 */
class RenderMarkdown {
  private $style;

  /**
   * Creates markdown
   *
   * @param  [:string] $style
   */
  public function __construct($style) {
    $this->style= $style;
  }

  /**
   * Converts api-doc "markup" to plain text w/ ASCII "art"
   *
   * @param  string $markdown
   * @return string text
   */
  public function render($markdown) {
    $style= $this->style;
    return preg_replace(
      array(
        '/# (.+)/',                                     // Prefixed first-level headline
        '/\*\*([^\n ][^\n\*]+[^\n ]|[^\n ]{1,2})\*\*/', // **bold**
        '/\*([^\n \*][^\n\*]+[^\n \*]|[^\n ]{1,2})\*/', // *italic*
        '/`([^`]+)`/',                                  // `preformat`
        '/(?<!\e)\[([^\]]+)\]\(([^\)]*)\)/'             // links
      ),
      array($style['h1'], $style['bold'], $style['italic'], $style['pre'], $style['link']),
      preg_replace(
        array(
          '/^(.+)\n=+$/m',                              // Underlined first-level headline
          '/^\* \* \*$/m',                              // horizontal rule
          '/^([*+-]) (.+)$/m',                          // unordered list
          '/^( *)```([a-z]*)\n *(.+)\n *```$/m',        // Code section
        ),
        array($style['h1'], $style['hr'], $style['li'], $style['code']),
        trim($markdown, "\r\n")
      )
    );
  }
}