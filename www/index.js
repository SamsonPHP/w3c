/**
 * Created by egorov on 31.03.2015.
 */
s('#samsonphp_w3c_controller').pageInit(function(token){
    // Perform asynchronous request
    s.ajax(token.a('href'), function(response){
        try {
            response = JSON.parse(response);
            s.trace(response);
        } catch(e) {

        }
    });
});