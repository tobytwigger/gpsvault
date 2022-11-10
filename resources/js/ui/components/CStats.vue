<template>
    <v-list two-line dense>
        <v-list-item v-for="(group, index) in schema" :key="group.title">
            <v-list-item-icon>
                <v-tooltip left>
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon
                            :data-hint="index === 0 ? 'You can hover over these icons to see what data they represent.' : null"
                            v-bind="attrs"
                            v-on="on"
                            color="indigo">
                            {{ group.icon }}
                        </v-icon>
                    </template>
                    <span>{{ group.title }}</span>
                </v-tooltip>
            </v-list-item-icon>

            <v-list-item-content v-for="stat in padData(group.data)" :key="stat.id ?? stat.value.unit + stat.label">
                <v-list-item-title>{{ stat.value.value }} {{ stat.value.unit }}</v-list-item-title>
                <v-list-item-subtitle>{{ stat.label }}</v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action v-if="selectable && !group.disabled">
                <v-btn icon @click="toggleStatGroup(group.label, $event)" :color="selected.indexOf(group.label) > -1 ? 'primary' : 'secondary'">
                    <v-icon>mdi-arrow-right-circle</v-icon>
                </v-btn>
<!--                    <v-icon color="grey lighten-1">mdi-chart-line</v-icon>-->
<!--                </v-switch>-->
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
        padData(data) {
            while(data.length < this.numberOfColumns) {
                data.push({
                    id: Math.random(),
                    value: {
                        value: null,
                        unit: null
                    },
                    label: null
                })
            }
            return data;
        },
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
        numberOfColumns() {
            let cols = 0;
            for(let group of this.schema) {
                cols = cols > group.data.length ? cols : group.data.length;
            }
            return cols;
        }
    }
}
</script>

<style scoped>

</style>
