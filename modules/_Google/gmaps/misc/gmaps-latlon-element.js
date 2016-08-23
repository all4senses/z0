// $Id: gmaps-latlon-element.js,v 1.1 2008/11/19 12:16:08 xmarket Exp $

var GMaps = GMaps || {};
GMaps.LATLONELEMENTS = {};
GMaps.LATLONELEMENTS_ARE_LOADED = false;

GMaps.attachLatlonElementBehavior = function() {
  var fields = Drupal.settings.gmapsLatlonElements;
  
  for (var i in fields) {
    GMaps.LATLONELEMENTS[i] = new GMapsLatlonElement(i, fields[i]);
  }
  
  GMaps.LATLONELEMENTS_ARE_LOADED = true;
}

GMapsLatlonElement = function (item, conf) {
  var latlon = this;
  this.item = item;
  this.latItem = $('#' + item + '-latitude');
  this.lonItem = $('#' + item + '-longitude');
  this.marker = null;
  this.dblClickListener = {};
  this.map = null;
  this.timerDelay = 300;
  this.markerTimer = null;

  if (typeof(conf) != 'undefined' && typeof(conf.containerId) != 'undefined') {
    this.map = GMaps.MAPS[conf.containerId];
  }
  
  if (this.map != null) {
    if (typeof(this.map.overlays.marker) == 'undefined' || typeof(this.map.overlays.marker[conf.markerId]) == 'undefined') {
      addCreateBehavior();
    } else {
      this.marker = this.map.overlays.marker[conf.markerId];
      addUpdateBehavior();
    }
  }

  this.setFieldValue = function(field, value) {
    field.get(0).value = value;
  }

  this.getFieldValue = function(field) {
    return field.get(0).value;
  }

  function addCreateBehavior() {
    latlon.latItem.attr('disabled', 'disabled');
    latlon.lonItem.attr('disabled', 'disabled');
    latlon.dblClickListener = GEvent.addListener(latlon.map.map, "dblclick", function(overlay, glatlon) {
      latlon.setLatLon(glatlon);
      addUpdateBehavior();
      GEvent.removeListener(latlon.dblClickListener);
      latlon.dblClickListener = null;
    });
  }

  function addUpdateBehavior() {
    GEvent.addListener(latlon.marker, "dragend", function() {
      latlon.setLatLon(latlon.marker.getLatLng());
    });
    latlon.dblClickListener = GEvent.addListener(latlon.map.map, "dblclick", function(overlay, glatlon) {
      latlon.setLatLon(glatlon);
    });

    addFieldListener(latlon.latItem);
    addFieldListener(latlon.lonItem);
  }

  function addMarker() {
    if (latlon.map != null) {
      var markerOpts = {icon: G_DEFAULT_ICON, draggable: true};
      var point = new GLatLng(latlon.getFieldValue(latlon.latItem), latlon.getFieldValue(latlon.lonItem));
      latlon.marker = new GMarker(point, markerOpts);
      latlon.map.map.addOverlay(latlon.marker);
      latlon.map.map.setCenter(point);
      latlon.map.overlays.marker = latlon.map.overlays.marker || {};
      latlon.map.overlays.marker[conf.markerId] = latlon.marker;
    }
    latlon.latItem.removeAttr('disabled');
    latlon.lonItem.removeAttr('disabled');
  }

  function updateMarker() {
    if (latlon.map != null) {
      if (latlon.marker == null) {
        addMarker();
      } else {
        var newPoint = new GLatLng(latlon.getFieldValue(latlon.latItem), latlon.getFieldValue(latlon.lonItem));
        latlon.marker.setLatLng(newPoint);
        latlon.map.map.setCenter(newPoint);
      }
    }
  }

  function clearValues() {
    latlon.setFieldValue(latlon.latItem, '');
    latlon.setFieldValue(latlon.lonItem, '');
    if (latlon.map != null) {
      if (latlon.marker != null) {
        latlon.map.map.removeOverlay(latlon.marker);
        latlon.marker = null;
        GEvent.removeListener(latlon.dblClickListener);
        latlon.dblClickListener = null
        latlon.map.overlays.marker[conf.markerId] = undefined;
      }
      addCreateBehavior();
    }
  }

  function addFieldListener(field) {
    field.keyup(function(event) {
      if (!event) {
        event = window.event;
      }
      switch (event.keyCode) {
        case 48:  // 0
        case 49:  // 1
        case 50:  // 2
        case 51:  // 3
        case 52:  // 4
        case 53:  // 5
        case 54:  // 6
        case 55:  // 7
        case 56:  // 8
        case 57:  // 9

        case 96:  // 0
        case 97:  // 1
        case 98:  // 2
        case 99:  // 3
        case 100:  // 4
        case 101:  // 5
        case 102:  // 6
        case 103:  // 7
        case 104:  // 8
        case 105:  // 9

        case 8:  // backspace
        case 46:  // del
        case 107:  // +
        case 109:  // -
        case 190:  // .
        case 61:  // shift + 3 = "+"
          latlon.markerTimer = setTimeout(updateMarker, latlon.timerDelay);
          return true;

        default: // all other keys
          return true;
      }
    })
    .keydown(function () {
      if (latlon.markerTimer != null) {
        clearTimeout(latlon.markerTimer);
        latlon.markerTimer = null;
      }
    });
  }

  this.setLatLon = function (lat, lon) {
    if (typeof(lat) == 'object' && lat != null && lat != '') {
      lon = lat.lng();
      lat = lat.lat();
    } else {
      if (lat == null || lat == '' || lon == null || lon == '') {
        clearValues();
        return;
      }
    }
    latlon.setFieldValue(latlon.latItem, lat);
    latlon.setFieldValue(latlon.lonItem, lon);
    updateMarker();
  }

  this.getLatLon = function () {
    var ret = {};
    ret.latitude = latlon.getFieldValue(latlon.latItem);
    ret.longitude = latlon.getFieldValue(latlon.lonItem);
    return ret;
  }
};

GMaps.testLatlonRequirements = function() {
  if (typeof(Drupal.settings.gmaps) == 'undefined' || typeof(Drupal.settings.gmaps.maps) == 'undefined' || (typeof(GMaps.MAPS_ARE_LOADED) != 'undefined' && GMaps.MAPS_ARE_LOADED)) {
    GMaps.attachLatlonElementBehavior();
  } else {
    setTimeout(GMaps.testLatlonRequirements, 300);
  }
}

// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready( function () {
    GMaps.testLatlonRequirements();
  });
}
