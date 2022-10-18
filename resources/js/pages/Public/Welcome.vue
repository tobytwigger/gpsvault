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
                                color="white"
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
                        <h2 class="display-2 font-weight-bold mb-3">Features</h2>

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
                                    <div class="text-center" v-if="url !== null">
                                        <a :href="url" style="text-decoration: none; color: white" target="_blank">
                                            <v-icon
                                                color="white"
                                                style="cursor: pointer;"
                                                class="display-3 text-white mb-4"
                                            >{{icon}}</v-icon>

                                            <div
                                                class="title font-weight-regular text-uppercase"
                                            >
                                                {{title}}
                                            </div>
                                        </a>
                                    </div>
                                    <div class="text-center" v-else>
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
                    title: 'Analyse activities',
                    icon: 'mdi-fountain-pen-tip',
                    text: 'Automatically upload new rides. Browse in depth analysis and stats.'
                },
                {
                    title: 'Strava',
                    icon: 'mdi-cellphone',
                    text: 'Import all your rides from Strava, including photos.'
                },
                {
                    title: 'Routes',
                    icon: 'mdi-pencil-box-outline',
                    text: 'Plan a route'
                },
                {
                    title: 'Multi-day tours',
                    icon: 'mdi-image-size-select-actual',
                    text: 'Plan a tour spanning multiple days'
                },
                {
                    title: 'Backups',
                    icon: 'mdi-ice-pop',
                    text: 'Create a backup instantly at any time, and download it for safekeeping.'
                },
                {
                    title: 'Compare',
                    icon: 'mdi-help-circle-outline',
                    text: 'Switch between datasets to see the differences between analysis by Strava and us.'
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
                    title: 'View source code'
                },
                {
                    icon: 'mdi-sd',
                    url: null,
                    title: '20GB free storage'
                },
                {
                    icon: 'mdi-lock',
                    url: null,
                    title: 'Registration closed',
                    // icon: 'mdi-lock-open-variant',
                    // url: route('register')
                    // title: 'Registration open'
                }
            ]
        }
    }
}
</script>

<style scoped>

</style>
