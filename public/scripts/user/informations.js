/**
 * 
 */

window.onload = function() {

    var displayModifyPwd = getById('displayModifyPwd') ;
    var displayPopUpEditUser = getById("displayPopUpEditUser") ;

    //var changePwd = getById('changePwd');


    //Open the pop up for the modfication of the password
    displayModifyPwd.onclick = function() {
        $("#changePwd").dialog("open") ;
    } ;

    //Open the pop up for the modfication of the password
    displayPopUpEditUser.onclick = function() {
        $("#editUser").dialog("open") ;
    } ;


    //Param of the pop up of the modification of the password
    $("#changePwd").dialog({
        autoOpen : false,
        height : 300,
        width : 500,
        modal : true,
        buttons : {
            "Valider" : function() {
                var formulaireEnvoi = document.getElementById("modifyPwdForm") ;
                var xhr = new XMLHttpRequest() ;
                xhr.open('POST', formulaireEnvoi.action, true) ;

                //formData va contenir les données à envoyer en POST par Ajax
                //ici formData reçoit toutes les données du form
                var formData = new FormData(formulaireEnvoi) ;

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText) ;
                        var response = JSON.parse(xhr.responseText) ;
                        if (response['code'] != '42') {
                            document.getElementById('errorModifyPwd').innerHTML = response['code'] ;
                        }
                        else {
                            $("#changePwd").dialog("close") ;
                            flashMessage("Le mot de passe a bien été modifié!") ;
                        }
                    }else {
                        alert('An error occurred!') ;
                    }
                } ;
                xhr.send(formData) ;
                return false ;
            },
            "Annuler" : function() {
                $("#changePwd").dialog("close") ;
            }
        }
    }) ;

    //Param of the pop up of the edit of a user
    $("#editUser").dialog({
        autoOpen : false,
        height : 300,
        width : 500,
        modal : true,
        buttons : {
            "Valider" : function() {
                var formulaireEnvoi = document.getElementById("edituserform") ;
                var xhr = new XMLHttpRequest() ;
                xhr.open('POST', formulaireEnvoi.action, true) ;

                //formData va contenir les données à envoyer en POST par Ajax
                //ici formData reçoit toutes les données du form
                var formData = new FormData(formulaireEnvoi) ;

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText) ;
                        var response = JSON.parse(xhr.responseText) ;
                        if (response['code'] != '42') {
                            document.getElementById('errorUploadMsgEdit').innerHTML = response['code'] ;
                        }else {
                            $("#editUser").dialog("close") ;
                            location.reload(true) ;
                            flashMessage("L'utilisateur a bien été édité!") ;
                        }
                    }else {
                        alert('An error occurred!') ;
                    }
                } ;
                xhr.send(formData) ;

                return false ;
            }, // Close the window/popup
            "Annuler" : function() {
                $("#editUser").dialog("close") ;
            }
        }
    }) ;

    //fct useless réalisée par Jessica Cherruau
    function getById(id) {
        return document.getElementById(id) ;
    }

}
