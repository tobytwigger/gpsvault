<template>
    <v-list two-line dense>
        <v-list-item v-for="group in schema" :key="group.title">
            <v-list-item-icon>
                <v-tooltip left>
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                            v-bind="attrs"
                            v-on="on"
                            color="indigo">
                            {{ group.icon }}
                        </v-icon>
                    </template>
                    <span>{{ group.title }}</span>
                </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content v-for="stat in group.data" :key="stat.value.unit + stat.label">
                <v-list-item-title>{{ stat.value.value }} {{ stat.value.unit }}</v-list-item-title>
                <v-list-item-subtitle>{{ stat.label }}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action v-if="selectable && !group.disabled">
                <v-switch :value="selected.indexOf(group.label) > -1" @change="toggleStatGroup(group.label, $event)">
                    <v-icon color="grey lighten-1">mdi-chart-line</v-icon>
                </v-switch>
            </v-list-item-action>
        </v-list-item>
        <slot name="append" v-bind:selected="selected" v-bind:toggle-stat-group="toggleStatGroup">

        </slot>
    </v-list>
</template>

<script>
export default {
    name: "CStats",
    props: {
        schema: {
            required: true,
            type: Array
        },
        limit: {
            required: false,
            type: Number,
            default: null
        },
        selectable: {
            required: false,
            type: Boolean,
            default: false
        },
        value: {
            required: false,
            type: Array,
            default: () => []
        },
        multiple: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    methods: {
        toggleStatGroup(label, selected) {
            let opts = this.selected;
            if(selected) {
                if(opts.indexOf(label) === -1) {
                    if(this.multiple) {
                        opts.push(label);
                    } else {
                        opts = [label];
                    }
                }
            } else {
                if(opts.indexOf(label) > -1) {
                    opts.splice(opts.indexOf(label), 1)
                }
            }
            this.selected = opts;
        }
    },
    computed: {
        selected: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val)
            }
        },
    }
}
</script>

<style scoped>

</style>
