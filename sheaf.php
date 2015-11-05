<?php /**************************************************************
**
** sheaf.php
**
**   A library that supports the representation and automated
**   rendering of lecture notes for mathematics and computer
**   science courses.
**
**   Web:     sheaf.io
**   Version: 0.0.1.0
**
*/

//namespace sheaf;

/********************************************************************
** Container class for sheaf data structure and the associated
** functionality.
*/

global $sheaf; $sheaf = null;

class Sheaf {

  // Common constants.
  public static $blocks = array(
      'definition' => 'Definition',
      'fact' => 'Fact',
      'theorem' => 'Theorem',
      'conjecture' => 'Conjecture',
      'algorithm' => 'Algorithm',
      'protocol' => 'Protocol',
      'example' => 'Example',
      'exercise' => 'Exercise',
      'diagram' => ''
    );

  // The configuration.
  private $sheaf = null;

  public function Sheaf ($m) {

    global $sheaf;
    $sheaf = $m;

    if (!isset($sheaf))
      $sheaf = array();
    if (!array_key_exists('file', $sheaf) && !array_key_exists('content', $sheaf))
      die(sprintf("sheaf: no input file or content specified; exiting."));
    if (!array_key_exists('path', $sheaf))
      $sheaf['path'] = '';
    if (!array_key_exists('toc', $sheaf))
      $sheaf['toc'] = 'true';
    if (!array_key_exists('message', $sheaf))
      $sheaf['message'] = '';

    $this->sheaf = $sheaf;
  }

  /******************************************************************
  ** XML Parsing and HTML rendering procedure for the table of
  ** contents.
  */

  public function html() {
    if (array_key_exists('file', $this->sheaf))
      $xml = file_get_contents($this->sheaf['file']);
    else if (array_key_exists('content', $this->sheaf))
      $xml = $this->sheaf['content'];

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo "\n".'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo "\n"."<html>"."\n";

    $tocHTML = sheaf::parse_render_toc($this->sheaf, $xml);
    sheaf::parse_render($this->sheaf, $xml, $tocHTML);

    echo "\n"."</html>"."\n"."<!--eof-->";
  }

