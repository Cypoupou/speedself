/**
 * 
 */


window.onload = function(){
	'use strict';
	
	$(".autocompleteAddress").autocomplete({
		source: (window.location.hostname.indexOf('localhost') === -1
				&& window.location.hostname.indexOf('dvrftwsc01') === -1) ?
				"/mailer/autocompleteaddress":
				"/TRACv2/public/mailer/autocompleteaddress",
		minlength: 2,
		
	});
	
	//create a dynamical list of attachments
	var fileInput = getById("files-0");
	fileInput.onchange = function(){
		//create a list element 
		var elem = document.createElement('LI');
		//create a copy of the existing input file
		var input = this.cloneNode(true);
		input.setAttribute("class", 'uploadFiles hidden');
		var span = document.createElement("SPAN");
		span.innerHTML = this.value;
		var deleteIcon = document.createElement('A');
		deleteIcon.setAttribute("class", "deleteIcon");
		if(window.location.hostname.indexOf('localhost') === -1
				&& window.location.hostname.indexOf('dvrftwsc01') === -1)
			deleteIcon.innerHTML = "<img src='/images/delete_icon.png' alt='suppr'>";
		else
			deleteIcon.innerHTML = "<img src='/TRACv2/public/images/delete_icon.png' alt='suppr'>";
		deleteIcon.onclick = function(){
			var parent = this.parentNode;
			parent.parentNode.removeChild(parent);
		};
		elem.appendChild(input);
		elem.appendChild(span);
		elem.appendChild(deleteIcon);
		getById("files-0").parentNode.appendChild(elem);
		getById("files-0").value = "";
	}
	var sendButton = getById('sendButton');
	/**
	 * Onclick event du formulaire principal : vérification du formulaire
	 * des pièces jointes. Si le formulaire est correcte et les pièces jointes aussi envoi
	 * d'un mail et fermture de la page
	 * Sinon affichage d'un message d'erreur
	 */
	sendButton.onclick = function(){
		
		var msg = getById('errorMsg');	//p du message d'erreur
		msg.innerHTML = "";

                //vérification de l'ajout d'un piece jointe (demande de validation s'il n'y en a pas)
                var pj = document.getElementsByClassName('uploadFiles');
                if ( pj.length > 0 ){
                    //il y a au moins une PJ
                }else {
                    var ret = confirm("Le message ne contient pas de pièces jointes, envoyer quand même ?");
                    if(ret){
                        //l'utilisateur à dit oui
                    }else {
                        return false;
                    }
                }
		
		//vérification de la taille des fichiers
		if(!checkFilesSize(document.getElementsByClassName('uploadFiles'))){
			msg.innerHTML += "Fichiers trop volumineux.";
			flashMessage("Vos pièces jointes sont trop volumineuses");
			return false;
		}
		// si aucun destinataire n'est entré, on ne peut pas envoyer le mail
		if(getById('to').value.trim() == '' && getById("to-autocomplete").value.trim() == ''){
			msg.innerHTML += "Aucun destinataire n'a été entré.";
			return false;
		}

		//vérification de la saisie des mails
		var emailFieldIds = ['to','cc','cci'];
		var bValid = true;

		for(var i = 0; i < emailFieldIds.length ; i++){
			var isCorrect = true; //vérifie si ce champs spécifique est faux
			var field = getById(emailFieldIds[i]);
			var tab = field.value.split(";");

			//pour toutes les adresses extraits de la chaine, on vérifie le format et si elle n'est pas vide
			for(var j = 0 ; j < tab.length ; j++){
				tab[j] = tab[j].trim();
				if(tab.length == 1 && tab[0] == "") break;
				// si l'email est vide on le supprime de la liste
				if(tab[j] == ''){
					// removes 1 element from index j
					tab.splice(j, 1);
					j--;
				}
				else{
					isCorrect = isCorrect & checkEmailAddress(tab[j]);
				}
			}
			if(!isCorrect){
				bValid = false;
				addStateError($(field));
				msg.innerHTML += 'Des champs sont incorrects';
			}
			if(tab.length == 1 && tab[0] == "")
				field.value = '';
			
			//on verifie si les champs d'auto complétion possèdent une adresse
			//correcte. Dans ce cas on ajoute au champs qui va être envoyé en AJAX
			// sinon on signale l'erreur
			if(getById(emailFieldIds[i]+"-autocomplete").value.trim() != ""){
				if(!addToAddressField(emailFieldIds[i]))
				{
					bValid = false;
					msg.innerHTML += 'L\'adresse est incorrecte';
				}
			}
		}

		if(bValid){
			getById("sendButton").style.display = "none";
			if(window.location.hostname.indexOf('localhost') === -1
					&& window.location.hostname.indexOf('dvrftwsc01') === -1)
				getById("errorMsg").innerHTML = "<img src='/images/loading.gif' class='loading' alt='loading'>";
			else
				getById("errorMsg").innerHTML = "<img src='/TRACv2/public/images/loading.gif' class='loading' alt='loading'>";
			
			var formulaireEnvoi = getById("formMail");
			var xhr = new XMLHttpRequest();
			
			//get the content of the ckeditor
			var valueOfCkeditor = CKEDITOR.instances['content'].getData();
			xhr.open('POST', formulaireEnvoi.action, true);
			
			//formData va contenir les données à envoyer en POST par Ajax
			//ici formData reçoit toutes les données du form
			var formData = new FormData(formulaireEnvoi);
			
			//ajouter le contenu du ckEditor au formData(autrement,
			// on ne peut pas envoyer la valeur)
			formData.append('content', valueOfCkeditor);
                        
                        //On ajoute la destination => interne/externe
                        var dest = document.getElementById("dest").value;
			formData.append('dest', dest);
			
			//we add the value of the span "type" to know what date to update
			formData.append('type', getById("type_of_email").innerHTML);
			
			// we use the same asynchronous treatment for both actions
			xhr.onload = function () {
				if (xhr.status === 200) {
					//on teste si le mails a bien été envoyé
					var response = JSON.parse(xhr.responseText);
					if(response['code'] != '0'){
						getById("sendButton").style.display = "block";
						flashMessage(response['msg']);
						getById('errorMsg').innerHTML = response['msg'];
					}
					else{
						//une fois le mail envoyé on peut quitter la page
						getById('content').innerHTML = "<p class='importantMessage'>"+response['msg']+"</p>";
						getById('content').innerHTML += "<p>Cette fenêtre se fermera automatiquement au bout de 3 secondes</p>";
						window.opener.location.reload();
						setTimeout(function(){
							window.close();
						}, 3000);
					}
				}
				else {
					alert('An error occurred!');
				}
			};
			
			xhr.send(formData);
		}
		return false;
	}
	
	//add to icon to add email adresses
	var icons = document.getElementsByClassName("add-to");
	Array.prototype.forEach.call (icons, function (icon) {
		icon.onclick = function(){
			addToAddressField(this.id.replace("add-to-", ""));
		}
	});
}


