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
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
const props = defineProps<{
    user: User;
    tasks?: Task[];
}>();
</script>

<template>
  <div class="flex gap-6">
        <!-- Profile Aside -->
        <aside class="w-80 flex-shrink-0">
             <Card class="w-80">
        <CardHeader class="items-center text-center space-y-4">
            <!-- Avatar -->
            <div
                class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white"
            >
                <span class="text-3xl font-bold">
                    {{ user.name.charAt(0).toUpperCase() }}
                </span>
            </div>

            <div>
                <CardTitle class="text-xl">{{ user.name }}</CardTitle>
                <p class="text-sm text-muted-foreground">{{ user.email }}</p>
            </div>
        </CardHeader>

        <CardContent class="space-y-6">
            <!-- Departments -->
            <div class="space-y-2 text-center">
                <h3 class="text-sm font-semibold">Departments</h3>

                <div
                    v-if="user.departments && user.departments.length"
                    class="flex flex-wrap justify-center gap-2"
                >
                    <Badge
                        v-for="dept in user.departments"
                        :key="dept.id"
                        variant="outline"
                        class="rounded-lg"
                    >
                        {{ dept.name }}
                    </Badge>
                </div>

                <p v-else class="text-sm text-muted-foreground italic">
                    No departments assigned
                </p>
            </div>

            <!-- User Meta -->
            <div class="grid grid-cols-2 gap-4 border-t pt-4 text-sm">
                <div>
                    <p class="text-xs text-muted-foreground">Created</p>
                    <p class="font-medium">
                        {{ new Date(user.created_at).toLocaleDateString() }}
                    </p>
                </div>

                <div>
                    <p class="text-xs text-muted-foreground">Updated</p>
                    <p class="font-medium">
                        {{ new Date(user.updated_at).toLocaleDateString() }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
        </aside>

        <!-- Tasks Section -->
        <div class="flex-1">
            <div class="rounded-lg border  shadow-sm">
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
