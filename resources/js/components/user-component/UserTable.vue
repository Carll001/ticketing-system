<script setup lang="ts">
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { User } from '@/types';
import { Button } from '@/components/ui/button';
import { Badge } from '../ui/badge';
import { router } from '@inertiajs/vue3';
import userLink from '@/routes/user';

const props = defineProps<{
    users: User[],
}>();

const editUser = (id: string) => {
    router.visit(userLink.show(id).url)
}

</script>
<template>
    <Table>
        <TableCaption>A list of your recent invoices.</TableCaption>
        <TableHeader>
            <TableRow>
                <TableHead>

                </TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Department</TableHead>
                <TableHead class="text-right">
                    Action
                </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow v-for="(user, index) in props.users" :key="user.id">
                <TableCell class="font-medium">
                    {{ index + 1 }}
                </TableCell>
                <TableCell>{{ user.name }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell>
                    <div v-if="user.departments && user.departments?.length > 0" class="flex flex-wrap gap-1">
                        <Badge v-for="dept in user.departments" :key="dept.id" variant="outline" class="rounded-lg">
                            {{ dept.name }}
                        </Badge>
                    </div>
                    <span v-else class="text-muted-foreground text-sm italic">
                        No departments
                    </span>
                </TableCell>
                <TableCell class="text-right">
                    <div class="space-x-2">
                        <Button size="sm" variant="destructive">Delete</Button>
                        <Button size="sm" variant="secondary">Edit</Button>
                        <Button size="sm" variant="default" @click="editUser(user.id)">View</Button>
                    </div>
                </TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>