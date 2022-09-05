<template>
    <c-form-section description="Change which data sources we'll show you first." title="Stats order">
        <v-list>
            <draggable
                v-model="schema"
            >
                <template v-for="(source, index) in schema">
                    <v-list-item :key="source.value">
                        <v-list-item-icon>
                            <v-icon>mdi-menu</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title v-text="source.text"></v-list-item-title>
                            <v-list-item-subtitle class="px-3" v-text="source.description"></v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                </template>
            </draggable>
        </v-list>

        <template #actions>
            <v-btn :disabled="form.processing" :loading="form.processing" @click="submit">
                Save
            </v-btn>
        </template>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';
import draggable from "vuedraggable";

export default {
    name: 'CStatsOrderSetting',
    components: {
        CFormSection,
        draggable
    },

    data() {
        return {
            form: this.$inertia.form({
                stats_order_preference: this.$setting.stats_order_preference
            }),
            sources: {
                php: {text: 'GPS Vault', value: 'php', description: 'Our own analysis of your activities.'},
                strava: {text: 'Strava', value: 'strava', description: 'Analysis done by Strava.'},
            }
        }
    },

    methods: {
        submit() {
            this.form.post(route('settings.store'), {
                errorBag: 'updateStatsOrderSetting',
                preserveScroll: true
            });
        },
    },

    computed: {
        schema: {
            get() {
                return this.form.stats_order_preference.map(id => this.sources[id])
            },
            set(val) {
                this.form.stats_order_preference = val.map(source => source.value);
            }
        }
    }
}
</script>