/**
 * Check if this is a valid email address
 * @param email string to test
 * @returns {Boolean} true if it is an email address, else false
 */
function checkEmailAddress(email){
	var regexp = /^[A-Za-z0-9\.\-\_]{1,117}\@[A-Za-z0-9\-\.]{1,117}\.(com|net|fr)$/;
	if (!(regexp.test(email)))
		return false;
	else
		return true;
}

/**
 * Remove the error highlightning to an object
 * Only accept Jquery Object
 * @param o the Jquery object
 */
function addStateError( o ){
	o.addClass( "ui-state-error" );
}

/**
 * Check if the files exceeds the files capacity by POST supported by PHP
 * MAX_FILE_SIZE is a field created by Zend Framework, initialized by php consts
 * @param arrayFiles
 * @returns {Boolean} true : the limit is not exceeded, otherwise false 
 */
function checkFilesSize(arrayFiles){
	var sizeFile =0;
	for(var indexName = 0; indexName < arrayFiles.length; indexName++){
		//check if input file or input text
		if(arrayFiles[indexName].getAttribute("type") == "file"){
			for(var i=0; i<arrayFiles[indexName].files.length; i++){
				sizeFile += arrayFiles[indexName].files[i].size;
			}
		}
		if(arrayFiles[indexName].getAttribute("type") == "text"){
			
		}
	}

	if(sizeFile>=parseInt(getById('MAX_FILE_SIZE').value)){
		return false;
	}
	return true;
}

/**
 * Add email address typed/searched by autocompletion in the hidden field. This hidden field
 * is posted with the submission of the form
 * @param field field where the address is typed/autocomplete
 * @returns {Boolean} true : the address is correct and added to the hidden field, a bubble with the email
 * address appears. False : the address is incorrect
 */
function addToAddressField(field){
	var address = getById(field+"-autocomplete").value;
	if(!checkEmailAddress(address)){
		getById(field+"-autocomplete").setAttribute('class', getById(field+"-autocomplete").getAttribute('class')+" ui-state-error");
		return false;
	}
	else{
		if(getById(field).value === "")
			getById(field).value = address;
		else
			getById(field).value = getById(field).value+";"+address;
		
		getById(field+"-autocomplete").value = "";
		
		if(getById(field+"-autocomplete").getAttribute('class') != null)
			getById(field+"-autocomplete").setAttribute('class', getById(field+"-autocomplete").getAttribute('class').replace(" ui-state-error", ""));

		getById(field+"-email_cards").appendChild(createBubbleEmail(address, field));
		return true;
	}
}

/**
 * Create a "bubble" : this is an HTML element containing the email address
 * It is more readable for the user
 * Theses elements can be deleted, so it results that the address is deleted in the hidden field 
 * @param email address
 * @param field
 * @returns an HTML element : a bubble
 */
function createBubbleEmail(email, field){
	var elem = document.createElement('P');
	elem.innerHTML = email;
	elem.setAttribute("class", field+"Mail email_card");
	icon = document.createElement("IMG");
	icon.className = "deleteIconEmail";
	if(window.location.hostname.indexOf('localhost') === -1
			&& window.location.hostname.indexOf('dvrftwsc01') === -1)
		icon.src = "/images/delete_icon.png";
	else
		icon.src = "/TRACv2/public/images/delete_icon.png";
	elem.appendChild(icon);
	icon.onclick = function(){
		var parent = this.parentNode;
		var mailToDelete = parent.innerHTML.substring(0,parent.innerHTML.indexOf("<"));
		var field = parent.className.replace("Mail email_card","");
		parent.parentNode.removeChild(parent);
		getById(field).value = getById(field).value.replace(mailToDelete, '');
		getById(field).value = getById(field).value.replace(";;", ';');
	};
	return elem;
}
