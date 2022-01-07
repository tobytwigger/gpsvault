<template>
    <c-app-wrapper title="Your Activities">
        <v-row>
            <v-col cols="12" md="6" lg="4" xl="3" v-for="activity in activities.data" :key="activity.id">
                <c-activity-card :activity="activity"></c-activity-card>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-pagination
                    circle
                    v-model="page"
                    :length="activities.last_page"
                ></v-pagination>
            </v-col>
        </v-row>
    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/wrappers/CAppWrapper';
import CActivityCard from '../../ui/components/Activity/CActivityCard';
export default {
    name: "Index",
    components: {CActivityCard, CAppWrapper},
    props: {
        activities: {
            required: true,
            type: Object
        },
    },
    methods: {
        getPageUrl(page) {
            let url = new URL(this.activities.path);
            url.searchParams.set('page', page);
            return url.toString();
        }
    },
    computed: {
        page: {
            get() {
                return this.activities.current_page
            },
            set(page) {
                this.$inertia.get(this.getPageUrl(page));
            }
        }
    }
}
</script>

<style scoped>

</style>
