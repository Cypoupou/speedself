/**
 * Created by DDJK5031 on 23/03/2018
 */

window.onload = function(){
    
    //Entree
    $( function() {
        $( "#sortable1, #sortable2" ).sortable({
            connectWith: ".connectedSortable1"
        }).disableSelection();
    } );
    $( function() {
        $( "#sortable1" ).sortable({
          items: "li:not(.ui-state-disabled)"
        });
        $( "#sortable2" ).sortable({
          cancel: ".ui-state-disabled"
        });
        $( "#sortable1 li, #sortable2 li" ).disableSelection();
    } );

    //Plat
    $( function() {
        $( "#sortable3, #sortable4" ).sortable({
            connectWith: ".connectedSortable2"
        }).disableSelection();
    } );
    $( function() {
        $( "#sortable3" ).sortable({
          items: "li:not(.ui-state-disabled)"
        });
        $( "#sortable4" ).sortable({
          cancel: ".ui-state-disabled"
        });
        $( "#sortable3 li, #sortable4 li" ).disableSelection();
    } );

    //Dessert
    $( function() {
        $( "#sortable5, #sortable6" ).sortable({
            connectWith: ".connectedSortable3"
        }).disableSelection();
    } );
    $( function() {
        $( "#sortable5" ).sortable({
          items: "li:not(.ui-state-disabled)"
        });
        $( "#sortable6" ).sortable({
          cancel: ".ui-state-disabled"
        });
        $( "#sortable5 li, #sortable6 li" ).disableSelection();
    } );
    
    
    
    
    var boutonModification = document.getElementsByClassName("saveButton");
    boutonModification[0].onclick = function () {
        
        var date = $(this).attr('data-date');
        var entree = [];
        $('#sortable2 li').each(function () {
            entree.push(this.id); //id
        });
        var plat = [];
        $('#sortable4 li').each(function () {
            plat.push(this.id); //id
        });
        var dessert = [];
        $('#sortable6 li').each(function () {
            dessert.push(this.id); //id
        });
        
        var formulaireEnvoi = getById("editdayForm");
        var xhr = new XMLHttpRequest();
        xhr.open('POST', formulaireEnvoi.action, true);
       
        //formData va contenir les données à envoyer en POST par Ajax
        //ici formData reçoit toutes les données du form
        var formData = new FormData(formulaireEnvoi);
        
        formData.append('date', date);
        formData.append('entree', entree);
        formData.append('plat', plat);
        formData.append('dessert', dessert);
        
        xhr.onload = function () {
            //alert(xhr.status);
            if (xhr.status === 200) {
                console.log(xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                
                if (response['code'] == '-1') {
                    flashMessageFalse(response['msg']);
                }
                if (response['code'] == '1') {
                    if(window.location.hostname.indexOf('localhost') === -1){
                        window.location.href = '/accueil';//response['url'];
                    }else{
                        window.location.href = '/speedself/public/accueil';
                    }
                }
            }else {
                alert('An error occurred!');
            }
        };

        xhr.send(formData);
        return false;
    };
    
};

