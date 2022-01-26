<template>
    <v-card
        class="mx-auto"
        max-width="344"
        outlined
    >

        {{client}}
        <v-card-title>
            {{client.name}}
        </v-card-title>

        <v-card-subtitle v-if="client.description">
            {{ client.description }}
        </v-card-subtitle>

<!--        <v-card-text>-->
<!--            <v-chip-->
<!--                outlined-->
<!--                class="ma-2"-->
<!--                color="indigo"-->
<!--            >-->
<!--                <v-icon left>-->
<!--                    mdi-ruler-->
<!--                </v-icon>-->
<!--                {{ convertDistance(activity.distance) }}-->
<!--            </v-chip>-->
<!--            &lt;!&ndash;&ndash;&gt;-->
<!--            <v-chip-->
<!--                outlined-->
<!--                color="indigo"-->
<!--            >-->
<!--                <v-icon left>-->
<!--                    mdi-calendar-range-->
<!--                </v-icon>-->
<!--                {{ toDateTime(activity.started_at) }}-->
<!--            </v-chip>-->
<!--        </v-card-text>-->

<!--        &lt;!&ndash;            <v-list-item-avatar&ndash;&gt;-->
<!--        &lt;!&ndash;                tile&ndash;&gt;-->
<!--        &lt;!&ndash;                size="80"&ndash;&gt;-->
<!--        &lt;!&ndash;                color="grey"&ndash;&gt;-->
<!--        &lt;!&ndash;            ></v-list-item-avatar>&ndash;&gt;-->
<!--        &lt;!&ndash;        </v-list-item>&ndash;&gt;-->

<!--        <v-card-actions>-->
<!--            <v-btn-->
<!--                color="deep-purple lighten-2"-->
<!--                text-->
<!--                @click="$inertia.get(route('activity.show', activity.id))"-->
<!--            >-->
<!--                View-->
<!--            </v-btn>-->

<!--            <v-spacer></v-spacer>-->

<!--        </v-card-actions>-->

    </v-card>
</template>

<script>
export default {
    name: "CStravaClientCard",
    props: {
        client: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
