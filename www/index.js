/**
 * Created by egorov on 31.03.2015.
 */
s('#samsonphp_w3c_controller').pageInit(function(token){
    // Perform asynchronous request
    s.ajax(token.a('href'), function(response){
        try {
            response = JSON.parse(response);
            if (response.html) {
                // Draw panel to DOM
                var panel = s(response.html);
                s(document.body).append(panel);

                // Hide panel button
                s('.w3c-panel_close-btn', panel).click(function(){
                   panel.remove();
                });
            }
        } catch(e) {

        }
    });
});