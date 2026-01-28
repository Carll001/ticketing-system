<script setup lang="ts">
import TransactionTable from '@/components/tranasction-components/TransactionTable.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Transaction } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import transaction from '@/routes/transaction';
import { ref, watch } from 'vue';



const props = defineProps<{
    transactions: {
        data: Transaction[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
}>();

const goToPage = (page: number) => {
    if (page >= 1 && page <= props.transactions.last_page) {
        router.get(
            transaction.index.url(),
            { page },
            {
                preserveState: true,
                replace: true,
            }
        );
    }
};

const search = ref('');

watch(search, (value) => {
    router.get(
        transaction.index.url(), // siguraduhing tama ang route
        { search: value ?? '' },
        { preserveState: true, replace: true }
    );
});

</script>
<template>

    <Head title="transaction" />
    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <section class="flex items-center justify-between">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" class="pl-10" placeholder="Search..." />

                </div>
                <!-- <div>
                    <PermissionGuard permission="can create user">
                        <CreateUserForm :departments="props.departments" />
                    </PermissionGuard>
                </div> -->
            </section>
            <section>
                <TransactionTable :transaction="props.transactions.data"/>
                <div class="border-t px-6 py-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm">Showing {{ props.transactions.from }} to {{ props.transactions.to }} of {{ props.transactions.total }} transactions</p>
                        <div class="flex gap-2">
                            <Button variant="outline" size="sm" :disabled="props.transactions.current_page === 1" @click="goToPage(props.transactions.current_page - 1)">Previous</Button>
                            <Button variant="outline" size="sm" :disabled="props.transactions.current_page === props.transactions.last_page" @click="goToPage(props.transactions.current_page + 1)">Next</Button>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>