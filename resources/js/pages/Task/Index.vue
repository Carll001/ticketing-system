<script setup lang="ts">
import PermissionGuard from '@/components/PermissionGuard.vue';
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
    tasks: {
        data: Task[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search ?? '');

const goToPage = (page: number) => {
    if (page >= 1 && page <= props.tasks.last_page) {
        router.get(
            taskLink.index.url(),
            { page, search: search.value || undefined },
            {
                preserveState: true,
                replace: true,
            },
        );
    }
};

watch(search, (value) => {
    router.get(
        taskLink.index.url(),
        { search: value ?? '' },
        {
            preserveState: true,
            replace: true
        }
    );
});


// watch(search, (value) => {
//     router.get(
//         taskLink.index.url(),
//         { search: value },
//         {
//             preserveState: true,
//             replace: true,
//         },
//     );
// });

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
                <PermissionGuard permission="can create task">
                    <Button @click="createTask" class="cursor-pointer">Creat Task</Button>
                </PermissionGuard>
            </div>
            <div class="relative w-120">
                <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input v-model="search" class="pl-10" placeholder="Search..." />
            </div>
            <div class="flex flex-col gap-4">
                <TaskCard :tasks="props.tasks.data" />
            </div>
            <!-- pagination -->
            <div class="border-t px-6 py-4">
                <div class="flex items-center justify-between">
                    <p class="text-sm">Showing {{ props.tasks.from }} to {{ props.tasks.to }} of {{ props.tasks.total }}
                        tasks</p>
                    <div class="flex gap-2">
                        <Button variant="outline" size="sm" :disabled="props.tasks.current_page === 1"
                            @click="goToPage(props.tasks.current_page - 1)">Previous</Button>
                        <Button variant="outline" size="sm"
                            :disabled="props.tasks.current_page === props.tasks.last_page"
                            @click="goToPage(props.tasks.current_page + 1)">Next</Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
