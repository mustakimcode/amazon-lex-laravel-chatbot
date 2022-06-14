<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">Chat Component</div>

					<div class="card-body" v-if="!isEnded">
						<div class="row mb-3">
							<div class="mb-12" v-for="message in messages">
								<label for="email" class="col-md-12 text-md-start"
									>{{ message.sender_name ? message.sender_name : "you" }} :
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
						<div class="row mb-3">
							<label for="email" class="col-md-12 text-md-start">
								Email : {{ slots["Email"] }}
							</label>

							<label for="firstname" class="col-md-12 text-md-start">
								FirstName : {{ slots["FirstName"] }}
							</label>

							<label for="lastname" class="col-md-12 text-md-start">
								LastName : {{ slots["LastName"] }}
							</label>

<br>
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
				slots: {},
				newMessage: "",
				isEnded: false,
			};
		},
		methods: {
			addMessage(message) {
				axios.post("api/message/send", { message }).then((response) => {
					if (response.data.dialogState == "ReadyForFulfillment") {
						this.isEnded = true;
						this.slots = response.data.slots;
						setTimeout(function(){ window.location = "/home"; },3000);
					}
					if (response.data.messages) {
						response.data.messages.forEach((element) => {
							this.messages.push(element);
						});
					}

					this.newMessage = "";
				});
			},
			sendMessage() {
				if (this.newMessage.trim()) {
					this.addMessage(this.newMessage);
				}
			},
		},
	};
</script>
