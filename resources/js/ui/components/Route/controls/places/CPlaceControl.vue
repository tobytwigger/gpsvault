<template>
    <div>
        <v-row>
            <v-col style="margin: 10px;">
                <v-card
                    max-width="600"
                    class="mx-auto"
                >
                    <v-toolbar
                        color="light-blue"
                        dark
                    >
                        <v-toolbar-title>Places ({{places.length}})</v-toolbar-title>

                        <v-spacer></v-spacer>

<!--                        <v-btn icon @click="showSettings = !showSettings">-->
<!--                            <v-icon>mdi-cog</v-icon>-->
<!--                        </v-btn>-->
                    </v-toolbar>

                    <v-list
                        v-if="Object.keys(errors).length > 0"
                    >
                        <v-list-item v-for="error in Object.keys(errors)" :key="error">
                            <v-list-item-content>
                                <span class="red--text">{{errors[error][0]}}</span>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>

                    <v-list
                        style="overflow-y:auto;"
                        height="500px"
                        subheader
                        two-line>
                        <v-subheader>Filter place types</v-subheader>

                        <v-list-item-group
                            v-model="dynamicSelectedPlaceTypes"
                            multiple
                            active-class=""
                        >

                            <v-list-item
                                v-for="(placeType, index) in placeTypes" :key="placeType.key"
                                :value="placeType.key"
                            >
                                <template v-slot:default="{ active }">
                                    <v-list-item-action>
                                        <v-checkbox :input-value="active"></v-checkbox>
                                    </v-list-item-action>

                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon>{{placeType.icon}}</v-icon>
                                            {{ placeType.title }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle v-if="placeType.hasOwnProperty('description')">
                                            {{ placeType.description }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </template>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>


export default {
    name: "CPlaceControl",
    data() {
        return {
            placeTypes: [
                {key: 'food_drink', 'title': 'Food & Drink', 'icon': 'mdi-food-fork-drink'},
                {key: 'shops', 'title': 'Shops', 'icon': 'mdi-basket'},
                {key: 'tourist', 'title': 'Tourist Sites', 'icon': 'mdi-eiffel-tower'},
                {key: 'accommodation', 'title': 'Accommodation', 'icon': 'mdi-bed'},
                {key: 'water', 'title': 'Water', 'icon': 'mdi-water'},
                {key: 'toilets', 'title': 'Toilets', 'icon': 'mdi-toilet'},
                {key: 'other', 'title': 'Other', 'icon': 'mdi-crosshairs-question'},
            ],
            selectedPlaceTypes: [],
            places: [],
            errors: {}
        }
    },
    mounted() {
        this.selectedPlaceTypes = this.placeControl.visibleTypes;
        this.placeControl.onPlacesUpdated((places) => {
            this.places = places;
        });
        this.placeControl.onErrorsUpdated((errors) => {
            this.errors = errors;
        });
    },
    computed: {
        dynamicSelectedPlaceTypes: {
            get() {
                return this.selectedPlaceTypes;
            },
            set(val) {
                this.placeControl.setVisibleTypes(val);
                this.selectedPlaceTypes = val;
            }
        }
    },

    props: {
        placeControl: {
            required: true
        },
    },
}
</script>

<style scoped>
</style>
