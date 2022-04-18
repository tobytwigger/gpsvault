<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
<!--        <v-img-->
<!--            v-if="tour.cover_image"-->
<!--            :src="tour.cover_image"-->
<!--            height="200px"-->
<!--        ></v-img>-->


        <v-card-title>
            {{tour.name}}
        </v-card-title>

        <v-card-subtitle v-if="tour.description">
            {{ tour.description }}
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
                {{ convertDistance(tour.distance) }}
            </v-chip>

            <v-chip
                outlined
                color="indigo"
            >
                <v-icon left>
                    mdi-calendar-today
                </v-icon>
                {{ tour.stages.length }} day ride
            </v-chip>

<!--            <v-chip-->
<!--                outlined-->
<!--                color="indigo"-->
<!--            >-->
<!--                <v-icon left>-->
<!--                    mdi-image-filter-hdr-->
<!--                </v-icon>-->
<!--                {{ convertElevation(tour.elevation_gain) }}-->
<!--            </v-chip>-->
        </v-card-text>

        <v-card-actions>
            <v-btn
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('tour.show', tour.id))"
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
    name: "CTourCard",
    mixins: [units],
    props: {
        tour: {
            required: true,
            type: Object
        }
    },
    methods: {
        convertDistance(value) {
            if(value) {
                let converted = this.convert(value, 'distance');
                return converted.value + converted.unit;
            }
        },
        convertElevation(value) {
            if(value) {
                let converted = this.convert(value, 'elevation');
                return converted.value + converted.unit;
            }
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
