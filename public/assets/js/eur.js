$(function() {

    function get_refresh_data(dispobj){

        var disp_body = "#" + dispobj + "_tbody";
        var cache_object = "#" + dispobj + "_cache";
        var btc_path = $(disp_body).attr("data-path");
        var cache_data = $(cache_object).val();

        $.ajax({
            url: btc_path,
            dataType: "json",
            data: cache_data,
            contentType: "application/json; charset=utf-8",
            type: 'POST',
            error: function(){
                // will fire when timeout is reached
                console.log("page error " + dispobj);
            },
            success: function(response){
                //do something
                console.log("page refresh");
                $(cache_object).val(response.data);
                $(disp_body).html(response.text);
            },
            complete: function () {
                setTimeout(function(){get_refresh_data(dispobj);}, 1000);
            }
        });

    }

    get_refresh_data("btc_eur");

});