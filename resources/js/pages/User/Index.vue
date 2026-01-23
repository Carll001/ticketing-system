<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import CreateUserForm from '@/components/user-component/CreateUserForm.vue';
import UserTable from '@/components/user-component/UserTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Department, User } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { type BreadcrumbItem } from '@/types';
import { ref, watch } from 'vue';
import user from '@/routes/user';
import PermissionGuard from '@/components/PermissionGuard.vue';

const props = defineProps<{
    users: User[];
    departments: Department[];
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(
        user.index.url(),
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users Management',
        href: ''
    },
];

</script>
<template>
    <Head title="Manage Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <section class="flex items-center justify-between">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" class="pl-10" placeholder="Search..." />
                </div>
                <div>
                    <PermissionGuard permission="can create user">
                        <CreateUserForm :departments="props.departments" />
                    </PermissionGuard>
                </div>
            </section>
            <section>
                <UserTable :users="props.users" :departments="props.departments" />
            </section>
        </div>
    </AppLayout>
</template>
