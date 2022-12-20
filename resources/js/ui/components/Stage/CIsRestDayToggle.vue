<template>
    <c-confirmation-dialog ref="confirmation" button-text="Convert" :loading="form.processing" :title="title" @confirm="submit">
        <template v-slot:activator="{trigger,showing}">
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        icon
                        @click="trigger"
                        :disabled="showing"
                        link
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>{{icon}}</v-icon>
                    </v-btn>
                </template>
                {{ tooltip }}
            </v-tooltip>
        </template>
        {{ body }}
    </c-confirmation-dialog>
</template>

<script>
import CRouteSelect from '../Route/CRouteSelect';
import CActivitySelect from '../Activity/CActivitySelect';
import CConfirmationDialog from '../CConfirmationDialog';

export default {
    name: "CIsRestDayToggle",
    components: {CConfirmationDialog, CActivitySelect, CRouteSelect},
    props: {
        oldStage: {
            required: true,
            type: Object,
        },
        tourId: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                is_rest_day: false,
                route_id: null,
                _method: 'patch'
            })
        }
    },
    mounted() {
        this.updateFromOldStage();
    },
    computed: {
        title() {
            if(this.oldStage.is_rest_day) {
                return 'Convert stage to cycling day';
            }
            return 'Convert stage to rest day';
        },
        tooltip() {
            if(this.oldStage.is_rest_day) {
                return 'Convert this stage from a rest day to a cycling day.';
            }
            return 'Convert this stage to a rest day.';
        },
        body() {
            if(this.oldStage.is_rest_day) {
                return 'This will keep all your notes and the stage description, but convert it to a cycling day so you can add a route.';
            }
            return 'This will detach the route from this stage and convert the stage to a rest day. It will not delete the route completely.';
        },
        icon() {
            if(this.oldStage.is_rest_day) {
                return 'mdi-sleep';
            }
            return 'mdi-sleep-off';
        }
    },
    methods: {
        updateFromOldStage() {
            this.form.is_rest_day = !this.oldStage.is_rest_day;
        },
        submit() {
            this.form.post(
                route('tour.stage.update', [this.tourId, this.oldStage.id]),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldStage();
                        this.$refs.confirmation.close();
                    }
                });
        }
    }
}
</script>

<style scoped>

</style>
