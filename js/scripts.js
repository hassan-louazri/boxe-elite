function switchLanguage(language) {
    // Set the lang attribute on the html tag
    document.documentElement.lang = language;

    // Get all elements with a lang attribute
    const elements = document.querySelectorAll('[lang]');

    elements.forEach(element => {
        // Show elements that match the selected language, hide others
        if (element.getAttribute('lang') === language) {
            element.classList.remove('hidden');
        } else {
            element.classList.add('hidden');
        }
    });

    console.log("switchted to: ", document.documentElement.lang);
}
function sendResponse(status, message) {
    let response_box = document.getElementById('response');
    response_box.style.className = '';
    response_box.innerText = message;
    response_box.classList.add(status);
}
function doRequest(e){
    let response_box = document.getElementById('response');
    response_box.classList.remove('d-none');
    response_box.innerHTML = `<img src="assets/img/loading.gif" style="width: 50px;margin: auto;display: block;" alt="Loading ...">`;
    if (e.target.attributes.lang.value === "fr") {
        var name    = document.getElementById('inputName_fr').value;
        var email   = document.getElementById('inputEmail_fr').value;
        var subject = document.getElementById('inputQuestion_fr').value;
        var content = document.getElementById('inputMessage_fr').value;    
    }else{
        var name    = document.getElementById('inputName_en').value;
        var email   = document.getElementById('inputEmail_en').value;
        var subject = document.getElementById('inputQuestion_en').value;
        var content = document.getElementById('inputMessage_en').value;
    }
    if (name == "" || email== "" || subject== "" || content== ""){
        return sendResponse("bad_response", "Veuillez remplir tous les champs du formulaire." );
    }
    const xhr = new XMLHttpRequest();
          xhr.open("POST", window.location.href.split('#')[0] + "send_email.php");
          xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8")
    const body = JSON.stringify({
            name: name,
            email:email,
            subject:subject,
            content: content
            //CSRF TOKEN NEDDED
        });
          xhr.onload = () => {
            if (xhr.status == 200) {
              let resp = JSON.parse(xhr.responseText);
              console.log(resp.errors);
              console.log(resp.errors.length);
              if (resp.errors.length >= 1) {
                sendResponse('bad_response', `Inputs : ${resp.errors[0]}`);
              }else{
                if (resp.email_sent) {
                    sendResponse('good_response', "Votre message a été bien reçu.");
                }else{
                    sendResponse('bad_response', "Quelque chose ne marche pas.");
                }
              }
            //   sendResponse();
            } else {
                sendResponse("bad_response" ,`Error: ${xhr.status} (Status)`);
            }
          };
          xhr.send(body);

}
window.onload = function(){
    let sub = document.getElementsByClassName('btn_submit')
    
    sub[0].addEventListener('click',doRequest)
    sub[1].addEventListener('click',doRequest)
}