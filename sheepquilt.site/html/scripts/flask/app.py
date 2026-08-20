from flask import Flask
from hello_html_flask import get_html

app = Flask(__name__)

@app.route("/")
def home():
    return "<h1>Flask is working!</h1>"

@app.route("/hello-html-flask")
def hello_html_flask():
    return get_html()