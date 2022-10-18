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

        <div v-if="errors.length > 0">
            {{ errors }}
        </div>
    </c-form-section>
</template>

<script>
import CFormSection from './CFormSection';
import draggable from "vuedraggable";
import settingCard from '../../mixins/settingCard';

export default {
    name: 'CStatsOrderSetting',
    components: {
        CFormSection,
        draggable
    },
    mixins: [settingCard],

    data() {
        return {
            sources: {
                php: {text: 'GPS Vault', value: 'php', description: 'Our own analysis of your activities.'},
                strava: {text: 'Strava', value: 'strava', description: 'Analysis done by Strava.'},
            }
        }
    },

    computed: {
        schema: {
            get() {
                return this.value.map(id => this.sources[id])
            },
            set(val) {
                this.value = val.map(source => source.value);
            }
        }
    }
}
</script>
