var checkSession = function (okRedirect, failRedirect) {
    okRedirect = typeof okRedirect === 'undefined' ? '/' : okRedirect
    failRedirect = typeof failRedirect === 'undefined' ? LOGIN_URL : failRedirect
    $.ajax({
        url: SL_API + '/credentials',
        type: "GET",
        dataType: 'json',
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            if (data && data.session) {
                $.ajax({
                    url: '/login/createSession',
                    type: "POST",
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function (sessionData) {
                        console.log(sessionData)
                        if (sessionData.status && sessionData.status === 'OK') {
                            // window.location.href = okRedirect
                            return true
                        }
                        // window.location.href = failRedirect
                        return true
                    },
                    error: function (xhr, ajaxOptions, thrownError) { //Add these parameters to display the required response
                        alert('Sua autenticação expirou ou é inválida, faça o login novamente.')
                        console.log(xhr.status);
                        console.log(xhr.responseText);
                        // window.location.href = failRedirect
                        return true
                    }
                });
            } else {
                // window.location.href = LOGIN_URL
            }
        },
        error: function (xhr, ajaxOptions, thrownError) { //Add these parameters to display the required response
            alert('Falha ao obter as credenciais de acesso.')
            console.log(xhr.status);
            console.log(xhr.responseText);
            // window.location.href = failRedirect
            return true
        }
    });
};

export default checkSession;