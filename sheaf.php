<?php /************************************************************************
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

/******************************************************************************
** Container class for sheaf data structure and the associated functionality.
*/

global $sheaf; $sheaf = null;

class Sheaf {

  // Common constants.
  public static $blocks = array(
      'true' => array(
          'definition' => 'Definition',
          'fact' => 'Fact',
          'proposition' => 'Proposition',
          'lemma' => 'Lemma',
          'theorem' => 'Theorem',
          'conjecture' => 'Conjecture',
          'algorithm' => 'Algorithm',
          'protocol' => 'Protocol'
        ),
      'task' => array(
          'example' => 'Example',
          'exercise' => 'Exercise'
        ),
      'other' => array(
          'table' => '',
          'diagram' => ''
        )
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

  /****************************************************************************
  ** Public function to render the complete document as HTML.
  */
  public function html() {
    if (array_key_exists('file', $this->sheaf))
      $xml = @file_get_contents($this->sheaf['file']);
    else if (array_key_exists('content', $this->sheaf))
      $xml = $this->sheaf['content'];

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo "\n".'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo "\n"."<html>"."\n";

    $tocHTML = sheaf::parse_render_toc($this->sheaf, $xml);
    sheaf::parse_render($this->sheaf, $xml, $tocHTML);

    echo "\n"."</html>"."\n"."<!--eof-->";
  }

  /****************************************************************************
  ** XML Parsing and HTML rendering procedure for the table of contents.
  */
  private static function parse_render_toc($sheaf, $xml) {

    if ($sheaf['toc'] === 'false' || $sheaf['toc'] === false)
      return "";

    global $tocHTML; $tocHTML = "";
    global $counter; $counter = array(
        'section' => 1,
        'subsection' => 1,
        'assignment' => 1,
        'project' => 0,
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
      $pathLeaf = $name;

      // If we are importing a file, process its contents for TOC rendering.
      if ( $pathLeaf === 'include' ) {
        $imported = @file_get_contents($attrs['sheaf']);
        sheaf::do_xml_parse("parse_render_toc_lft", "parse_render_toc_val", "parse_render_toc_rgt", $imported);
        return;
      }

      if ($tagPath === '/sheaf')
        $tocHTML .= '<div class="toc"><ul>';
      if ($tagPath === '/sheaf/section') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : $counter['section'];
        $tocHTML .= ' <li>'.$counter['section'].'. <a href="#'.$id.'">'.$attrs['title']."</a>\n  <ul>";
      }
      if ($tagPath === '/sheaf/review') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ('R.'.$counter['review']);
        $tocHTML .= ' <li><a href="#'.$id.'"><i>Review #'.$counter['review'].': '.$attrs['title']."</i></a>\n  <ul>";
      }
      if ($pathLeaf === 'midterm') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ('M.'.$counter['midterm']);
        $tocHTML .= ' <li><a href="#'.$id.'"><b>Midterm: '.$attrs['title']."</b></a>\n  <ul>";
      }
      if ($pathLeaf === 'final') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : 'F';
        $tocHTML .= ' <li><a href="#'.$id.'"><b>Final: '.$attrs['title']."</b></a>\n  <ul>";
      }
      if ($tagPath === '/sheaf/appendix') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : $counter['appendix'];
        $tocHTML .= ' <li>Appendix '.$counter['appendix'].'. <a href="#'.$id.'">'.$attrs['title']."</a>\n  <ul>";
      }
      if ($tagPath === '/sheaf/references') {
        $tocHTML .= ' <li><a href="#bib">References</a></li>';
      }
      if ($tagPath === '/sheaf/section/subsection') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        $tocHTML .= '  <li>'.$counter['section'].'.'.$counter['subsection'].'.' . ' <a href="#'.$id.'">'.$attrs['title'].'</a></li>';
      }
      if ($tagPath === '/sheaf/appendix/subsection') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['appendix'].'.'.$counter['subsection']);
        $tocHTML .= '  <li>'.$counter['appendix'].'.'.$counter['subsection'].'.'
                  . ' <a href="#'.$id.'">'.$attrs['title'].'</a></li>';
      }
      if ($pathLeaf === 'assignment') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        $tocHTML .= '  <li>'.$counter['section'].'.'.$counter['subsection'].'.'
                  . ' <a href="#'.$id.'"><b>Assignment #'.sheaf::strval0($counter['assignment']).': '.$attrs['title'].'</b></a></li>';
      }
      if ($pathLeaf === 'project') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        $tocHTML .= '  <li>'.$counter['section'].'.'.$counter['subsection'].'.'
                  . ' <a href="#'.$id.'"><b>Project #'.sheaf::strval0($counter['project']).': '.$attrs['title'].'</b></a></li>';
      }
    }}
    if (!function_exists('parse_render_toc_val')) {function parse_render_toc_val($parser, $data) {
      // Do nothing.
    }}
    if (!function_exists('parse_render_toc_rgt')) {function parse_render_toc_rgt($parser, $name) {
      global $tocHTML;
      global $counter;
      global $tagPath;
      $pathLeaf = $name;

      if ($tagPath === '/sheaf') { $tocHTML .= '</ul></div>'; }
      if ($tagPath === '/sheaf/section') { $counter['section']++; $counter['subsection'] = 1; $tocHTML .= "\n  </ul>\n </li>"; }
      if ($tagPath === '/sheaf/review') { $counter['review']++; $tocHTML .= "\n  </ul>\n </li>"; }
      if ($pathLeaf === 'midterm') { $counter['midterm']++; $tocHTML .= "\n  </ul>\n </li>"; }
      if ($pathLeaf === 'final') { $tocHTML .= "\n  </ul>\n </li>"; }
      if ($tagPath === '/sheaf/section/subsection') { $counter['subsection']++; }
      if ($pathLeaf === 'assignment') { $counter['subsection']++; $counter['assignment']++; }
      if ($pathLeaf === 'project') { $counter['subsection']++; $counter['project']++; }
      if ($tagPath === '/sheaf/appendix') { $counter['appendix']++; $counter['subsection'] = 1; $tocHTML .= "\n  </ul>\n </li>"; }
      if ($tagPath === '/sheaf/appendix/subsection') { $counter['subsection']++; }
      if ($tagPath === '/sheaf/references') { }

      $tagPath = substr($tagPath, 0, strlen($tagPath) - strlen($name) - 1);
    }}

    sheaf::do_xml_parse("parse_render_toc_lft", "parse_render_toc_val", "parse_render_toc_rgt", $xml);
    return $tocHTML;
  }

  /****************************************************************************
  ** XML Parsing and HTML rendering procedure for the document contents.
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
        'project' => 0,
        'review' => 1,
        'midterm' => 1,
        'reference' => 1,
        'appendix' => 'A'
      );

    global $tagPath; $tagPath = '';
    global $lastSubsectionWasWork; $lastSubsectionWasWork = false;

    /**************************************************************************
    ** Add XML parsing handler for opening delimiters.
    */
    if (!function_exists('parse_render_lft')) {function parse_render_lft($parser, $name, $attrs) {
      global $sheaf;
      global $attributes;
      global $hooks;
      global $counter;
      global $tagPath;
      global $lastSubsectionWasWork;
      global $tocHTML;
      $pathPrefix = $tagPath;
      $tagPath .= '/'.$name;
      $pathLeaf = $name;

      // Update the hooks.
      array_push($hooks, (array_key_exists('hooks', $attrs)) ? $attrs['hooks'] : "");
      $attributes[] = $attrs;

      // If we are importing a file, render it.
      if ( $pathLeaf === 'include' ) {
        $imported = @file_get_contents($attrs['sheaf']);
        sheaf::do_xml_parse("parse_render_lft", "parse_render_val", "parse_render_rgt", $imported);
        return;
      }

      // Render the header for the entire HTML document.
      if ($tagPath === '/sheaf') {
        echo '<head>';
        echo "\n".'<meta charset="utf-8">';
        echo "\n".'<title>'.$attrs['title'].'</title>';
        echo "\n".'<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>';
        echo "\n".'<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/styles/vs.min.css">';
        echo "\n".'<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.1.0/highlight.min.js"></script>';
        echo "\n".'<script src="http://d3js.org/d3.v3.min.js"></script>';
        echo "\n".'<script type="text/javascript" src="'.$sheaf['path'].'protoql.js"></script>';
        echo "\n".'<link rel="stylesheet" href="'.$sheaf['path'].'sheaf.css">';
        echo "\n".'<script type="text/javascript" src="'.$sheaf['path'].'sheaf.js"></script>';
        echo "\n".'<script>hljs.initHighlightingOnLoad();</script>';
        echo "\n".'</head>';
        echo "\n".'<body>';
        echo "\n".'<div class="sheaf" id="sheaf">';

        echo $sheaf['message'];
        echo $tocHTML;
      }

      // Top-level structural components (sections, subsections, appendices, and special sections).      
      if ($tagPath === '/sheaf/section' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : $counter['section'];
        echo "\n".'<a name="'.$id.'"></a>'."\n".'<div class="section"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link($id).'<span class="header_numeral">'.$counter['section'].'.</span> '.$attrs['title'].'</h2>';
        $lastSubsectionWasWork = false;
      }
      if ($tagPath === '/sheaf/review') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ('R.'.$counter['review']);
        echo '<a name="'.$id.'"></a><div class="review"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link($id).'<span class="header_numeral">Review #'.$counter['review'].'.</span> '.$attrs['title'].'</h2>';
        $lastSubsectionWasWork = false;
      }
      if ($pathLeaf === 'midterm') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ('M.'.$counter['midterm']);
        echo '<a name="'.$id.'"></a><div class="work midterm"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link($id).'<span class="header_numeral">Midterm.</span> '.$attrs['title'].'</h2>';
        $lastSubsectionWasWork = false;
      }
      if ($pathLeaf === 'final') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : 'F';
        echo '<a name="'.$id.'"></a><div class="work final"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link($id).'<span class="header_numeral">Final.</span> '.$attrs['title'].'</h2>';
        $lastSubsectionWasWork = false;
      }
      if ($tagPath === '/sheaf/appendix') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : $counter['appendix'];
        echo '<a name="'.$id.'"></a><div class="appendix"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link($id).'<span class="header_numeral">Appendix '.$counter['appendix'].'.</span> '.$attrs['title'].'</h2>';
        $lastSubsectionWasWork = false;
      }
      if ($tagPath === '/sheaf/references') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : $counter['appendix'];
        echo '<a name="bib"></a><div class="references"><hr '.($lastSubsectionWasWork?'class="last_subsection_was_work"':'').'/>';
        echo '<h2 class="linked">'.sheaf::link("bib").'<span class="header_numeral">References</span></h2><table>';
        $lastSubsectionWasWork = false;
      }
      if ($tagPath === '/sheaf/references/reference') {
        echo '<tr><td class="cite"><a name="'.$attrs['id'].'"></a>['.$counter['reference'].']</td><td>';
        if (array_key_exists('author', $attrs)) echo ' '.$attrs['author'].'.';
        if (array_key_exists('title', $attrs)) echo ' "<b>'.$attrs['title'].'</b>".';
        if (array_key_exists('book', $attrs)) {
          echo ' <i>'.$attrs['book'].'</i>';
          if (array_key_exists('publication', $attrs)) echo ' '.$attrs['publication'];
          echo '.';
        }
        if (array_key_exists('url', $attrs)) echo ' <a href="'.$attrs['url'].'">'.$attrs['url'].'</a>';
        echo '</td></tr>';
        $counter['reference']++;
      }
      if ($tagPath === '/sheaf/section/subsection' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        echo "\n  ".'<a name="'.$id.'"></a><div class="subsection">';
        echo '<h3 class="linked">'.sheaf::link($id).'<span class="header_numeral">'.$counter['section'].'.'.$counter['subsection'].'.</span> '. $attrs['title'].'</h3>';
      }
      if ($tagPath === '/sheaf/appendix/subsection' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['appendix'].'.'.$counter['subsection']);
        echo "\n  ".'<a name="'.$id.'"></a><div class="subsection">';
        echo '<h3 class="linked">'.sheaf::link($id).'<span class="header_numeral">'.$counter['appendix'].'.'.$counter['subsection'].'.</span> '. $attrs['title'].'</h3>';
      }
      if ($pathLeaf === 'assignment') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        echo '<br/><hr class="work_separator"/>'
           . '<a name="'.$id.'"></a>'
           . '<a name="assignment'.sheaf::strval0($counter['assignment']).'"></a>'
           . '<a name="hw'.sheaf::strval0($counter['assignment']).'"></a>'
           . '<div class="work assignment">';
        echo '<h3 class="work_header linked">'.sheaf::link($id).'<span class="header_numeral">'
           . $counter['section'].'.'.$counter['subsection'].'.</span> '
           . '<span class="work_title">Assignment #'.sheaf::strval0($counter['assignment']).': '.$attrs['title'].'</span></h3>';
      }
      if ($pathLeaf === 'project') {
        $id = (array_key_exists('id', $attrs)) ? $attrs['id'] : ($counter['section'].'.'.$counter['subsection']);
        echo '<br/><hr class="work_separator"/>'
           . '<a name="'.$id.'"></a>'
           . '<a name="project'.sheaf::strval0($counter['project']).'"></a>'
           . '<a name="hw'.sheaf::strval0($counter['project']).'"></a>'
           . '<div class="work project">';
        echo '<h3 class="work_header linked">'.sheaf::link($id).'<span class="header_numeral">'
           . $counter['section'].'.'.$counter['subsection'].'.</span> '
           . '<span class="work_title">Project #'.sheaf::strval0($counter['project']).': '.$attrs['title'].'</span></h3>';
      }

      // Categorized blocks that appear at top level.
      if ( $pathPrefix === '/sheaf/section'
        || $pathPrefix === '/sheaf/review'
        || $pathPrefix === '/sheaf/appendix'
        || $pathPrefix === '/sheaf/section/subsection'
        || $pathPrefix === '/sheaf/appendix/subsection'
         ) {
        foreach (sheaf::$blocks as $kind => $bs) {
          foreach ($bs as $tag => $name) {
            if ($pathLeaf === $tag) {
              $id = array_key_exists('id', $attrs) ? $attrs['id'] : '';
              $classes = $tag.' '.$kind.((array_key_exists('required', $attrs) && $attrs['required'] === 'true') ? ('_required') : '');
              echo "\n".'<a name="'.$id.'"></a>'
                 . '<div class="linked block">'
                 . '<div class="link-block">[<a href="'.'#'.$id.'">link</a>]&nbsp;&nbsp;</div>'
                 . '<div style="width:100%; display:inline-block;">'
                 . '<div style="width:auto;" class="'.$classes.'">';
              if (strlen($name) > 0) // Only show label if there is a label.
                echo '<span class="block_label">'.$name.((array_key_exists('title', $attrs)) ? (' ('.$attrs['title'].')') : '').':</span> ';
            }
          } // For each categorized block.
        } // For each kind of categorized block.

        if ($pathLeaf === "paragraph") echo '<div class="paragraph">' . ((array_key_exists('title', $attrs)) ? ('<b>'.$attrs['title'].'.</b> ') : '');
        if ($pathLeaf === "orderedlist") echo '<ol'.((array_key_exists('style', $attrs)) ? (' style="'.$attrs['style'].'"') : '').'>';
        if ($pathLeaf === "unorderedlist") echo '<ul class="top">';
        if ($pathLeaf === "text") echo "\n".'<span class="text top">';
        if ($pathLeaf === "content") echo "\n".'<div class="top">';
        if ($pathLeaf === "code") echo "\n".'<div class="code top"><div class="source">';
        if ($pathLeaf === "plugin") echo "\n".'<div class="top">';

      } else { // Handlers for blocks that do not appear at top level.

        // Assignment/project/exam instructions, problems, and problem parts.
        if ($pathLeaf === "instructions") echo '<div class="instructions">';
        if ($pathLeaf === 'problems') echo '<ol class="problems">';
        if ($pathLeaf === 'problem') echo '<li class="problem">';
        if ($pathLeaf === 'parts') echo '<ol class="parts">';
        if ($pathLeaf === 'part') echo '<li class="part">';

        // Solutions (in examples, exercises, and problems).
        if ($pathLeaf === "solution") echo "\n".'<div class="button"><button class="solution_toggle">show solution</button></div><div class="solution_container" style="display:none;"><div class="solution">';

        // Paragraphs and lists.
        if ($pathLeaf === "paragraph") echo '<div class="paragraph">' . ((array_key_exists('title', $attrs)) ? ('<b>'.$attrs['title'].'.</b> ') : '');
        if ($pathLeaf === "orderedlist") echo '<ol'.((array_key_exists('style', $attrs)) ? (' style="'.$attrs['style'].'"') : '').'>';
        if ($pathLeaf === "unorderedlist") echo '<ul>';
        if ($pathLeaf === "item") echo '<li>' . ((array_key_exists('title', $attrs)) ? ('<b>'.$attrs['title'].': </b>') : '');

        // Source code, text, content, and plugin blocks.
        if ($pathLeaf === "text") echo "\n".'<span class="text">';
        if ($pathLeaf === "content") echo "\n".'<div>';
        if ($pathLeaf === "code") echo "\n".'<div class="code"><div class="source">';
        if ($pathLeaf === "table") echo "\n".'<div>';
        if ($pathLeaf === "diagram") echo "\n".'<div class="diagram">';
        if ($pathLeaf === "plugin") echo "\n".'<div>';

        // Inference rule tables, inference rules, and inference rule components.
        if ($pathLeaf === "inferences") echo '<div class="inferences">';
        if ($pathLeaf === "inferencesTable") echo '<table style="font-size:14px;"><tr>';
        if ($pathLeaf === "inferencesTableCol") echo '<td>';
        if ($pathLeaf === "inference") echo '<table class="inference"><tr>' . ((array_key_exists('title', $attrs)) ? ('<td class="title">['.$attrs['title'].']</td>') : '') . '<td><table>';
        if ($pathLeaf === "premises") echo '<tr><td class="premises">&nbsp;';
        if ($pathLeaf === "conclusion") echo '<tr><td class="conclusion">&nbsp;';

      } // Blocks that are not at top level.
    }} // Add XML parsing handler for opening delimiters.

    /**************************************************************************
    ** Add XML parsing handler for delimited content.
    */
    if (!function_exists('parse_render_val')) { function parse_render_val($parser, $data) {
      global $hooks;
      global $counter;
      global $tagPath;
      $pathLeaf = sheaf::pathLeaf($tagPath);

      // Render the XML delimited content as HTML.
      if ( $pathLeaf === 'definition'
        || $pathLeaf === 'fact'
        || $pathLeaf === 'proposition'
        || $pathLeaf === 'lemma'
        || $pathLeaf === 'theorem'
        || $pathLeaf === 'conjecture'
        || $pathLeaf === 'algorithm'
        || $pathLeaf === 'protocol'
        || $pathLeaf === 'example'
        || $pathLeaf === 'exercise'
        || $pathLeaf === 'instructions'
        || $pathLeaf === 'solution'
        || $pathLeaf === 'paragraph'
        || $pathLeaf === 'item'
        || $pathLeaf === 'text'
        || $pathLeaf === 'content'
        || $pathLeaf === 'code'
        || $pathLeaf === 'table'
        || $pathLeaf === 'diagram'
        || $pathLeaf === 'plugin'
        || $pathLeaf === 'premises'
        || $pathLeaf === 'conclusion'
         ) {
        // Apply the hooks.
        $out = $data;
        $applied = array();
        foreach ($hooks as $hooklist) {
          if (strlen($hooklist) > 0) {
            foreach (explode(',', $hooklist) as $hook) {
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
    }} // Add XML parsing handler for delimited content.

    /**************************************************************************
    ** Add XML parsing handler for terminating delimiters.
    */
    if (!function_exists('parse_render_rgt')) {function parse_render_rgt($parser, $name) {
      global $attributes;
      global $hooks;
      global $counter;
      global $tagPath;
      global $lastSubsectionWasWork;
      $pathPrefix = sheaf::pathPrefix($tagPath);
      $pathLeaf = $name;

      $attrs = array_pop($attributes);

      // Update the hooks.
      array_pop($hooks);

      // Render the XML as HTML.
      if ($tagPath === '/sheaf') {
        echo "\n".'</div><div class="footer"><div class="sheaflink">represented and rendered using <a href="http://sheaf.io">sheaf</a></div></div></body>';
      }
      if ($tagPath === '/sheaf/section' && (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false')) {
        echo "\n".'</div>';
        $counter['section']++;
        $counter['subsection'] = 1;
      }
      if ($tagPath === '/sheaf/review') { $counter['review']++; echo "\n".'</div>'; }
      if ($pathLeaf === 'midterm') { $counter['midterm']++; echo "\n".'</div>'; }
      if ($pathLeaf === 'final') { echo "\n".'</div>'; }
      if ($tagPath === '/sheaf/appendix') { $counter['appendix']++; $counter['subsection'] = 1; echo '</div>'; }
      if ($tagPath === '/sheaf/references') { echo '</table></div>'; }
      if ($tagPath === '/sheaf/references/reference') { }
      if ($tagPath === '/sheaf/section/subsection' || $tagPath === '/sheaf/appendix/subsection') {
        if (!array_key_exists('visible', $attrs) || $attrs['visible'] !== 'false') {
          echo '</div>';
          $counter['subsection']++;
          $lastSubsectionWasWork = false;
        }
      }
      if ($pathLeaf === 'assignment') { $counter['subsection']++; $counter['assignment']++; echo "\n".'</div><hr class="work_separator"/><br/>'; $lastSubsectionWasWork = true; }
      if ($pathLeaf === 'project') { $counter['subsection']++; $counter['project']++; echo "\n".'</div><hr class="work_separator"/><br/>'; $lastSubsectionWasWork = true; }

      // Categorized blocks that appear at top level.
      if ( $pathPrefix === '/sheaf/section'
        || $pathPrefix === '/sheaf/review'
        || $pathPrefix === '/sheaf/appendix'
        || $pathPrefix === '/sheaf/section/subsection'
        || $pathPrefix === '/sheaf/appendix/subsection'
         ) {

        if ( $pathLeaf === 'definition'
          || $pathLeaf === 'fact'
          || $pathLeaf === 'proposition'
          || $pathLeaf === 'lemma'
          || $pathLeaf === 'theorem'
          || $pathLeaf === 'conjecture'
          || $pathLeaf === 'algorithm'
          || $pathLeaf === 'protocol'
          || $pathLeaf === 'example'
          || $pathLeaf === 'exercise'
          || $pathLeaf === 'table'
          || $pathLeaf === 'diagram'
          )
          echo '</div></div></div>';

        if ($pathLeaf === 'paragraph') echo '</div>';
        if ($pathLeaf === "orderedlist") echo '</ol>';
        if ($pathLeaf === "unorderedlist") echo '</ul>';
        if ($pathLeaf === "text") echo '</span>';
        if ($pathLeaf === "content") echo '</div>';
        if ($pathLeaf === "code") echo '</div></div>';
        if ($pathLeaf === "plugin") echo '</div>';

      } else { // Handlers for blocks that do not appear at top level.

        if ($pathLeaf === 'instructions') echo '</div>';
        if ($pathLeaf === 'problems')  echo '</ol>';
        if ($pathLeaf === 'problem') echo '</li>';
        if ($pathLeaf === 'parts') echo '</ol>';
        if ($pathLeaf === 'part') echo '</li>';

        if ($pathLeaf === "solution") echo '</div></div><div class="solution_spacer"></div>';

        if ($pathLeaf === 'paragraph') echo '</div>';
        if ($pathLeaf === "orderedlist") echo '</ol>';
        if ($pathLeaf === "unorderedlist") echo '</ul>';
        if ($pathLeaf === "item") echo '</li>';

        if ($pathLeaf === "text") echo '</span>';
        if ($pathLeaf === "content") echo '</div>';
        if ($pathLeaf === "code") echo '</div></div>';
        if ($pathLeaf === "table") echo '</div>';
        if ($pathLeaf === "diagram") echo '</div>';
        if ($pathLeaf === "plugin") echo '</div>';

        if ($pathLeaf === "inferences") echo '</div>';
        if ($pathLeaf === "inferencesTable") echo '</tr></table>';
        if ($pathLeaf === "inferencesTableCol") echo '</td>';
        if ($pathLeaf === "inference") echo '</table></td></tr></table>';
        if ($pathLeaf === "premises") echo '&nbsp;</td></tr>';
        if ($pathLeaf === "conclusion") echo '&nbsp;</td></tr>';

      } // Blocks that are not at top level.

      $tagPath = substr($tagPath, 0, strlen($tagPath) - strlen($name) - 1);
    }} // Add XML parsing handler for terminating delimiters.

    sheaf::do_xml_parse("parse_render_lft", "parse_render_val", "parse_render_rgt", $xml);
    return null;
  }

    /****************************************************************************
  ** Functions for defining and invoking XML parsers.
  */

  private static function mk_xml_parser($startF, $datF, $endF) {
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
    xml_set_element_handler($xml_parser, $startF, $endF);
    xml_set_character_data_handler($xml_parser, $datF);
    return $xml_parser;
  }

  public static function do_xml_parse($startF, $datF, $endF, $xml) {
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

  /****************************************************************************
  ** Other utility functions.
  */

  public static function strval0($i) {
    return $i == 0 ? "0" : $i;
  }

  public static function link($target) {
    return '<span class="link-title">[<a href="#'.$target.'">link</a>]&nbsp;&nbsp;</span>';
  }

  public static function pathLeaf($path) {
    $a = explode("/", $path);
    return (count($a) < 1) ? null : $a[count($a)-1];
  }

  public static function pathPrefix($path) {
    $a = explode("/", $path);
    array_pop($a);
    return implode("/", $a);
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