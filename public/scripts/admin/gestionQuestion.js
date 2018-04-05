/**
 * 
 */

window.onload = function(){
	
	var displayPopUpCreateQuestion = document.getElementById("displayPopUpCreate");
	var deleteQuestionForm = document.getElementsByClassName("delete");
	var displayPopUpEditQuestion = document.getElementsByClassName("displayPopUpEdit");
	
	
	//Param of the pop up of the creation of a user
	$("#createQuestion").dialog(
			{
		autoOpen: false,

		height: 250,

		width: 250,

		modal: true,

		buttons: {
                    "Valider": function() {
                            var formulaireEnvoi = document.getElementById("createQuestionForm");
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
                                                    $("#createQuestion").dialog("close");
                                                    location.reload(true);
                                                    flashMessage("La question a bien été crée!");

                                            }
                                    }
                                    else {
                                            alert('An error occurred!');
                                    }
                            };
                            xhr.send(formData);

                        return false;
                    },
                    //Reset les valeurs dans les inputs du form qui s'affiche dans la pop quand on clique sur annuler
                    "Annuler": function(){
                            $( "#createQuestion" ).dialog("close");
                            //le reset des valeurs de fonctionne pas
                            document.getElementById("questionNameCreate").value = "";
                    }
		} 

		});
	
	//Param of the pop up of the edit of a user
	$("#editQuestion").dialog(
			{
		autoOpen: false,

		height: 250,

		width: 250,

		modal: true,

		buttons: {
			"Valider": function() {
				var formulaireEnvoi = document.getElementById("editQuestionForm");
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
							$("#editQuestion").dialog("close");
							location.reload(true);
							flashMessage("La question a bien été édité!");
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
				$( "#editQuestion" ).dialog("close");
				}
			} 

			});
	
	//Open the pop up for the creation of the user
	displayPopUpCreateQuestion.onclick = function()
	{
		
		$("#createQuestion").dialog("open");
		
	}
	

	//Use to submit the form when you delete a user. Ask for confirmation.
	for(var i = 0 ; i < deleteQuestionForm.length ; i++)
	{
		deleteQuestionForm[i].onclick = function()
		{
			var ret = confirm("Etes-vous sûr de vouloir supprimer cette question?");
			if(ret){
				this.parentNode.submit();
				flashMessage("La question a bien été supprimé!");
			}
			return false;
	
		}
	}
	
	//Use to hydrate the form when you want to edit a user
	for(var i = 0 ; i < displayPopUpEditQuestion.length ; i++)
	{
		displayPopUpEditQuestion[i].onclick = function()
		{
			var questionId = this.parentNode.childNodes[1].value;
			var formulaireEnvoi = this.parentNode;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', formulaireEnvoi.action, true);
			
			//formData va contenir les données à envoyer en POST par Ajax
			var formData = new FormData();
			formData.append('questionId', questionId);
			
			xhr.onload = function () {
				if (xhr.status === 200) {
					console.log(xhr.responseText);
					var response = JSON.parse(xhr.responseText);
					$("#questionIdEdit").val(questionId);
					$("#questionNameEdit").val(response['QuestionName']);
					$("#editQuestion").dialog("open");
				}
				else {
					alert('An error occurred!');
				}
			};
			xhr.send(formData);
	
		}
	}
	

}