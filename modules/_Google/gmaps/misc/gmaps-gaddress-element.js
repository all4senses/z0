// $Id: gmaps-gaddress-element.js,v 1.1 2008/11/19 12:16:08 xmarket Exp $

var GMaps = GMaps || {};
GMaps.GADDRESSELEMENTS = {};
GMaps.GADDRESSELEMENTS_ARE_LOADED = false;

GMaps.attachGAddressElementBehavior = function() {
  var fields = Drupal.settings.gmapsGAddressElements;
  
  for (var i in fields) {
    GMaps.GADDRESSELEMENTS[i] = new GMapsGAddressElement(i);
  }
  
  GMaps.GADDRESSELEMENTS_ARE_LOADED = true;
}

GMapsGAddressElement = function (item) {
  var ga = this;

  var queryField = $('#' + item + '-query');
  var queryOptions = {};
  queryOptions.restrictCountry = $('#' + item + '-restrictcountry');
  var geoCoder = new GClientGeocoder();
  var address = {};
  var af = ['country', 'adminarea', 'subadminarea', 'locality', 'deplocality', 'thoroughfare', 'postalcode'];
  for (var i = 0; i < af.length; i++) {
    address[af[i]] = $('#' + item + '-address-' + af[i]);
  }
  var coordinates = GMaps.LATLONELEMENTS[item + '-coordinates'];
  var popup = new GMapsGAddressPopup(queryField, item + '-popup', parseResult);

  var debugarea = $('#' + item + '-debugarea').get(0);
  
  $('#' + item + '-search').click(function() {
    getLocations();
    queryField.get(0).focus();
  });

  queryField
    .keydown(function (event) { return onKeyDown(this, event); })
    .blur(function () { popup.select(false); popup.hide(); });

  function onKeyDown(input, e) {
    if (!e) {
      e = window.event;
    }
    switch (e.keyCode) {
      case 27: // esc
        if (popup.isPopulated()) {
          popup.select(false);
          popup.hide();
        }
        return true;
      case 40: // down arrow
        if (popup.isPopulated()) {
          popup.selectDown();
          return false;
        }
        return true;
      case 38: // up arrow
        if (popup.isPopulated()) {
          popup.selectUp();
          return false;
        }
        return true;
      case 13: // enter
        if (popup.isPopulated()) {
          popup.hide();
        } else {
          getLocations();
        }
        return false;
      default: // all other keys
        return true;
    }
  }

  function getLocations() {
    var query = queryField.get(0).value;
    if (query.length > 0) {
      if (queryOptions.restrictCountry.get(0).checked) {
        var cc = address.country.get(0).options[address.country.get(0).selectedIndex].value;
        if (cc != '') {
          query += ', ' + cc;
        }
      }
      geoCoder.getLocations(query, parseResponse);
    }
  }
  
  function parseResponse(response) {
    if (!response || response.Status.code != 200) {
      var msg = 'Unknown error.';
      //pm.address = Drupal.settings.gcg.invalidAddress;
      switch(response.Status.code) {
        case G_GEO_BAD_REQUEST:
          msg = 'Invalid query.';
          break;
        case G_GEO_SERVER_ERROR:
          msg = 'The Geocoder occured an internal server error.';
          break;
        case G_GEO_UNKNOWN_ADDRESS:
          msg = 'No location found.';
          break;
        case G_GEO_UNAVAILABLE_ADDRESS:
          msg = 'Access denied to location.';
          break;
        case G_GEO_TOO_MANY_QUERIES:
          msg = 'Service temporarly unavaliable, because the given key has gone over the requests limit.';
          break;
      }
      var info = document.createElement('div');
      $(info).addClass('messages error').html(msg).css({display : 'none'});
      $(queryField).before(info);
      //$(info).show();
      $(info).slideDown('normal');
      setTimeout(function() {
        $(info).slideUp('slow', function () {$(info).remove()})
      }, 1500);
    } else {
      if (response.Placemark.length == 1) {
        parseResult(response.Placemark[0]);
      } else {
        popup.populate(response.Placemark);
      }
    }
  }
  
  function parseResult(result) {
    clearValues();
    if (debugarea != null) {
      debugarea.value = result.toSource();
    }
    setValues(GMaps.parseGeocoderPlacemark(result));
  }

  function clearValues() {
    for (var i = 0; i < af.length; i++) {
      $('#' + item + '-address-' + af[i]).get(0).value = '';
    }
    coordinates.setLatLon(null, null);
  }
  
  function setValues(values) {
    if (typeof(values.address) != 'undefined') {
      for (var i = 0; i < af.length; i++) {
        if (typeof(values.address[af[i]]) != 'undefined') {
          $('#' + item + '-address-' + af[i]).get(0).value = values.address[af[i]];
        }
      }
    }
    if (typeof(values.coordinates) != 'undefined') {
      coordinates.setLatLon(values.coordinates.latitude, values.coordinates.longitude);
    }
  }
};

