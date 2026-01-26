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
import { Department, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { Badge } from '../ui/badge';
import DeleteUserModal from './DeleteUserModal.vue';
import EditUserForm from './EditUserForm.vue';


const props = defineProps<{
    users: User[];
    departments?: Department[];
}>();

</script>
<template>
    <div class="overflow-hidden rounded-lg border">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Department</TableHead>
                    <TableHead class="text-right"> Action </TableHead>
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
                        <div
                            v-if="
                                user.departments && user.departments?.length > 0
                            "
                            class="flex flex-wrap gap-1"
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
                        <span
                            v-else
                            class="text-sm text-muted-foreground italic"
                        >
                            No departments
                        </span>
                    </TableCell>
                    <TableCell class="text-right">
                        <div class="space-x-2">
                            <DeleteUserModal :user="user" />
                            <EditUserForm
                                :user="user"
                                :departments="props.departments"
                            />
                            <Button
                                size="sm"
                                variant="default"
                                @click="router.visit(`/user/${user.id}`)"
                                >View</Button
                            >
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
