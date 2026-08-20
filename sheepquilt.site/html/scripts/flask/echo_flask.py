from datetime import datetime
from flask import request

def get_html():
    data = request.get_data(as_text=True)
    hostname = request.host
    formatted_time = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    user_agent = request.headers.get("User-Agent")
    client_ip = request.remote_addr
    http_method = request.method

    return f"""
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Echo Flask</title>
</head>
<body>
    <h1>Echo Flask</h1>

    <p><strong>Data:</strong> {data}</p>
    <p><strong>Hostname:</strong> {hostname}</p>
    <p><strong>Date and Time:</strong> {formatted_time}</p>
    <p><strong>User Agent:</strong> {user_agent}</p>
    <p><strong>Client IP Address:</strong> {client_ip}</p>
    <p><strong>HTTP Method:</strong> {http_method}</p>
</body>
</html>
"""