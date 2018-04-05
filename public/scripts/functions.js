/**
 * get an element by its id
 * @param string id id of the element
 */
function getById(id){
	return document.getElementById(id);
}

/**
 * Reconnaissance de la fonction trim en JS
 */
if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	}
}

/**
 * Create a highlighted span on the top of the page with a message
 * The message disappears 2 sec after 
 * @param message message to show
 */
function flashMessage(message){
	var span = document.createElement('span');
	span.innerHTML = message;
	span.className = "flashMessage";
	document.body.appendChild(span);
	setTimeout(function(){
		$(span).fadeOut(1000);
		setTimeout(function(){
			span.parentNode.removeChild(span);
		}, 2000);
	}, 4000);
}

