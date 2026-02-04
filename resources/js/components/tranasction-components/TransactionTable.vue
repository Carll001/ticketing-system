<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Department, Transaction, User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { Badge } from '../ui/badge';
import user from '@/routes/user';
import task from '@/routes/task';

const props = defineProps<{
    transactions: Transaction[]
}>();


</script>
<template>
    <div class="overflow-hidden rounded-lg border">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Transaction Number</TableHead>
                    <TableHead>Content</TableHead>
                    <TableHead></TableHead>
                    <!-- <TableHead class="text-right"> Action </TableHead> -->
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="(transaction, index) in props.transactions" :key="transaction.id">
                    <TableCell class="font-medium">
                        {{ index + 1 }}
                    </TableCell>
                    <TableCell>{{ transaction.transaction_number }}</TableCell>
                    <TableCell>{{ transaction.content }}</TableCell>
                    <TableCell class="text-right">{{ new Date(transaction.created_at).toLocaleString() }}</TableCell>
                    <!-- <TableCell class="text-right">
                        <div class="space-x-2">

                        </div>
                    </TableCell> -->
                </TableRow>
            </TableBody>
        </Table>
        
    </div>
</template>
