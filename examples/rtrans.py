#!/usr/bin/env python
#-*- encoding: utf-8 -*-
# Written by kenkeiras under WTFPLv2
# Raw translator API example

from sys import stdout, stderr, argv
from urllib2 import urlopen, Request, build_opener
from urllib import urlencode
from json import loads
from MultipartPostHandler import MultipartPostHandler

global API_url
API_url = "http://127.0.0.1/rtrans/api.php" # Change me !

def get_translations():
    url = API_url + "?op=get_translations"
    r = Request( url )
    print loads(urlopen(r).read())

def translate_string( s, trans ):
    p = urlencode({'op':'translate_string',
                   'str': s,
                    'trans': trans})
                    
    url = API_url + "?%s" % p
    r = Request( url )
    t = urlopen(r).read()
    if len(t) > 0:
        print ">>>", t
    else:
        print >>stderr, "No translation available"
        
def add_to_database( f, trans ):
  opener = build_opener(MultipartPostHandler)
  params = { "op" : "add_to_database",
              "trans" : trans,
             "file" : open(f, "rb") }

  r = opener.open(API_url, params)
  print r.read()
  
def translate( f, trans ):
  opener = build_opener(MultipartPostHandler)
  params = { "op" : "translate",
              "trans" : trans,
             "file" : open(f, "rb"),
             'add': ""} # Optional

  r = opener.open(API_url, params)
  print r.read()
  


options = [
    "get_translations",
    "add_to_database",
    "translate",
    "translate_string"
    ]
    
rt_request = {'translate_string': translate_string,
              'add_to_database': add_to_database,
              'translate': translate }

if len(argv) < 2 or not argv[1] in options:
    print >>stderr, """%s <option> [<file/string>] [<translation>]
Options:
 get_translations
 add_to_database ( requires file )
 translate (requires file)
 translate_string (requires string)
""" % argv[0]
    exit( 0 )
    
if argv[1] == "get_translations":
    get_translations()

elif len(argv) < 4:
    print >>stderr, "Three parameter needed"
else:
    rt_request[argv[1]](argv[2], argv[3])