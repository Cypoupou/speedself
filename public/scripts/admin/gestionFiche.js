/**
 * 
 */

window.onload = function(){
    
    var deleteFiche = document.getElementsByClassName("deleteFiche");
    
    deleteFiche[0].onclick = function () {
        
        var NumberFiche = $(this).data('number');
        
        if(NumberFiche != 0){

            var ret = confirm("Etes-vous sûr de vouloir supprimer ?");
            if (ret) {

                var xhr = new XMLHttpRequest();
                    if(window.location.hostname.indexOf('localhost') === -1)
                        xhr.open('POST', '/admin/deletefiche', true);
                    else
                        xhr.open('POST', '/TRACv2/public/admin/deletefiche', true);


                //formData va contenir les données à envoyer en POST par Ajax
                //ici formData reçoit toutes les données du form
                var formData = new FormData();
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        if (response['id'] == "0") {
                            flashMessage("Erreur lors de la suppression, veuillez contacter un administrateur");
                        }else {                
                            location.reload(true);
                            flashMessage("Suppresion réussie!");
                        }
                    }else {
                        alert('An error occurred!');
                    }
                };

                xhr.send(formData);
                return false;
            }
        }
    };
    
}