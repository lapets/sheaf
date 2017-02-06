/* ****************************************************************************
** 
** protoql.js
**
** An embedded language for rapid assembly, querying, and interactive visual
** rendering of common, abstract mathematical structures.
**
**   Web:     protoql.org
**   Version: 0.0.3.0
**
*/

(function (protoql) {

  "use strict";

  /*****************************************************************************
  ** Notation library (individually instantiated by each visualization object).
  */

  protoql.Notation = (function(Visualization) {
  
    var V = Visualization, Notation = {};

    /***************************************************************************
    ** Static routines and methods.
    */

    Notation.decorators =
      (function(raw) {
        raw = raw.trim();
        if (raw.length > 0 && raw.charAt(0) == "!") {
          V.interaction = {'zoom':false, 'pan':false, 'drag':false, 'edit':false};
          return raw.substr(1);
        } else if (raw.length > 0 && raw.charAt(0) == "#") {
          V.interaction = {'zoom':true, 'pan':true, 'drag':true, 'edit':true};
          return raw.substr(1);
        } else
          return raw;
      });

    Notation.graph =
      (function(raw) {
        raw = Notation.decorators(raw);
        if (raw.length == 0 || raw.substr(0,6) != "graph(") return false;
        var member = (function(x,xs) { for (var i=0; i<xs.length; i++) if (xs[i]==x) return true; return false; });
        var set = (function(l) { var s = []; for (var i=0; i<l.length; i++) if (!member(l[i],s)) s.push(l[i]); return s; });
        raw = raw.trim()
          .replace(/{/g, "set<[").replace(/}/g, "]>")
          .replace(/\(/g, "[").replace(/\)/g, "]")
          .replace(/</g, "(").replace(/>/g, ")")
          .replace("graph[", "[");
        var args = eval(raw), nodes = null, links = null;
        if (args.length == 1) {
          links = args[0], nodes = [];
          for (var i = 0; i < links.length; i++)
            nodes = nodes.concat([links[i][0], links[i][1]]);
          nodes = set(nodes);
        } else if (args.length == 2) {
          nodes = set(args[0]), links = args[1];
        }
        var listOfListsOfNodes = [];
        var rows = Math.ceil(Math.sqrt(nodes.length)), cols = Math.ceil(Math.sqrt(nodes.length));
        var col = 0;
        for (var i = 0; i < nodes.length; i++) {
          if ((i % cols) == 0)
            listOfListsOfNodes.push([]);
          listOfListsOfNodes[listOfListsOfNodes.length-1].push(nodes[i]);
          for (var j = 0; j < links.length; j++) {
            if (links[j][0] == nodes[i]) links[j][0] = [listOfListsOfNodes.length-1, (i % cols)];
            if (links[j][1] == nodes[i]) links[j][1] = [listOfListsOfNodes.length-1, (i % cols)];
          }
        }
        return Notation.listOfListsOfNodesToData({nodes:listOfListsOfNodes, links:links, groups:[]}, false);
      });

    Notation.relation =
      (function(raw) {
        raw = Notation.decorators(raw);
        if (raw.length == 0 || raw.substr(0,9) != "relation(") return false;
        var member = (function(x,xs) { for (var i=0; i<xs.length; i++) if (xs[i]==x) return true; return false; });
        var set = (function(l) { var s = []; for (var i=0; i<l.length; i++) if (!member(l[i],s)) s.push(l[i]); return s; });
        raw = raw.trim()
          .replace(/{/g, "set<[").replace(/}/g, "]>")
          .replace(/\(/g, "[").replace(/\)/g, "]")
          .replace(/</g, "(").replace(/>/g, ")")
          .replace("relation[", "[");
        var args = eval(raw), dom = null, cod = null, links = null;
        if (args.length == 1) {
          links = args[0], dom = [];
          for (var i = 0; i < links.length; i++)
            dom = dom.concat([links[i][0], links[i][1]]);
          cod = dom = set(dom);
        } else if (args.length == 2) {
          dom = args[0], cod = args[0], links = args[1];
        } else if (args.length == 3) {
          dom = args[0], cod = args[1], links = args[2];
        }
        var listOfListsOfNodes = [];
        for (var i = 0; i < Math.max(dom.length, cod.length); i++) {
          var a = (i < dom.length) ? dom[i] : null;
          var b = (i < cod.length) ? cod[i] : null;  
          listOfListsOfNodes.push([a, null, b]);
          for (var j = 0; j < links.length; j++) {
            if (a != null && links[j][0] == a) links[j][0] = [i, 0];
            if (b != null && links[j][1] == b) links[j][1] = [i, 2];
          }
        }
        return Notation.listOfListsOfNodesToData({nodes:listOfListsOfNodes, links:links, groups:[]}, false);
      });

    Notation.table =
      (function(raw) {
        raw = Notation.decorators(raw);
        if (raw.length == 0 || raw.substr(0,6) != "table(")
          return false;
        raw = raw.trim().replace("table(", "(");
        var listOfListsOfNodes = eval(raw);
        var check = true;
        for (var r = 0; r < listOfListsOfNodes.length; r++)
          for (var c = 0; c < listOfListsOfNodes[r].length; c++)
            check = check && (
                   (listOfListsOfNodes[r][c] == null) || (typeof listOfListsOfNodes[r][c] === "string")
                || (listOfListsOfNodes[r][c].constructor === Array && listOfListsOfNodes[r][c].length == 1)
              );
        if (!check)
          return false;

        return Notation.listOfListsOfNodesToData({nodes:listOfListsOfNodes, links:[], groups:[]}, true);
      });

    Notation.listOfListsOfNodesToData =
      (function(d, deriveLinks) {
        var rows = d.nodes.length;
        var cols = 0; for (var i = 0; i < d.nodes.length; i++) cols = Math.max(cols, d.nodes[i].length);

        V.layout.grid.width = Math.floor(V.dimensions.width/cols);
        V.layout.grid.height = Math.floor(V.dimensions.height/rows);
        //var x = d3.scale.ordinal().domain(d3.range(cols)).rangePoints([0, V.dimensions.width], 1);
        //var y = d3.scale.ordinal().domain(d3.range(rows)).rangePoints([0, V.dimensions.height], 1);
        var c = d3.scale.category20().domain(d3.range(7));
        V.data.nodes = [];
        for (var row = 0; row < d.nodes.length; row++) {
          for (var col = 0; col < d.nodes[row].length; col++) {
            if (d.nodes[row][col] != null) {
              var t = (typeof d.nodes[row][col] === "string") ? d.nodes[row][col] : d.nodes[row][col][0];
              var es_txt = t.split("``"), t = es_txt[es_txt.length - 1], es = (es_txt.length == 2) ? es_txt[0].split("`") : [];
              var shape = (typeof d.nodes[row][col] === "string") ? "circle" : "rect";
              var node = {
                  color: c(Math.floor(Math.random()*7)), shape: shape, text: t, 
                  rx: 5, ry: 5, offx: 0, offy: 0, x: V.layout.grid.width*col, y: V.layout.grid.height*row
                };
              d.nodes[row][col] = node;
              V.data.nodes.push(node);
              if (deriveLinks) {
                for (var i = 0; i < es.length; i++) {
                  var e = es[i].split(":")[0],
                      lbl = (es[i].split(":").length > 0) ? es[i].split(":")[1] : null;
                  var tr = row, tc = col;
                  for (var j = 0; j < e.length; j++) {
                    if (e[j] == "u") tr -= 1;
                    else if (e[j] == "d") tr += 1;
                    else if (e[j] == "l") tc -= 1;
                    else if (e[j] == "r") tc += 1;
                    else if (e[j] == "s") /* Self-link; nothing. */;
                  }
                  d.links.push([[row,col], [tr,tc]].concat((lbl != null) ? [lbl] : []));
                }
              }
            }
          }
        }
        V.data.links = [];
        for (var i = 0; i < d.links.length; i++) {
          var s = d.links[i][0], t = d.links[i][1],
              e = {source:d.nodes[s[0]][s[1]], target:d.nodes[t[0]][t[1]], curved:false};
          if (d.links[i].length == 3)
            e.label = d.links[i][2];
          V.data.links.push(e);
        }

        return true;
      });

    /***************************************************************************
    ** Initialization.
    */

    return Notation;

  }); // /protoql.Notation

  /*****************************************************************************
  ** Geometry library (individually instantiated by each visualization object).
  */

  protoql.Geometry = (function(Visualization) {

    var V = Visualization, Geometry = {};

    /***************************************************************************
    ** Static routines for vector arithmetic and shape intersections.
    */

    Geometry.sum = (function(v, w) { return {x: v.x + w.x, y: v.y + w.y}; });
    Geometry.diff = (function(v, w) { return {x: v.x - w.x, y: v.y - w.y}; });
    Geometry.length = (function(v) { return Math.sqrt(Math.pow(v.x,2) + Math.pow(v.y,2)); });
    Geometry.scale = (function(s, v) { return {x: s*v.x, y: s*v.y}; });
    Geometry.middle = (function(v, w) { return Geometry.sum(v,Geometry.scale(0.5,Geometry.diff(w,v))); });
    Geometry.rotate = (function(v) { return {x:0-v.y, y:v.x}; });
    Geometry.normalize =
      (function(v) {
        var d = Math.sqrt(Math.pow(v.x,2) + Math.pow(v.y,2));
        return {x: v.x/d, y: v.y/d};
      });
    Geometry.orth =
      (function(s, t) {
        return Geometry.rotate(Geometry.normalize(Geometry.diff(t, s)));
      });
    Geometry.offpath =
      (function(s, t, a) {
        var r = Geometry.sum(Geometry.middle(s, t), Geometry.scale(a, Geometry.orth(s, t)));
        if(isNaN(r.x) || isNaN(r.y))
          return Geometry.middle(s, t);
        return r;
      });

    Geometry.intersects =
      (function(c, d, a, b) {
        var x1 = c.x, x2 = d.x, x3 = a.x, x4 = b.x, y1 = c.y, y2 = d.y, y3 = a.y, y4 = b.y,
            x13 = x1 - x3, x21 = x2 - x1, x43 = x4 - x3, y13 = y1 - y3, y21 = y2 - y1, y43 = y4 - y3,
            ua = (x43 * y13 - y43 * x13) / (y43 * x21 - x43 * y21);
        return {x:x1 + ua * x21, y:y1 + ua * y21};
      });

    Geometry.maxRadiusToOutside =
      (function(s, t) {
        var r1 = null, r2 = null;
        if (s.shape == 'rect') r1 = Math.max(s.height, s.width)/1.7;
        else if (s.shape == "circle") r1 = s.r;
        if (t.shape == 'rect') r2 = Math.max(t.height, t.width)/1.7;
        else if (t.shape == "circle") r2 = t.r;
        return Math.max(r1, r2);
      });

    Geometry.inShape =
      (function(p, s) {
        if (s.shape == 'rect')
          return p.x > s.x - (s.width/2) && p.x < s.x +(s.width/2) && p.y > s.y - (s.height/2) && p.y < s.y + (s.height/2);
        else if (s.shape == "circle")
          return Math.sqrt(Math.pow(s.x - p.x, 2) + Math.pow(s.y - p.y, 2)) < s.r;
      });

    Geometry.inLine =
      (function(a,c,d) {
        return a.x >= Math.min(c.x,d.x) && a.x <= Math.max(c.x,d.x) && a.y >= Math.min(c.y,d.y) && a.y <= Math.max(c.y,d.y);
      });

    Geometry.onEdgeCirc =
      (function(e, which, offset) {
        offset = (offset == null) ? {x:0, y:0} : offset; // So symmetric edges intersect in different locations.
        var c = e[which];
        var dx = (which == "source" ? -1 : 1)*(e.source.x - e.target.x), 
            dy = (which == "source" ? -1 : 1)*(e.target.y - e.source.y);
        var d = Math.sqrt(Math.pow(dx, 2) + Math.pow(dy, 2));
        var x = c.x + ((c.r*dx)/d), y = c.y - ((c.r*dy)/d);
        return {x:isNaN(x) ? c.x : x, y:isNaN(y) ? c.y : y};
      });

    Geometry.onEdgeRect =
      (function(e, which, offset) {
        offset = (offset == null) ? {x:0, y:0} : offset; // So symmetric edges intersect in different locations.
        var r = Geometry.centeredPaddedRect(e[which]);
        var a = Geometry.sum(e.source, offset), b = Geometry.sum(e.target, offset);
        var lines = [
            [r, {x:r.x+r.width,y:r.y}], [{x:r.x,y:r.y+r.height}, {x:r.x+r.width,y:r.y+r.height}],
            [r, {x:r.x,y:r.y+r.height}], [{x:r.x+r.width,y:r.y}, {x:r.x+r.width,y:r.y+r.height}]
          ];
        var inters = lines.map(function(x) { return Geometry.intersects(x[0],x[1],a,b); });
        if (Geometry.inLine(inters[0],a,b) && inters[0].x >= r.x && inters[0].x <= r.x + r.width)
          return inters[0];
        else if (Geometry.inLine(inters[1],a,b) && inters[1].x >= r.x && inters[1].x <= r.x + r.width)
          return inters[1];
        else if (Geometry.inLine(inters[2],a,b) && inters[2].y >= r.y && inters[2].y <= r.y + r.height)
          return inters[2];
        else if (Geometry.inLine(inters[3],a,b) && inters[3].y >= r.y && inters[3].y <= r.y + r.height)
          return inters[3];
        else
          return e[which];
      });

    Geometry.onEdge =
      (function(e, which, offset) {
        if (e[which].shape == "rect")
          return Geometry.onEdgeRect(e, which, offset);
        if (e[which].shape == "circle")
          return Geometry.onEdgeCirc(e, which, offset);
      });

    Geometry.centeredPaddedRect =
      (function(node) {
        var rect = {};
        rect.x = node.x - (node.width + V.dimensions.padding) / 2;
        rect.y = node.y - (node.height + V.dimensions.padding) / 2;
        rect.width = node.width + V.dimensions.padding;
        rect.height = node.height + V.dimensions.padding;
        return rect;
      });

    /***************************************************************************
    ** Initialization.
    */

    return Geometry;

  }); // /protoql.Geometry

  /*****************************************************************************
  ** Interactive visualization objects.
  */

  protoql.Visualizations = (function(arg) {
    var vs = [];    
    if (arg.constructor === Array)
      for (var i = 0; i < arg.length; i++)
        vs.push(protoql.Visualization(arg[i]));
    else if (arg instanceof jQuery && typeof arg.each === "function")
      arg.each(function(index) {
        vs.push(protoql.Visualization($(this)));
      });
    return vs;
  });

  protoql.Visualization = (function(arg) {

    // Process constructor argument.    
    var id = null, obj = null, val = null;
    if (typeof arg === "string") {
      id = arg;
      obj = document.getElementById(id);
      val = (obj != null) ? obj.innerHTML : null;
    } else if (arg instanceof jQuery) {
      obj = arg;
      if (typeof obj.attr('id') !== "string") // Generate random identifier.
        obj.attr('id', "id"+Date.now()+""+Math.floor(Math.random()*10000));
      id = obj.attr('id');
      val = obj.html();
    }

    var Visualization = {}, V = Visualization;

    /***************************************************************************
    ** Data fields and properties.
    */

    Visualization.divId = id;
    Visualization.obj = obj;
    Visualization.val = val;
    Visualization.svg = null;
    Visualization.vis = null;
    Visualization.zoom = d3.behavior.zoom();
    Visualization.zoompan = {translate:{x:0, y:0}, scale:1};
    Visualization.built = false;
    Visualization.force = null;
    Visualization.data = {nodes: null, links: null, groups: null};
    Visualization.nodesEnter = null;
    Visualization.linksEnter = null;
    Visualization.dimensions = {width: null, height: null, padding: null};
    Visualization.layout = {grid: {width: null, height: null}};
    Visualization.interaction = {zoom:true, pan:true, drag:true, edit:false};
    var G = Visualization.geometry = protoql.Geometry(Visualization);
    var N = Visualization.notation = protoql.Notation(Visualization);

    /***************************************************************************
    ** Routines and methods.
    */
    
    var lineFunction = 
      d3.svg
        .line()
        .x(function (d) { return d.x; })
        .y(function (d) { return d.y; })
        .interpolate("basis");

    // Resolve collisions between nodes.
    var collision =
      (function(alpha) {
        var padding = 6;
        var maxRadius = 7;
        var quadtree = d3.geom.quadtree(nodes);
        return function(d) {
          var r = d.r + maxRadius + padding,
              nx1 = d.x - r, nx2 = d.x + r,
              ny1 = d.y - r, ny2 = d.y + r;
          quadtree.visit(function(quad, x1, y1, x2, y2) {
            if (quad.point && (quad.point !== d)) {
              var x = d.x - quad.point.x, y = d.y - quad.point.y,
                  l = Math.sqrt(x * x + y * y),
                  r = d.r + quad.point.r + (d.color !== quad.point.color) * padding;
              if (l < r) {
                l = (l - r) / l * alpha;
                d.x -= x *= l;
                d.y -= y *= l;
                quad.point.x += x;
                quad.point.y += y;
              }
            }
            return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
          });
        };
      });

    var alignment =
      (function(alpha) {
        return function(d) {
          d.cx = Math.floor(d.x - (d.x % V.layout.grid.width)+(V.layout.grid.width*0.5));
          d.x += (d.cx - d.x) * alpha;
          d.cy = Math.floor(d.y - (d.y % V.layout.grid.height)+(V.layout.grid.height*0.5));
          d.y += (d.cy - d.y) * alpha;
        };
      });

    var tick =
      (function(e) {
        V.nodesEnter
          .each(alignment(0.8 * e.alpha))
          //.each(collision(.5))
          .attr("transform", function (d) { return "translate("+(d.x + d.offx)+","+(d.y + d.offy)+")"; });
        V.linksEnter
          .attr("d", function (e) {
            var s = G.onEdge(e, 'source'), t = G.onEdge(e, 'target'), 
                points = null, lbl = [e.source, e.target, 15];
            if (G.inShape(t, e.source) || G.inShape(s, e.target)) {
              var orient = (e.source == e.target) ? -1 : 1,
                  es = e.source, et = e.target, 
                  d = G.diff(e.source, e.target),
                  l = G.length(d),
                  m = Math.abs(G.maxRadiusToOutside(e.source, e.target)),
                  kx = (l < 2) ? m + 9 : d.x * Math.min(1.3*m, 1.5*(m/(l+1))), ky = (l < 2) ? m + 9 : d.y * Math.min(1.3*m, 1.5*(m/(l+1))),
                  p = {x:es.x + 1.3*ky, y:es.y - kx}, q = {x:et.x + (orient*(0.9)*ky), y:et.y - kx},
                  s = G.onEdge({source:es, target:p}, 'source'), t = G.onEdge({source:q, target:et}, 'target');
              points = [s, p, q, t];
              lbl = [p,q,10];
            } else if (e.curved == true) {
              var off = G.scale(5, G.orth(s, t)), s = G.onEdge(e, 'source', off), t = G.onEdge(e, 'target', off);
              points = [s, G.offpath(s, t, 5), t];
            } else {
              points = [s, t];
            }

            // Position the edge label.
            var t = V.zoompan.translate, s = V.zoompan.scale,
                o = G.sum(G.offpath(G.scale(s, lbl[0]), G.scale(s, lbl[1]), lbl[2]*s), t);
            e.labelText.attr("transform", function (d) { return "translate("+o.x+","+o.y+") scale(" + s + ")"; });

            return lineFunction(points);
          });
      });

    var textToSpans =
      (function(d) {
        var lines = d.text.split("`");
        var dy = '13';
        d.textElt.text('');
        for (var i = 0; i < lines.length; i++) {
          d.textElt.append('tspan').text(lines[i]).attr('x', 0).attr('dy', dy);
          dy = '15';
        }
      });

    var symmetricLinks =
      (function(links) {
        for (var i = 0; i < links.length; i++) {
          for (var j = 0; j < i; j++) {
            if ( links[i].source == links[j].target
              && links[j].source == links[i].target
              && links[i].source != links[i].target
              && links[j].source != links[j].target
               ) {
              links[i].curved = true;
              links[j].curved = true;
            }
          }
        }
      });

    Visualization.height =
      // Public method.
      (function() {
        return V.dimensions.height;
      });

    Visualization.build =
      // Public method.
      (function(value) {
        var raw = null, data = null;
        if (V.obj instanceof jQuery) {
          raw = (value != null) ? value : obj.html();
          obj.html("");
          if (V.dimensions.width == null || V.dimensions.height == null)
            V.dimensions = {width:V.obj.width(), height:V.obj.height(), padding:10};
        } else {
          raw = (value != null) ? value : obj.innerHTML;
          obj.innerHTML = "";
          if (V.dimensions.width == null || V.dimensions.height == null)
            V.dimensions = {width:V.obj.clientWidth, height:V.obj.clientHeight, padding:10};
        }

        if (V.dimensions.width == null || isNaN(V.dimensions.width) || V.dimensions.width == 0)
          V.dimensions.width = 600;
        if (V.dimensions.height == null || isNaN(V.dimensions.height) || V.dimensions.height == 0)
          V.dimensions.height = 300;

        if (V.notation.relation(raw)) {}
        else if (V.notation.graph(raw)) {}
        else if (V.notation.table(raw)) {}
        else {
          console.log("Representation not recognized.");
          return;
        }

        symmetricLinks(V.data.links); // Mark symmetric pairs of links.
        V.force = 
          d3.layout.force()
            .nodes(V.data.nodes)
            //.links(V.data.links) // Disabled to avoid inadvertent clustering after building.
            .size([V.dimensions.width, V.dimensions.height])
            .gravity(0).charge(0).linkStrength(0)
            .on("tick", tick)
            .start()
          ;
        if (V.svg != null)
          V.svg.remove();
        V.svg = 
          d3.select('#' + id)
            .append("svg")
              .attr("width", "100%").attr("height", "100%")
              .attr('viewBox', '0 0 ' + V.dimensions.width + ' ' + V.dimensions.height)
              .attr('preserveAspectRatio', 'xMidYMid meet')
            ;

        // If editing of the diagram notation is enabled.
        if (V.interaction.edit) {
          V.console =
            d3.select('#' + id)
              .append("textarea")
                .attr("rows","4").attr("style", "width:"+(V.dimensions.width-6)+"px; margin:0px;")
                .property("value", (raw.trim().charAt(0) == "#") ? raw.trim().substr(1) : raw.trim())
                .each(function() { V.txt = this; })
              ;
          document.getElementById(id).style.padding = '0px';
          document.getElementById(id).style.height = 'auto';
        }

        var canvas = V.svg.append('rect').attr({'width':'100%', 'height':'100%'}).attr('fill-opacity', '0');
        if (V.interaction.zoom)
          canvas.call(
              V.zoom
                .on("zoom", function() {
                  var tr = d3.event.translate, sc = d3.event.scale;
                  V.zoompan = {translate:{x:tr[0], y:tr[1]},scale:sc};
                  V.vis.attr("transform", "translate(" + tr + ")" + " scale(" + sc + ")");
                  V.force.resume();
                })
            )
          .on("dblclick.zoom", function() {
              if (V.txt != null)
                V.build(V.txt.value);
              var edgeFade = 20, panZoom = 200;
              V.linksEnter.each(function(e) { e.labelText.transition().duration(edgeFade).attr("opacity", 0); });
              setTimeout(function() { // Wait for edges to fade out.
                V.vis.transition().duration(panZoom).attr('transform', 'translate(0,0) scale(1)');
                V.zoom.translate([0,0]).scale(1);
                V.zoompan = {translate:{x:0, y:0},scale:1};
                setTimeout(function() {V.linksEnter.each(function(e) {e.labelText.transition().duration(edgeFade).attr("opacity", 1);})}, panZoom);
              }, edgeFade);
            })
          ;
        V.vis =
          V.svg
            .append('g')//.attr('transform', 'translate(250,250) scale(0.3)')
            ;
        V.svg
          .append('svg:defs')
          .append('svg:marker')
            .attr('id', 'end-arrow-' + V.divId) // Use separate namespaces for each diagram's arrow ends.
            .attr('viewBox', '0 -5 10 10').attr('refX', 8)
            .attr('markerWidth', 8).attr('markerHeight', 8).attr('orient', 'auto')
          .append('svg:path')
            .attr('d', 'M0,-5L10,0L0,5L2,0')/*.attr('stroke-width', '1px')*/.attr('fill', '#000000')
          ;
        V.linksEnter = 
          V.vis.selectAll(".link")
            .data(V.data.links).enter()
            .append("path").attr("class", "link")
              .style("stroke", "black")
              .style("fill", "none")
              //.style("marker-end", function(l) { return 'url(' + window.location.href + '#end-arrow-' + V.divId + ')'; })
              .style("marker-end", function(l) { return 'url(#end-arrow-' + V.divId + ')'; })
            .each(function(e) { // Add edge labels.
              e.labelText = 
                V.svg
                  .append("text")
                    .text(function () { return e.label; })
                    .attr("text-anchor", "middle")
                    .attr("font-size", "12px")
                    .style("cursor", "all-scroll")
                ;
            })
            ;
        V.nodesEnter = 
          V.vis.selectAll(".node")
            .data(V.data.nodes).enter()
            .append("g")
              .attr("x", function(d) { return d.cx; })
              .attr("y", function(d) { return d.cy; })
              .call((V.interaction.drag ? V.force.drag : function(){}))
            ;
        V.nodesEnter.filter(function (d) {return d.shape == "rect";})
          .append("rect")
            .attr("class", "node_rect")
            .attr("rx", function(d) { return d.rx; })
            .attr("ry", function(d) { return d.ry; })
            .attr("width", function(d) { return d.width; })
            .attr("height", function(d) { return d.height; })
            .style("fill", function(d) { return "#DADADA"; /*d.color*/; })
            .style("stroke", "#4F4F4F")
            .style("opacity", 0.8)
            .style("cursor", "all-scroll")
          ;
        V.nodesEnter.filter(function (d) {return d.shape == "circle";})
          .append("circle")
            .attr("class", "node_circle")
            .attr("r", function(d) { return d.r; })
            .style("stroke", "#4F4F4F")
            .style("fill", function(d) { return "#DADADA"; /*d.color*/; })
            .style("opacity", 0.8)
            .style("cursor", "all-scroll")
          ;
        V.nodesEnter
          .append("text")
            .text(function (d) { d.textElt = d3.select(this); return d.text; })
            .each(textToSpans)
            .attr("text-anchor", "middle")
            .style("cursor", "all-scroll")
            //.style("stroke", "white").style("fill", "white")
            .each(function(d) {
                var bbox = this.parentNode.getBBox();
                d.width = Math.max(19, bbox.width);
                d.height = Math.max(19, bbox.height);
              })
          ;
        V.vis.selectAll(".node_rect")
          .attr("x", function(d) { return -0.5 * (d.width + V.dimensions.padding); })
          .attr("y", function(d) { return -0.5 * V.dimensions.padding; })
          .attr("width", function(d) { return d.width + V.dimensions.padding; })
          .attr("height", function(d) { return d.height + V.dimensions.padding; })
          .each(function(d) { d.offy = ((-1) * (d.height/2)); })
        ;
        V.vis.selectAll(".node_circle")
          .attr("cy", function(d) { return 0.5*this.parentNode.getBBox().height; })
          .attr("r", function(d) { var bbox = this.parentNode.getBBox(); d.r = V.dimensions.padding-3+((Math.max(bbox.width, bbox.height))/2); return d.r; })
          .each(function(d) { d.offy = ((-1) * (d.height/2)); })
          ;
        V.built = true;
      });

    /***************************************************************************
    ** Initialization.
    */

    Visualization.build();

    return Visualization;

  }); // /protoql.Visualization

})(typeof exports !== 'undefined' ? exports : (this.protoql = {}));
/* eof */