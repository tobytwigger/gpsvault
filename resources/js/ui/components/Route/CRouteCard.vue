<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
        <v-img
            v-if="routeModel.cover_image"
            :src="routeModel.cover_image"
            height="200px"
        ></v-img>


        <v-card-title>
            {{routeModel.name}}
        </v-card-title>

        <v-card-subtitle v-if="routeModel.description">
            {{ routeModel.description }}
        </v-card-subtitle>

        <v-card-text>
            <v-chip
                outlined
                class="ma-2"
                color="indigo"
            >
                <v-icon left>
                    mdi-ruler
                </v-icon>
                {{ convertDistance(routeModel.distance) }}
            </v-chip>
        </v-card-text>

        <v-card-actions>
            <v-btn
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('route.show', routeModel.id))"
            >
                View
            </v-btn>

            <v-spacer></v-spacer>

        </v-card-actions>

    </v-card>
</template>

<script>
import moment from 'moment';
import units from '../../mixins/units';

export default {
    name: "CRouteCard",
    mixins: [units],
    props: {
        routeModel: {
            required: true,
            type: Object
        }
    },
    methods: {
        convertDistance(value) {
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
