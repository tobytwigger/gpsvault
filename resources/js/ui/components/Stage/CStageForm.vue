<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">

                        <v-text-field
                            id="name"
                            v-model="form.name"
                            label="Name"
                            hint="A name for the stage"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <c-route-select
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                            label="Route"
                            hint="The route you're planning to follow on this day"
                            id="route_id"
                            v-model="form.route_id"
                        >

                        </c-route-select>
                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="form.processing"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        {{ buttonText }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import CRouteSelect from '../Route/CRouteSelect';

export default {
    name: "CStageForm",
    components: {CRouteSelect},
    props: {
        oldStage: {
            required: false,
            type: Object,
            default: null
        },
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
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
                name: null,
                stage_number: null,
                route_id: null,
                tour_id: this.tourId,
                _method: this.oldStage ? 'patch' : 'post'
            })
        }
    },
    mounted() {
        this.updateFromOldStage();
    },
    methods: {
        updateFromOldStage() {
            if(this.oldStage) {
                this.form.name = this.oldStage.name;
                this.form.stage_number = this.oldStage.stage_number;
                this.form.route_id = this.oldStage.route_id;
            }
        },
        submit() {
            this.form.post(
                this.oldStage
                    ? route('stage.update', this.oldStage.id)
                    : route('stage.store'),
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
