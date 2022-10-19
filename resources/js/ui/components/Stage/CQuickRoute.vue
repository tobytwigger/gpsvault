<template>
    <div class="text-center">
        <v-form @submit.prevent="submit">
            <div class="d-flex">
                <c-route-select
                    :error-messages="form.errors.hasOwnProperty('route_id') ? [form.errors.route_id] : []"
                    label="Route"
                    hint="The route you're planning to follow on this day"
                    id="route_id"
                    v-model="form.route_id"
                >
                </c-route-select>
                <v-btn type="submit">Link</v-btn>
            </div>
        </v-form>
        <v-row wrap no-gutters>
            <v-col cols="5" class="text-center">
                <v-divider />
            </v-col>
            <v-col cols="2" class="text-center">
                OR
            </v-col>
            <v-col cols="5" class="text-center">
                <v-divider />
            </v-col>
        </v-row>

        <v-btn link @click="$inertia.visit(route('planner.create'))">Plan a route</v-btn>
    </div>
</template>

<script>
import CRouteSelect from '../Route/CRouteSelect';

export default {
    name: "CQuickRoute",
    components: {CRouteSelect},
    props: {
        oldStage: {
            required: false,
            type: Object,
            default: null
        },
        tourId: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                route_id: null,
                _method: 'patch'
            })
        }
    },
    mounted() {
        this.updateFromOldStage();
    },
    methods: {
        updateFromOldStage() {
            this.form.route_id = this.oldStage.route_id;
        },
        submit() {
            this.form.post(route('tour.stage.update', [this.tourId, this.oldStage.id]),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldStage();
                        this.showDialog = false;
                    }
                });
        },
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
