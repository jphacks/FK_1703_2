#!/usr/bin/env python
# coding: utf-8

import json
import os
import re

import cherrypy
import requests
from google.cloud import translate

import lexrank

from cherrypy.process.plugins import Daemonizer
d = Daemonizer(cherrypy.engine)
d.subscribe()

# goo
API_ENDPOINT_KEYWORD = "https://labs.goo.ne.jp/api/keyword"
API_KEY = "f2dcd13f940ffe14e1095bca0e55b9d75d904bb33d7a94a95dcbe4753d673383"
# google
os.environ["GOOGLE_APPLICATION_CREDENTIALS"] = "./for Grandea-3a9c5701afe9.json"


def get_keywords(text, keywords_num):
    title = " "
    body = text

    data = {"app_id": API_KEY,
            "title": title,
            "body": body,
            "max_num": keywords_num}
    r = requests.post(url=API_ENDPOINT_KEYWORD, data=data)
    return r.json()['keywords']


class Summarizer(object):
    def __init__(self):
        self.summarizers = {}

    @cherrypy.expose
    def index(self):
        return """<html>
          <head></head>
          <body>
            <form method="get" action="summarize">
              text:<input type="text" value=" " name="text" />
              #summary:<input type="text" value="4" name="sent_limit" />
              #keyword:<input type="text" value="5" name="keywords_num" />
              <button type="submit">summarize!</button>
            </form>
            
            <form method="get" action="translate">
              text:<input type="text" value=" " name="text" />
              #target language:<input type="text" value="en" name="target" />
              <button type="submit">translate!</button>
            </form>
          </body>
        </html>"""

    @cherrypy.expose
    def summarize(self, text, keywords_num=5, **summarizer_params):
        for param, value in summarizer_params.items():
            if value == '':
                del summarizer_params[param]
                continue
            elif re.match(r'^\d*.\d+$', value):
                value = float(value)
            elif re.match(r'^\d+$', value):
                value = int(value)
            elif value == 'true':
                value = True
            elif value == 'false':
                value = False
            summarizer_params[param] = value

        summarizer = lexrank.summarize
        summary, debug_info = summarizer(text, **summarizer_params)

        keywords = get_keywords(text, keywords_num)
        res = json.dumps({'summary': summary,
                          "keywords": keywords},
                         ensure_ascii=False, indent=2)
        return res.encode('utf-8')

    @cherrypy.expose
    def translate(self, text, target='en'):
        translate_client = translate.Client()
        translation = translate_client.translate(
            text,
            target_language=target)
        return translation['translatedText']


if __name__ == '__main__':
    cherrypy.config.update({
        'server.socket_host': "0.0.0.0",
        'server.socket_port': 8080,
        'tools.encode.on': True,
        'tools.encode.encoding': 'utf-8'
    })

    conf = {
        '/': {
            'tools.staticdir.root': os.path.dirname(os.path.abspath(__file__))
        },
        '/summarize': {
            'tools.response_headers.on': True,
            'tools.response_headers.headers': [
                ('Content-type', 'application/json')
            ]
        }
    }
    cherrypy.quickstart(Summarizer(), '/', conf)
