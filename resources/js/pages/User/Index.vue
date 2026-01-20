<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Department, User } from '@/types';
import CreateUserForm from '@/components/user-component/CreateUserForm.vue';
import UserTable from '@/components/user-component/UserTable.vue';

const props = defineProps<{
    users: User[],
    departments: Department[],
}>();

</script>
<template>

    <Head title="Manage Users" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <section class="flex justify-between items-center">
                <div>
                    <h3 class="texl-xl">Manage Users</h3>
                    <p class="text-sm text-muted-foreground">manage user</p>
                </div>
                <div>
                    <CreateUserForm :departments="props.departments"/>
                </div>
            </section>
            <section>
                <UserTable :users="props.users"/>
            </section>
        </div>
    </AppLayout>
</template>