'use strict'

angular.module('nastavipiskotkeApp', [])
  .controller 'NastaviPiskotke', ($scope) ->
    $scope.isVisible = true
    isString = (obj) ->
      toString.call(obj) is "[object String]"

    cookies = (name, value) ->
      if name
        if angular.isUndefined value
          for c in document.cookie.split(" ")
            co = c.split("=")
            if co[0] is name
              return co[1]
          return undefined
            
        else
          if isString(value)
            cookieLength = (document.cookie = escape(name) + "=" + escape(value) + ";path=/;expires=Thu, 01 Jan 2084 00:00:00 GMT").length + 1
            
            # per http://www.ietf.org/rfc/rfc2109.txt browser must allow at minimum:
            # - 300 cookies
            # - 20 cookies per unique domain
            # - 4096 bytes per cookie
            $log.warn "Cookie '" + name + "' possibly not set or overflowed because it was too large (" + cookieLength + " > 4096 bytes)!"  if cookieLength > 4096
      else
        if document.cookie isnt lastCookieString
          lastCookieString = document.cookie
          cookieArray = lastCookieString.split("; ")
          lastCookies = {}
          i = 0
          while i < cookieArray.length
            cookie = cookieArray[i]
            index = cookie.indexOf("=")
            if index > 0 #ignore nameless cookies
              name = unescape(cookie.substring(0, index))
              
              # the first value that is seen for a cookie is the most
              # specific one.  values for the same cookie name that
              # follow are for less specific paths.
              lastCookies[name] = unescape(cookie.substring(index + 1))  if angular.isUndefined lastCookies[name]
            i++
        lastCookies
    $scope.strinjamse = (odstrani)->
      cookies "uporabnik_privolil", 'da'
      $scope.isVisible = false
      

    $scope.izbrano = (isf)->
      stanje = cookies "uporabnik_privolil"
      if stanje is isf
        return  "✓" 
      else
        return  ""

    
    $scope.nestrinjam = ->
      cookies "uporabnik_privolil", 'ne'

