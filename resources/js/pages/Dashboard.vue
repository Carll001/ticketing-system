<script setup lang="ts">
import TransactionTable from '@/components/tranasction-components/TransactionTable.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Building2, UserCheck, UserCog, Users } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    totalUsers: number;
    staffCount: number;
    adminCount: number;
    totalDepartments: number;
    totalTasks: number;
    transactions: any[];
}>();

// Calculate percentages and additional stats
const userStats = computed(() => ({
    adminPercentage: (props.adminCount / props.totalUsers) * 100,
    staffPercentage: (props.staffCount / props.totalUsers) * 100,
    userDistribution: [
        { role: 'Admin', count: props.adminCount, color: 'bg-blue-500' },
        { role: 'Staff', count: props.staffCount, color: 'bg-emerald-500' },
        { role: 'Total', count: props.totalUsers, color: 'bg-purple-500' },
    ],
}));

const quickAccessButtons = [
    {
        label: 'Add Users',
        href: '',
        icon: '',
        permission: '',
    },
    {
        label: 'Add Task',
        href: '',
        icon: '',
        permission: '',
    },
    {
        label: 'Add Preset',
        href: '',
        icon: '',
        permission: '',
    },
    {
        label: 'Add Department',
        href: '',
        icon: '',
        permission: '',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">
                            Dashboard Overview
                        </h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Welcome back! Here's what's happening with your
                            organization today.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Users Card -->
                <Card
                    class="p-6 transition-shadow duration-200 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <Users class="h-4 w-4" />
                                Total Users
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ props.totalUsers }}
                            </p>
                        </div>
                        <div class="rounded-full bg-blue-600/10 p-3">
                            <Users
                                class="h-6 w-6 rounded-lg text-blue-600 dark:text-blue-400"
                            />
                        </div>
                    </div>
                </Card>

                <!-- Staff Card -->
                <Card
                    class="p-6 transition-shadow duration-200 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <UserCheck class="h-4 w-4" />
                                Staff Members
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ props.staffCount }}
                            </p>
                        </div>
                        <div
                            class="rounded-full bg-emerald-50 p-3 dark:bg-emerald-500/10"
                        >
                            <UserCheck
                                class="h-6 w-6 text-emerald-600 dark:text-emerald-400"
                            />
                        </div>
                    </div>
                </Card>

                <!-- Admin Card -->
                <Card
                    class="p-6 transition-shadow duration-200 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <UserCog class="h-4 w-4" />
                                Administrators
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ props.adminCount }}
                            </p>
                        </div>
                        <div
                            class="rounded-full bg-purple-50 p-3 dark:bg-purple-500/10"
                        >
                            <UserCog
                                class="h-6 w-6 text-purple-600 dark:text-purple-400"
                            />
                        </div>
                    </div>
                </Card>

                <!-- Departments Card -->
                <Card
                    class="p-6 transition-shadow duration-200 hover:shadow-lg"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                            >
                                <Building2 class="h-4 w-4" />
                                Departments
                            </p>
                            <p class="mt-2 text-3xl font-bold">
                                {{ props.totalDepartments }}
                            </p>
                        </div>
                        <div
                            class="rounded-full bg-amber-50 p-3 dark:bg-amber-500/10"
                        >
                            <Building2
                                class="h-6 w-6 text-amber-600 dark:text-amber-400"
                            />
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Bottom Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Tasks Card -->
                <div class="lg:col-span-2">
                    <Card class="h-full p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">
                                    Tranaction Overview
                                </h3>
                                <p class="text-sm text-muted-foreground">
                                    Total transactions in the system
                                </p>
                            </div>
                        </div>
                        <div class="lg:col-span-2">
                            <!-- Table wrapper -->
                            <div
                                class="flex-1 overflow-x-hidden overflow-y-auto"
                            >
                                <TransactionTable
                                    :transactions="props.transactions"
                                />
                            </div>
                        </div>
                    </Card>
                </div>

                <div class="space-y-4">
                    <Card class="h-fit p-6">
                        <CardHeader>
                            <CardTitle>User Distribution</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div>
                              
                                <div class="space-y-3 p-2 pr-0">
                                    <div
                                        v-for="stat in userStats.userDistribution"
                                        :key="stat.role"
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-2">
                                            <div
                                                :class="[
                                                    'h-2 w-2 rounded-full',
                                                    stat.color,
                                                ]"
                                            ></div>
                                            <span class="text-sm">{{
                                                stat.role
                                            }}</span>
                                        </div>
                                        <span class="text-sm font-medium">{{
                                            stat.count
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                <!-- Quick Stats -->
            </div>
        </div>
    </AppLayout>
</template>
