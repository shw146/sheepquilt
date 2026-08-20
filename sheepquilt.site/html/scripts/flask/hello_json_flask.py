from datetime import datetime
from flask import request

def get_json():
    formatted_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    client_ip = request.remote_addr

    return {
        "message": "Hello from Shawn!",
        "description": "This is using Flask/Python",
        "current_time": formatted_time,
        "client_ip": client_ip
    }