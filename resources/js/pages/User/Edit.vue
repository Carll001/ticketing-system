<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Department, User } from '@/types';
import EditUserForm from '@/components/user-component/EditUserForm.vue';

const props = defineProps<{
    user: User,
    departments: Department[],
}>();
</script>
<template>
    <Head title="Edit User" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <section class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl">Edit User</h3>
                    <p class="text-sm text-muted-foreground">Update user information</p>
                </div>
            </section>
            <section class="bg-white rounded-lg p-6 shadow-sm">
                <EditUserForm :user="props.user" :departments="props.departments" />
            </section>
        </div>
    </AppLayout>
</template>
