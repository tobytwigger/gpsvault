<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
        <v-card-title>
            {{place.name}}
        </v-card-title>

        <v-card-subtitle v-if="place.description">
            {{ place.description }}
        </v-card-subtitle>

        <v-card-actions>
            <v-btn
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('place.show', place.id))"
            >
                View
            </v-btn>

        </v-card-actions>

    </v-card>
</template>

<script>
import moment from 'moment';
import units from '../../mixins/units';
import CPlaceForm from './CPlaceForm';

export default {
    name: "CPlaceCard",
    components: {CPlaceForm},
    mixins: [units],
    props: {
        place: {
            required: true,
            type: Object
        },
        edit: {
            required: false,
            type: Boolean,
            default: true
        }
    },
    methods: {
        convertDistance(value) {
            if(value === null) {
                return 'No distance data';
            }
            let converted = this.convert(value, 'distance');
            return converted.value + converted.unit;
        },
        toDateTime(value) {
            if (value === null) {
                return 'No Date';
            }
            return moment(value).format('DD/MM/YYYY');
        },
    }
}
</script>

<style scoped>

</style>