  private static function parse_render_toc($sheaf, $xml) {

    if ($sheaf['toc'] === 'false' || $sheaf['toc'] === false)
      return "";

    global $tocHTML; $tocHTML = "";
    global $counter; $counter = array(
        'section' => 1,
        'subsection' => 1,
        'assignment' => 1,
        'review' => 1,
        'midterm' => 1,
        'appendix' => 'A'
      );

    global $tagPath; $tagPath = '';

    if (!function_exists('parse_render_toc_lft')) {function parse_render_toc_lft($parser, $name, $attrs) {
      global $tocHTML;
      global $counter;
      global $tagPath;
      $tagPath .= '/'.$name;

      if ($tagPath == '/sheaf')
        $tocHTML .= '<div class="toc"><ul>';
      if ($tagPath == '/sheaf/section') {
        $id = $counter['section']; //$attrs['id'];
        $tocHTML .= ' <li>'.$counter['section'].'. <a href="#'.$id.'">'.$attrs['title']."</a>\n  <ul>";
      }
      if ($tagPath == '/sheaf/review') {
        $id = 'R.'.$counter['review']; //$attrs['id'];
        $tocHTML .= ' <li><a href="#'.$id.'"><i>Review #'.$counter['review'].': '.$attrs['title']."</i></a>\n  <ul>";
      }
      if ($tagPath == '/sheaf/midterm') {
        $id = 'M.'.$counter['midterm'];
        $tocHTML .= ' <li><a href="#'.$id.'"><b>Midterm: '.$attrs['title']."</b></a>\n  <ul>";
      }
      if ($tagPath == '/sheaf/final') {
        $id = 'F';
        $tocHTML .= ' <li><a href="#'.$id.'"><b>Final: '.$attrs['title']."</b></a>\n  <ul>";
      }
      if ($tagPath == '/sheaf/appendix') {
        $id = $counter['appendix']; //$attrs['id'];
        $tocHTML .= ' <li>Appendix '.$counter['appendix'].'. <a href="#'.$id.'">'.$attrs['title']."</a>\n  <ul>";
      }
      if ($tagPath == '/sheaf/section/subsection') {
        $id = $counter['section'].'.'.$counter['subsection']; //$attrs['id'];
        $tocHTML .=
            '  <li>'.$counter['section'].'.'.$counter['subsection'].'.'
          . ' <a href="#'.$id.'">'.$attrs['title'].'</a></li>';
      }
      if ($tagPath == '/sheaf/appendix/subsection') {
        $id = $counter['appendix'].'.'.$counter['subsection']; //$attrs['id'];
        $tocHTML .=
            '  <li>'.$counter['appendix'].'.'.$counter['subsection'].'.'
          . ' <a href="#'.$id.'">'.$attrs['title'].'</a></li>';
      }
      if ($tagPath == '/sheaf/section/assignment') {
        $id = $counter['section'].'.'.$counter['subsection']; //$attrs['id'];
        $tocHTML .=
            '  <li>'.$counter['section'].'.'.$counter['subsection'].'.'
          . ' <a href="#'.$id.'"><b>Assignment #'.$counter['assignment'].': '.$attrs['title'].'</b></a></li>';
      }
    }}
    if (!function_exists('parse_render_toc_val')) { function parse_render_toc_val($parser, $data) {
      // Nothing.
    }}
    if (!function_exists('parse_render_toc_rgt')) {function parse_render_toc_rgt($parser, $name) {
      global $tocHTML;
      global $counter;
      global $tagPath;

      if ($tagPath == '/sheaf')
        $tocHTML .= '</ul></div>';
      if ($tagPath == '/sheaf/section') {
        $tocHTML .= "\n  </ul>\n </li>";
        $counter['section']++;
        $counter['subsection'] = 1;
      }
      if ($tagPath == '/sheaf/review') {
        $tocHTML .= "\n  </ul>\n </li>";
        $counter['review']++;
      }
      if ($tagPath == '/sheaf/midterm') {
        $tocHTML .= "\n  </ul>\n </li>";
        $counter['midterm']++;
      }
      if ($tagPath == '/sheaf/final') {
        $tocHTML .= "\n  </ul>\n </li>";
      }
      if ($tagPath == '/sheaf/section/subsection') {
        $counter['subsection']++;
      }
      if ($tagPath == '/sheaf/section/assignment') {
        $counter['subsection']++;
        $counter['assignment']++;
      }
      if ($tagPath == '/sheaf/appendix') {
        $tocHTML .= "\n  </ul>\n </li>";
        $counter['appendix']++;
        $counter['subsection'] = 1;
      }
      if ($tagPath == '/sheaf/appendix/subsection') {
        $counter['subsection']++;
      }

      $tagPath = substr($tagPath, 0, strlen($tagPath) - strlen($name) - 1);
    }}

    sheaf::do_xml_parse("parse_render_toc_lft", "parse_render_toc_val", "parse_render_toc_rgt", $xml);
    return $tocHTML;
  }
  /******************************************************************
  ** XML Parsing and HTML rendering procedure for the document
  ** contents.
  */

