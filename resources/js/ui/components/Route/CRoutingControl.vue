<template>
    <div>
        <v-row width="100%">
            <v-col>
                This is a test
                {{schema}}

                <v-btn>
                    Testing 1234
                </v-btn>
            </v-col>
        </v-row>
    </div>
</template>

<script>

export default {
    name: "CRoutingControl",
    props: {
        schema: {
            required: false,
//            required: true,
            type: Object,
            default: () => {
                return {

                }
            }
        }
    },
    computed: {
        _schema: {
            get: function() {
                return this.schema;
            },
            set: function(val) {
                return this.$emit('update:schema', val);
            }
        }
    },
    methods: {
    },

}
</script>

<style scoped>
</style>
