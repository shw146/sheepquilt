from flask import Flask, jsonify
from hello_html_flask import get_html
from hello_json_flask import get_json
from environment_flask import get_html as get_environment_html
from echo_flask import get_html as get_echo_html

app = Flask(__name__)

@app.route("/")
def home():
    return "<h1>Flask is working!</h1>"

@app.route("/hello-html-flask")
def hello_html_flask():
    return get_html()

@app.route("/hello-json-flask")
def hello_json_flask():
    return jsonify(get_json())

@app.route("/environment-flask")
def environment_flask():
    return get_environment_html()

@app.route("/echo-flask", methods=["GET", "POST", "PUT", "DELETE"])
def echo_flask():
    return get_echo_html()