/**
 * 
 */

window.onload = function(){
	
    var displayPopUpCreateStock = document.getElementById("displayPopUpCreateStock");
    var deleteStockForm = document.getElementsByClassName("deleteStock");
    var displayPopUpEditStock = document.getElementsByClassName("displayPopUpEditStock");


    //Param of the pop up of the creation of a stock
    $("#createStock").dialog({
            autoOpen: false,
            height: 300,
            width: 300,
            modal: true,

            buttons: {
            "Valider": function() {
                var formulaireEnvoi = document.getElementById("createstockform");
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
                            document.getElementById('errorUploadMsg').innerHTML = response['code'];
                        }else{
                            $("#createStock").dialog("close");
                            location.reload(true);
                            flashMessage("Le produit a bien été crée!");
                        }
                    }else {
                        alert('An error occurred!');
                    }
                };
                xhr.send(formData);

            return false;
            }
            , //Reset les valeurs dans les inputs du form qui s'affiche dans la pop quand on clique sur annuler
            "Annuler": function(){
                document.getElementById("name").value = "";
                document.getElementById("number").value= "";
                document.getElementById("price").value= "";
                $( "#createStock" ).dialog("close");
            }
        }
    });

    //Param of the pop up of the edit of a stock
    $("#editStock").dialog({
        autoOpen: false,
        height: 300,
        width: 300,
        modal: true,

        buttons: {
            "Valider": function() {
                var idStock = document.getElementById('idStock').value
                var formulaireEnvoi = document.getElementById("editstockform");
                var xhr = new XMLHttpRequest();
                xhr.open('POST', formulaireEnvoi.action, true);

                //formData va contenir les données à envoyer en POST par Ajax
                //ici formData reçoit toutes les données du form
                var formData = new FormData(formulaireEnvoi);
                formData.append('idStock', idStock);

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        if(response['code'] != '42'){
                            document.getElementById('errorUploadMsgEdit').innerHTML = response['code'];
                        }else{
                            $("#editStock").dialog("close");
                            location.reload(true);
                            flashMessage("Le produit a bien été édité!");
                        }
                    }else {
                        alert('An error occurred!');
                    }
                };
                xhr.send(formData);

            return false;
            }
            , // Close the window/popup
            "Annuler": function(){
                $( "#editStock" ).dialog("close");
            }
        } 

    });

    //Open the pop up for the creation of the stock
    displayPopUpCreateStock.onclick = function(){
        $("#createStock").dialog("open");
    }


    //Use to submit the form when you delete a stock. Ask for confirmation.
    for(var i = 0 ; i < deleteStockForm.length ; i++){
        deleteStockForm[i].onclick = function(){
            var ret = confirm("Etes-vous sûr de vouloir supprimer ce produit?");
            if(ret){
                this.parentNode.submit();
                flashMessage("Le produit a bien été supprimé!");
            }
            return false;
        }
    }

    //Use to hydrate the form when you want to edit a stock
    for(var i = 0 ; i < displayPopUpEditStock.length ; i++){
        displayPopUpEditStock[i].onclick = function(){
            var idStock = this.parentNode.childNodes[1].value;
            var formulaireEnvoi = this.parentNode;
            var xhr = new XMLHttpRequest();
            xhr.open('POST', formulaireEnvoi.action, true);

            //formData va contenir les données à envoyer en POST par Ajax
            var formData = new FormData();
            formData.append('idStock', idStock);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log(xhr.responseText);
                    var response = JSON.parse(xhr.responseText);
                    $("#idStock").val(idStock);
                    $("#StockName").val(response['StockName']);
                    $("#StockNumber").val(response['StockNumber']);
                    $("#StockPrice").val(response['StockPrice']);
                    $("#editStock").dialog("open");
                }else {
                    alert('An error occurred!');
                }
            };
            xhr.send(formData);

        }
    }

}

$(document).ready(function() {
    $('#tabStock').DataTable({paging: false}); 
} );