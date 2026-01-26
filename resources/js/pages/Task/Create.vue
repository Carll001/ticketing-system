<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner'
import TaskCreateForm from '@/components/task-components/TaskCreateForm.vue'
import { BreadcrumbItem, Department } from '@/types';
import taskLink from '@/routes/task';

const props = defineProps<{
    departments: Department[],
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: 'Create',
        href: taskLink.create().url,
    },
];


</script>
<template>
    <Head title="Create Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="p-4 flex flex-col flex-1">
            <TaskCreateForm :departments="departments"/>
        </div>
    </AppLayout>
</template>