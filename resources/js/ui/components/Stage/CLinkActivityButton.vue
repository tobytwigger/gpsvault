<template>
    <div>
        <c-activity-select
            :error-messages="form.errors.hasOwnProperty('activity_id') ? [form.errors.activity_id] : []"
            label="Activity"
            hint="The activity you're planning to follow on this day"
            id="activity_id"
            v-model="form.activity_id">
            <template #activator="{trigger, showing}">
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            color="deep-purple lighten-2"
                            text
                            v-bind="attrs"
                            v-on="on"
                            :loading="showing"
                            @click.stop="trigger"
                            :disabled="showing"
                        >
                            Link Activity
                        </v-btn>
                    </template>
                    Link a activity to this stage
                </v-tooltip>
            </template>
        </c-activity-select>
    </div>
</template>

<script>
import CGridTable from '../../reusables/table/CGridTable';
import CInfiniteScrollIterator from '../../reusables/table/CInfiniteScrollIterator';
import CApiScrollIterator from '../../reusables/table/CApiScrollIterator';
import CActivityCard from '../Activity/CActivityCard';
import CActivitySelect from '../Activity/CActivitySelect';
export default {
    name: "CLinkActivityButton",
    components: {CActivitySelect, CActivityCard, CApiScrollIterator, CInfiniteScrollIterator, CGridTable},
    props: {
        stage: {
            required: true,
            type: Object,
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                activity_id: null,
                _method: 'patch'
            }),
            query: null,
            loading: false
        }
    },
    watch: {
        activityId(val) {
            if(val !== null) {
                this.submit();
            }
        }
    },
    computed: {
        activityId() {
            return this.form.activity_id;
        }
    },
    methods: {
        submit() {
            this.loading = true;
            this.form.post(route('tour.stage.update', [this.stage.tour_id, this.stage.id]),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.reset();
                    },
                    onFinish: () => this.loading = false
                });
        },
    }
}
</script>

<style scoped>

</style>
