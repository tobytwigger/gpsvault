<template>
    <div>
        <v-row>
            <v-col>
                <v-card class="pa-2 ma-4">
                        <v-row>
                            <v-col cols="4">
                                <v-card-title
                                    class="text-center"
                                    v-text="title"
                                ></v-card-title>

                                <v-card-subtitle v-text="description"></v-card-subtitle>

                                <slot name="body"></slot>

<!--                                <v-card-actions>-->
<!--                                    <v-spacer></v-spacer>-->
<!--                                    <slot name="actions"></slot>-->
<!--                                </v-card-actions>-->
                            </v-col>
                            <v-col cols="8">
                                <slot name="default"></slot>
                            </v-col>
                        </v-row>
                </v-card>
            </v-col>
        </v-row>


    </div>
</template>

<script>
export default {
    name: "CFormSection",
    props: {
        title: {
            required: true,
            type: String
        },
        description: {
            required: true,
            type: String
        },
    }
}
</script>

<style scoped>

</style>
