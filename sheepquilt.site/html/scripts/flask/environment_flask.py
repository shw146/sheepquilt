from flask import request

def get_html():
    environment = request.environ

    html = """
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <title>HTTP Environment</title>
</head>
<body>
    <h1>HTTP Environment</h1>
    <table border='1'>
        <tr>
            <th>Environment Variable</th>
            <th>Value</th>
        </tr>
"""

    for key, value in sorted(environment.items()):
        html += f"""
        <tr>
            <td>{key}</td>
            <td>{value}</td>
        </tr>
"""

    html += """
    </table>
</body>
</html>
"""

    return html