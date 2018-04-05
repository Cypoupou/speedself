/**
 * 
 */

window.onload = function(){

	var displayPopUpCreateContact = document.getElementById("displayPopUpCreate");
	var deleteContactForm = document.getElementsByClassName("delete");
	var displayPopUpEditContact = document.getElementsByClassName("displayPopUpEdit");
	
	
	//Param of the pop up of the creation of a contact
	$("#createContact").dialog(
			{
		autoOpen: false,

		height: 300,

		width: 300,

		modal: true,

		buttons: {
		"Valider": function() {
			var formulaireEnvoi = document.getElementById("createcontactform");
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
						$("#createContact").dialog("close");
						location.reload(true);
						flashMessage("Le contact a bien été crée!");
						
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
			document.getElementById("contactfirstname").value = "";
			document.getElementById("contactlastname").value= "";
			document.getElementById("contactemail").value= "";
			$( "#createContact" ).dialog("close");
			
			
			}
		} 

		});
	
	//Param of the pop up of the edit of a contact
	$("#editContact").dialog(
			{
		autoOpen: false,

		height: 300,

		width: 300,

		modal: true,

		buttons: {
			"Valider": function() {
				var formulaireEnvoi = document.getElementById("editcontactform");
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
							$("#editContact").dialog("close");
							location.reload(true);
							flashMessage("Le contact a bien été édité!");
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
				$( "#editContact" ).dialog("close");
				}
			} 

			});



	//Open the pop up for the creation of the contact
	displayPopUpCreateContact.onclick = function()
	{
		
		$("#createContact").dialog("open");
		
	}


	//Use to submit the form when you delete a contact. Ask for confirmation.
	for(var i = 0 ; i < deleteContactForm.length ; i++)
	{
		deleteContactForm[i].onclick = function()
		{
			var ret = confirm("Etes-vous sûr de vouloir supprimer ce contact?");
			if(ret){
				this.parentNode.submit();
				flashMessage("Le contact a bien été supprimé!");
			}
			return false;
	
		}
	}
	
	//Use to hydrate the form when you want to edit a contact
	for(var i = 0 ; i < displayPopUpEditContact.length ; i++)
	{
		displayPopUpEditContact[i].onclick = function()
		{
			var contactId = this.parentNode.childNodes[1].value;
			var formulaireEnvoi = this.parentNode;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', formulaireEnvoi.action, true);
			
			//formData va contenir les données à envoyer en POST par Ajax
			var formData = new FormData();
			formData.append('contactId', contactId);
			
			xhr.onload = function () {
				if (xhr.status === 200) {
					console.log(xhr.responseText);
					var response = JSON.parse(xhr.responseText);
					$("#contactId").val(contactId);
					$("#contactfirstnameEdit").val(response['ContactFirstName']);
					$("#contactlastnameEdit").val(response['ContactLastName']);
					$("#contactemailEdit").val(response['ContactMail']);
					$("#editContact").dialog("open");
				}
				else {
					alert('An error occurred!');
				}
			};
			xhr.send(formData);
	
		}
	}

}