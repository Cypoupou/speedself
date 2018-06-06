/**
 * Created by DDJK5031 on 23/03/2018
 */

window.onload = function(){
    
    // Mise en place du total du panier
    var total = '<test class="total">'+0+'<test>';
    $("#total").after(total);
    
    // Clic sur une checkbox ==> calcul du total par rapport aux checkbox checké
    $("input[type='checkbox']").change(function() {
        var checkedValue = 0; 
        var inputElements = document.getElementsByClassName('check');
        for(var i=0; inputElements[i]; ++i){
            if(inputElements[i].checked){
                checkedValue += Number(inputElements[i].value);
            }
        }
        $(".total").remove();
        var total = '<test class="total">'+checkedValue+'<test>';
        $("#total").after(total);
    });
    
    
    
    var boutonReservation = document.getElementsByClassName("saveButton");
    boutonReservation[0].onclick = function () {
        
        var date = $(this).attr('data-date');
        var total = 0; 
        var inputElements = document.getElementsByClassName('check');
        for(var i=0; inputElements[i]; ++i){
            if(inputElements[i].checked){
                total += Number(inputElements[i].value);
            }
        }
        
        var formulaireEnvoi = getById("reservationForm");
        var xhr = new XMLHttpRequest();
        xhr.open('POST', formulaireEnvoi.action, true);
       
        //formData va contenir les données à envoyer en POST par Ajax
        //ici formData reçoit toutes les données du form
        var formData = new FormData(formulaireEnvoi);
        
        formData.append('date', date);
        formData.append('total', total);
        
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
                        window.location.href = '/repas/reservation';//response['url'];
                    }else{
                        window.location.href = '/speedself/public/repas/reservation';
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
