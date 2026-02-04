<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Task, User } from '@/types';
import { Badge } from '../ui/badge';
import { useInitials } from '@/composables/useInitials';
import { Separator } from '../ui/separator';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const auth = computed(() => page.props.auth);
const { getInitials } = useInitials();

const props = defineProps<{
    user: User;
    tasks?: Task[];
    userPermissions?: string[];
}>();

// // Debug: Log the tasks to see if they have steps
// console.log('ShowUser tasks:', props.tasks);
// if (props.tasks) {
//     props.tasks.forEach(task => {
//         console.log(`Task: ${task.title}, Steps:`, task.steps);
//     });
// }

</script>

<template>
    <div class="flex gap-6">
        <!-- Profile Aside -->
        <aside class="w-80 flex-shrink-0">
            <Card class="w-80">
                <CardHeader class="items-center space-y-4 text-center">
                    <!-- Avatar -->
                    <div class="flex justify-center">
                        <div
                            class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white">
                            <span class="text-3xl font-bold">
                                {{ getInitials(user.name) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <CardTitle class="text-xl">{{ user.name }}</CardTitle>
                        <p class="text-sm text-muted-foreground">
                            {{ user.email }}
                        </p>
                    </div>
                </CardHeader>

                <CardContent class="space-y-6">
                    <!-- Departments -->
                    <div class="space-y-2 text-center">
                        <h3 class="text-sm font-semibold">Departments</h3>

                        <div v-if="user.departments && user.departments.length"
                            class="flex flex-wrap justify-center gap-2">
                            <Badge v-for="dept in user.departments" :key="dept.id" variant="outline" class="rounded-lg">
                                {{ dept.name }}
                            </Badge>
                        </div>

                        <p v-else class="text-sm text-muted-foreground italic">
                            No departments assigned
                        </p>
                    </div>

                    <div>
                        <Separator class="my-4" />
                        <h3 class="text-sm font-semibold text-center">Permissions</h3>

                        <div v-if="userPermissions && userPermissions.length"
                            class="flex flex-wrap justify-center gap-2 mt-2">
                            <Badge v-for="perm in userPermissions" :key="perm" variant="outline" class="rounded-lg">
                                {{ perm }}
                            </Badge>
                        </div>

                        <p v-else class="text-sm text-muted-foreground italic text-center mt-2">
                            No permissions assigned
                        </p>
                    </div>
                    <Separator class="my-4" />
                    <!-- User Meta -->
                    <div class="grid gap-4 text-sm flex text-center">
                        <div>
                            <p class="text-xs text-muted-foreground">Created</p>
                            <p class="font-medium">
                                {{
                                    new Date(
                                        user.created_at,
                                    ).toLocaleDateString()
                                }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </aside>

        <!-- Tasks Section -->
        <div class="flex-1">
            <div class="rounded-lg border shadow-sm">
                <div class="border-b p-6">
                    <h3 class="text-lg font-semibold">Assigned Steps</h3>
                </div>

                <div v-if="tasks && tasks.length > 0" class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="font-bold">Task Title</TableHead>
                                <TableHead class="font-bold">Steps Title</TableHead>
                                <TableHead class="font-bold">Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="task in tasks" :key="task.id">
                                <TableCell class="font-medium">{{
                                    task.title
                                }}</TableCell>
                                <TableCell>
                                    <div v-if="task.steps && task.steps.length" class="flex flex-wrap gap-2">
                                        <RouterLink v-for="step in task.steps" :key="step.id" :to="`/steps/${step.id}`">
                                            {{ step.title }}
                                        </RouterLink>
                                    </div>
                                    <span v-else class="text-sm text-muted-foreground">-</span>
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline" class="cursor-pointer hover:bg-accent text-blue-600">
                                        Assigned
                                    </Badge>
                                    <Badge variant="outline" class="cursor-pointer hover:bg-accent text-yellow-600">
                                        Pending
                                    </Badge>
                                    <Badge variant="outline" class="cursor-pointer hover:bg-accent text-green-600">
                                        Completed
                                    </Badge>
                                    <Badge variant="outline" class="cursor-pointer hover:bg-accent text-red-600">
                                        Cancelled
                                    </Badge>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <div v-else class="p-6 text-center text-muted-foreground">
                    <p>No tasks assigned to this user</p>
                </div>
            </div>
        </div>
    </div>
    <!-- <pre>{{ props }}</pre> -->
</template>
