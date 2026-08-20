from datetime import datetime
from flask import request

formatted_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
client_ip = request.remote_addr

def get_html():
    return f"""
<!DOCTYPE html>
<html lang = 'en'>
<head>
    <meta charset = 'utf-8'>
    <title>Hello Flask</title>
</head>
<body>
    <h1>Hello from Shawn!</h1>
    <p>This is using flask/python</p>
    <p>The current date and time is: {formatted_time}</p>
    <p>Your ip is: {client_ip}</p>
</body>
</html>
"""