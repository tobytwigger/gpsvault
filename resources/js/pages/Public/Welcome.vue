<template>
    <c-public-wrapper title="Welcome">
        <cycle-store-demo></cycle-store-demo>
    </c-public-wrapper>
</template>

<script>
import CPublicWrapper from 'ui/layouts/CPublicWrapper';
import CycleStoreDemo from './CycleStoreDemo';
export default {
    name: "Welcome",
    components: {CycleStoreDemo, CPublicWrapper}
}
</script>

<style scoped>

</style>
