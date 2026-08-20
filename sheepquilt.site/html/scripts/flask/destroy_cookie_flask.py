from flask import make_response


def destroy_cookie():

    html = """
    <!DOCTYPE html>
    <html>
    <head>
        <title>Cookie Destroyed</title>
    </head>
    <body>
        <h1>Cookie Destroyed</h1>
        <p>The cookie has been deleted.</p>

        <a href="/flask/state1_flask">
            Go to State 1
        </a>
        <a href="/flask/state2_flask">
            Go to State 2
        </a>
        <a href="/scripts/flask/sessioning.html">
            Go back to Sessioning
        </a>
    </body>
    </html>
    """

    response = make_response(html)

    # Delete the cookie
    response.delete_cookie("name")

    return response