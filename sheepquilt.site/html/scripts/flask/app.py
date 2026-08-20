from flask import Flask, jsonify
from hello_html_flask import get_html
from hello_json_flask import get_json
from environment_flask import get_html as get_environment_html
from echo_flask import get_html as get_echo_html
from state1_flask import state1
from state2_flask import state2
from destroy_cookie_flask import destroy_cookie

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

@app.route("/state1_flask", methods=["POST"])
def state1_route():
    return state1()


@app.route("/state2_flask", methods=["GET"])
def state2_route():
    return state2()


@app.route("/destroy_cookie_flask", methods=["GET"])
def destroy_cookie_route():
    return destroy_cookie()