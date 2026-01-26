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
    transaction: Transaction[]
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
                    <!-- <TableHead class="text-right"> Action </TableHead> -->
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="(transaction, index) in props.transaction" :key="transaction.id">
                    <TableCell class="font-medium">
                        {{ index + 1 }}
                    </TableCell>
                    <TableCell>{{ transaction.transaction_number }}</TableCell>
                    <TableCell>
                        <Link v-if="transaction.user" :href="user.show({ id: transaction.user.id }).url" class="hover:underline">
                            {{ transaction.user.name }}
                        </Link>
                        {{ transaction.content }} : 
                        <Link v-if="transaction.task" :href="task.show({id: transaction.task.id}).url" class="hover:underline">
                            {{ transaction.task.title }}
                        </Link>
                        <Link v-if="transaction.step" :href="task.show({id: transaction.step.id}).url" class="hover:underline">
                            {{ transaction.step.title }}
                        </Link>
                        <Link v-if="transaction.department" :href="task.show({id: transaction.department.id}).url" class="hover:underline">
                            {{ transaction.department.name }}
                        </Link>
                    </TableCell>
                    <!-- <TableCell class="text-right">
                        <div class="space-x-2">

                        </div>
                    </TableCell> -->
                </TableRow>
            </TableBody>
        </Table>
        <pre>{{ props.transaction }}</pre>
    </div>
</template>