  private static function parse_render($sheaf, $xml, $tocHTML = "") {
    global $sheaf;
    global $attributes; $attributes = array();
    global $hooks; $hooks = array();
    global $tocHTML;

    global $counter; $counter = array(
        'section' => 1,
        'subsection' => 1,
        'assignment' => 1,
        'review' => 1,
        'midterm' => 1,
        'appendix' => 'A'
      );

    global $tagPath; $tagPath = '';

    if (!function_exists('parse_render_lft')) {function parse_render_lft($parser, $name, $attrs) {
      global $sheaf;
      global $attributes;
      global $hooks;
      global $counter;
      global $tagPath;
      global $tocHTML;
      $tagPath .= '/'.$name;
      $pathLeaf = sheaf::pathLeaf($tagPath);

      // Update the hooks.
      array_push($hooks, (array_key_exists('hooks', $attrs)) ? $attrs['hooks'] : "");
      $attributes[] = $attrs;

      // Render the XML as HTML.
      if ($tagPath == '/sheaf') {
        echo '<head>';
        echo "\n".'<meta charset="utf-8">';
        echo "\n".'<title>'.$attrs['title'].'</title>';
        echo "\n".'<link rel="stylesheet" href="'.$sheaf['path'].'sheaf.css">';
        echo "\n".'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>';
        echo "\n".'<script type="text/javascript" src="'.$sheaf['path'].'sheaf.js"></script>';
        echo "\n".'</head>';
        echo "\n".'<body>';
        echo "\n".'<div class="sheaf" id="sheaf">';

        echo $sheaf['message'];
        echo $tocHTML;
      }
      if ($tagPath == '/sheaf/section' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = $counter['section']; //$attrs['id'];
        echo "\n".'<a name="'.$id.'"></a>'."\n".'<div class="section"><hr style="margin-bottom:120px;"/>';
        echo '<h2 class="linked"><span class="link-title">'
           // . '[<a href="?id='.$id.'">page</a>]<br/>'
           . '[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;'
           . '</span>'
           . '<span class="header_numeral">'.$counter['section'].'.</span> '.$attrs['title'].'</h2>';
      }
      if ($tagPath == '/sheaf/review') {
        $id = 'R.'.$counter['review']; //$attrs['id'];
        echo '<a name="'.$id.'"></a><div class="review"><hr style="margin-bottom:120px;"/>';
        echo '<h2 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">Review #'.$counter['review'].'.</span> '.$attrs['title'].'</h2>';
      }
      if ($tagPath == '/sheaf/midterm') {
        $id = 'M.'.$counter['midterm']; //$attrs['id'];
        echo '<a name="'.$id.'"></a><div class="midterm"><hr style="margin-bottom:120px;"/>';
        echo '<h2 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">Midterm.</span> '.$attrs['title'].'</h2>';
      }
      if ($tagPath == '/sheaf/final') {
        $id = 'F'; //$attrs['id'];
        echo '<a name="'.$id.'"></a><div class="final"><hr style="margin-bottom:120px;"/>';
        echo '<h2 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">Final.</span> '.$attrs['title'].'</h2>';
      }
      if ($tagPath == '/sheaf/section/subsection' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = $counter['section'].'.'.$counter['subsection']; //$attrs['id'];
        echo "\n  ".'<a name="'.$id.'"></a><div class="subsection">';
        echo '<h3 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">'.$counter['section'].'.'.$counter['subsection'].'.</span> '
           . $attrs['title'].'</h3>';
      }
      if ($tagPath == '/sheaf/section/assignment') {
        $id = $counter['section'].'.'.$counter['subsection']; //$attrs['id'];
        echo '<br/><hr/>'
           . '<a name="'.$id.'"></a>'
           . '<a name="assignment'.$counter['assignment'].'"></a>'
           . '<a name="hw'.$counter['assignment'].'"></a>'
           . '<div class="assignment">';
        echo '<h3 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">'
           . $counter['section'].'.'.$counter['subsection'].'.</span> '
           . '<span class="assignment_title">Assignment #'.$counter['assignment'].': '.$attrs['title'].'</span></h3>';
      }
      if ($pathLeaf === 'problems')  echo '<ol class="problems">';
      if ($pathLeaf === 'problem') echo '<li class="problem">';
      if ($pathLeaf === 'parts') echo '<ol class="parts">';
      if ($pathLeaf === 'part') echo '<li class="part">';

      if ($tagPath == '/sheaf/appendix') {
        $id = $counter['appendix']; //$attrs['id'];
        echo '<a name="'.$id.'"></a><div class="appendix"><hr style="margin-bottom:120px;"/>';
        echo '<h2 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">Appendix '.$counter['appendix'].'.</span> '.$attrs['title'].'</h2>';
      }
      if ($tagPath == '/sheaf/appendix/subsection' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = $counter['appendix'].'.'.$counter['subsection']; //$attrs['id'];
        echo '<a name="'.$id.'"></a><div class="subsection">';
        echo '<h3 class="linked"><span class="link-title">[<a href="#'.$id.'">link</a>]&nbsp;&nbsp;</span>'
           . '<span class="header_numeral">'.$counter['appendix'].'.'.$counter['subsection'].'.</span> '
           . $attrs['title'].'</h3>';
      }

      // Categorized blocks.
      foreach (sheaf::$blocks as $tag => $name) {
        if ( $tagPath == '/sheaf/section/subsection/'.$tag
          || $tagPath == '/sheaf/appendix/subsection/'.$tag
          || $tagPath == '/sheaf/review/'.$tag ) {
          $id = array_key_exists('id', $attrs) ? $attrs['id'] : '';
          $link = array_key_exists('link', $attrs) ? $attrs['link'] : '#'.$id;
          $classes = $tag.((array_key_exists('required', $attrs) && $attrs['required'] == 'true') ? '_required' : '');
          echo "\n".'<a name="'.$id.'"></a>'
             . '<div class="linked block" style="white-space:nowrap;">'
             . '<div style=" display:inline; vertical-align:middle;" class="link-block">[<a href="'.$link.'">link</a>]&nbsp;&nbsp;</div>'
             . '<div style=" width:100%; display:inline-block;">'
             . '<div style="width:auto;" class="'.$classes.'">';
          if (strlen($name) > 0) { // Only show label if there is a label.
            echo '<span class="block_label">'.$name;
            if (array_key_exists('title', $attrs))
              echo ' ('.$attrs['title'].')';
            echo ':</span> ';
          }
        }
      }

      // Assignment and exam instructions.
      if ($pathLeaf === "instructions") {
        echo '<div class="instructions">';
      }

      // Paragraphs with and without titles.
      if ($pathLeaf == "paragraph") {
        echo '<div class="paragraph">';
        if (array_key_exists('title', $attrs))
          echo '<b>'.$attrs['title'].'.</b> ';
      }

      // Ordered and unordered lists.
      if ($pathLeaf === "orderedlist") {
         echo '<ol'.((array_key_exists('style', $attrs)) ? ' style="'.$attrs['style'].'"' : '').'>';
      }
      if ($pathLeaf === "unorderedlist") echo '<ul>';
      if ($pathLeaf === "item") {
        echo '<li>';
        if (array_key_exists('title', $attrs))
          echo '<b>'.$attrs['title'].': </b>';
      }

      // Collections of inference rules.
      if ($pathLeaf === "inferences") {
        echo '<div class="inferences">';
      }
      if ($pathLeaf === "inferencesTable") {
        echo '<table style="font-size:14px;"><tr>';
      }
      if ($pathLeaf === "inferencesTableCol") {
        echo '<td>';
      }
      if ($pathLeaf === "inference") {
        echo '<table class="inference"><tr>';
        if (array_key_exists('title', $attrs))
          echo '<td class="title">['.$attrs['title'].']</td>';
        echo '<td><table>';
      }
      if ($pathLeaf === "premises") {
        echo '<tr><td class="premises">&nbsp;';
      }
      if ($pathLeaf === "conclusion") {
        echo '<tr><td class="conclusion">&nbsp;';
      }

      // Solutions (in examples, exercises, and problems).
      if ($pathLeaf == "solution") echo "\n".'<div class="button"><button class="solution_toggle">show solution</button></div><div class="solution_container" style="display:none;"><div class="solution">';

      // Source code and text blocks.
      if ($pathLeaf == "code") echo "\n".'<div class="code"><div class="source">'; //<pre>
      if ($pathLeaf == "text") echo "\n".'<span class="text">';
      if ($pathLeaf == "content") echo "\n".'<div>';
    }}
    if (!function_exists('parse_render_val')) { function parse_render_val($parser, $data) {
      global $hooks;
      global $counter;
      global $tagPath;
      $pathLeaf = sheaf::pathLeaf($tagPath);

      // Render the XML as HTML.
      if ( $tagPath == '/sheaf/section/subsection/definition'
        || $tagPath == '/sheaf/appendix/subsection/definition'
        || $tagPath == '/sheaf/section/subsection/fact'
        || $tagPath == '/sheaf/appendix/subsection/fact'
        || $tagPath == '/sheaf/section/subsection/theorem'
        || $tagPath == '/sheaf/appendix/subsection/theorem'
        || $tagPath == '/sheaf/section/subsection/conjecture'
        || $tagPath == '/sheaf/appendix/subsection/conjecture'
        || $tagPath == '/sheaf/section/subsection/algorithm'
        || $tagPath == '/sheaf/appendix/subsection/algorithm'
        || $tagPath == '/sheaf/section/subsection/protocol'
        || $tagPath == '/sheaf/appendix/subsection/protocol'
        || $tagPath == '/sheaf/section/subsection/example'
        || $tagPath == '/sheaf/appendix/subsection/example'
        || $tagPath == '/sheaf/section/subsection/exercise'
        || $tagPath == '/sheaf/appendix/subsection/exercise'
        || $tagPath == '/sheaf/section/subsection/diagram'
        || $tagPath == '/sheaf/appendix/subsection/diagram'
        || $tagPath == '/sheaf/review/exercise'
        || $pathLeaf === 'paragraph'
        || $pathLeaf === 'solution'
        || $pathLeaf === 'code'
        || $pathLeaf === 'content'
        || $pathLeaf === 'text'
        || $pathLeaf === 'instructions'
        || $pathLeaf === 'item'
        || $pathLeaf === 'premises'
        || $pathLeaf === 'conclusion'
         ) {
        // Apply the hooks.
        $out = $data;
        $applied = array();
        foreach ($hooks as $hooklist) {
          if (strlen($hooklist) > 0) {
            foreach (split(',', $hooklist) as $hook) {
              if (!in_array($hook, $applied)) {
                $out = call_user_func('sheaf_hook_'.$hook, $out);
                $applied[] = $hook;
              }
            }
          }
        }
        if ($pathLeaf === 'text' || $pathLeaf === 'item')
          $out = trim($out);

        echo $out;
      }
    }}
    if (!function_exists('parse_render_rgt')) {function parse_render_rgt($parser, $name) {
      global $attributes;
      global $hooks;
      global $counter;
      global $tagPath;
      $pathLeaf = sheaf::pathLeaf($tagPath);

      $attrs = array_pop($attributes);

      // Update the hooks.
      array_pop($hooks);

      // Render the XML as HTML.
      if ($tagPath == '/sheaf')
        echo "\n".'</div><div class="footer"><div class="sheaflink">represented and rendered using <a href="http://sheaf.io">sheaf</a></div></div></body>';
      if ($tagPath == '/sheaf/section' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        echo "\n".'</div>';
        $counter['section']++;
        $counter['subsection'] = 1;
      }
      if ($tagPath == '/sheaf/review') {
        echo "\n".'</div>';
        $counter['review']++;
      }
      if ($tagPath == '/sheaf/midterm') {
        echo "\n".'</div>';
        $counter['midterm']++;
      }
      if ($tagPath == '/sheaf/appendix') {
        echo '</div>';
        $counter['appendix']++;
        $counter['subsection'] = 1;
      }
      if ($tagPath == '/sheaf/section/subsection' || $tagPath == '/sheaf/appendix/subsection') {
        if (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false') {
          echo '</div>';
          $counter['subsection']++;
        }
      }
      if ($tagPath == '/sheaf/section/assignment') {
        echo "\n".'</div><hr/><br/>';
        $counter['subsection']++;
        $counter['assignment']++;
      }

      if ($pathLeaf === 'problems')  echo '</ol>';
      if ($pathLeaf === 'problem') echo '</li>';
      if ($pathLeaf === 'parts') echo '</ol>';
      if ($pathLeaf === 'part') echo '</li>';

      // Restored original block of code to deal with bug.
      /*if ($tagPath == '/sheaf/review/exercise')
        echo '</div></div></div>';
      else
        foreach (sheaf::$blocks as $tag => $name) {
          if ( $tagPath == ('/sheaf/section/subsection/'.$tag)
            || $tagPath == ('/sheaf/appendix/subsection/'.$tag)
             )
            echo '</div></div></div>';
          break;
        }*/

      if ( $tagPath == '/sheaf/section/subsection/definition'
        || $tagPath == '/sheaf/appendix/subsection/definition'
        || $tagPath == '/sheaf/section/subsection/fact'
        || $tagPath == '/sheaf/appendix/subsection/fact'
        || $tagPath == '/sheaf/section/subsection/theorem'
        || $tagPath == '/sheaf/appendix/subsection/theorem'
        || $tagPath == '/sheaf/section/subsection/conjecture'
        || $tagPath == '/sheaf/appendix/subsection/conjecture'
        || $tagPath == '/sheaf/section/subsection/algorithm'
        || $tagPath == '/sheaf/appendix/subsection/algorithm'
        || $tagPath == '/sheaf/section/subsection/protocol'
        || $tagPath == '/sheaf/appendix/subsection/protocol'
        || $tagPath == '/sheaf/section/subsection/example'
        || $tagPath == '/sheaf/appendix/subsection/example'
        || $tagPath == '/sheaf/section/subsection/exercise'
        || $tagPath == '/sheaf/appendix/subsection/exercise'
        || $tagPath == '/sheaf/section/subsection/diagram'
        || $tagPath == '/sheaf/appendix/subsection/diagram'
        || $tagPath == '/sheaf/review/exercise' ) {
        echo '</div></div></div>';
      }
      // Above block is obsolete but restored to deal with a bug.

      if ( $pathLeaf === 'instructions' ) echo '</div>';

      if ( $pathLeaf === 'paragraph' ) echo '</div>';

      if ($pathLeaf == "orderedlist") echo '</ol>';
      if ($pathLeaf == "unorderedlist") echo '</ul>';
      if ($pathLeaf == "item") echo '</li>';

      if ($pathLeaf === "inferences") echo '</div>';
      if ($pathLeaf === "inferencesTable")  echo '</tr></table>';
      if ($pathLeaf === "inferencesTableCol") echo '</td>';
      if ($pathLeaf === "inference") echo '</table></td></tr></table>';
      if ($pathLeaf === "premises") echo '&nbsp;</td></tr>';
      if ($pathLeaf === "conclusion") echo '&nbsp;</td></tr>';

      if ($pathLeaf == "solution") echo '</div></div><div class="solution_spacer"></div>';
      if ($pathLeaf == "text") echo '</span>';
      if ($pathLeaf == "content") echo '</div>';
      if ($pathLeaf == "code") echo '</div></div>'; //</pre>

      $tagPath = substr($tagPath, 0, strlen($tagPath) - strlen($name) - 1);
    }}

    sheaf::do_xml_parse("parse_render_lft", "parse_render_val", "parse_render_rgt", $xml);
    return null;
  }

