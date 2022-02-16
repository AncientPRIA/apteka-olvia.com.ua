const refresh = () => {
    console.log('csrf_refresh');
    var url = baseUrl + '/csrf_refresh';
    $.ajax({
        url: url,
        type: 'POST',
        cache: false,
        success: function success(response) {
            if (response['status'] === '1') {
                $('meta[name="csrf-token"]').attr('content', response['content']);
            } else {
                console.log('CSRF refresh failed');
            }
        },
        error: function error(response) {
            console.log("error", response);
        }
    });
};

module.exports = {
    refresh: refresh,
};