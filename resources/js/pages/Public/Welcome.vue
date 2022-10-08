<template>
    <c-public-wrapper title="Welcome">

        <div class="row">
            <div class="col">
                <v-parallax
                    dark
                    src="https://cdn.vuetifyjs.com/images/backgrounds/vbanner.jpg"
                >
                    <v-row
                        align="center"
                        justify="center"
                    >
                        <v-col
                            class="text-center"
                            cols="12"
                        >
                            <h1 class="text-h2 font-weight-black mb-4">
                                GPS Vault
                            </h1>
                            <h4 class="subheading mb-4">
                                Your cycling data, your way
                            </h4>
                            <v-btn
                                class="align-self-end"
                                fab
                                outlined
                                @click="$vuetify.goTo('#about')"
                            >
                                <v-icon>mdi-chevron-double-down</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-parallax>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <section id="about">
                    <div class="py-12"></div>

                    <v-container class="text-center">
                        <h2 class="display-2 font-weight-bold mb-3">Our Aim</h2>

                        <v-responsive
                            class="mx-auto mb-8"
                            width="56"
                        >
                            <v-divider class="mb-1"></v-divider>

                            <v-divider></v-divider>
                        </v-responsive>

                        <v-responsive
                            class="mx-auto title font-weight-light mb-8"
                            max-width="720"
                        >
                            Take control of your cycling data. From planning routes and tours to analysing activities and backing up your rides, your data stays safe and always accessible.
                        </v-responsive>

                        <div></div>

                        <v-btn
                            color="grey"
                            :href="route('register')"
                            outlined
                            large
                        >
            <span class="grey--text text--darken-1 font-weight-bold">
              Sign Up
            </span>
                        </v-btn>
                    </v-container>

                    <div class="py-12"></div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <section
                    id="features"
                    class="grey lighten-3"
                >
                    <div class="py-12"></div>

                    <v-container class="text-center">
                        <h2 class="display-2 font-weight-bold mb-3">FEATURES</h2>

                        <v-responsive
                            class="mx-auto mb-12"
                            width="56"
                        >
                            <v-divider class="mb-1"></v-divider>

                            <v-divider></v-divider>
                        </v-responsive>

                        <v-row>
                            <v-col
                                v-for="({ icon, title, text }, i) in features"
                                :key="i"
                                cols="12"
                                md="4"
                            >
                                <v-card
                                    class="py-12 px-4"
                                    color="grey lighten-5"
                                    flat
                                >
                                    <v-theme-provider dark>
                                        <div>
                                            <v-avatar
                                                color="primary"
                                                size="88"
                                            >
                                                <v-icon
                                                    large
                                                    v-text="icon"
                                                ></v-icon>
                                            </v-avatar>
                                        </div>
                                    </v-theme-provider>

                                    <v-card-title
                                        class="justify-center font-weight-black text-uppercase"
                                        v-text="title"
                                    ></v-card-title>

                                    <v-card-text
                                        class="subtitle-1"
                                        v-text="text"
                                    >
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-container>

                    <div class="py-12"></div>
                </section>


            </div>
        </div>

        <div class="row">
            <div class="col">
                <section id="stats">
                    <v-parallax
                        :height="$vuetify.breakpoint.smAndDown ? 700 : 500"
                        src="https://images.unsplash.com/photo-1510915228340-29c85a43dcfe?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80"
                    >
                        <v-container fill-height>
                            <v-row class="mx-auto">
                                <v-col
                                    v-for="{icon, url, title} of stats"
                                    :key="title"
                                    cols="12"
                                    md="3"
                                >
                                    <div class="text-center">
                                        <v-icon
                                            color="white"
                                            class="display-3 text-white mb-4"
                                        >{{icon}}</v-icon>

                                        <div
                                            class="title font-weight-regular text-uppercase"
                                            v-text="title"
                                        ></div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-parallax>
                </section>
            </div>
        </div>
    </c-public-wrapper>
</template>

<script>
import CPublicWrapper from 'ui/layouts/CPublicWrapper';

export default {
    name: "Welcome",
    components: {CPublicWrapper},
    data() {
        return {
            features: [
                {
                    title: 'Pixel Perfect Design',
                    icon: 'mdi-fountain-pen-tip',
                    text: 'Lorem ipsum'
                },
                {
                    title: 'Retina Ready',
                    icon: 'mdi-cellphone',
                    text: 'Lorem ipsum'
                },
                {
                    title: 'Easily Customizable',
                    icon: 'mdi-pencil-box-outline',
                    text: 'Lorem ipsum'
                },
                {
                    title: 'Image Parallax',
                    icon: 'mdi-image-size-select-actual',
                    text: 'Lorem ipsum'
                },
                {
                    title: 'Seo Optimized',
                    icon: 'mdi-ice-pop',
                    text: 'Lorem ipsum'
                },
                {
                    title: '24/7 Support',
                    icon: 'mdi-help-circle-outline',
                    text: 'Lorem ipsum'
                },
            ],
            stats: [
                {
                    icon: 'mdi-currency-gbp',
                    url: null,
                    title: 'Free forever'
                },
                {
                    icon: 'mdi-github',
                    url: 'https://github.com/tobytwigger/gpsvault',
                    title: 'View on GitHub'
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>
