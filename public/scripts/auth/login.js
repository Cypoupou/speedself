/**
 * Created by DDJK5031 on 23/03/2018
 */

window.onload = function(){
    
    var displayLoginForm = document.getElementById("displayLoginForm");
    var displayPasswordForm = document.getElementById("displayPasswordForgottenForm");
    var displaySignInForm = document.getElementById("displaySignInForm");

    //Param of the pop up of the login page
    $("#loginpopupForm").dialog({
        autoOpen: false,
        height: 300,
        width: 500,
        modal: true,

        buttons: {
            "Valider": {
                id :'btnValider',
                text: "Valider",
                click: function(e){
                    var formulaireEnvoi = document.getElementById("loginForm");
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', formulaireEnvoi.action, true);

                    //formData va contenir les données à envoyer en POST par Ajax
                    //ici formData reçoit toutes les données du form
                    var formData = new FormData(formulaireEnvoi);

                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            console.log(xhr.responseText);
                            var response = JSON.parse(xhr.responseText);
                            if(response['code'] != '42'){
                                document.getElementById('errorLogin').innerHTML = response['code'];
                            }else {
                                $("#loginpopupForm").dialog("close");
                                if(window.location.hostname.indexOf('localhost') === -1)
                                    window.location.href ='/accueil';
                                else
                                    window.location.href ='/speedself/public/accueil';
                                flashMessage("Vous êtes connecté !");
                            }
                        }else {
                            alert('An error occurred !');
                        }
                    };
                    xhr.send(formData);
                    return false;
                }
            },
            // Close the window/popup
            "Annuler": function(){
                $( "#loginpopupForm" ).dialog("close");
            }
        },
        open: function () {
            $(document).keydown(function (event) {
                // if enter key is pressed
                if (event.keyCode == 13) {
                    $("#btnValider").click();
                }
            });
        }
    });

    //Param of the pop up of the login page
    $("#passwordforgottenpopupForm").dialog({
        autoOpen: false,
        height: 250,
        width: 400,
        modal: true,

        buttons: {
            "Valider": function() {
                var formulaireEnvoiPassword = document.getElementById("passwordforgottenForm");
                var xhr = new XMLHttpRequest();
                xhr.open('POST', formulaireEnvoiPassword.action, true);

                //formData va contenir les données à envoyer en POST par Ajax
                //ici formData reçoit toutes les données du form
                var formData = new FormData(formulaireEnvoiPassword);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        if(response['code'] != '42'){
                            document.getElementById('errorPassword').innerHTML = response['code'];
                        }else{
                            $("#passwordforgottenpopupForm").dialog("close");
                            if(window.location.hostname.indexOf('localhost') === -1)
                                    window.location.href ='/auth/login';
                            else
                                    window.location.href ='/speedself/public/auth/login';
                            flashMessage("Un mail vous a été envoyé sur votre boite e-mail !");
                        }
                    }else {
                        alert('An error occurred!');
                    }
                };
                xhr.send(formData);
                return false;
            },
            // Close the window/popup
            "Annuler": function(){
                $( "#passwordforgottenpopupForm" ).dialog("close");
            }
        }
    });
    
    //Param of the pop up of the signin page
    $("#signinpopupForm").dialog({
        autoOpen: false,
        height: 450,
        width: 500,
        modal: true,
        
        buttons: {
            "Valider": {
                id :'btnValider',
                text: "Valider",
                click: function(e){
                    var formulaireEnvoi = document.getElementById("signinForm");
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', formulaireEnvoi.action, true);
                    
                    //formData va contenir les données à envoyer en POST par Ajax
                    //ici formData reçoit toutes les données du form
                    var formData = new FormData(formulaireEnvoi);
                    
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            console.log(xhr.responseText);
                            var response = JSON.parse(xhr.responseText);
                            if(response['code'] != '42'){
                                document.getElementById('errorSignIn').innerHTML = response['code'];
                            }else {
                                $("#signinpopupForm").dialog("close");
                                if(window.location.hostname.indexOf('localhost') === -1)
                                    window.location.href ='/auth';
                                else
                                    window.location.href ='/speedself/public/auth';
                                flashMessage("Un email de confiramtion vous a été envoyé !");
                            }
                        }else {
                            alert('An error occurred !');
                        }
                    };
                    xhr.send(formData);
                    return false;
                }
            },
            // Close the window/popup
            "Annuler": function(){
                $( "#signinpopupForm" ).dialog("close");
            }
        },
        open: function () {
            $(document).keydown(function (event) {
                // if enter key is pressed
                if (event.keyCode == 13) {
                    $("#btnValider").click();
                }
            });
        }
    });

    //Open the pop up for the login page
    displayLoginForm.onclick = function(){
        $("#loginpopupForm").dialog("open");
    };

    //Open the pop up for the password forgotten page
    displayPasswordForm.onclick = function(){
        $("#passwordforgottenpopupForm").dialog("open");
    };
    
    //Open the pop up for the login page
    displaySignInForm.onclick = function(){
        $("#signinpopupForm").dialog("open");
    };
    
};
