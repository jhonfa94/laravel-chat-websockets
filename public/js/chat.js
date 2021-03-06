// const { default: axios } = require("axios");

// REPOSITORIO https://codepen.io/sajadhsm/pen/odaBdd
const msgerForm = get(".msger-inputarea");
const msgerInput = get(".msger-input");
const msgerChat = get(".msger-chat");
const PERSON_IMG = "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
const chatWith = get(".chatWith");
const chatStatus = get(".chatStatus");
const typing = get(".typing");
const chatId = window.location.pathname.split("/")[2];
let authUser;
let typingTimer = false;

window.onload = () => {
    // Get user data
    axios.get('/auth/user').then(res => {
        // console.log("res", res);
        authUser = res.data.authUser;
        // console.log(authUser);
    }).then(() => {
        // LISTADO DE USUARIOS
        axios.get(`/chat/${chatId}/get_users`).then(res => {
            // console.log("res", res);
            let results = res.data.filter(user => user.id != authUser.id);
            if (results.length > 0) {
                chatWith.innerHTML = results[0].name;
            }
            // console.log(authUser);
        }).catch(err => {
            console.log(err);
        })

    }).then(() => {
        axios.get(`/chat/${chatId}/get_messages`).then(res => {
            // console.log("res", res);
            appendMessages(res.data.messages);
        }).catch(err => {
            console.log(err);
        })

    })
        .then(() => {
            // LARAVEL ECHO
            Echo.join(`chat.${chatId}`)
                .listen('MessageSend', (e) => {
                    // console.log("Event: ", e);
                    // console.log("Event Message: ", e.message);
                    appendMessage(
                        e.message.user.name,
                        PERSON_IMG,
                        'left',
                        e.message.content,
                        formatDate(new Date(e.message.created_at))
                    );

                })
                .here(users => {
                    // console.log("Users: ", users);
                    // console.log(authUser);

                    let result = users.filter(user => user.id != authUser.id);
                    if (result.length > 0) {
                        chatStatus.className = 'chatStatus online';
                    }
                })
                .joining(user => {
                    // console.log("User: ", user);
                    if (user.id != authUser.id) {
                        chatStatus.className = 'chatStatus online';
                    }
                })
                .leaving(user => {
                    // console.log("User: ", user);
                    if (user.id != authUser.id) {
                        chatStatus.className = 'chatStatus offline';
                    }
                })
                .listenForWhisper('typing', (e) => {
                    // console.log(e);
                    if (e > 0)
                        typing.style.display = '';

                    if (typingTimer) {
                        clearTimeout(typingTimer);
                    }

                    typingTimer = setTimeout(() => {

                        typing.style.display = 'none';

                        typingTimer = false;

                    }, 3000);
                });

        })
        .catch(err => {
            console.log(err);
        });


}

msgerForm.addEventListener("submit", event => {

    event.preventDefault();

    const msgText = msgerInput.value;

    if (!msgText) return;

    axios.post('/message/send', {
        message: msgText,
        chat_id: 1
    }).then(res => {

        appendMessage(
            res.data.user.name,
            PERSON_IMG,
            'right',
            res.data.content,
            formatDate(new Date(res.data.created_at))
        );

    }).catch(error => {
        console.log(error);
    });

    msgerInput.value = "";

});

function appendMessages(messages) {
    let side = 'left';
    messages.forEach(message => {
        side = (message.user_id == authUser.id) ? 'right' : 'left';

        appendMessage(
            message.user.name,
            PERSON_IMG,
            side,
            message.content,
            formatDate(new Date(message.created_at))
        );
    });
}

function appendMessage(name, img, side, text, date) {
    //   Simple solution for small apps
    const msgHTML = `
    <div class="msg ${side}-msg">
      <div class="msg-img" style="background-image: url(${img})"></div>

      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">${name}</div>
          <div class="msg-info-time">${date}</div>
        </div>

        <div class="msg-text">${text}</div>
      </div>
    </div>
  `;

    msgerChat.insertAdjacentHTML("beforeend", msgHTML);
    // msgerChat.scrollTop += 500;
    scrollTopBottom();
}





// Utils
function get(selector, root = document) {
    return root.querySelector(selector);
}

function formatDate(date) {
    const d = date.getDate();
    const mo = date.getMonth() + 1;
    const y = date.getFullYear();
    const h = "0" + date.getHours();
    const m = "0" + date.getMinutes();
    return `${d}/${mo}/${y} ${h.slice(-2)}:${m.slice(-2)}`;
}

function scrollTopBottom() {
    msgerChat.scrollTop = msgerChat.scrollHeight;
}

function sendTypingEvent() {
    typingTimer = true;

    Echo.join(`chat.${chatId}`)
        .whisper('typing', msgerInput.value.length);
}

