<script>
import axios from 'axios';
import { format } from 'date-fns';
export default {
    data() {
        return {
            messages: [],
            messageText: "",
        }
    },

    methods: {
        formatTime(dateString) {
            return format(new Date(dateString), 'HH:mm');
        },

        async contactData(){
            await axios.get("/contacts/192714047");
        },

        async fetchMessages() {
            try {
                const response = await axios.get("/messages/192714047");
                this.messages = response.data;
            } catch (error) {
                console.error("Failed to fetch messages:", error);
            }
        },

        sendMessage() {
            if (this.messageText.trim() !== "") {
                axios.post(
                    `/send/`,
                    {
                        type: "text",
                        content: this.messageText,
                        watermark: Math.floor(Date.now() / 1000)
                    },
                )
                    .then(response => {
                        console.log("Message sent:", response.data);
                        this.messageText = "";
            })

                    .catch(error => console.error("Error sending message:", error));
            }
        }
    },
    mounted() {
        this.fetchMessages();
        setInterval(() => {
            this.fetchMessages();
        }, 1500);

        this.contactData();
        setInterval(() => {
            this.contactData();
        }, 500);
    },

    computed: {
        sortedMessages() {
            return this.messages.sort((a, b) => new Date(a.delivered_at) - new Date(b.delivered_at));
        },
        groupedMessages() {
            const grouped = {};

            this.sortedMessages.forEach(message => {
                const date = new Date(message.delivered_at);
                const dateString = date.toLocaleDateString();

                if (!grouped[dateString]) {
                    grouped[dateString] = [];
                }
                grouped[dateString].push(message);
            });

            return grouped;
        }
    },

}


</script>

<template>
    <div>
        <section>
            <div class="container py-5">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-10 col-lg-8 col-xl-6">
                        <div class="card" id="chat2">
                            <div class="card-header d-flex justify-content-between align-items-center p-3">
                                <h5 class="mb-0">Chat</h5>
                            </div>

                            <div class="card-body chat-window" style="position: relative; height: 400px; overflow-y: auto;">
                                <div v-for="(messages, date) in groupedMessages" :key="date">

                                    <div class="divider d-flex align-items-center mb-4">
                                        <p class="text-center mx-3 mb-0" style="color: #a2aab7;">{{ date }}</p>
                                    </div>
                                    <div v-for="message in messages" :key="message.id" class="mb-4">
                                        <div v-if="message.sender_type === 'operator' || message.sender_type === 'project'" class="d-flex flex-row justify-content-end">
                                            <div class="message-bubble operator-message">
                                                <p>
                                                    {{ message.message }}
                                                    <em class="em1">{{ formatTime(message.delivered_at) }}</em>
                                                </p>
                                            </div>
                                            <img :src="message.image"
                                                 alt="Operator Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        </div>

                                        <div v-else class="d-flex flex-row justify-content-start">
                                            <img :src="message.image"
                                                 alt="Contact Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <div class="message-bubble contact-message">
                                                <p>
                                                    {{ message.message }}
                                                    <em class="em1">{{ formatTime(message.delivered_at) }}</em>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                                <input type="text" class="form-control form-control-lg" id="exampleFormControlInput1"
                                       v-model="messageText" placeholder="Type message">
                                <a class="ms-3"><svg @click="sendMessage()" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
                                    <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
                                </svg></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>

p{
    margin-bottom: 0;
}

em{
    font-size: smaller;
}

.em1 {
    display: block;
    text-align: right;
    font-size: smaller;
}


.d-flex > div {
    margin-left: 13px;
    margin-right: 13px;
}

.chat-window {
    height: 400px;
    overflow-y: auto;
}

#chat2 .form-control {
    border-color: transparent;
}

#chat2 .form-control:focus {
    border-color: transparent;
    box-shadow: inset 0px 0px 0px 1px transparent;
}

.divider:after,
.divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
}

.operator-message {
    background-color: #4a90e2;
    text-align: left;
    border-top-right-radius: 0;
}

.contact-message {
    background-color: #737373;
    text-align: left;

}

.message-bubble {
    max-width: 70%;
    padding: 10px;
    border-radius: 15px;
    margin-bottom: 5px;
    position: relative;
    color: #fff;
    font-size: 14px;

}


</style>
