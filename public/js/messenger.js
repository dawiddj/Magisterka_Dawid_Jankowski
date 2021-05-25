/*!
 * jQuery plugin
 * Wtyczka obsługująca messenger;
 */
(function($) {
    $.fn.messengerWindow = function(options) {
        var settings = $.extend({
            imagesUrlPrefix: ""
        }, options );

        var $getMessagesTimeout = null; // obiekt przechowujący timeout/wyzwalacz funkcji pobierającej listę wiadomości;
        var $getMessagesTimestamp = 0; // data ostatniego pobrania wiadomości w formacie timestamp;

        var $messageContentObj = $('#message-content'); // input/pole tekstowe, w którym wpisujemy wiadomość;
        var $sendMessageButton = $('button#send-message'); // przycisk "Wyślij"
        var $messengerChatObj = $('#messenger-chat'); // <div>, w którym wyświetlane są wiadomości;

        var $notificationModal = $('#notificationModal'); // modal do wyświetlania powiadomień
        var $notificationsCounter = $('#notificationsCounter'); // licznik nieodczytanych powiadomień
        var $notificationsCountLabel = $('#notificationsCountLabel'); // etykieta z liczbą powiadomień;

        /**
         * Funkcja wysyłająca wiadomość;
         */
        var submitMessage = function() {
            clearTimeout($getMessagesTimeout);

            // pobieramy treść wiadomości z pola tekstowego;
            var $messageContent = $messageContentObj.val();

            var formData = new FormData($('#messenger-form')[0]);

            // zmieniamy tekst w przycisku i wyłączamy go;
            $sendMessageButton.html('<i class="fa fa-refresh fa-spin"></i> WYSYŁANIE...').attr('disabled', true);

            $.ajax({
                url: Routing.generate('app_messenger_message_save'),
                data: formData,
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    // reset formularza
                    $('#messenger-form')[0].reset();

                    // przywracamy przycisk do stanu wyjściowego;
                    $sendMessageButton.html('<i class="fa fa-share"></i> Wyślij').attr('disabled', false);

                    // uruchamiamy pobranie listy wiadomości w celu wyświetlenia nowo wysłanej wiadomości;
                    getMessagesList(10);
                },
                error: function(error) {
                    $sendMessageButton.html('<i class="fa fa-share"></i> Wyślij').attr('disabled', false);
                    getMessagesList(10);
                }
            });
        };

        /**
         * Funkcja pobierająca listę wiadomości do messengera;
         * @param $timeoutTimer
         */
        var getMessagesList = function($timeoutTimer) {
            if (!$timeoutTimer) { // nie podano opóźnienia? Ustawiamy na 2 sekundy;
                $timeoutTimer = 2000;
            }

            var searchPhrase = $('#search-message-input').val();

            // przerywamy timeout z pobieraniem wiadomości, jeśli taki istnieje, aby nie doszło do zwielokrotnienia żądań;
            clearTimeout($getMessagesTimeout);

            // ustawiamy od nowa wyzwalacz właściwej funkcji;
            $getMessagesTimeout = setTimeout(function() {
                $.ajax({
                    url: Routing.generate('app_messenger_messages_ajax_list'),
                    data: {
                        time: $getMessagesTimestamp,
                        searchPhrase: searchPhrase
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        // sprawdzamy, czy okienko wiadomości jest przewinięte na sam dół;
                        var isScrolledDown = (($messengerChatObj[0].scrollHeight - $messengerChatObj.scrollTop() - $messengerChatObj.outerHeight()) < 1);

                        if (searchPhrase.length > 0) {
                            $('#messenger-chat').html('');
                        }

                        $.each(response.messages, function(messageId, messageData) {
                            if ($('.message-row[data-message-id='+messageId+']').length === 0) {
                                generateMessageOnList(messageData);
                            }
                        });

                        // jeśli jest to pierwsze uruchomienie (brak timestampa) albo okienko jest przewinięte w dół -> po wygenerowaniu wiadomości przewijamy na sam dół;
                        if ($getMessagesTimestamp === 0 || isScrolledDown) {
                            $messengerChatObj.scrollTop($messengerChatObj[0].scrollHeight);
                        }

                        generateOnlineUsers(response.onlineUsers); // wyświetlamy listę aktywnych użytkowników;

                        $getMessagesTimestamp = response.timestamp;

                        getMessagesList(2000); // rekurencja w przypadku powodzenia;
                    },
                    error: function() {
                        getMessagesList(2000); // rekurencja w przypadku błędu;
                    }
                });
            }, $timeoutTimer);
        };

        /**
         * Funkcja generująca pojedynczą wiadomość na liście
         */
        var generateMessageOnList = function(messageObj) {
            var message =
                '<div class="item message-row" data-message-id="'+messageObj.id+'">\n\
                    <img src="'+settings.imagesUrlPrefix+messageObj.userImagePath+'" alt="user image" class="'+(messageObj.offline ? 'offline' : '')+'">\
                    <p class="message">\n\
                        <a href="#" class="name">\n\
                        <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> '+messageObj.createdAt+'</small>\n\
                            '+messageObj.userPersonalities+' '+(messageObj.ownMessage ? '(JA)' : '')+'\n\
                        </a>\n\
                        '+messageObj.content+'\n\
                    </p>';

            if (messageObj.files) {
                for (var i in messageObj.files) {
                    var file = messageObj.files[i];

                    message += '\<div class="col-md-12 text-center">\
                        \<a href="/files/display/'+file.hash+'" target="_blank">\
                            \<img style="max-height:32px" src="/dist/img/icons/'+(file.mimeType.substr(0, 5) === 'image' ? 'image' : 'document')+ '.png" />\
                            '+file.fileName+'\
                        </a>\
                    </div>';
                }
            }

            message += '</div>';

            $('#messenger-chat').append(message);
        };

        /**
         * Funkcja wyświetlająca listę aktywnych użytkowników
         */
        var generateOnlineUsers = function(usersArray) {
            $('#online-users').html(''); // czyścimy tablicę użytkowników online;

            if (usersArray) {
                $.each(usersArray, function(userKey, userData) {
                    $('#online-users').append('\n\
                        <div class="col-md-12">\n\
                            <img style="max-width:25px; height:auto" src="'+settings.imagesUrlPrefix+userData.userImagePath+'" class="img-circle" alt="'+userData.firstName+' '+userData.lastName+'">\n\
                            <span class="hidden-xs">'+userData.firstName+' '+userData.lastName+'</span>\n\
                        </div>\n\
                    ')
                })
            }
        };

        // konstruktor: uruchomienie pobierania listy natychmiast po zainicjowaniu pluginu;
        this.each(function() {
            getMessagesList(100);
        });

        this.readNotification = function(button) {
            $notificationModal.find('.modal-title').html('Powiadomienie');
            $notificationModal.find('.modal-body').html(button.find('.notification-content').html());
            $notificationModal.modal('show');

            var notificationsCount = parseInt($notificationsCounter.data('notifications-count'));

            $.ajax({
                url: Routing.generate('app_notification_read', { id: button.data('notification-id') }),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        notificationsCount = (notificationsCount - 1);
                        $notificationsCounter.html(
                            ((notificationsCount > 0)
                                ? 'Masz nowe powiadomienia ('+notificationsCount+')'
                                : 'Nie masz nowych powiadomień')
                        );
                        $notificationsCountLabel.html(notificationsCount).toggle((notificationsCount > 1));
                    }
                },
                error: function() {

                }
            });
        };

        $(document).ready(function() {
            $(document).on('submit', '#messenger-form', function(e) {
                e.preventDefault(); // blokujemy wysłanie formularza z przeładowaniem strony - wszystko załatwiamy funkcją submitMessage()
            });

            $(document).on('click', 'input#message-content', function(e) {
                e.preventDefault(); // blokujemy wysłanie wiadomości po kliknięciu w same pole tekstowe
            });

            $(document).on('click', 'button#send-message', function() { // kliknięcie w przycisk "Wyślij" - uruchamiamy wysyłanie;
                submitMessage();
            });

            $(document).on('click', 'button#search-message', function() {
                getMessagesList(); // aktywujemy wyszukiwanie po wpisanej frazie
            });

            $(document).on('click', 'a.notification-button', function() {
                instance.readNotification($(this));
            });

            $(document).keyup(function(e) { // przechwytywanie naciśnięć klawiszy
                var code = e.which;

                if (code === 13) { // jeśli kod klawisza to 13 (ENTER) - wysyłamy wiadomość lub wyszukujemy listę wiadomości, w zależności co jest aktywne;
                    e.preventDefault();

                    if ($('#message-content').is(':focus')) {
                        submitMessage();
                    } else if ($('#search-message-input').is(':focus')) {
                        getMessagesList();
                    }
                }
            });
        });

        var instance = this;

        return this;
    };
})(jQuery);