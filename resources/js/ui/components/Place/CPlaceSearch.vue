<template>
    <div>
        <v-progress-circular
            v-if="loading"
            :size="70"
            :width="7"
            color="primary"
            indeterminate
        ></v-progress-circular>

        <div v-else-if="places === null">No places found</div>

        <c-pagination-iterator v-else :paginator="places" item-key="id">
            <template v-slot:default="{item}">
                <c-place-card :place="item" :add-to-route="true" @addToRoute="addToRoute"></c-place-card>
            </template>
        </c-pagination-iterator>
    </div>
</template>

<script>
import CPaginationIterator from '../CPaginationIterator';
import CPlaceCard from './CPlaceCard';
export default {
    name: "CPlaceSearch",
    components: {CPlaceCard, CPaginationIterator},
    props: {
        routeId: {
            required: true,
            type: Number
        }
    },
    data() {
        return {
            loading: false,
            places: null
        }
    },
    mounted() {
        this.loadPlaces();
    },
    methods: {
        addToRoute(place) {
            this.$emit('addToRoute', place);
        },
        loadPlaces() {
            this.loading = true;
            axios.get(route('place.search'), {params: {exclude_route_id: this.routeId}})
                .then(response => this.places = response.data)
                .then(() => this.loading = false);
        }
    }
}
</script>

<style scoped>

</style>
