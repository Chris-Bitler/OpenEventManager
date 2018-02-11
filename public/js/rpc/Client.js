class Client {

    /**
     * Create a new client with the specified url
     * @param url The url to the API
     * @constructor
     */
    constructor(url) {
        this.url = url;
        this.id = 1;
    }

    request(method, data, callback) {
        let request = {
            "jsonrpc": "2.0",
            "method": method,
            "params": data,
            "id": this.id
        };

        $.post(this.url, JSON.stringify(request)).done(function( data ) {
                let result = JSON.parse(data);
                if (result.id === request.id) {
                    callback(result);
                }
        });
    }


}
