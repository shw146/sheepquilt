from flask import request


def state2():

    cookie_value = request.cookies.get("name")

    if cookie_value:
        return f"""
        <!DOCTYPE html>
        <html>
        <head>
            <title>State 2</title>
        </head>
        <body>
            <h1>State 2</h1>
            <p>The cookie contains: {cookie_value}</p>

            <a href="/flask/state1_flask">
                Go to State 1
            </a>
            <a href="/flask/destroy_cookie_flask">
                Destroy Cookie
            </a>
        </body>
        </html>
        """

    return """
    <!DOCTYPE html>
    <html>
    <head>
        <title>State 2</title>
    </head>
    <body>
        <h1>State 2</h1>
        <p>No cookie has been set.</p>

        <a href="/flask/state1_flask">
                    Go to State 1
        </a>
        <a href="/flask/destroy_cookie_flask">
            Destroy Cookie
        </a>
    </body>
    </html>
    """