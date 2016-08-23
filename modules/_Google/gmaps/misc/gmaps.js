// $Id: gmaps.js,v 1.2 2008/12/13 19:47:56 xmarket Exp $

var GMaps = GMaps || {};
GMaps.CONTENTLOADER_ICON = {};
GMaps.ICONS = {};
GMaps.MAPS = {};
GMaps.MAPS_ARE_LOADED = false;
GMaps.collapsedMaps = {}

GMaps.loadIcons = function () {
  var conf = Drupal.settings.gmaps.contentLoaderIcon;
  if (conf.type == 'factory') {
    GMaps.CONTENTLOADER_ICON = GMaps.createFactoryIcon(conf);
  }
  else {
    GMaps.CONTENTLOADER_ICON = GMaps.createIcon(conf);
  }

  for (var i in Drupal.settings.gmaps.icons) {
    conf = Drupal.settings.gmaps.icons[i];
    if (conf.type == 'factory') {
      GMaps.ICONS[i] = GMaps.createFactoryIcon(conf);
    }
    else {
      GMaps.ICONS[i] = GMaps.createIcon(conf);
    }
  }
}

GMaps.createFactoryIcon = function(opts) {
  var width = opts.icon_width || 32;
  var height = opts.icon_height || 32;
   
  var icon = new GIcon(G_DEFAULT_ICON);
  icon.image = opts.image;
  icon.iconSize = new GSize(width, height);
  icon.shadowSize = new GSize(Math.floor(width*1.6), height);
  icon.iconAnchor = new GPoint(width/2, height);
  icon.infoWindowAnchor = new GPoint(width/2, Math.floor(height/12));
  icon.printImage = opts.printimage;
  icon.mozPrintImage = opts.mozprintimage;
  icon.transparent = opts.transparent;

  icon.imageMap = [
      width/2, height,
      (7/16)*width, (5/8)*height,
      (5/16)*width, (7/16)*height,
      (7/32)*width, (5/16)*height,
      (5/16)*width, (1/8)*height,
      (1/2)*width, 0,
      (11/16)*width, (1/8)*height,
      (25/32)*width, (5/16)*height,
      (11/16)*width, (7/16)*height,
      (9/16)*width, (5/8)*height
  ];
  for (var i = 0; i < icon.imageMap.length; i++) {
    icon.imageMap[i] = parseInt(icon.imageMap[i]);
  }

  return icon;
}

GMaps.createIcon = function(opts) {
  var icon = new GIcon(G_DEFAULT_ICON, opts.image);
  
  if (typeof(opts.icon_width) != 'undefined') {
    icon.iconSize = new GSize(opts.icon_width, opts.icon_height);
  }
  if (typeof(opts.iconanchor_x) != 'undefined') {
    icon.iconAnchor = new GPoint(opts.iconanchor_x, opts.iconanchor_y);
  }

  if (typeof(opts.shadow) != 'undefined' && opts.shadow != '') {
    icon.shadow = opts.shadow;
    icon.shadowSize = new GSize(opts.shadow_width, opts.shadow_height);
  }

  if (typeof(opts.iwanchor_x) != 'undefined') {
    icon.infoWindowAnchor = new GPoint(opts.iwanchor_x, opts.iwanchor_y);
  }

  if (typeof(opts.printimage) != 'undefined' && opts.printimage != '') {
    icon.printImage = opts.printimage;
  }
  if (typeof(opts.mozprintimage) != 'undefined' && opts.mozprintimage != '') {
    icon.mozPrintImage = opts.mozprintimage;
  }
  if (typeof(opts.printshadow) != 'undefined' && opts.printshadow != '') {
    icon.printShadow = opts.printshadow;
  }
  if (typeof(opts.transparent) != 'undefined' && opts.transparent != '') {
    icon.transparent = opts.transparent;
  }

  if (typeof(opts.imagemap) != 'undefined' && opts.imagemap != '') {
    icon.imageMap = opt.imagemap.split(',');
    for (var i = 0; i < icon.imageMap.length; i++) {
      icon.imageMap[i] = parseInt(icon.imageMap[i]);
    }
  }
  
  if (typeof(opts.maxheight) != 'undefined' && opts.maxheight != '') {
    icon.maxHeight = opts.maxheight;
  }
  
  if (typeof(opts.dcimage) != 'undefined' && opts.dcimage != '') {
    icon.dragCrossImage = opts.dcimage;
    icon.dragCrossSize = new GSize(opts.dc_width, opts.dc_height);
    icon.dragCrossAnchor = new GPoint(opts.dcanchor_x, opts.dcanchor_y);
  }

  return icon;
}

