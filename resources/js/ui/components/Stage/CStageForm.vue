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

                        <v-switch
                            id="is_rest_day"
                            v-model="form.is_rest_day"
                            label="Rest day?"
                            hint="Is this a rest day?"
                            name="is_rest_day"
                            :error="form.errors.hasOwnProperty('is_rest_day')"
                            :error-messages="form.errors.hasOwnProperty('is_rest_day') ? [form.errors.is_rest_day] : []"
                        ></v-switch>

                        <v-text-field
                            id="stage_number"
                            v-model="form.stage_number"
                            label="Stage"
                            hint="A number for the stage"
                            name="stage_number"
                            type="number"
                            :error="form.errors.hasOwnProperty('stage_number')"
                            :error-messages="form.errors.hasOwnProperty('stage_number') ? [form.errors.stage_number] : []"
                        ></v-text-field>

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

                        <v-textarea
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the stage"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-date-picker
                            id="date"
                            v-model="form.date"
                            label="Date"
                            hint="A date for the stage"
                            name="date"
                            :error="form.errors.hasOwnProperty('date')"
                            :error-messages="form.errors.hasOwnProperty('date') ? [form.errors.date] : []"
                        ></v-date-picker>

                        <c-route-select
                            :error-messages="form.errors.hasOwnProperty('route_id') ? [form.errors.route_id] : []"
                            label="Route"
                            hint="The route you're planning to follow on this day"
                            id="route_id"
                            v-model="form.route_id"
                        >

                        </c-route-select>

                        <c-activity-select
                            :error-messages="form.errors.hasOwnProperty('activity_id') ? [form.errors.activity_id] : []"
                            label="Activity"
                            hint="The activity associated with this day"
                            id="activity_id"
                            v-model="form.activity_id"
                        >
                        </c-activity-select>
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
import CActivitySelect from '../Activity/CActivitySelect';

export default {
    name: "CStageForm",
    components: {CActivitySelect, CRouteSelect},
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
                description: null,
                stage_number: null,
                route_id: null,
                tour_id: this.tourId,
                activity_id: null,
                date: null,
                is_rest_day: false,
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
                this.form.description = this.oldStage.description;
                this.form.stage_number = this.oldStage.stage_number;
                this.form.route_id = this.oldStage.route_id;
                this.form.activity_id = this.oldStage.activity_id;
                this.form.date = this.oldStage.date;
                this.form.is_rest_day = this.oldStage.is_rest_day;
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
