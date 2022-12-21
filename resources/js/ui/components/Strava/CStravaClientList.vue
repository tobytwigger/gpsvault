<template>
    <div>
        <c-iterator :key="stravaClientScroller" :paginator="clients" item-key="id" ref="apiScroller" :infinite-scroll="true">
            <template #default="{item, index, deleteItem}">
                <c-strava-client-card :client="item" :type="item.type"></c-strava-client-card>
            </template>
        </c-iterator>
    </div>
</template>

<script>
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CStravaClientCard from './CStravaClientCard';
import CApiScrollIterator from '../../reusables/table/CApiScrollIterator';

export default {
    name: "CStravaClientList",
    components: {CApiScrollIterator, CStravaClientCard, CPaginationIterator},
    props: {
        clients: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            stravaClientScroller: (Math.random() + 1).toString(36).substring(7)
        }
    },
}
</script>

<style scoped>

</style>
