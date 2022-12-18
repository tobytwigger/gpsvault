<template>
    <c-public-wrapper title="Contact">

        <div class="row">
            <div class="col">
                <v-parallax
                    class="v-parallax-opaque-image"
                    src="/dist/images/public/contact-hero.jpg"
                >
                    <v-row
                        align="center"
                        justify="center"
                    >
                        <v-col
                            class="text-center"
                            cols="12"
                        >
                            <h1 class="text-h1 font-weight-black mb-4" :class="{'black--text': !$vuetify.theme.dark, 'white--text': $vuetify.theme.dark}">
                                Contact Us
                            </h1>
                            <v-btn
                                class="align-self-end"
                                fab
                                :color="$vuetify.theme.dark ? 'white' : 'black'"
                                outlined
                                @click="$vuetify.goTo('#email')"
                            >
                                <v-icon>mdi-chevron-double-down</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-parallax>
            </div>
        </div>

        <v-row align="center"
               justify="center"
               class="mb-3">
            <v-col cols="12"
                    md="4"
                    sm="8">
                <section id="email">
                    <v-form @submit.prevent="submit">
                        <v-card>
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
                                    label="Subject *"
                                    hint="Message Subject"
                                    name="subject"
                                    required
                                    type="text"
                                    :error="form.errors.hasOwnProperty('subject')"
                                    :error-messages="form.errors.hasOwnProperty('subject') ? [form.errors.subject] : []"
                                ></v-text-field>

                                <v-textarea
                                    id="content"
                                    v-model="form.content"
                                    label="Content *"
                                    required
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
                </section>
            </v-col>
        </v-row>



    </c-public-wrapper>
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
