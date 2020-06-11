function ajaxRequest(type, url, callback, data = null){

    let xhr = new XMLHttpRequest();

    if(type !== "POST"){
        if(data != null){
            url+="?"+data;
        }
    }

    xhr.open(type, url);

    if(type === "POST"){
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    xhr.onload = () => {
        httpErrors(xhr.status);
        if(callback!=null){
            callback(xhr.responseText);
        }
    };

    if(type === "POST"){
        xhr.send(data);
    }else{
        xhr.send();
    }

}

function httpErrors(errorCode){
    switch (errorCode) {
        case 200:
            break;
        case 201:
            break;
        case 400: alert("Bad Request");
            break;
        case 401: alert("Unauthorized");
            break;
        case 403: alert("Forbidden");
            break;
        case 404: alert("Not Found");
            break;
        case 500: alert("Server Error");
            break;
        case 503: alert("Server Unavailable");
             break;
             default:
             }
}