GMaps.loadMaps = function () {
    for (var i in Drupal.settings.gmaps.maps) {
      var conf = Drupal.settings.gmaps.maps[i].config;
      $('#' + i).css('height', conf.height + 'px');
      if (conf.width != 0) {
        $('#' + i + '-wrapper').css('width', conf.width + 'px');
      }
      var point = new GLatLng(conf.center.latitude, conf.center.longitude);
      var mapOpts = {};
      mapOpts.mapTypes = [];
      for (var m = 0; m < conf.allowed_tiles.length; m++) {
        var tile = conf.allowed_tiles[m];
        if (tile.toUpperCase() == tile) {
          mapOpts.mapTypes.push(eval(tile));
        } else {
          //TODO: implement custom
        }
        if (tile == conf.default_tile) {
          var defType = mapOpts.mapTypes[mapOpts.mapTypes.length - 1];
        }
      }
      //mapOpts.mapTypes = GMaps.getAllowedTiles(conf.allowed_tiles);
      //var defType = GMaps.getAllowedTiles([conf.default_tile]);
      
      if (conf.methods.googlebar) {
        mapOpts.googleBarOptions = {};
        mapOpts.googleBarOptions.showOnLoad = Number(conf.googlebar_options.showonload);
        mapOpts.googleBarOptions.linkTarget = conf.googlebar_options.linktarget;
        if (conf.googlebar_options.resultlist == 'element') {
          mapOpts.googleBarOptions.resultList = document.getElementById(i + '-result-list');
        } else {
          mapOpts.googleBarOptions.resultList = eval(conf.googlebar_options.resultlist);
        }
        mapOpts.googleBarOptions.suppressInitialResultSelection = Number(conf.googlebar_options.suppress_selection);
        mapOpts.googleBarOptions.suppressZoomToBounds = Number(conf.googlebar_options.suppress_zoom);
      }

      var map = new GMap2(document.getElementById(i), mapOpts);
      map.setCenter(point, Number(conf.default_zoom), defType);
      GMaps.MAPS[i] = {};
      GMaps.MAPS[i].map = map;
      
      var contentLoader = new GMarker(new GLatLng(0, 0), {icon: GMaps.CONTENTLOADER_ICON, clickable: false, draggable: false});
      map.addOverlay(contentLoader);
      contentLoader.hide();
      GMaps.MAPS[i].contentLoader = contentLoader;

      if (conf.methods.dragging) {
        map.enableDragging();
      } else {
        map.disableDragging();
      }
      if (conf.methods.infowindow) {
        map.enableInfoWindow();
      } else {
        map.disableInfoWindow();
      }
      if (conf.methods.doubleclickzoom) {
        map.enableDoubleClickZoom();
      } else {
        map.disableDoubleClickZoom();
      }
      if (conf.methods.continuouszoom) {
        map.enableContinuousZoom();
      } else {
        map.disableContinuousZoom();
      }
      if (conf.methods.scrollwheelzoom) {
        map.enableScrollWheelZoom();
      } else {
        map.disableScrollWheelZoom();
      }
      if (conf.methods.googlebar) {
        map.enableGoogleBar();
      } else {
        map.disableGoogleBar();
      }
      if (conf.methods.keyboardhandler) {
        GMaps.MAPS[i].keyboardHandler = new GKeyboardHandler(map);
      }
      var ctrl = null;
      switch(Number(conf.map_control)) {
        case 1:
          ctrl = new GSmallZoomControl();
          break;
        case 2:
          ctrl = new GSmallMapControl();
          break;
        case 3:
          ctrl = new GLargeMapControl();
          break;
        default:
          break;
      }
      if (ctrl != null) {
        map.addControl(ctrl);
        GMaps.MAPS[i].mapControl = ctrl;
      }

      var ctrl = null;
      switch(Number(conf.type_control)) {
        case 1:
          ctrl = new GMapTypeControl(Number(conf.type_control_shortnames));
          break;
        case 2:
          ctrl = new GHierarchicalMapTypeControl(Number(conf.type_control_shortnames));
          break;
        case 3:
          ctrl = new GMenuMapTypeControl(Number(conf.type_control_shortnames));
          break;
        default:
          break;
      }
      if (ctrl != null) {
        map.addControl(ctrl);
        GMaps.MAPS[i].typeControl = ctrl;
      }
      
      if (Number(conf.scale_control)) {
        ctrl = new GScaleControl();
        map.addControl(ctrl);
        GMaps.MAPS[i].scaleControl = ctrl;
      }

      if (Number(conf.overview_control)) {
        ctrl = new GOverviewMapControl();
        map.addControl(ctrl);
        GMaps.MAPS[i].overviewControl = ctrl;
      }

      //TODO: avoid multiple binding
      setTimeout(function(){
        var fs = $('#' + i).parents('fieldset.collapsed').get(0);
        var r = function() {
          $('div.gmaps-container', fs).each(function() {
            center = GMaps.MAPS[this.id].map.getCenter();
            GMaps.MAPS[this.id].map.checkResize();
            GMaps.MAPS[this.id].map.setCenter(center);
          })
          $(fs).children('legend').children('a').unbind('click', r);
        }
        $(fs).children('legend').children('a').bind('click', r);
      }, 0);

      GMaps.MAPS[i].overlays = {};
      var bounds = null;
      //fieldset handling, args: map.conf + overlays
      if (typeof(Drupal.settings.gmaps.maps[i].overlays) == 'object') {
        for (var o in Drupal.settings.gmaps.maps[i].overlays) {
          var ret = eval(Drupal.settings.gmaps.handlers[o] + "(i, conf, Drupal.settings.gmaps.maps[i].overlays[o])");
          if (ret != null) {
            GMaps.MAPS[i].overlays[o] = ret.overlays;
          }
          if (bounds == null) {
            bounds = ret.bounds;
          } else {
            var sw = new GLatLng(Math.min(bounds.getSouthWest().lat(), ret.bounds.getSouthWest().lat()), Math.min(bounds.getSouthWest().lng(), ret.bounds.getSouthWest().lng()));
            var ne = new GLatLng(Math.max(bounds.getSouthWest().lat(), ret.bounds.getSouthWest().lat()), Math.max(bounds.getSouthWest().lng(), ret.bounds.getSouthWest().lng()));
            bounds = new GLatLngBounds(sw, ne);
          }
        }
        if (conf.methods.auto_center_zoom && bounds != null) {
          map.setCenter(bounds.getCenter(),map.getBoundsZoomLevel(bounds));
          map.savePosition();
          if (typeof(GMaps.MAPS[i].markerManager) != 'undefined') {
            GMaps.MAPS[i].markerManager.refresh();
          }
        }
      }

      if (conf.methods.resize) {
        var container = $('#'+ i);
        GMaps.MAPS[i].staticOffset = null;

        function startDrag(e) {
          GMaps.MAPS[e.data].staticOffset = $('#'+ e.data).height() - Drupal.mousePosition(e).y;
          //$(document).mousemove(GMaps.MAPS[i].map.performDrag).mouseup(GMaps.MAPS[i].map.endDrag);
          $(document).bind('mousemove', e.data, performDrag).bind('mouseup', e.data, endDrag);
          return false;
        }

        function performDrag (e) {
          $('#' + e.data).height(Math.max(32, GMaps.MAPS[e.data].staticOffset + Drupal.mousePosition(e).y) + 'px');
          return false;
        }

        function endDrag (e) {
          $(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
          if (bounds == null) {bounds = GMaps.MAPS[e.data].map.getBounds();}
          GMaps.MAPS[e.data].map.checkResize();
          GMaps.MAPS[e.data].map.setCenter(bounds.getCenter(), GMaps.MAPS[e.data].map.getBoundsZoomLevel(bounds));
          if (typeof(GMaps.MAPS[e.data].markerManager) != 'undefined') {
            GMaps.MAPS[e.data].markerManager.refresh();
          }
          //bounds = GMaps.MAPS[].map.getBounds();
        }

        // When wrapping the text area, work around an IE margin bug.  See:
        // http://jaspan.com/ie-inherited-margin-bug-form-elements-and-haslayout
        $($('#' + i)).wrap('<div class="resizable-gmaps-container"><span></span></div>')
          .parent().append($('<div class="grippie"></div>').bind('mousedown', i, startDrag));

        var grippie = $('div.grippie', $(container).parent())[0];
        grippie.style.marginRight = (grippie.offsetWidth - $(container)[0].offsetWidth) +'px';

      }
    }
  GMaps.MAPS_ARE_LOADED = true;
}

// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready( function () {
    if (GBrowserIsCompatible()) {
      if (typeof(Drupal.settings.gmaps.maps) == 'object') {
        GMaps.loadIcons();
        GMaps.loadMaps();
      }
    } else {
      alert(Drupal.settings.gmaps.unsupportedBrowser);
    }
  });
}
