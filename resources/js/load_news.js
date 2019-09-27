loadNews = function () {
    'use strict';
    
    var url = '/api/news';

    $.ajax(url, {
        accepts: {
            html: 'text/html'
        },
        method: 'GET',
        dataType: 'html',
        success: function (data) {
            $('#news-feed').html(data);
        }
    });
}
