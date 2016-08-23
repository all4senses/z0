// $Id: gmaps-marker.js,v 1.1 2008/11/19 12:16:08 xmarket Exp $

var GMaps = GMaps || {};

/**
 * Add markers to the map.
 */
GMaps.addMarkers = function (container, mapconf, markers) {
  if (typeof(markers) == 'object') {
    var ret = {};
    ret.overlays = {};
    if (Number(mapconf.marker_options.marker_manager)) {
      var mmOpts = {
        borderPadding : mapconf.marker_manager_options.borderpadding + "px",
        trackMarkers : mapconf.marker_manager_options.trackmarkers
      };
      if (mapconf.marker_manager_options.maxzoom != 'u') {
        mmOpts.maxZoom = mapconf.marker_manager_options.maxzoom;
      }
      var markerManager = new GMarkerManager(GMaps.MAPS[container].map, mmOpts);
    }
    for (var i in markers) {
      var conf = markers[i];

      var marker = GMaps.createMarker(container, mapconf, conf);

      if (typeof(markerManager) == 'undefined') {
        GMaps.MAPS[container].map.addOverlay(marker);
      } else {
        if (typeof(conf.zoomrange) != 'undefined') {
          var minZoom = conf.zoomrange.min;
          var maxZoom = typeof(conf.zoomrange.max) == 'undefined' ? null : conf.zoomrange.max;
        } else {
          var minZoom = 0;
          var maxZoom = null;
        }
        markerManager.addMarker(marker, minZoom, maxZoom);
      }
      if (typeof(conf.id) != 'undefined') {
        ret.overlays[conf.id] = marker;
      }
      if (mapconf.methods.auto_center_zoom) {
        if (typeof(ret.bounds) == 'undefined') {
          ret.bounds = new GLatLngBounds(marker.getLatLng(), marker.getLatLng());
        } else {
          ret.bounds.extend(marker.getLatLng());
        }
      }
    }
    if (typeof(markerManager) != 'undefined') {
      GMaps.MAPS[container].markerManager = markerManager;
    }
    return ret;
  }
  return null;
}

GMaps.createMarker = function (container, mapconf, conf) {
  var icon = G_DEFAULT_ICON;
  if (typeof(conf.icon) != 'undefined') {
    if (conf.icon == 'i') {
      icon = GMaps.ICONS[mapconf.marker_options.default_icon];
    } else if (Number(conf.icon) > 0) {
      icon = GMaps.ICONS[conf.icon];
    }
  }
  var markerOpts = {icon: icon, title: conf.title};
  if (typeof(conf.draggable) != 'undefined' && Number(conf.draggable)) {
    markerOpts.draggable = true;
    if (typeof(conf.drag_options) != 'undefined') {
      if (typeof(conf.drag_options.dcmove) != 'undefined') {
        markerOpts.dragCrossMove = conf.drag_options.dcmove;
      }
      if (typeof(conf.drag_options.bouncy) != 'undefined') {
        markerOpts.bouncy = conf.drag_options.bouncy;
      }
      if (typeof(conf.drag_options.bouncegravity) != 'undefined') {
        markerOpts.bounceGravity = conf.drag_options.bouncegravity;
      }
      if (typeof(conf.drag_options.autopan) != 'undefined') {
        markerOpts.autoPan = conf.drag_options.autopan;
      }
    }
  }
  if (typeof(conf.url) == 'undefined' && typeof(conf.iw_content) == 'undefined' && typeof(conf.iw_autocomplete) == 'undefined') {
    markerOpts.clickable = false;
  }
  var point = new GLatLng(conf.latitude, conf.longitude);
  var marker = new GMarker(point, markerOpts)    

  if (typeof(conf.url) != 'undefined' || typeof(conf.iw_content) != 'undefined' || typeof(conf.iw_autocomplete) != 'undefined') {
    GEvent.addListener(marker, "click", function() {
      if (typeof(conf.url) != 'undefined') {
        if (mapconf.marker_options.linktarget == '_blank') {
          window.open(conf.url);
        } else {
          window.location.href = conf.url;
        }
      }
      else if (typeof(conf.iw_content) != 'undefined') {
        if (typeof(marker.gmaps_iw_content) == 'undefined') {
          marker.gmaps_iw_content = GMaps.prepareInfoWindowContent(container, mapconf.marker_options.linktarget, conf.iw_content);
        }
        GMaps.openMarkerInfoWindow(marker, conf, container, mapconf.marker_options);
      } else {
        GMaps.openMarkerInfoWindow(marker, conf, container, mapconf.marker_options);
      }
    });
  }

  return marker;
}

GMaps.prepareInfoWindowContent = function(container, target, content) {
  var div = $('<div id="' + container + '-iw-content"></div>').css('display', 'none');
  $(div).html(content);
  $('a', $(div)).attr('target', target);
  return $(div).html();
}

GMaps.openMarkerInfoWindow = function(marker, conf, container, marker_options) {
  if (typeof(marker.gmaps_iw_content) != 'undefined') {
    marker.openInfoWindowHtml(marker.gmaps_iw_content, {maxWidth: marker_options.iw_maxwidth, noCloseOnClick: marker_options.iw_nocloseonclick});
    return;
  }
  this.timer = setTimeout(function() {
    marker.hide();
    GMaps.MAPS[container].contentLoader.setLatLng(marker.getLatLng());
    GMaps.MAPS[container].contentLoader.show();

    // Ajax GET request for autocompletion
    $.ajax({
      type: "GET",
      url: conf.iw_autocomplete,
      success: function (data) {
        // Parse back result
        if (typeof data == 'string') {
          data = eval('(' + data + ');');
          marker.gmaps_iw_content = GMaps.prepareInfoWindowContent(container, marker_options.linktarget, data);

          GMaps.MAPS[container].contentLoader.hide();
          marker.show();
          marker.openInfoWindowHtml(marker.gmaps_iw_content, {maxWidth: marker_options.iw_maxwidth, noCloseOnClick: marker_options.iw_nocloseonclick});
        } else {
          GMaps.MAPS[container].contentLoader.hide();
          marker.show();
          alert('Unspecified error');
        }
      },
      error: function (xmlhttp) {
        alert('An HTTP error '+ xmlhttp.status +' occured.\n'+ conf.iw_autocomplete);
      }
    });
  }, 0);
}