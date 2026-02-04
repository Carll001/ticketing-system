<script setup lang="ts">
import { Card } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { computed } from 'vue';

const props = defineProps<{
    totalUsers: number;
    staffCount: number;       // Add this
    adminCount: number;       // Add this
    totalDepartments: number;
    totalTasks: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 lg:grid-cols-3">
                <Card class="flex items-center justify-between p-6">
                    <div>
                        <p class="flex text-center text-sm text-muted-foreground">
                            Admin
                        </p>
                        <p class="item-center flex justify-center text-3xl font-bold">
                            {{ props.adminCount }}
                        </p>
                    </div>
                </Card>
                <Card class="flex items-center justify-between p-6">
                    <div>
                        <p class="flex text-center text-sm text-muted-foreground">
                            Staff
                        </p>
                        <p class="item-center flex justify-center text-3xl font-bold">
                            {{ props.staffCount }}
                        </p>
                    </div>
                </Card>
                <Card class="flex items-center justify-between p-6">
                    <div>
                        <p class="flex text-center text-sm text-muted-foreground">
                            All User
                        </p>
                        <p class="item-center flex justify-center text-3xl font-bold">
                            {{ props.totalUsers }}
                        </p>
                    </div>
                </Card>

                <Card class="flex items-center justify-between p-6">
                    <div class="text-center">
                        <p class="text-sm text-muted-foreground">
                            Total Departments
                        </p>
                        <p class="text-3xl font-bold">
                            {{ props.totalDepartments }}
                        </p>
                    </div>
                </Card>

                <Card class="flex items-center justify-between p-6">
                    <div class="text-center">
                        <p class="text-sm text-muted-foreground">Total Tasks</p>
                        <p class="text-3xl font-bold">{{ props.totalTasks }}</p>
                    </div>
                </Card>
            </div>

            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
