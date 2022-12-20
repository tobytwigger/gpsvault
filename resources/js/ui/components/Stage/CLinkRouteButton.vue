<template>
    <div>
        <c-route-select
            :error-messages="form.errors.hasOwnProperty('route_id') ? [form.errors.route_id] : []"
            label="Route"
            hint="The route you're planning to follow on this day"
            id="route_id"
            v-model="form.route_id">
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
                            Link Route
                        </v-btn>
                    </template>
                    Link a route to this stage
                </v-tooltip>
            </template>
        </c-route-select>
    </div>
</template>

<script>
import CGridTable from '../../reusables/table/CGridTable';
import CInfiniteScrollIterator from '../../reusables/table/CInfiniteScrollIterator';
import CApiScrollIterator from '../../reusables/table/CApiScrollIterator';
import CRouteCard from '../Route/CRouteCard';
import CRouteSelect from '../Route/CRouteSelect';
export default {
    name: "CLinkRouteButton",
    components: {CRouteSelect, CRouteCard, CApiScrollIterator, CInfiniteScrollIterator, CGridTable},
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
                route_id: null,
                _method: 'patch'
            }),
            query: null,
            loading: false
        }
    },
    watch: {
        routeId(val) {
            if(val !== null) {
                this.submit();
            }
        }
    },
    computed: {
        routeId() {
            return this.form.route_id;
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
