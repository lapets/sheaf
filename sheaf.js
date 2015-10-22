/********************************************************************
**
** sheaf.js
**
**   A library that supports the representation and automated
**   rendering of lecture notes for mathematics and computer
**   science courses.
**
**   Web:     sheaf.io
**   Version: 0.0.1.0
**
**   JavaScript functions used for rendering of sheaf course
**   material XML files as HTML webpages.
*/

/********************************************************************
*/
$(document).ready(function() {
  $('.solution_text').hide();
  $('.solution_toggle').click(function(e) {
    e.preventDefault();
    var sol = $(this).prev();
    $(this).text((sol.is(':visible')) ? 'Click to Show Solution' : 'Click to Hide Solution');
    sol.slideToggle();
  });
})

/*eof*/
