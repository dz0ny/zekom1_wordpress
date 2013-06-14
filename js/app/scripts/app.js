(function() {
  'use strict';
  angular.module('nastavipiskotkeApp', []).controller('NastaviPiskotke', function($scope) {
    var cookies, isString;
    $scope.isVisible = true;
    isString = function(obj) {
      return toString.call(obj) === "[object String]";
    };
    cookies = function(name, value) {
      var c, co, cookie, cookieArray, cookieLength, i, index, lastCookieString, lastCookies, _i, _len, _ref;
      if (name) {
        if (angular.isUndefined(value)) {
          _ref = document.cookie.split(" ");
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            c = _ref[_i];
            co = c.split("=");
            if (co[0] === name) {
              return co[1];
            }
          }
          return void 0;
        } else {
          if (isString(value)) {
            cookieLength = (document.cookie = escape(name) + "=" + escape(value) + ";path=/;expires=Thu, 01 Jan 2084 00:00:00 GMT").length + 1;
            if (cookieLength > 4096) {
              return $log.warn("Cookie '" + name + "' possibly not set or overflowed because it was too large (" + cookieLength + " > 4096 bytes)!");
            }
          }
        }
      } else {
        if (document.cookie !== lastCookieString) {
          lastCookieString = document.cookie;
          cookieArray = lastCookieString.split("; ");
          lastCookies = {};
          i = 0;
          while (i < cookieArray.length) {
            cookie = cookieArray[i];
            index = cookie.indexOf("=");
            if (index > 0) {
              name = unescape(cookie.substring(0, index));
              if (angular.isUndefined(lastCookies[name])) {
                lastCookies[name] = unescape(cookie.substring(index + 1));
              }
            }
            i++;
          }
        }
        return lastCookies;
      }
    };
    $scope.strinjamse = function(odstrani) {
      cookies("uporabnik_privolil", 'da');
      return $scope.isVisible = false;
    };
    $scope.izbrano = function(isf) {
      var stanje;
      stanje = cookies("uporabnik_privolil");
      if (stanje === isf) {
        return "✓";
      } else {
        return "";
      }
    };
    return $scope.nestrinjam = function() {
      return cookies("uporabnik_privolil", 'ne');
    };
  });

}).call(this);
