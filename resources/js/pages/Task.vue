<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import Create from '@/components/task-components/Create.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

</script>
<template>
    <AppLayout>
        <Head title="Task" />
        <div class="p-4 flex flex-col flex-1">
            <div class="flex items-center justify-between">
                <Heading title="Tasks"/>
                <Create/>
            </div>
            
        </div>
    </AppLayout>
</template>