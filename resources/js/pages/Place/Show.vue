<template>
    <c-app-wrapper :title="place.name">
        <v-row>
            <v-col>
                {{place.description}}
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <v-list flat>
                    <v-list-item v-if="place.url" :href="place.url">
                        <v-list-item-icon>
                            <v-icon>mdi-link</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title v-text="place.url"></v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item v-if="place.email" :href="'mailto:' + place.email">
                        <v-list-item-icon>
                            <v-icon>mdi-at</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title v-text="place.email"></v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item v-if="place.phone_number" :href="'tel:' + place.phone_number">
                        <v-list-item-icon>
                            <v-icon>mdi-phone</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title v-text="place.phone_number"></v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item v-if="place.address" :href="'tel:' + place.address">
                        <v-list-item-icon>
                            <v-icon>mdi-home</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title v-text="place.address"></v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <c-location-input :value="{lat: place.location.coordinates[1], lng: place.location.coordinates[0]}" :disabled="true"></c-location-input>
            </v-col>
        </v-row>

        <template #headerActions>
            <c-place-form :old-place="place" button-text="Update" title="Update place">
                <template v-slot:activator="{trigger, showing}">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                data-hint="Edit information about the place"
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="trigger"
                                :disabled="showing"
                            >
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </template>
                        <span>Add place</span>
                    </v-tooltip>
                </template>
            </c-place-form>
        </template>
    </c-app-wrapper>

</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CLocationInput from '../../ui/components/Map/CLocationInput';
import CPlaceForm from '../../ui/components/Place/CPlaceForm';
export default {
    name: "Show",
    components: {CPlaceForm, CLocationInput, CAppWrapper},
    props: {
        place: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
