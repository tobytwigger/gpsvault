<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >
        <v-img
            v-if="activity.cover_image"
            :src="activity.cover_image"
            height="200px"
        ></v-img>


        <v-card-title>
            {{activity.name}}
        </v-card-title>

        <v-card-subtitle v-if="activity.description">
            {{ activity.description }}
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
                {{ convertDistance(activity.distance) }}
            </v-chip>
<!---->
            <v-chip
                outlined
                color="indigo"
            >
                <v-icon left>
                    mdi-calendar-range
                </v-icon>
                {{ toDateTime(activity.started_at) }}
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
                @click="$inertia.get(route('activity.show', activity.id))"
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
    name: "CActivityCard",
    mixins: [units],
    props: {
        activity: {
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
