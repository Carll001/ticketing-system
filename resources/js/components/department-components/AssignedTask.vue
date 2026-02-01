<script setup lang="ts">
import comment from '@/routes/step/comment';
import { Badge } from '../ui/badge';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../ui/table';
import { Task } from '@/types';

const props = defineProps<{
    tasks: Task[]
}>();
</script>
<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead>Task Title</TableHead>
                <TableHead>Due Date</TableHead>
                <TableHead>Status</TableHead>
            </TableRow>
        </TableHeader>

        <TableBody>
            <TableRow v-for="task in tasks" :key="task.id">
                <TableCell class="font-medium">{{ task.title }}</TableCell>
                <TableCell>{{ task.due_date ? new Date(task.due_date).toLocaleDateString() : '-' }}</TableCell>
                <TableCell>
                    <Badge variant="secondary">Assigned</Badge>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>