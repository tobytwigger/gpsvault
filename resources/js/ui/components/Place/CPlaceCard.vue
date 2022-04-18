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

            <v-spacer></v-spacer>

            <v-tooltip bottom v-if="place.url">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        icon
                        link
                        :href="place.url"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-earth</v-icon>
                    </v-btn>
                </template>
                View Website
            </v-tooltip>

            <v-tooltip bottom v-if="place.user_id === $page.props.user.id">
                <template v-slot:activator="{ on, attrs }">
                    <c-place-form :old-place="place" title="Edit place" button-text="Update">
                        <template v-slot:activator="{trigger,showing}">

                            <v-btn
                                icon
                                v-bind="attrs"
                                v-on="on"
                                :disabled="showing"
                                @click="trigger"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                    </c-place-form>
                </template>
                Edit Place
            </v-tooltip>




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
