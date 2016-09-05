/******************************************************************************
**
** sheaf.js
**
**   A library that supports the representation and automated rendering of
**   lecture notes for mathematics and computer science courses.
**
**   Web:     sheaf.io
**   Version: 0.0.1.0
**
**   JavaScript functions used for rendering of sheaf course
**   material XML files as HTML webpages.
*/

/******************************************************************************
*/

$(document).ready(function() {
  // Render diagrams.
  protoql.Visualizations($('.pql')); 
  
  // Adjust source code styling (in addition to highlight.js styling).
  $('code').each(function(){
    $(this).css('background-color','#F7F7F7');
    $(this).html(($(this).html().trim()));
  });

  // Hide the solutions.
  $('.solution_toggle').click(function(e) {
    var sol = $(this).parent().next();
    $(this).text((sol.is(':visible')) ? 'show solution' : 'hide solution');
    sol.slideToggle();
  });

  // Update links to references with indices.
  var nameToBibIndex = {};
  $('.cite').each(function() {
    var name = $(this)[0].children[0].name, index = $(this)[0].innerText.slice(1,-1);
    nameToBibIndex[name] = index;
  });
  $('a').each(function() {
    var name = $(this)[0].attributes[0].nodeValue.slice(1);
    if (nameToBibIndex[name] != null && $(this)[0].childNodes[0].data == '#')
      $(this)[0].childNodes[0].data = nameToBibIndex[name];
  });
})

/*eof*/