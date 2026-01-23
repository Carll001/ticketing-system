<script setup lang="ts">
import TaskCard from '@/components/task-components/TaskCard.vue';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import taskLink from '@/routes/task';
import { type BreadcrumbItem, Task } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    tasks:  {data: Task[]};
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(
        taskLink.index.url(),
        { search: value },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Task',
        href: taskLink.index().url,
    },
];

// const assignedUser = computed(() => {
//     if (!form.assigned_to) return 'Anyone';

//     const user = props.users.find(
//         (user) => String(user.id) === form.assigned_to,
//     );

//     return user?.name;
// });


const createTask = () => {
    router.visit(taskLink.create(), {
        preserveState: false,
    });
};
</script>
<template>
    <Head title="Task" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex justify-end">
                <Button @click="createTask" class="cursor-pointer">Creat Task</Button>
            </div>
            <div class="relative w-120">
                <Search
                    class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input v-model="search" class="pl-10" placeholder="Search..." />
            </div>
            <div class="flex flex-col gap-4">
                <TaskCard :tasks="props.tasks.data" />
            </div>
        </div>
    </AppLayout>
</template>
