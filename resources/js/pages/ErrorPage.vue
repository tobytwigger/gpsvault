<template>
    <c-vuetify-wrapper title="Something went wrong">
        <c-public-nav-bar title="Something went wrong" :drawer="false"></c-public-nav-bar>

        <v-main>
            <div class="py-12"></div>

            <v-container class="text-center">
                <v-responsive
                    class="mx-auto title font-weight-light mb-8"
                    max-width="400"
                >
                    <v-img
                        :src="'/dist/images/errors/' + imageName"
                        alt="404 error">

                    </v-img>
                </v-responsive>



                <h2 class="display-2 font-weight-bold mb-3">{{ status }} | {{title}}</h2>

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
                    <p>{{ description }}</p>

                    <p>If this problem persists, please contact support.</p>
                </v-responsive>

            </v-container>

            <div class="py-12"></div>

        </v-main>

        <c-footer></c-footer>
    </c-vuetify-wrapper>
</template>

<script>
import CVuetifyWrapper from '../ui/layouts/CVuetifyWrapper';
import CFooter from '../ui/components/Page/CFooter';
import CPublicNavBar from '../ui/components/Page/CPublicNavBar';
export default {
    name: "Error",
    components: {CPublicNavBar, CFooter, CVuetifyWrapper},
    props: {
        status: {
            required: true,
            type: Number
        },
        user: {
            required: false,
            type: Object,
            default: null
        }
    },
    data() {
        return {
            statuses: {
                403: {
                    title: 'Access denied',
                    imageName: '403.png',
                    description: 'You do not have permission to view this page.'
                },
                404: {
                    title: 'Page not found',
                    imageName: '404.png',
                    description: 'The page you were looking for could not be found. It may have moved or have been deleted.'
                },
                500: {
                    title: 'Something went wrong',
                    imageName: '500.png',
                    description: 'This is usually something wrong with our side. Please try again.'
                },
                503: {
                    title: 'In the garage',
                    imageName: '503.png',
                    description: 'The site is undergoing important maintenance. Please try again later.'
                }
            }
        }
    },
    computed: {
        imageName() {
            return this.statuses[this.status]?.imageName ?? '500.png';
        },
        title() {
            return this.statuses[this.status]?.title ?? 'Something went wrong';
        },
        description() {
            return this.statuses[this.status]?.description ?? 'Something went wrong during the last action. Please try again.';
        }
    }
}
</script>

<style scoped>

</style>