  ///////////////////////////////////////////////////////////////////
  // Functions for defining and invoking XML parsers.

  private static function mk_xml_parser($startF, $datF, $endF) {
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
    xml_set_element_handler($xml_parser, $startF, $endF);
    xml_set_character_data_handler($xml_parser, $datF);
    return $xml_parser;
  }

  private static function do_xml_parse($startF, $datF, $endF, $xml) {
    $xml_parser = sheaf::mk_xml_parser($startF, $datF, $endF);
    if (!xml_parse($xml_parser, $xml))
      die(
        sprintf(
          "XML error: %s at line %d",
          xml_error_string(xml_get_error_code($xml_parser)),
          xml_get_current_line_number($xml_parser)
        )
      );
    xml_parser_free($xml_parser);
  }

  ///////////////////////////////////////////////////////////////////
  // Other utility functions.

  public static function pathLeaf($path) {
    $a = split("/", $path);
    return (count($a) < 1) ? null : $a[count($a)-1];
  }

  private static function endsWith($str, $suf) {
    return $suf === "" || substr($str, -strlen($suf)) === $str;
  }

  public static function startsWith($haystack, $needle) {
      return $needle === "" || strpos($haystack, $needle) === 0;
  }

  public static function r($t,$x,$y) {
    return str_replace($x,$y,$t);
  }
}

/*eof*/ ?>