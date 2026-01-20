<script setup lang="ts">
import { User, Task } from '@/types';
import { Badge } from '../ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

const props = defineProps<{
    user: User;
    tasks?: Task[];
}>();
</script>

<template>
  <div class="flex gap-6">
        <!-- Profile Aside -->
        <aside class="w-80 flex-shrink-0">
            <div class="rounded-lg border bg-white p-6 shadow-sm">
                <!-- Avatar Circle -->
                <div class="mb-6 flex justify-center">
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white">
                        <span class="text-3xl font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                </div>

                <!-- User Info -->
                <div class="space-y-4 text-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ user.name }}</h2>
                        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                    </div>

                    <!-- Departments -->
                    <div class="space-y-2">
                        <h3 class="text-sm font-semibold">Departments</h3>
                        <div v-if="user.departments && user.departments.length > 0" class="flex flex-wrap justify-center gap-2">
                            <Badge v-for="dept in user.departments" :key="dept.id" variant="outline" class="rounded-lg">
                                {{ dept.name }}
                            </Badge>
                        </div>
                        <div v-else class="text-sm text-muted-foreground italic">
                            No departments assigned
                        </div>
                    </div>

                    <!-- User Details -->
                    <div class="grid grid-cols-2 gap-3 border-t pt-4 text-left">
                        <div>
                            <p class="text-xs text-muted-foreground">Created</p>
                            <p class="text-sm font-medium">{{ new Date(user.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">Last Updated</p>
                            <p class="text-sm font-medium">{{ new Date(user.updated_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Tasks Section -->
        <div class="flex-1">
            <div class="rounded-lg border bg-white shadow-sm">
                <div class="border-b p-6">
                    <h3 class="text-lg font-semibold">Assigned Tasks</h3>
                </div>

                <div v-if="tasks && tasks.length > 0" class="overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Task Title</TableHead>
                                <TableHead>Description</TableHead>
                                <TableHead>Type</TableHead>
                                <TableHead>Due Date</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="task in tasks" :key="task.id">
                                <TableCell class="font-medium">{{ task.title }}</TableCell>
                                <TableCell class="max-w-xs text-sm text-muted-foreground truncate">
                                    {{ task.description || '-' }}
                                </TableCell>
                                <TableCell>
                                    <Badge variant="outline">{{ task.type }}</Badge>
                                </TableCell>
                                <TableCell>{{ new Date(task.due_date).toLocaleDateString() }}</TableCell>
                                <TableCell>
                                    <Badge variant="secondary">Assigned</Badge>
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
</template>
