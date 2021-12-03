<template>
    <div>

        <ul class="list-disc">
            <li>The import ran on {{results.length}} activities.</li>
            <li>Of these {{results.length}}, {{successful.length}} were new activities that have now been imported</li>
            <li>Of the remaining {{results.length - successful.length}}, {{duplicated.length}} were new activities that have previously been imported</li>
            <li>Finally, {{unmatched.length}} were given in the archive but could not be matched up to an activity, and {{exception.length}} activities really didn't want to import.</li>
        </ul>


    </div>
</template>

<script>
export default {
    name: "StravaActivity",
    props: {
        results: {
            required: true,
            type: Array
        }
    },
    computed: {
        unmatched() {
            return this.results.filter(r => r.data && r.message === 'unmatched');
        },
        duplicated() {
            return this.results.filter(r => r.data && r.message === 'duplicate');
        },
        exception() {
            return this.results.filter(r => r.data && r.message === 'exception');
        },
        successful() {
            return this.results.filter(r => r.success === true);
        }
    }
}
</script>

<style scoped>

</style>
