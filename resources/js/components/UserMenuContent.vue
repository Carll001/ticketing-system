<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';

import { index as transactions } from '@/routes/transaction';
import { index as rejectedSteps } from '@/routes/rejectedStep';
import PermissionGuard from './PermissionGuard.vue';

interface Props {
    user: User;
}

const handleLogout = () => {
    router.flushAll();
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <PermissionGuard permission="can view transactions">
            <DropdownMenuItem :as-child="true">
                <Link class="block w-full cursor-pointer" :href="transactions()" prefetch>
                    <Settings class="mr-2 h-4 w-4" />
                    Transactions
                </Link>
            </DropdownMenuItem>
        </PermissionGuard>

        <PermissionGuard permission="can view rejected steps">
            <DropdownMenuItem :as-child="true">
                <Link class="block w-full cursor-pointer" :href="rejectedSteps()" prefetch>
                    <Settings class="mr-2 h-4 w-4" />
                    Rejected Steps
                </Link>
            </DropdownMenuItem>
        </PermissionGuard>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" :href="edit()" prefetch>
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link class="block w-full cursor-pointer" :href="logout()" @click="handleLogout" as="button"
            data-test="logout-button">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
