<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>
<template>
    <AppLayout>
        <Head title="Task" />
        <div class="p-4 flex flex-col flex-1">
            <Heading title="Tasks"/>
        </div>
    </AppLayout>
</template>