const form=document.getElementById("request-input");

form.addEventListener('submit', sendHTTPRequest);

function sendHTTPRequest(event){
    event.preventDefault();
    const endpoint = document.getElementById("endpoint").value;
    const request = new Request(endpoint, {
        method: document.getElementById("method").value,
        headers: {
            "Content-Type": document.getElementById("encoding").value
        }
    });

    try{
        fetch(request);
    }catch{
        console.error("Request Failed", error);
    }
}