let form = document.getElementById("delete-form");

form.addEventListener("submit", function (event) {
    if (!confirm("Are you sure you want to submit this form?")) {
        event.preventDefault();
    }
});