GMapsGAddressPopup = function (input, id, cbFn) {
  var gap = this;
  this.input = input;
  var selected = false;
  var populated = false;
  var popup = document.createElement('div');
  popup.id = id;
  $(popup).addClass('gaddress-geocoder-search-result').hide();
  $(input).before(popup);
  
  this.populate = function(items) {
    $(popup).css({
      marginTop: $(this.input).get(0).offsetHeight +'px',
      width: ($(this.input).get(0).offsetWidth - 4) +'px',
      display: 'none'
    });
    var ul = document.createElement('ul');
    for (i in items) {
      var item = items[i];
      var li = document.createElement('li');
      $(li)
        .html('<div>'+ Drupal.settings.gmapsGeocoderResultLevels[item.AddressDetails.Accuracy] + ' - ' + item.address +'</div>')
        .mousedown(function () { gap.select(this); gap.hide(); })
        .mouseover(function () { gap.highlight(this); })
        .mouseout(function () { gap.unhighlight(this); });
      li.geocoderResultValue = item;
      $(ul).append(li);
    }
    if (ul.childNodes.length > 0) {
      $(popup).empty().append(ul).show();
      populated = true;
    }
    else {
      gap.hide();
    }
  }

  this.select = function (node) {
    selected = node;
  }

  this.selectDown = function () {
    if (selected && selected.nextSibling) {
      this.highlight(selected.nextSibling);
    }
    else {
      var lis = $('li', popup);
      if (lis.size() > 0) {
        this.highlight(lis.get(0));
      }
    }
  }

  this.selectUp = function () {
    if (selected && selected.previousSibling) {
      this.highlight(selected.previousSibling);
    }
    else {
      var lis = $('li', popup);
      if (lis.size() > 0) {
        this.highlight(lis.get(lis.length - 1));
      }
    }
  }

  this.highlight = function (node) {
    if (selected) {
      $(selected).removeClass('selected');
    }
    $(node).addClass('selected');
    selected = node;
  }

  this.unhighlight = function (node) {
    $(node).removeClass('selected');
    selected = false;
  }

  this.hide = function (keycode) {
    // Select item if the right key or mousebutton was pressed
    if (populated) {
      if (selected && ((keycode && keycode != 46 && keycode != 8 && keycode != 27) || !keycode)) {
        //setTimeout(callback, 0, selected.geocoderResultValue);
        cbFn(selected.geocoderResultValue);
      }
      selected = false;
      $(popup).fadeOut('fast').empty();
      populated = false;
    }
  }
  
  this.isPopulated = function() {
    return populated;
  }

}

GMaps.parseGeocoderPlacemark = function(placemark) {
  function checkPostalCode() {
    if (typeof(cont.PostalCode) != 'undefined') {
      ret.address.postalcode = cont.PostalCode.PostalCodeNumber;
    }
  }

  var ret = {};
  ret.accuracy = placemark.AddressDetails.Accuracy;
  ret.returnedAddress = placemark.address;
  if (ret.accuracy == 0) {
    return ret;
  }

  if (typeof(placemark.Point) != 'undefined' && typeof(placemark.Point.coordinates) != 'undefined') {
    ret.coordinates = {};
    ret.coordinates.latitude = placemark.Point.coordinates[1];
    ret.coordinates.longitude = placemark.Point.coordinates[0];
  }
  
  if (typeof(placemark.AddressDetails.Country) == 'undefined') {
    return ret;
  }
  
  ret.address = {};
  ret.address.country = placemark.AddressDetails.Country.CountryNameCode;

  //the geocoder can suggest a result in a Premise item (accuracy 9)
  if (ret.accuracy > 8) {
    if (typeof(placemark.AddressDetails.Country.Premise) != 'undefined') {
      ret.address.locality = placemark.AddressDetails.Country.Premise.PremiseName;
    }
    return ret;
  }

  var cont = placemark.AddressDetails.Country;
  checkPostalCode();

  if (typeof(cont.AdministrativeArea) != 'undefined') {
    cont = cont.AdministrativeArea;
    ret.address.adminarea = cont.AdministrativeAreaName;
    checkPostalCode();
    if (typeof(cont.SubAdministrativeArea) != 'undefined') {
      cont = cont.SubAdministrativeArea;
      ret.address.subadminarea = cont.SubAdministrativeAreaName;
      checkPostalCode();
    }
  } else if (typeof(cont.SubAdministrativeArea) != 'undefined') {
    cont = cont.SubAdministrativeArea;
    ret.address.adminarea = cont.SubAdministrativeAreaName;
    checkPostalCode();
  }

  //cont = cont.Locality;
  //ret.address.locality = cont.LocalityName;
  //checkPostalCode();

  if (typeof(cont.Locality) != 'undefined') {
    cont = cont.Locality;
    ret.address.locality = cont.LocalityName;
    checkPostalCode();
    if (typeof(cont.DependentLocality) != 'undefined') {
      cont = cont.DependentLocality;
      ret.address.deplocality = cont.DependentLocalityName;
      checkPostalCode();
      if (typeof(cont.Thoroughfare) != 'undefined') {
        cont = cont.Thoroughfare;
        ret.address.thoroughfare = cont.ThoroughfareName;
        checkPostalCode();
      }
    } else if (typeof(cont.Thoroughfare) != 'undefined') {
      cont = cont.Thoroughfare;
      ret.address.thoroughfare = cont.ThoroughfareName;
      checkPostalCode();
    }
  } else if (typeof(cont.DependentLocality) != 'undefined') {
    cont = cont.DependentLocality;
    ret.address.deplocality = cont.DependentLocalityName;
    checkPostalCode();
    if (typeof(cont.Thoroughfare) != 'undefined') {
      cont = cont.Thoroughfare;
      ret.address.thoroughfare = cont.ThoroughfareName;
      checkPostalCode();
    }
  } else if (typeof(cont.Thoroughfare) != 'undefined') {
    cont = cont.Thoroughfare;
    ret.address.thoroughfare = cont.ThoroughfareName;
    checkPostalCode();
  }

  return ret;
}

GMaps.testGAddressRequirements = function() {
  if (typeof(GMaps.LATLONELEMENTS) != 'undefined' && GMaps.LATLONELEMENTS_ARE_LOADED) {
    GMaps.attachGAddressElementBehavior();
  } else {
    setTimeout(GMaps.testGAddressRequirements, 300);
  }
}

// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready( function () {
    GMaps.testGAddressRequirements();
  });
}
