<template>
    <c-public-wrapper title="Welcome">

        <v-row no-gutters>
            <v-col>
                <v-parallax
                    class="v-parallax-opaque-image"
                    src="/dist/images/public/welcome-hero-three.png"
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
                                GPS Vault
                            </h1>
                            <h4 class="text-h3 subheading mb-4" :class="{'black--text': !$vuetify.theme.dark, 'white--text': $vuetify.theme.dark}">
                                Your cycling data, your way
                            </h4>
                            <v-btn
                                class="align-self-end"
                                fab
                                :color="$vuetify.theme.dark ? 'white' : 'black'"
                                outlined
                                @click="$vuetify.goTo('#about')"
                            >
                                <v-icon>mdi-chevron-double-down</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-parallax>
            </v-col>
        </v-row>
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
                            Take control of your cycling data. From planning routes and tours to analysing activities
                            and backing up your rides, your data stays safe and always accessible.
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
                                    :color="'lighten-5 ' + ($vuetify.theme.dark ? 'black' : 'grey')"
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

        <v-row no-gutters>
            <v-col>
                <section id="stats">
                    <v-parallax
                        class="v-parallax-opaque-image"
                        :height="$vuetify.breakpoint.smAndDown ? 700 : 500"
                        src="/dist/images/public/welcome-hero.jpg"
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
                                                style="cursor: pointer;"
                                                class="display-3 text-white mb-4 features-coloured"
                                            >{{icon}}
                                            </v-icon>

                                            <div
                                                class="title font-weight-bold text-uppercase features-coloured"
                                            >
                                                {{title}}
                                            </div>
                                        </a>
                                    </div>
                                    <div class="text-center" v-else>
                                        <v-icon
                                            features-coloured
                                            class="display-3 text-white mb-4 features-coloured"
                                        >{{icon}}
                                        </v-icon>

                                        <div
                                            class="title font-weight-bold text-uppercase features-coloured"
                                            v-text="title"
                                        ></div>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-parallax>
                </section>
            </v-col>
        </v-row>
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
                    icon: 'mdi-bike',
                    text: 'Automatically upload new rides. Browse in depth analysis and stats.'
                },
                {
                    title: 'Strava',
                    icon: 'mdi-power-plug',
                    text: 'Import all your rides from Strava, including photos.'
                },
                {
                    title: 'Routes',
                    icon: 'mdi-routes',
                    text: 'Plan a route'
                },
                {
                    title: 'Multi-day tours',
                    icon: 'mdi-campfire',
                    text: 'Plan a tour spanning multiple days'
                },
                {
                    title: 'Backups',
                    icon: 'mdi-safe',
                    text: 'Create a backup instantly at any time, and download it for safekeeping.'
                },
                {
                    title: 'Compare',
                    icon: 'mdi-compare',
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
                    title: 'Open Source'
                },
                {
                    icon: 'mdi-sd',
                    url: null,
                    title: '20GB free storage'
                },
                {
                    icon: 'mdi-lock-open',
                    url: null,
                    title: 'Registration open',
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
    /*.features-coloured {*/
    /*    color: #30006a*/
    /*}*/
    /*i.features-coloured {*/
    /*    caret-color: #30006a;*/
    /*}*/
</style>
