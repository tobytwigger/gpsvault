<template>
    <Link :href="item.href">
        <div>
            <i
                class="vsm--icon"
                :class="item.icon"
                aria-hidden="true"
            >
            </i>

            {{item.title}}
        </div>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default {
    name: "VSidebarLink",
    components: {Link},
    props: {
        item: {
            required: true,
            type: Object
        }
    }
}
</script>

<style scoped>

</style>
