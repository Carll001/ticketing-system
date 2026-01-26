<script setup lang="ts">
import TransactionTable from '@/components/tranasction-components/TransactionTable.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Transaction } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';

const props = defineProps<{
    transactions: Transaction[]
}>();

</script>
<template>

    <Head title="transaction" />
    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <section class="flex items-center justify-between">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input  class="pl-10" placeholder="Search..." />
                </div>
                <!-- <div>
                    <PermissionGuard permission="can create user">
                        <CreateUserForm :departments="props.departments" />
                    </PermissionGuard>
                </div> -->
            </section>
            <section>
                <TransactionTable :transaction="props.transactions"/>
            </section>
        </div>
    </AppLayout>
</template>