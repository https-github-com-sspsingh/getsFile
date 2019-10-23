$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () 
    {
        if(o[this.name] !== undefined) 
        {
            if (!o[this.name].push) 
            {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } 
        else    {o[this.name] = this.value || '';}
    });
    return o;
};

$(function () {
    var IMG_PREFIX = 'img/swan-bus.JPG';
	
    (function () 
	{		
        (function()
        {
            /* EXPIRY - NOTIFICATIONS */
            var ID = $("#expiry_notifyID").val();

            if(parseInt(ID) > 0)
            {
                var countID = 1;
                for(var srID = 1; srID <= parseInt(ID); srID++)
                {
                    Lobibox.notify((countID == 1 ? 'info' :(countID == 2 ? 'default' :(countID == 3 ? 'warning' :(countID == 4 ? 'error' : 'success')))), {
                    img: IMG_PREFIX,
                    size: 'mini',
                    delay: 50000,
                    rounded: false,
                    position: 'bottom right',
                    title: $("#expiry_name_"+srID).val(),
                    msg: $("#expiry_type_"+srID).val() + ' , Expiry Date : ' + $("#expiry_date_"+srID).val()
                    });
                    
                    countID++;
                    countID = countID == 6 ? 1 : countID;
                }
            }
            
            /* COMPLAINT - NOTIFICATIONS */
            /*var compID = $("#duedate_notifyID").val();
            if(parseInt(compID) > 0)
            {
                var countID = 1;
                for(var srID = 1; srID <= parseInt(compID); srID++)
                {
                    Lobibox.notify((countID == 1 ? 'info' :(countID == 2 ? 'default' :(countID == 3 ? 'error' :(countID == 4 ? 'success' : 'warning')))), {
                    img: IMG_PREFIX,
                    size: 'mini',
                    delay: 50000,
                    rounded: false,
                    position: 'left bottom',
                    title: $("#complaint_refno_"+srID).val(),
                    /*msg: $("#expiry_serdate_"+srID).val() + ' , ' + $("#expiry_duedate_"+srID).val()*/
					/*msg: $("#expiry_duedate_"+srID).val()
                    });
                    
                    countID++;
                    countID = countID == 6 ? 1 : countID;
                }
            }*/
        })();
    })();
});