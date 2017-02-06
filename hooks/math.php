<?php /**************************************************************
**
** sheaf/hooks/math.php
** 
**   Hook for rendering simple, LaTeX-like markdown notation for
**   mathematics.
**
*/

/********************************************************************
*/

function sheaf_hook_math ($s) {

  $s=sheaf::r($s, '#[', '</td><td><table cellpadding="0" cellspacing="0" style="display:inline;"><tr><td class="html_frac_lft">&nbsp;</td><td><table cellpadding="0" cellspacing="0" style="font-size:12px;"><tr><td style="white-space:nowrap;">');
  $s=sheaf::r($s, '#;', '</td></tr><tr><td style="white-space:nowrap;">');
  $s=sheaf::r($s, '#,', '</td><td style="padding-left:8px; white-space:nowrap;">');
  $s=sheaf::r($s, '#]', '</td></tr></table></td><td class="html_frac_rgt">&nbsp;</td></tr></table></td><td>');

  $s=sheaf::r($s, '@(', '</td><td><table cellpadding="0" cellspacing="0" style="display:inline;"><tr><td class="html_frac_lft">&nbsp;</td><td><table cellpadding="0" cellspacing="0" style="font-size:12px;"><tr><td style="white-space:nowrap;">');
  $s=sheaf::r($s, '@;', '</td></tr><tr><td style="border-top:1px solid #000000; white-space:nowrap;">');
  $s=sheaf::r($s, '@)', '</td></tr></table></td><td class="html_frac_rgt">&nbsp;</td></tr></table></td><td>');

  $s=sheaf::r($s, '#(', '<span style="font-size:24px;">(</span></td><td><table cellpadding="0" cellspacing="0" style="display:inline;"><tr><td class="html_frac_lft">&nbsp;</td><td><table cellpadding="0" cellspacing="0" style="font-size:12px;"><tr><td style="white-space:nowrap;">');
  $s=sheaf::r($s, '#;', '</td></tr><tr><td style="white-space:nowrap;">');
  $s=sheaf::r($s, '#)', '</td></tr></table></td><td class="html_frac_rgt">&nbsp;</td></tr></table></td><td><span style="font-size:24px;">)</span>');

  $s=sheaf::r($s, '%{^', '<span style="text-decoration:overline;">');
  $s=sheaf::r($s, '}%', '</span>');

  $s=sheaf::r($s, '^{\\phi(%m)-1}', '<sup>\\phi(%m)-1</sup>');
  $s=sheaf::r($s, '^{\\phi(%m)}', '<sup>\\phi(%m)</sup>');
  $s=sheaf::r($s, '^{\\phi(%m) \cdot %k}', '<sup>\\phi(%m) \cdot %k</sup>');
  $s=sheaf::r($s, '^{\\varphi(%n)}', '<sup>\\varphi(%n)</sup>');
  $s=sheaf::r($s, '^{%k}', '<sup><i>k</i></sup>');
  $s=sheaf::r($s, '^{%d}', '<sup><i>d</i></sup>');
  $s=sheaf::r($s, '^{%i}', '<sup><i>i</i></sup>');
  $s=sheaf::r($s, '^{%a \cdot %b}', '<sup>%a \cdot %b</sup>');
  $s=sheaf::r($s, '^{%a}', '<sup><i>a</i></sup>');
  $s=sheaf::r($s, '^{%b}', '<sup><i>b</i></sup>');
  $s=sheaf::r($s, '^{%d}', '<sup><i>d</i></sup>');
  $s=sheaf::r($s, '^{%e}', '<sup><i>e</i></sup>');
  $s=sheaf::r($s, '^{%n}', '<sup><i>n</i></sup>');
  $s=sheaf::r($s, '^{%l}', '<sup><i>l</i></sup>');
  $s=sheaf::r($s, '^{%m}', '<sup><i>m</i></sup>');
  $s=sheaf::r($s, '^{%r}', '<sup><i>r</i></sup>');
  $s=sheaf::r($s, '^{%c}', '<sup><i>c</i></sup>');
  $s=sheaf::r($s, '^{%y}', '<sup><i>y</i></sup>');
  $s=sheaf::r($s, '^{1/2}', '<sup>1/2</sup>');
  $s=sheaf::r($s, '^{%n/2}', '<sup>%n/2</sup>');
  $s=sheaf::r($s, '^{%p-1}', '<sup>%p-1</sup>');
  $s=sheaf::r($s, '^{%p-2}', '<sup>%p-2</sup>');
  $s=sheaf::r($s, '^{%n-1}', '<sup>%n-1</sup>');
  $s=sheaf::r($s, '^{%m-1}', '<sup>%m-1</sup>');
  $s=sheaf::r($s, '^{%k-1}', '<sup>%k-1</sup>');
  $s=sheaf::r($s, '^{%d+1}', '<sup><i>d</i>+1</sup>');
  $s=sheaf::r($s, '^{%k+1}', '<sup><i>k</i>+1</sup>');
  $s=sheaf::r($s, '^{%z}', '<sup><i>z</i></sup>');
  $s=sheaf::r($s, '^{%z_2}', '<sup><i>z</i><sub>2</sub></sup>');
  $s=sheaf::r($s, '_0', '<sub>0</sub>');
  $s=sheaf::r($s, '_1', '<sub>1</sub>');
  $s=sheaf::r($s, '_2', '<sub>2</sub>');
  $s=sheaf::r($s, '_3', '<sub>3</sub>');
  $s=sheaf::r($s, '_4', '<sub>4</sub>');
  $s=sheaf::r($s, '_5', '<sub>5</sub>');
  $s=sheaf::r($s, '_6', '<sub>6</sub>');
  $s=sheaf::r($s, '_7', '<sub>7</sub>');
  $s=sheaf::r($s, '_8', '<sub>8</sub>');
  $s=sheaf::r($s, '_{10}', '<sub>10</sub>');
  $s=sheaf::r($s, '^0', '<sup>0</sup>');
  $s=sheaf::r($s, '^1', '<sup>1</sup>');
  $s=sheaf::r($s, '^2', '<sup>2</sup>');
  $s=sheaf::r($s, '^3', '<sup>3</sup>');
  $s=sheaf::r($s, '^4', '<sup>4</sup>');
  $s=sheaf::r($s, '^5', '<sup>5</sup>');
  $s=sheaf::r($s, '^6', '<sup>6</sup>');
  $s=sheaf::r($s, '^7', '<sup>7</sup>');
  $s=sheaf::r($s, '^8', '<sup>8</sup>');
  $s=sheaf::r($s, '^9', '<sup>9</sup>');
  $s=sheaf::r($s, '^{-1}', '<sup>-1</sup>');
  $s=sheaf::r($s, '^{11}', '<sup>11</sup>');
  $s=sheaf::r($s, '^{16}', '<sup>16</sup>');
  $s=sheaf::r($s, '^{8*32}', '<sup>8 \cdot 32</sup>');
  $s=sheaf::r($s, '^{256}', '<sup>256</sup>');
  $s=sheaf::r($s, '^{21}', '<sup>21</sup>');
  $s=sheaf::r($s, '_{32}', '<sub>32</sub>');
  $s=sheaf::r($s, '_{50}', '<sub>50</sub>');
  $s=sheaf::r($s, '_{51}', '<sub>51</sub>');
  $s=sheaf::r($s, '_{100}', '<sub>100</sub>');
  $s=sheaf::r($s, '_{%i}', '<sub><i>i</i></sub>');
  $s=sheaf::r($s, '_{%m}', '<sub><i>m</i></sub>');
  $s=sheaf::r($s, '_{%n}', '<sub><i>n</i></sub>');
  $s=sheaf::r($s, '_{%r}', '<sub><i>r</i></sub>');
  $s=sheaf::r($s, '_{%N}', '<sub><i>N</i></sub>');
  $s=sheaf::r($s, '_{%i-1}', '<sub><i>i</i>-1</sub>');
  $s=sheaf::r($s, '_{%n-1}', '<sub><i>n</i>-1</sub>');
  $s=sheaf::r($s, '_{%k-1}', '<sub><i>k</i>-1</sub>');
  $s=sheaf::r($s, '_{%k}', '<sub><i>k</i></sub>');
  $s=sheaf::r($s, '_{%g}', '<sub><i>g</i></sub>');
  $s=sheaf::r($s, '_{%j}', '<sub><i>j</i></sub>');
  $s=sheaf::r($s, '\\lfloor', '&lfloor;');
  $s=sheaf::r($s, '\\rfloor', '&rfloor;');
  $s=sheaf::r($s, '\\lceil', '&lceil;');
  $s=sheaf::r($s, '\\rceil', '&rceil;');
  $s=sheaf::r($s, '\\emptyset', '&empty;');
  $s=sheaf::r($s, '\\forall', '&forall;');
  $s=sheaf::r($s, '\\exists', '&exist;');
  $s=sheaf::r($s, '\\gcd', 'gcd');
  $s=sheaf::r($s, '\\max', 'max');
  $s=sheaf::r($s, '\\min', 'min');
  $s=sheaf::r($s, '\\dom', 'dom');
  $s=sheaf::r($s, '\\log', 'log');
  $s=sheaf::r($s, '\\ln', 'ln');
  $s=sheaf::r($s, '%-', '&#8722;');
  $s=sheaf::r($s, '\\sqrt', '&radic;');
  $s=sheaf::r($s, '\\phi', '&phi;');
  $s=sheaf::r($s, '\\varphi', '&phi;');
  $s=sheaf::r($s, '\\lambda', '&lambda;');
  $s=sheaf::r($s, '\\tau', '&tau;');
  $s=sheaf::r($s, '\\pm', '&plusmn;');
  $s=sheaf::r($s, '\\Gamma', '&Gamma;');
  $s=sheaf::r($s, '\\vdash', '&#8866;');
  $s=sheaf::r($s, '\\langle', '&lang;');
  $s=sheaf::r($s, '\\rangle', '&rang;');
  $s=sheaf::r($s, '\\0', '<b>0</b>');
  $s=sheaf::r($s, '\\1', '<b>1</b>');
  $s=sheaf::r($s, '\\top', '&#8868;');
  $s=sheaf::r($s, '\\bot', '&perp;');
  $s=sheaf::r($s, '%[', '<i style="text-decoration:underline;">');
  $s=sheaf::r($s, ']%', '</i>');

  $ops = array(
      '=' => '=',
      '&gt;' => '&gt;',
      '&lt;' => '&lt;',
      '\\gt' => '&gt;',
      '\\lt' => '&lt;',
      ':=' => ':=',
      '::=' => '::=',
      'iff' => 'iff',
      '\\models' => '&#8872;', //&#8871;
      '\\rightarrow' => '<span style="font-size:12px;">&#8594;</span>',
      '\\leftarrow' => '<span style="font-size:12px;">&#8592;</span>',
      '\\downarrow' => '<span style="font-size:12px;">&#8595;</span>',
      '\\uparrow' => '<span style="font-size:12px;">&#8593;</span>',
      '\\Leftrightarrow' => '<span style="font-size:16px;">&#8660;</span>',
      '\\Rightarrow' => '<span style="font-size:16px;">&rArr;</span>',
      '|' => '|',
      '\\nmid' => '&#8740;',
      '\\vdots' => '&#8942;',
      '\\leq' => '&le;',
      '\\geq' => '&ge;',
      '\\neq' => '&ne;',
      '\\not\\in' => '&notin;',
      '\\in' => '<span style="font-size:12px;">&#8712;</span>',
      '\\subset' => '&sub;',
      '\\cup' => '&cup;',
      '\\cap' => '&cap;',
      '\\times' => '&#215;',
      '\\Downarrow' => '&dArr;',
      '\\Sigma' => '&Sigma;',
      '\\sigma' => '&sigma;',
      '\\uplus' => '&#8846;',
      '\\oplus' => '&oplus;',
      '\\otimes' => '&otimes;',
      '\\mapsto' => '&#x21A6;',
      '\\neg' => '&not;',
      '\\wedge' => '&and;',
      '\\vee' => '&or;',
      '\\mod' => 'mod',
      '\\log' => 'log',
      '\\cdot' => '&sdot;',
      '\\not\\equiv' => '&#8802;',
      '\\equiv' => '&equiv;',
      '\\cong' => '&cong;',
      '\\approx' => '&approx;',
      '\\sim' => '&sim;',
      '\\varepsilon' => '&epsilon;',
      '\\circ' => '<span style="font-size:10px;">o</span>',
      '%~' => '&nbsp;&nbsp;&nbsp;&nbsp;'
    );

  foreach (str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') as $v)
    $s = str_replace('%'.$v, '<i>'.$v.'</i>', $s);
  foreach (str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') as $v)
    $s = str_replace('@'.$v, '<b>'.$v.'</b>', $s);

  $s = str_replace("\\begin{eqnarray}\n","\\begin{eqnarray}",$s);
  $s = str_replace('\\begin{eqnarray}', '<table style="padding-left:20px; margin:4px 0px 4px 0px;"><tr><td style="text-align:right; white-space:nowrap;"><table style="width:100%;"><tr><td style="text-align:right;">',$s);
  foreach ($ops as $rel => $relH)
    $s = str_replace('& '.$rel.' &', '<td></tr></table></td><td style="text-align:center;"> '.$relH.' </td><td><table style="white-space:nowrap;"><tr><td style="white-space:nowrap;">',$s);
  $s = str_replace('\\\\', '</td></tr></table></td></tr><tr><td style="text-align:right;"><table style="width:100%;"><tr><td style="text-align:right;">',$s);
  $s = str_replace('%%', '</td></tr></table></td></tr><tr><td style="text-align:right;"><table style="width:100%;"><tr><td style="text-align:right;">',$s);
  $s = str_replace('\\end{eqnarray}', '</td></tr></table></td></tr></table>',$s);

  foreach ($ops as $str => $html)
    $s = str_replace($str, $html, $s);

  $s=sheaf::r($s, '\\Z', '&#8484;');
  $s=sheaf::r($s, '\\N', '&#8469;');
  $s=sheaf::r($s, '\\R', '&#8477;');
  $s=sheaf::r($s, '\\U', '<b><i>U</i></b>');
  $s=sheaf::r($s, '\\D', '<b><i>D</i></b>');
  $s=sheaf::r($s, '\\powerset', '&weierp;');
  $s=sheaf::r($s, '\\Pr', 'Pr');

  return $s;
}

/*eof*/ ?>