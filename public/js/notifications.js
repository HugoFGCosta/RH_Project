function fetchNotifications() {
    $.ajax({
        url: '/notifications/unreadCount', // Certifique-se de que esta rota está correta
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.unread_count > 0) {
                $('#notification-bell').show();
            } else {
                $('#notification-bell').hide();
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
        }
    });
}

$(document).ready(function() {
    fetchNotifications();
    setInterval(fetchNotifications, 60000); // Verifica a cada minuto
});
