<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">Chat Component</div>

					<div class="card-body" v-if="!isEnded">
						<div class="row mb-2">
							<div
								class="mb-12"
								v-for="message in messages"
								v-bind:key="message.id"
							>
								<label
									for="email"
									:class="
										message.sender_name == message.sender_id
											? 'col-md-12 text-md-start'
											: 'col-md-12 text-md-end'
									"
									>{{ message.sender_name ? message.sender_name : "You" }} :
									{{ message.message }}
								</label>
							</div>
							<br />
						</div>
						<div class="row mb-3">
							<div class="col-md-12">
								<input
									id="new-message"
									type="text"
									class="form-control"
									v-model="newMessage"
									name="new-message"
									required
									@keyup.enter="sendMessage"
								/>
							</div>
						</div>
					</div>
					<div class="card-body" v-else>
						<div class="row mb-2">
							<div
								class="mb-12"
								v-for="message in messages"
								v-bind:key="message.id"
							>
								<label
									for="email"
									:class="
										message.sender_name == message.sender_id
											? 'col-md-12 text-md-start'
											: 'col-md-12 text-md-end'
									"
									>{{ message.sender_name ? message.sender_name : "You" }} :
									{{ message.message }}
								</label>
							</div>
							<br />
							<label for="notice" class="col-md-12 text-md-start">
								chating session is done, this page will redirected in seconds!!
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				messages: [],
				newMessage: "",
				isEnded: false,
			};
		},
		created() {
			this.initChat();
		},
		methods: {
			initChat() {
				axios.post("api/message/init").then((response) => {
					this.fetchMessage(response.data.messages);
					this.newMessage = "";
				});
			},
			addMessage(message) {
				axios.post("api/message/send", { message }).then((response) => {
					if (response.data.dialogState == "ReadyForFulfillment") {
						this.isEnded = true;
						setTimeout((this.isEnded = true), 2000);
						setTimeout(function () {
							window.location = "/home";
						}, 5000);
					}
					this.fetchMessage(response.data.messages);
					this.newMessage = "";
				});
			},
			sendMessage() {
				if (this.newMessage.trim()) {
					this.addMessage(this.newMessage);
				}
			},
			fetchMessage(messages) {
				if (messages) {
					messages.forEach((element) => {
						this.messages.push(element);
					});
				}
			},
		},
	};
</script>
