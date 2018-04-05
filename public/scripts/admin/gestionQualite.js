/**
 * 
 */

window.onload = function(){
	
	var displayPopUpCreateQualite = document.getElementById("displayPopUpCreate");
	var deleteQualiteForm = document.getElementsByClassName("delete");
	var displayPopUpEditQualite = document.getElementsByClassName("displayPopUpEdit");
	
	
	//Param of the pop up of the creation of a user
	$("#createQualite").dialog(
			{
		autoOpen: false,

		height: 250,

		width: 250,

		modal: true,

		buttons: {
                    "Valider": function() {
                            var formulaireEnvoi = document.getElementById("createQualiteForm");
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
                                            }
                                            else{
                                                    $("#createQualite").dialog("close");
                                                    location.reload(true);
                                                    flashMessage("La qualite a bien été crée!");

                                            }
                                    }
                                    else {
                                            alert('An error occurred!');
                                    }
                            };
                            xhr.send(formData);

                        return false;
                    }
                            , //Reset les valeurs dans les inputs du form qui s'affiche dans la pop quand on clique sur annuler
                    "Annuler": function(){
                            $( "#createQualite" ).dialog("close");
                            //le reset des valeurs de fonctionne pas
                            document.getElementById("qualiteNameCreate").value = "";
                    }
		} 

		});
	
	//Param of the pop up of the edit of a user
	$("#editQualite").dialog(
			{
		autoOpen: false,

		height: 250,

		width: 250,

		modal: true,

		buttons: {
			"Valider": function() {
				var formulaireEnvoi = document.getElementById("editQualiteForm");
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
							document.getElementById('errorUploadMsgEdit').innerHTML = response['code'];
						}
						else{
							$("#editQualite").dialog("close");
							location.reload(true);
							flashMessage("La qualite a bien été édité!");
						}
					}
					else {
						alert('An error occurred!');
					}
				};
				xhr.send(formData);
			
			return false;
		}
				, // Close the window/popup
			"Annuler": function(){
				$( "#editQualite" ).dialog("close");
				}
			} 

			});
	
	//Open the pop up for the creation of the user
	displayPopUpCreateQualite.onclick = function()
	{
		
		$("#createQualite").dialog("open");
		
	}
	

	//Use to submit the form when you delete a user. Ask for confirmation.
	for(var i = 0 ; i < deleteQualiteForm.length ; i++)
	{
		deleteQualiteForm[i].onclick = function()
		{
			var ret = confirm("Etes-vous sûr de vouloir supprimer cette qualite?");
			if(ret){
				this.parentNode.submit();
				flashMessage("La qualite a bien été supprimé!");
			}
			return false;
	
		}
	}
	
	//Use to hydrate the form when you want to edit a user
	for(var i = 0 ; i < displayPopUpEditQualite.length ; i++)
	{
		displayPopUpEditQualite[i].onclick = function()
		{
			var qualiteId = this.parentNode.childNodes[1].value;
			var formulaireEnvoi = this.parentNode;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', formulaireEnvoi.action, true);
			
			//formData va contenir les données à envoyer en POST par Ajax
			var formData = new FormData();
			formData.append('qualiteId', qualiteId);
			
			xhr.onload = function () {
				if (xhr.status === 200) {
					console.log(xhr.responseText);
					var response = JSON.parse(xhr.responseText);
					$("#qualiteIdEdit").val(qualiteId);
					$("#qualiteNameEdit").val(response['QualiteName']);
					$("#editQualite").dialog("open");
				}
				else {
					alert('An error occurred!');
				}
			};
			xhr.send(formData);
	
		}
	}
	

}