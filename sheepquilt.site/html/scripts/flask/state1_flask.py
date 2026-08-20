from flask import request, make_response


def state1():

    # Check whether the cookie exists
    cookie_value = request.cookies.get("name")

    if cookie_value:
        return f"""
        <!DOCTYPE html>
        <html>
        <head>
            <title>State 1</title>
        </head>
        <body>
            <h1>Cookie Found</h1>
            <p>Your cookie contains: {cookie_value}</p>

            <a href="/flask/state2_flask">
                Go to State 2
            </a>
            <a href="/flask/destroy_cookie_flask">
                Destroy Cookie
            </a>
        </body>
        </html>
        """

    # No cookie exists, so get the name from the form
    name = request.form.get("name")

    html = f"""
    <!DOCTYPE html>
    <html>
    <head>
        <title>State 1</title>
    </head>
    <body>
        <h1>New Cookie Created</h1>
        <p>Your name is: {name}</p>

        <a href="/flask/state2_flask">
            Go to State 2
        </a>
        <a href="/flask/destroy_cookie_flask">
            Destroy Cookie
        </a>
    </body>
    </html>
    """

    response = make_response(html)

    # Create the cookie
    response.set_cookie("name", name)

    return response