<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    permission: {
        type: String,
        required: true
    }
});

const page = usePage();

const hasPermission = computed(() => {
    // 1. Safe access to user
    const user = page.props.auth?.user;
    
    // 2. Ensure 'can' is treated as an array even if it's missing
    const permissions = user?.can || [];
    
    return permissions.includes(props.permission);
});
</script>

<template>
    <slot v-if="hasPermission" />
</template>