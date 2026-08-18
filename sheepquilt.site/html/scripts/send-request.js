const form=document.getElementById("request-input");

form.addEventListener('submit', sendHTTPRequest);

async function sendHTTPRequest(event) {
    event.preventDefault();

    let endpoint = document.getElementById("endpoint").value;
    const method = document.getElementById("method").value;
    const encoding = document.getElementById("encoding").value;
    let body = null;

    const formData = new FormData(form);

    if(method === "GET"){
        const parameters = new URLSearchParams(formData);
        endpoint += "?"+parameters.toString();
    }else if(encoding === "application/json"){
        var temp = {};
        formData.forEach(function(value, key){
            temp[key] = value;
        });
        body = JSON.stringify(temp);
    }else{
        body = new URLSearchParams(formData);
    }

    try {
        const response = await fetch(endpoint, {
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