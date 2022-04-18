<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-select
                            id="type"
                            v-model="form.type"
                            :error="form.errors.hasOwnProperty('type')"
                            :error-messages="form.errors.hasOwnProperty('type') ? [form.errors.type] : []"
                            :items="types"
                            hint="What type of place is this?"
                            item-text="label"
                            item-value="value"
                            label="Place Type"
                            name="type"
                        ></v-select>

                        <v-text-field
                            id="name"
                            v-model="form.name"
                            label="Name"
                            hint="A name for the place"
                            name="name"
                            type="text"
                            :error="form.errors.hasOwnProperty('name')"
                            :error-messages="form.errors.hasOwnProperty('name') ? [form.errors.name] : []"
                        ></v-text-field>

                        <v-textarea
                            id="description"
                            v-model="form.description"
                            label="Description"
                            hint="A description for the place"
                            name="description"
                            :error="form.errors.hasOwnProperty('description')"
                            :error-messages="form.errors.hasOwnProperty('description') ? [form.errors.description] : []"
                        ></v-textarea>

                        <v-text-field
                            id="url"
                            v-model="form.url"
                            label="URL"
                            hint="The website of the place"
                            name="url"
                            type="text"
                            :error="form.errors.hasOwnProperty('url')"
                            :error-messages="form.errors.hasOwnProperty('url') ? [form.errors.url] : []"
                        ></v-text-field>

                        <v-text-field
                            id="phone_number"
                            v-model="form.phone_number"
                            label="Phone Number"
                            hint="A phone number for the place"
                            name="phone_number"
                            type="text"
                            :error="form.errors.hasOwnProperty('phone_number')"
                            :error-messages="form.errors.hasOwnProperty('phone_number') ? [form.errors.phone_number] : []"
                        ></v-text-field>

                        <v-text-field
                            id="address"
                            v-model="form.address"
                            label="Address"
                            hint="An address for the place"
                            name="address"
                            type="text"
                            :error="form.errors.hasOwnProperty('address')"
                            :error-messages="form.errors.hasOwnProperty('address') ? [form.errors.address] : []"
                        ></v-text-field>

                        <v-text-field
                            id="email"
                            v-model="form.email"
                            label="Email"
                            hint="An email for the place"
                            name="email"
                            type="text"
                            :error="form.errors.hasOwnProperty('email')"
                            :error-messages="form.errors.hasOwnProperty('email') ? [form.errors.email] : []"
                        ></v-text-field>

                        <c-location-input
                            v-model="form.location"
                        ></c-location-input>


                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="form.processing"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        {{ buttonText }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>

import CLocationInput from '../CLocationInput';
export default {
    name: "CPlaceForm",
    components: {CLocationInput},
    props: {
        oldPlace: {
            required: false,
            type: Object,
            default: null
        },
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                name: null,
                description: null,
                type: null,
                phone_number: null,
                url: null,
                address: null,
                email: null,
                location: {lat: null, lng: null}
            }),
            types: [
                {label: 'Food & Drink', value: 'food_drink'},
                {label: 'Shops', value: 'shops'},
                {label: 'Amenities', value: 'amenities'},
                {label: 'Tourist', value: 'tourist'},
                {label: 'Accommodation', value: 'accommodation'},
                {label: 'Other', value: 'other'},
            ]
        }
    },
    mounted() {
        this.updateFromOldPlace();
    },
    methods: {
        updateFromOldPlace() {
            if(this.oldPlace) {
                this.form.name = this.oldPlace.name;
                this.form.description = this.oldPlace.description;
                this.form.type = this.oldPlace.type;
                this.form.phone_number = this.oldPlace.phone_number;
                this.form.url = this.oldPlace.url;
                this.form.address = this.oldPlace.address;
                this.form.email = this.oldPlace.email;
                this.form.location = {lng: this.oldPlace?.location?.coordinates[0] ?? null, lat: this.oldPlace?.location?.coordinates[1] ?? null};
            }
        },
        submit() {
            this.form.submit(
                this.oldPlace ? 'patch' : 'post',
                this.oldPlace
                    ? route('place.update', this.oldPlace.id)
                    : route('place.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.updateFromOldPlace();
                        this.showDialog = false;
                    }
                });
        },
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
