<template>
    <c-auth-wrapper title="Contact">

        <v-form @submit.prevent="submit">
            <v-card>
                <v-card-title class="justify-center">
                    <span class="primary--text">Contact Us</span>
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        id="name"
                        v-model="form.name"
                        label="Name"
                        hint="Your name (optional)"
                        name="name"
                        type="text"
                        :error="form.errors.hasOwnProperty('name')"
                        :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                    ></v-text-field>

                    <v-text-field
                        id="email"
                        v-model="form.email"
                        label="Email"
                        hint="An email address we can get back to you at (optional)"
                        name="email"
                        type="email"
                        :error="form.errors.hasOwnProperty('email')"
                        :error-messages="form.errors.hasOwnProperty('email') ? [form.errors.email] : []"
                    ></v-text-field>

                    <v-text-field
                        id="subject"
                        v-model="form.subject"
                        label="Subject"
                        hint="Message Subject"
                        required
                        name="subject"
                        type="text"
                        :error="form.errors.hasOwnProperty('subject')"
                        :error-messages="form.errors.hasOwnProperty('subject') ? [form.errors.subject] : []"
                    ></v-text-field>

                    <v-textarea
                        id="subject"
                        v-model="form.content"
                        label="Content"
                        hint="Your message"
                        name="content"
                        :error="form.errors.hasOwnProperty('content')"
                        :error-messages="form.errors.hasOwnProperty('content') ? [form.errors.content] : []"
                    ></v-textarea>

                </v-card-text>
                <v-card-actions>
                    <v-btn :loading="form.processing" :disabled="form.processing" aria-label="Send Email" block color="primary"
                           type="submit">
                        <v-icon>mdi-send</v-icon>
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-form>

    </c-auth-wrapper>
</template>

<script>

import CPublicWrapper from 'ui/layouts/CPublicWrapper';
import CAuthWrapper from '../../ui/layouts/CAuthWrapper';

export default {
    name: "Contact",
    components: {CAuthWrapper, CPublicWrapper},
    data() {
        return {
            form: this.$inertia.form({
                name: null,
                email: null,
                subject: null,
                content: null,
            })
        }
    },
    methods: {
        submit() {
            this.form.post(route('contact.store'), {
                onSuccess: () => {
                    this.form.reset();
                    alert('Your message has been sent');
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
