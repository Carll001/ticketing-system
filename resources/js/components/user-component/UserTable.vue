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
import userLink from '@/routes/user';
import { Department, User } from '@/types';
import { router } from '@inertiajs/vue3';
import { Building, Eye, Pencil } from 'lucide-vue-next';
import PermissionGuard from '../PermissionGuard.vue';
import { Badge } from '../ui/badge';
import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '../ui/empty';
import DeleteUserModal from './DeleteUserModal.vue';

const props = defineProps<{
    users: User[];
    departments?: Department[];
    pagination?: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
}>();

const goToPage = (page: number) => {
    if (page >= 1 && page <= (props.pagination?.last_page ?? 1)) {
        const params = new URLSearchParams(window.location.search);
        const search = params.get('search') || '';
        router.get(window.location.pathname, {
            page,
            search: search || undefined,
        });
    }
};

const viewUser = (id: string) => {
    router.visit(userLink.show(id).url)
}

const editUser = (id: string) => {
    router.visit(userLink.edit(id).url)
}
</script>
<template>
    <div class="overflow-hidden rounded-lg border h-128 flex flex-col">
        <Table class="flex-1">
            <TableHeader>
                <TableRow>
                    <TableHead>ID</TableHead>
                    <TableHead>Name</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Role</TableHead>
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
                    <TableCell>{{ user.role }}</TableCell>
                    <TableCell>
                        <div v-if="
                            user.departments && user.departments?.length > 0
                        " class="flex w-md flex-wrap gap-1">
                            <Badge v-for="dept in user.departments" :key="dept.id" variant="outline" class="rounded-lg">
                                {{ dept.name }}
                            </Badge>
                        </div>
                        <span v-else class="text-sm text-muted-foreground italic">
                            No department
                        </span>
                    </TableCell>
                    <TableCell class="text-right">
                        <div class="space-x-2">
                            <PermissionGuard permission="can view user">
                              <Button size="sm" variant="outline" @click="viewUser(user.id)"> <Eye class="w-4 h-4"/> View</Button>
                            </PermissionGuard>
                            <PermissionGuard permission="can edit user">
                                <!-- <EditUserForm :user="user" :departments="props.departments" /> -->
                                <Button @click="editUser(user.id)" variant="secondary" size="sm"><Pencil class="w-4 h-4"/> Edit</Button>
                            </PermissionGuard>
                              <PermissionGuard permission="can delete user">
                                <DeleteUserModal :user="user" />
                            </PermissionGuard>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <!-- pagination here -->
        <div v-if="props.pagination" class="border-t px-6 py-4 mt-auto">
            <div class="flex items-center justify-between">
                <p class="text-sm">
                    Showing {{ props.pagination.from }} to
                    {{ props.pagination.to }} of {{ props.pagination.total }} users
                </p>
                <div class="flex gap-2">
                    <Button variant="outline" size="sm" :disabled="props.pagination.current_page === 1"
                        @click="goToPage(props.pagination.current_page - 1)">Previous</Button>
                    <Button variant="outline" size="sm" :disabled="props.pagination.current_page ===
                        props.pagination.last_page
                        " @click="goToPage(props.pagination.current_page + 1)">Next</Button>
                </div>
            </div>
        </div>
    </div>


</template>
