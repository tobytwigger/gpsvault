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

        <v-card-text>
            <v-chip
                outlined
                class="ma-2"
                color="indigo"
            >
                <v-icon left>
                    mdi-ruler
                </v-icon>
                {{ convertDistance(place.distance) }}
            </v-chip>
<!---->
            <v-chip
                outlined
                color="indigo"
            >
                <v-icon left>
                    mdi-calendar-range
                </v-icon>
                {{ toDateTime(place.started_at) }}
            </v-chip>
        </v-card-text>

<!--            <v-list-item-avatar-->
<!--                tile-->
<!--                size="80"-->
<!--                color="grey"-->
<!--            ></v-list-item-avatar>-->
<!--        </v-list-item>-->

        <v-card-actions>
            <v-btn
                color="deep-purple lighten-2"
                text
                @click="$inertia.get(route('place.show', place.id))"
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
    name: "CPlaceCard",
    mixins: [units],
    props: {
        place: {
            required: true,
            type: Object
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
