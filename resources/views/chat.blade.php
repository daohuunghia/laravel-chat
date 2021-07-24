<html>
<head>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="/style.css" type="text/css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->
    <script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.11.0/echo.common.min.js"></script>
</head>
<body>
<div>
    @foreach (\App\Models\User::all() as $user)
        <div>
            <a href="/login/{{ $user->id }}">{{ $user->name }}</a>
        </div>
    @endforeach
    <div>
        <a href="/logout">Logout</a>
    </div>
</div>
<div id="app" class="container">
    <h3 class=" text-center">Messaging: User: {{ optional(auth()->user())->name }}</h3>
    <div class="messaging">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h4>Recent</h4>
                    </div>
                    <div class="srch_bar">
                        <div class="stylish-input-group">
                            <input type="text" class="search-bar"  placeholder="Search" >
                            <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span> </div>
                    </div>
                </div>
                <div class="inbox_chat">
                    <div v-for="(user, k) in users" :key="k" class="chat_list">
                        <div class="chat_people">
                            <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                            <div class="chat_ib">
                                <h5>@{{ user.name }} <span class="chat_date">Dec 25</span></h5>
                                <p>Test, which is a new approach to have all solutions
                                    astrology under one roof.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mesgs">
                <div class="msg_history">
                    <div v-for="message in messages">
                        <div v-if="message.user.id !== id" class="incoming_msg">
                            <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                            </div>
                            <div class="received_msg">
                                <div class="received_withd_msg">
                                    <p>@{{ message.message }}</p>
                                    <span class="time_date"> 11:01 AM | June 9</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="outgoing_msg">
                            <div class="sent_msg">
                                <p>@{{ message.message }}</p>
                                <span class="time_date"> 11:01 AM | June 9</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="type_msg">
                    <div class="input_msg_write">
                        <input @keyup.enter="sendMessage" v-model="message" type="text" class="write_msg" placeholder="Type a message" />
                        <button @click="sendMessage" class="msg_send_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center top_spac"> Design by <a target="_blank" href="https://www.linkedin.com/in/sunil-rajput-nattho-singh/">Sunil Rajput</a></p>
    </div>
</div>
<script>
    new Vue({
        el: '#app',
        data () {
            return {
                message: '',
                users: [],
                messages: [],
                id: {{ auth()->id() }},
            }
        },
        methods: {
            sendMessage () {
                axios.post('/message', { message: this.message })
                this.messages.push({ message: this.message, user: { id: this.id }})
                this.message = ''
            }
        },
        mounted() {
            const echo = new Echo({
                broadcaster: "socket.io",
                host: 'http://localhost:6001'
            })
            echo.join('chat')
            .here((users) => {
                this.users = users
            }).joining((user) => {
                this.users.push(user)
            }).leaving((user) => {
                const indexUser = this.users.findIndex(item => item.id === user.id)
                if (indexUser > -1) {
                    this.users.splice(indexUser, 1)
                }
            })
            .listen('SendMessage', (data) => {
                this.messages.push({ message: data.message, user: { id: data.user.id }})
            })
        }
    })
</script>
</body>
</html>
