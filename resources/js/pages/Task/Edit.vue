<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { ChevronLeft } from 'lucide-vue-next';
import { Separator } from '@/components/ui/separator';
import { toast } from 'vue-sonner'
import TaskCreateForm from '@/components/task-components/TaskCreateForm.vue'
import { BreadcrumbItem, Department, Task } from '@/types';
import TaskEditForm from '@/components/task-components/TaskEditForm.vue';
import taskLink from '@/routes/task';

const props = defineProps<{
    departments: Department[],
    task: {data: Task},
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
    {
        title: props.task.data.title,
        href: taskLink.show(props.task.data.id).url,
    },
    {
        title: `Edit ${props.task.data.title}`,
        href: taskLink.edit(props.task.data.id).url,
    },
];



</script>
<template>
    <Head :title=" `Edit ${props.task.data.title}` " />
    <AppLayout :breadcrumbs="breadcrumbs">
        
        <div class="p-4 flex flex-col flex-1">
            <TaskEditForm :departments="departments" :task="task"/>
        </div>
    </AppLayout>
</template>