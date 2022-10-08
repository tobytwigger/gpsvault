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
                            <h1 class="text-h4 font-weight-thin mb-4">
                                GPS Vault
                            </h1>
                            <h4 class="subheading mb-4">
                                Your cycling data, your way
                            </h4>
                            <v-btn
                                class="align-self-end"
                                fab
                                outlined
                                @click="$vuetify.goTo('#features')"
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


                <section id="features">
                    <v-sheet>
                        <div class="base-section-heading text-center mb-12">
                            <h1 class="text-uppercase text-h5 font-weight-bold mb-2 text-center"> Theme Features </h1>
                            <v-divider/>

                            <p class="base-body text-body-1 mx-auto grey--text text--darken-1 text-center mb-10"
                               style="max-width: 700px;"> Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero, in!
                                Asperiores, impedit libero. Veniam rerum saepe unde nihil possimus quibusdam esse accusamus
                                mollitia
                                magni fuga. </p>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-12">

                                    <div class="pt-2 mb-8 d-flex" v-for="feature in features">
                                        <v-avatar :icon="feature.icon">

                                        </v-avatar>
                                        <div>
                                    <span
                                        class="text-uppercase"
                                    >{{feature.title}}</span>

                                            <div
                                                class="mx-auto"
                                            >
                                                {{feature.text}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </v-sheet>
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
                    color: 'primary',
                    dark: true,
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
            ]
        }
    }
}
</script>

<style scoped>

</style>
