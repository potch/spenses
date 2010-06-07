function async(url, params, callback) {
    
    var xhr=new XMLHttpRequest();
    
    var queryString = [];
    
    for (p in params) {
        queryString.push(p + "=" + encodeURIComponent(params[p]));
    }
    queryString = queryString.join("&");
    
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Content-length", queryString.length);
    xhr.setRequestHeader("Connection", "close");
 
	xhr.onreadystatechange = function() {
        if (xhr.readyState != 4)  { return; }
    	if (xhr.responseText) {
            callback(eval(xhr.responseText));
        }
	}
	
	xhr.open("POST", url, false);
	xhr.send(queryString);
}

function testAsync(result) {
    console.log(result);
}