const form=document.getElementById("request-input");

form.addEventListener('submit', sendHTTPRequest);

async function sendHTTPRequest(event) {
    event.preventDefault();

    const endpoint = document.getElementById("endpoint").value;
    const method = document.getElementById("method").value;
    const encoding = document.getElementById("encoding").value;

    const formData = new FormData(form);

    let url = endpoint;
    let body = null;

    if (method === "GET") {
        const params = new URLSearchParams(formData);
        url += "?" + params.toString();
    } else if (encoding === "application/json") {
        body = JSON.stringify(Object.fromEntries(formData));
    } else {
        body = new URLSearchParams(formData);
    }

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": encoding
            },
            body: body
        });

        const html = await response.text();

        // Replace the current page with PHP's response
        document.open();
        document.write(html);
        document.close();

    } catch (error) {
        console.error("Request Failed", error);
    }
}