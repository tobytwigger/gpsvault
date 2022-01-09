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
                {{ toKilometers(activity.distance) }}km
            </v-chip>

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

            <v-btn
                icon
                @click="showExtra = !showExtra"
            >
                <v-icon>{{ showExtra ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
            </v-btn>
        </v-card-actions>

        <v-expand-transition>
            <div v-show="showExtra">
                <v-divider></v-divider>

                <v-card-text>
                    I'm a thing. But, like most politicians, he promised more than he could deliver. You won't have time for sleeping, soldier, not with all the bed making you'll be doing. Then we'll go with that data file! Hey, you add a one and two zeros to that or we walk! You're going to do his laundry? I've got to find a way to escape.
                </v-card-text>
            </div>
        </v-expand-transition>

    </v-card>
</template>

<script>
import moment from 'moment';

export default {
    name: "CActivityCard",
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showExtra: false
        }
    },
    methods: {
        toKilometers(value) {
            return Math.round(value / 10) / 100;
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
