/**
 * 
 */

window.onload = function() {

    var displayPopUpCreateUser = document.getElementById("displayPopUpCreateUser") ;
    var deleteUserForm = document.getElementsByClassName("deleteUser") ;
    var displayPopUpEditUser = document.getElementsByClassName("displayPopUpEditUser") ;


    //Param of the pop up of the creation of a user
    $("#createUser").dialog({
        autoOpen : false,
        height : 500,
        width : 600,
        modal : true,
        buttons : {
            "Valider" : function() {
                var formulaireEnvoi = document.getElementById("createuserform") ;
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
                            document.getElementById('errorUploadMsg').innerHTML = response['code'] ;
                        }else {
                            $("#createUser").dialog("close") ;
                            location.reload(true) ;
                            flashMessage("L'utilisateur a bien été crée!") ;
                        }
                    }else {
                        alert('An error occurred!') ;
                    }
                } ;
                xhr.send(formData) ;
                return false ;
            }
            , //Reset les valeurs dans les inputs du form qui s'affiche dans la pop quand on clique sur annuler
            "Annuler" : function() {
                document.getElementById("username").value = "" ;
                document.getElementById("firstname").value = "" ;
                document.getElementById("lastname").value = "" ;
                document.getElementById("password").value = "" ;
                document.getElementById("passwordbis").value = "" ;
                document.getElementById("access").value = "" ;
                document.getElementById("email").value = "" ;
                $("#createUser").dialog("close") ;
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
            }
            , // Close the window/popup
            "Annuler" : function() {
                $("#editUser").dialog("close") ;
            }
        }

    }) ;

    //Open the pop up for the creation of the user
    displayPopUpCreateUser.onclick = function() {
        $("#createUser").dialog("open") ;
    }


    //Use to submit the form when you delete a user. Ask for confirmation.
    for (var i = 0 ; i < deleteUserForm.length ; i++) {
        deleteUserForm[i].onclick = function() {
            var ret = confirm("Etes-vous sûr de vouloir supprimer cet utilisateur?") ;
            if (ret) {
                this.parentNode.submit() ;
                flashMessage("L'utilisateur a bien été supprimé!") ;
            }
            return false ;
        }
    }

    //Use to hydrate the form when you want to edit a user
    for (var i = 0 ; i < displayPopUpEditUser.length ; i++) {
        displayPopUpEditUser[i].onclick = function() {
            var idUser = this.parentNode.childNodes[1].value ;
            var formulaireEnvoi = this.parentNode ;
            var xhr = new XMLHttpRequest() ;
            xhr.open('POST', formulaireEnvoi.action, true) ;

            //formData va contenir les données à envoyer en POST par Ajax
            var formData = new FormData() ;
            formData.append('idUser', idUser) ;

            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log(xhr.responseText) ;
                    var response = JSON.parse(xhr.responseText) ;
                    $("#idUserEdit").val(idUser) ;
                    $("#firstnameEdit").val(response['UserFirstName']) ;
                    $("#lastnameEdit").val(response['UserLastName']) ;
                    $("#emailEdit").val(response['UserEmail']) ;
                    $("#telEdit").val(response['UserTel']) ;
                    $("#groupEdit").val(response['UserGroupIdF']) ;
                    $("#accessEdit").val(response['IdAccessF']) ;
                    $("#editUser").dialog("open") ;
                }else {
                    alert('An error occurred!') ;
                }
            } ;
            xhr.send(formData) ;
        }
    }

}

$(document).ready(function() {
    $('#tabUser').DataTable({paging : false}) ;
}) ;